<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn tháng {{ $invoice->month }}/{{ $invoice->year }} - Phòng {{ $invoice->contract->room->room_number }}</title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 148mm;
            margin: auto;
            padding: 10px;
            border: 1px solid #eee;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #4f46e5;
            font-size: 20px;
            text-transform: uppercase;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .info-col {
            width: 48%;
        }
        .info-col p {
            margin: 3px 0;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 10px;
            text-align: right;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 45%;
            text-align: center;
        }
        .signature p {
            margin-bottom: 60px;
        }
        .no-print {
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                border: none;
            }
        }
        .btn {
            background: #4f46e5;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="javascript:window.print()" class="btn">In hóa đơn</a>
        <a href="{{ route('invoices.show', $invoice) }}" style="margin-left: 10px; color: #666;">Quay lại</a>
    </div>

    <div class="invoice-box">
        <div class="header">
            <h1>Hóa đơn tiền phòng</h1>
            <p>Tháng {{ $invoice->month }} năm {{ $invoice->year }}</p>
        </div>

        <div class="info-section">
            <div class="info-col">
                <p><span class="info-label">Khách thuê:</span> {{ $invoice->contract->tenant->name }}</p>
                <p><span class="info-label">Số điện thoại:</span> {{ $invoice->contract->tenant->phone }}</p>
                <p><span class="info-label">Tòa nhà:</span> {{ $invoice->contract->room->building->name }}</p>
            </div>
            <div class="info-col text-right">
                <p><span class="info-label">Số phòng:</span> <strong>{{ $invoice->contract->room->room_number }}</strong></p>
                <p><span class="info-label">Mã HĐ:</span> #{{ $invoice->id }}</p>
                <p><span class="info-label">Ngày tạo:</span> {{ $invoice->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Dịch vụ</th>
                    <th class="text-right">SL</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->details as $detail)
                <tr>
                    <td>{{ $detail->name }}</td>
                    <td class="text-right">{{ number_format($detail->quantity, ($detail->quantity == (int)$detail->quantity ? 0 : 1)) }}</td>
                    <td class="text-right">{{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                Tổng cộng: <span style="color: #4f46e5;">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</span>
            </div>
            <p style="font-style: italic; font-size: 11px; margin-top: 5px;">(Viết bằng chữ: ........................................................................................)</p>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Khách thuê</p>
                <span>(Ký và ghi rõ họ tên)</span>
            </div>
            <div class="signature">
                <p>Người lập phiếu</p>
                <span>(Ký và ghi rõ họ tên)</span>
            </div>
        </div>
    </div>
</body>
</html>
