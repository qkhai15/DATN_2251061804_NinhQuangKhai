import os
# Cấu hình cờ cho PaddleOCR trên Windows ngay đầu file trước khi import bất kỳ thư viện AI nào khác!
os.environ["FLAGS_enable_pir_api"] = "0"
os.environ["FLAGS_use_mkldnn"] = "0"

import cv2
import numpy as np
import re
import traceback
import logging
from flask import Flask, request, jsonify
from flask_cors import CORS

logging.getLogger("ppocr").setLevel(logging.ERROR)
from paddleocr import PaddleOCR

app = Flask(__name__)
CORS(app)

print("[OCR] Loading PaddleOCR model...")
ocr = PaddleOCR(use_angle_cls=False, lang='en', show_log=False)
print("[OCR] PaddleOCR ready.")


# ─────────────────────────────────────────────────────────────────────────────
# UTIL
# ─────────────────────────────────────────────────────────────────────────────
def resize_to_max(img, max_dim=1280):
    h, w = img.shape[:2]
    if max(h, w) <= max_dim:
        return img
    scale = max_dim / max(h, w)
    return cv2.resize(img, (int(w * scale), int(h * scale)), interpolation=cv2.INTER_AREA)


# ─────────────────────────────────────────────────────────────────────────────
# STEP 1: Locate the black display band
# KEY: The band is ALWAYS in the UPPER portion of the photo (top 50%).
# Search only there to avoid false positives from dark areas below.
# ─────────────────────────────────────────────────────────────────────────────
def find_dark_band(img, search_top_ratio=0.55):
    """
    Find the black horizontal display band in the TOP `search_top_ratio` of image.
    Returns (x1, y1, x2, y2) or None.
    """
    h, w = img.shape[:2]

    # ── Only analyse the upper portion of the image
    search_h = int(h * search_top_ratio)
    roi = img[:search_h, :]

    gray = cv2.cvtColor(roi, cv2.COLOR_BGR2GRAY)
    gray = cv2.GaussianBlur(gray, (5, 5), 0)
    rh, rw = gray.shape

    best = None
    best_score = 0

    for thresh_val in [50, 70, 90, 110]:
        _, dark_mask = cv2.threshold(gray, thresh_val, 255, cv2.THRESH_BINARY_INV)

        # Strong horizontal closing: join digits inside the display strip
        kw = max(50, rw // 10)
        kh = max(4, rh // 40)
        kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (kw, kh))
        closed = cv2.morphologyEx(dark_mask, cv2.MORPH_CLOSE, kernel)

        cnts, _ = cv2.findContours(closed, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

        for cnt in cnts:
            x, y, cw, ch = cv2.boundingRect(cnt)
            if ch == 0:
                continue
            aspect = cw / float(ch)
            area   = cw * ch

            # Must be a wide horizontal band (aspect >= 3)
            if aspect < 3.0:
                continue
            # Must cover at least 25% of image width
            if cw < rw * 0.25:
                continue
            # Reasonable area
            if area < rw * rh * 0.005:
                continue

            # Prefer bands higher up (closer to top)
            # y_norm=0 → top of roi, y_norm=1 → bottom of roi
            cy_norm = (y + ch / 2) / rh
            score = area * (1.0 - cy_norm * 0.5)   # Higher = higher score
            score *= (1.0 - (thresh_val - 50) / 200.0)  # Stricter thresh = more confident

            if score > best_score:
                best_score = score
                # x1=0: band chạy từ mép trái, không cắt số đầu tiên
                # x2=rw: giữ toàn bộ chiều rộng (kWh sẽ được trim sau)
                pad_y = int(ch * 0.30)
                best = (
                    0,                             # x1: luôn về mép trái
                    max(0, y - pad_y),             # y1: thêm padding trên
                    rw,                            # x2: luôn về mép phải
                    min(search_h, y + ch + pad_y), # y2: thêm padding dưới
                )

    if best:
        print(f"[band] Found via threshold. box={best} in top {int(search_top_ratio*100)}% of image")
        return best

    # ── Row-scan fallback: look for rows with very low mean brightness
    row_means = gray.mean(axis=1)
    dark_rows = np.where(row_means < 90)[0]

    if len(dark_rows) >= 3:
        # Find longest consecutive run of dark rows
        runs, cs, ce = [], dark_rows[0], dark_rows[0]
        for i in range(1, len(dark_rows)):
            if dark_rows[i] == dark_rows[i - 1] + 1:
                ce = dark_rows[i]
            else:
                runs.append((cs, ce, ce - cs + 1))
                cs = ce = dark_rows[i]
        runs.append((cs, ce, ce - cs + 1))
        runs.sort(key=lambda r: -r[2])

        br = runs[0]
        if br[2] >= rh * 0.01:
            pad = int(br[2] * 0.35)
            y1 = max(0, br[0] - pad)
            y2 = min(search_h, br[1] + pad)
            mx = int(rw * 0.03)
            box = (mx, y1, rw - mx, y2)
            print(f"[band] Found via row-scan. box={box}")
            return box

    print("[band] Not found in top portion → using top 45% center crop as fallback")
    return None


# ─────────────────────────────────────────────────────────────────────────────
# STEP 2: Trim the kWh label + red fractional digit from the RIGHT side
# The rightmost ~20-22% of the display contains the red digit and "kWh" text
# which should NOT be read as part of the meter value.
# ─────────────────────────────────────────────────────────────────────────────
def trim_kwh_side(band_bgr):
    """
    Remove the right portion of the display band that contains the
    red fractional digit and 'kWh' label.
    Returns the trimmed crop (only the integer kWh digits remain).
    """
    h, w = band_bgr.shape[:2]

    # Detect red pixels: the fractional digit is RED on the GELEX EMIC meter
    hsv = cv2.cvtColor(band_bgr, cv2.COLOR_BGR2HSV)
    # Red hue wraps around 0/180 in HSV
    red_mask1 = cv2.inRange(hsv, (0, 80, 80), (10, 255, 255))
    red_mask2 = cv2.inRange(hsv, (165, 80, 80), (180, 255, 255))
    red_mask  = cv2.bitwise_or(red_mask1, red_mask2)

    # Sum red pixels per column
    col_red = red_mask.sum(axis=0)  # shape: (w,)

    # Find the leftmost column that has significant red (= start of red digit)
    red_threshold = h * 15  # at least 15 pixel-height of red
    red_cols = np.where(col_red > red_threshold)[0]

    if len(red_cols) > 0:
        cut_x = max(int(red_cols[0]) - 5, int(w * 0.60))
        print(f"[trim] Red digit detected at col {red_cols[0]}, cutting at col {cut_x}")
    else:
        # Fallback: cut at 78% (before kWh label)
        cut_x = int(w * 0.78)
        print(f"[trim] No red digit detected, cutting at col {cut_x} (78%)")

    return band_bgr[:, :cut_x]


# ─────────────────────────────────────────────────────────────────────────────
# STEP 3: Clean the display so only digit pixels remain (pure white on black)
# Removes:
#   - Cell dividers (thin vertical lines between digit compartments)
#   - Frame border pixels
#   - Noise
# ─────────────────────────────────────────────────────────────────────────────
def clean_display(band_bgr, scale=4):
    """
    Returns list of (name, gray_img) variants ready for OCR.
    """
    gray = cv2.cvtColor(band_bgr, cv2.COLOR_BGR2GRAY)
    bh, bw = gray.shape

    large = cv2.resize(gray, (bw * scale, bh * scale), interpolation=cv2.INTER_CUBIC)
    clahe = cv2.createCLAHE(clipLimit=4.0, tileGridSize=(4, 4))
    enhanced = clahe.apply(large)

    padded_raw = cv2.copyMakeBorder(large, 50, 50, 50, 50, cv2.BORDER_CONSTANT, value=0)
    kernel3 = np.ones((3, 3), np.uint8)
    dilated_raw = cv2.dilate(large, kernel3, iterations=1)
    dilated_clahe = cv2.dilate(enhanced, kernel3, iterations=1)
    _, thresh = cv2.threshold(large, 0, 255, cv2.THRESH_BINARY | cv2.THRESH_OTSU)
    dilated_thresh = cv2.dilate(thresh, kernel3, iterations=1)

    variants = [
        ("raw", large),
        ("clahe", enhanced),
        ("padded_raw", padded_raw),
        ("dilated_raw", dilated_raw),
        ("dilated_clahe", dilated_clahe),
        ("thresh", thresh),
        ("dilated_thresh", dilated_thresh)
    ]
    return variants


# ─────────────────────────────────────────────────────────────────────────────
# STEP 4: OCR
# ─────────────────────────────────────────────────────────────────────────────
def run_ocr(variants):
    candidates = []
    for name, gray in variants:
        bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
        try:
            results = ocr.ocr(bgr, cls=False)
            texts = []
            if results and results[0]:
                for bbox, (text, conf) in results[0]:
                    texts.append((text, round(conf, 2)))
                    digits = re.sub(r'[^0-9]', '', text)
                    if digits and conf > 0.10:
                        cx = sum(p[0] for p in bbox) / 4
                        candidates.append({"digits": digits, "conf": conf, "cx": cx, "src": name})
            if texts:
                print(f"  [{name:<14}]: {texts}")
        except Exception as ex:
            print(f"  [{name}] error: {ex}")
    return candidates


def pick_best(candidates):
    if not candidates:
        return None, 0.0

    print(f"[OCR DEBUG] pick_best received candidates: {candidates}")
    
    scored = []
    for c in candidates:
        d = c["digits"]
        s = c["conf"] * 60
        if len(d) == 5:   s += 100
        elif len(d) == 6: s += 40
        elif len(d) == 4: s += 10
        elif len(d) > 6:  s -= 40
        elif len(d) < 4:  s -= 40
        scored.append((s, c))

    scored.sort(key=lambda x: -x[0])
    best = scored[0][1]
    print(f"  [best] '{best['digits']}' conf={best['conf']:.2f} src={best['src']}")
    return best["digits"][:6], best["conf"]


# ─────────────────────────────────────────────────────────────────────────────
# MAIN ENDPOINT
# ─────────────────────────────────────────────────────────────────────────────
@app.route('/ocr', methods=['POST'])
def ocr_process():
    if 'image' not in request.files:
        return jsonify({"success": False, "message": "Không có file ảnh"}), 400

    img_bytes = request.files['image'].read()

    try:
        nparr  = np.frombuffer(img_bytes, np.uint8)
        img_orig = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        if img_orig is None:
            return jsonify({"success": False, "message": "Không thể đọc ảnh"}), 400

        oh, ow = img_orig.shape[:2]
        print(f"\n[OCR] Image: {ow}×{oh}")

        img = resize_to_max(img_orig, max_dim=1280)
        h, w = img.shape[:2]
        print(f"[OCR] Resized: {w}×{h}")

        reading    = None
        confidence = 0.0
        source     = "none"

        # ── Phase 1: find band in top 55% of image
        band_box = find_dark_band(img, search_top_ratio=0.55)

        if band_box:
            x1, y1, x2, y2 = band_box
            band = img[y1:y2, x1:x2]
            bh, bw = band.shape[:2]
            print(f"[OCR] Band crop: {bw}×{bh}")

            # Remove kWh label + red digit on the right
            band_trimmed = trim_kwh_side(band)
            th, tw = band_trimmed.shape[:2]
            print(f"[OCR] Trimmed band: {tw}×{th}")

            variants   = clean_display(band_trimmed, scale=4)
            candidates = run_ocr(variants)
            reading, confidence = pick_best(candidates)
            source = "band"
            print(f"[OCR] Band result: '{reading}' conf={confidence:.2f}")

        # ── Phase 2: fallback — top-center crop of the image
        if not reading or len(str(reading)) < 4 or confidence < 0.25:
            print("[OCR] Falling back to top-center crop…")
            # Take upper 45% × center 80% of image
            fc = img[int(h*0.05):int(h*0.50), int(w*0.10):int(w*0.90)]
            fc_trim  = trim_kwh_side(fc)
            variants2  = clean_display(fc_trim, scale=3)
            candidates2 = run_ocr(variants2)
            r2, c2 = pick_best(candidates2)
            print(f"[OCR] Fallback result: '{r2}' conf={c2:.2f}")
            if r2 and (not reading or c2 > confidence):
                reading, confidence, source = r2, c2, "fallback_top_center"

        print(f"[OCR] FINAL -> '{reading}' conf={confidence:.2f} src={source}")

        if not reading:
            return jsonify({
                "success": False,
                "message": "Không nhận diện được số. Hãy chụp thẳng, gần vào vùng số đen."
            })

        return jsonify({
            "success": True,
            "data":   {"value": reading, "confidence": round(float(confidence), 3), "source": source},
            "result": reading,
            "confidence": round(float(confidence), 3),
        })

    except Exception as e:
        print(traceback.format_exc())
        return jsonify({"success": False, "message": f"Lỗi: {str(e)}"}), 500


@app.route('/health', methods=['GET'])
def health():
    return jsonify({"status": "ok", "engine": "PaddleOCR (CPU)", "version": "3.0"})


if __name__ == '__main__':
    port = int(os.environ.get('PORT', 5000))
    print(f"[OCR Server v3.0] Starting on port {port}…")
    app.run(host='0.0.0.0', port=port, debug=False)

