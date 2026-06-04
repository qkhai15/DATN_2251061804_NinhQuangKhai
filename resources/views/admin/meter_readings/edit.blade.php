@extends('layouts.admin')

@section('title', 'Chỉnh sửa Chỉ số')

@section('content')
<div class="mb-8">
    <a href="{{ route('meter-readings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chỉnh sửa Chỉ số Điện & Nước</h1>
</div>

<div class="max-w-2xl bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
    <form action="{{ route('meter-readings.update', $meter_reading) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Phòng</label>
            <input type="text" readonly disabled value="Phòng {{ $meter_reading->room->room_number }} ({{ $meter_reading->room->building->name }})" class="w-full px-5 py-4 bg-gray-100 border border-gray-100 rounded-2xl text-sm font-bold text-gray-500 cursor-not-allowed outline-none">
            <input type="hidden" name="room_id" value="{{ $meter_reading->room_id }}">
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Loại chỉ số</label>
                <select name="type" disabled class="w-full px-5 py-4 bg-gray-100 border border-gray-100 rounded-2xl text-sm font-bold text-gray-500 cursor-not-allowed outline-none appearance-none">
                    <option value="electricity" {{ $meter_reading->type == 'electricity' ? 'selected' : '' }}>Điện (kWh)</option>
                    <option value="water" {{ $meter_reading->type == 'water' ? 'selected' : '' }}>Nước (m³)</option>
                </select>
                <input type="hidden" name="type" value="{{ $meter_reading->type }}">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Ngày ghi nhận</label>
                <input type="date" name="read_date" required value="{{ \Carbon\Carbon::parse($meter_reading->read_date)->format('Y-m-d') }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
            </div>
        </div>

        <!-- Latest Reading Info & Usage Calculation -->
        <div id="latest-reading-info" class="p-5 bg-slate-50 rounded-[2rem] border border-gray-100 flex items-center justify-between transition-all duration-300">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Chỉ số cũ</p>
                <p id="latest-value-text" class="text-xl font-black text-slate-700 mt-1">
                    {{ $meter_reading->old_value }} {{ $meter_reading->type == 'electricity' ? 'kWh' : 'm³' }}
                </p>
            </div>
            <div id="usage-calc" class="text-right">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Lượng tiêu thụ</p>
                <p id="usage-value-text" class="text-xl font-black text-indigo-600 mt-1">
                    +{{ $meter_reading->new_value - $meter_reading->old_value }} {{ $meter_reading->type == 'electricity' ? 'kWh' : 'm³' }}
                </p>
            </div>
        </div>

        {{-- OCR Scan Section --}}
        <div class="relative">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chỉ số mới</label>
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="number" id="new_value" name="new_value" required value="{{ $meter_reading->new_value }}"
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-base font-black text-gray-900 transition"
                        placeholder="VD: 9761">
                </div>
                <button type="button" id="scan-ocr-btn"
                    class="flex-shrink-0 p-4 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-100 transition border border-indigo-100 group relative"
                    title="Chụp ảnh công tơ để tự động nhận số">
                    <i class="fas fa-camera text-xl group-hover:scale-110 transition-transform"></i>
                    <span id="ocr-loading" class="hidden absolute inset-0 flex items-center justify-center bg-indigo-100 rounded-2xl">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </span>
                </button>
            </div>
            <input type="file" id="ocr-image-input" accept="image/*" capture="environment" class="hidden">

            {{-- Preview + Result Panel (hidden by default) --}}
            <div id="ocr-panel" class="hidden mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 space-y-3">
                <div class="flex gap-4 items-start">
                    <img id="ocr-preview" src="" alt="Preview" class="w-32 h-20 object-cover rounded-xl border border-gray-200 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kết quả nhận diện</p>
                        <p id="ocr-result-text" class="text-2xl font-black text-indigo-700 tracking-widest">—</p>
                        <div id="ocr-confidence-bar" class="mt-2">
                            <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                                <span>Độ tin cậy</span>
                                <span id="ocr-confidence-pct">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="ocr-confidence-fill" class="h-1.5 rounded-full bg-indigo-500 transition-all" style="width:0%"></div>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" id="ocr-accept-btn"
                                class="px-4 py-2 bg-emerald-500 text-white text-xs font-bold rounded-xl hover:bg-emerald-600 transition">
                                <i class="fas fa-check mr-1"></i> Dùng số này
                            </button>
                            <button type="button" id="ocr-retry-btn"
                                class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-300 transition">
                                <i class="fas fa-redo mr-1"></i> Chụp lại
                            </button>
                        </div>
                    </div>
                </div>
                <p id="ocr-tip" class="text-[10px] text-amber-600 font-medium hidden">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Độ tin cậy thấp. Hãy kiểm tra lại số hoặc chụp gần hơn vào vùng số đen.
                </p>
            </div>

            {{-- Tips --}}
            <div class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">
                    <i class="fas fa-lightbulb mr-1"></i> Mẹo chụp ảnh chuẩn
                </p>
                <ul class="text-[10px] text-blue-500 space-y-0.5 list-disc pl-4">
                    <li>Chụp thẳng góc vào <strong>vùng số đen</strong> trên mặt công tơ</li>
                    <li>Đảm bảo ánh sáng đủ, không bị lóa hoặc tối</li>
                    <li>Giữ điện thoại thẳng, tránh nghiêng</li>
                </ul>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                Cập nhật chỉ số
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scanBtn       = document.getElementById('scan-ocr-btn');
    const imageInput    = document.getElementById('ocr-image-input');
    const newValueInput = document.getElementById('new_value');
    const loadingIcon   = document.getElementById('ocr-loading');
    const ocrPanel      = document.getElementById('ocr-panel');
    const ocrPreview    = document.getElementById('ocr-preview');
    const ocrResultText = document.getElementById('ocr-result-text');
    const ocrConfPct    = document.getElementById('ocr-confidence-pct');
    const ocrConfFill   = document.getElementById('ocr-confidence-fill');
    const ocrAcceptBtn  = document.getElementById('ocr-accept-btn');
    const ocrRetryBtn   = document.getElementById('ocr-retry-btn');
    const ocrTip        = document.getElementById('ocr-tip');

    const typeValue     = "{{ $meter_reading->type }}";
    const unitText      = typeValue === 'electricity' ? ' kWh' : ' m³';

    const latestReadingInfo = document.getElementById('latest-reading-info');
    const latestValueText  = document.getElementById('latest-value-text');
    const usageCalc        = document.getElementById('usage-calc');
    const usageValueText   = document.getElementById('usage-value-text');
    const oldReadingValue  = parseFloat("{{ $meter_reading->old_value }}");

    let pendingValue = null;

    function updateUsageDisplay() {
        const newValue = parseFloat(newValueInput.value);
        if (!isNaN(newValue) && newValue >= oldReadingValue) {
            const usage = newValue - oldReadingValue;
            usageValueText.textContent = `+${usage.toLocaleString()}${unitText}`;
            usageCalc.classList.remove('hidden');
        } else {
            usageCalc.classList.add('hidden');
        }
    }

    newValueInput.addEventListener('input', updateUsageDisplay);

    scanBtn.addEventListener('click', () => imageInput.click());
    ocrRetryBtn.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', async function () {
        if (!this.files || !this.files[0]) return;

        const file = this.files[0];

        // Show preview immediately
        const objectUrl = URL.createObjectURL(file);
        ocrPreview.src = objectUrl;
        ocrPanel.classList.remove('hidden');
        ocrResultText.textContent = '…';
        ocrConfPct.textContent = '';
        ocrConfFill.style.width = '0%';
        ocrTip.classList.add('hidden');

        const formData = new FormData();
        formData.append('image', file);

        loadingIcon.classList.remove('hidden');
        scanBtn.disabled = true;
        ocrAcceptBtn.disabled = true;

        try {
            const response = await fetch('http://127.0.0.1:5000/ocr', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            console.log('[OCR Result]', result);

            if (result.success) {
                const value = result.data?.value ?? result.result ?? null;
                const conf  = result.data?.confidence ?? result.confidence ?? 0;

                if (value) {
                    pendingValue = value;
                    ocrResultText.textContent = value + unitText;

                    const pct = Math.round(conf * 100);
                    ocrConfPct.textContent = pct + '%';
                    ocrConfFill.style.width = pct + '%';
                    ocrConfFill.className = 'h-1.5 rounded-full transition-all ' +
                        (pct >= 70 ? 'bg-emerald-500' : pct >= 40 ? 'bg-amber-400' : 'bg-red-400');

                    ocrAcceptBtn.disabled = false;
                    if (pct < 60) ocrTip.classList.remove('hidden');
                } else {
                    ocrResultText.textContent = 'Không đọc được';
                    ocrTip.classList.remove('hidden');
                }
            } else {
                ocrResultText.textContent = 'Thất bại';
                ocrTip.classList.remove('hidden');
                console.warn('[OCR] Failed:', result.message);
            }
        } catch (error) {
            console.error('[OCR] Connection error:', error);
            ocrResultText.textContent = 'Lỗi kết nối';
            ocrPanel.classList.add('hidden');
            alert('Không kết nối được server OCR (cổng 5000). Hãy đảm bảo server AI đã khởi động.');
        } finally {
            loadingIcon.classList.add('hidden');
            scanBtn.disabled = false;
            imageInput.value = '';
        }
    });

    ocrAcceptBtn.addEventListener('click', function () {
        if (pendingValue !== null) {
            newValueInput.value = pendingValue;
            updateUsageDisplay();
            newValueInput.classList.add('ring-2', 'ring-emerald-500');
            setTimeout(() => newValueInput.classList.remove('ring-2', 'ring-emerald-500'), 2000);
            ocrPanel.classList.add('hidden');
            pendingValue = null;
        }
    });
});
</script>
@endsection
