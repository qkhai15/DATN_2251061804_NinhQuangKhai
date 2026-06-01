@extends('layouts.admin')

@section('title', 'Chi tiết Hóa đơn')

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <a href="{{ route('tenant.invoices.index') }}" class="text-indigo-600 font-bold text-xs uppercase tracking-widest flex items-center mb-4 hover:translate-x-[-4px] transition-transform">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hóa đơn Tháng {{ $invoice->month }}/{{ $invoice->year }}</h1>
        <p class="text-gray-500 mt-2 text-sm font-medium">Mã hóa đơn: #INV-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm flex items-center shadow-sm">
            <i class="fas fa-print mr-2 text-xs"></i> Tải PDF
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Billing Details -->
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-50 overflow-hidden">
            <div class="p-10 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Chi tiết dịch vụ</h3>
                <div class="text-[10px] font-black text-indigo-500 uppercase px-3 py-1 bg-indigo-50 rounded-full">Phòng {{ $invoice->contract->room->room_number }}</div>
            </div>
            <div class="p-10">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-300 uppercase tracking-widest border-b border-gray-100">
                            <th class="pb-6 pl-2">Mô tả</th>
                            <th class="pb-6 text-right">Đơn giá</th>
                            <th class="pb-6 text-center">SL</th>
                            <th class="pb-6 pr-2 text-right">Thế giới</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($invoice->details as $detail)
                        <tr class="group">
                            <td class="py-6 pl-2">
                                <span class="font-bold text-gray-900 block text-lg">{{ $detail->name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase mt-1 tracking-wider italic">
                                    {{ $detail->service_id ? 'Dịch vụ hàng tháng' : 'Theo hợp đồng' }}
                                </span>
                            </td>
                            <td class="py-6 text-right font-bold text-gray-500">{{ number_format($detail->unit_price, 0, ',', '.') }} đ</td>
                            <td class="py-6 text-center font-bold text-gray-400">{{ $detail->quantity }}</td>
                            <td class="py-6 pr-2 text-right font-black text-gray-900 text-lg">{{ number_format($detail->sub_total, 0, ',', '.') }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-900 text-white rounded-2xl">
                            <td colspan="3" class="py-8 pl-10 text-sm font-black uppercase tracking-[0.3em]">Tổng số tiền cần thanh toán</td>
                            <td class="py-8 pr-10 text-right font-black text-3xl">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }} <span class="text-sm font-bold opacity-50 ml-1">đ</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Instructions & QR -->
        <div class="bg-indigo-50 rounded-[2.5rem] p-10 border border-indigo-100 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <div class="md:col-span-2 flex items-center gap-6">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center shrink-0">
                    <i class="fas fa-university text-2xl text-indigo-600"></i>
                </div>
                <div>
                    <h4 class="font-black text-gray-900 uppercase text-xs tracking-wider mb-2">Thông tin chuyển khoản</h4>
                    @php
                        $bankName = \App\Models\SystemSetting::get('bank_name', 'vcb');
                        $bankAccount = \App\Models\SystemSetting::get('bank_account_number', '0123456789');
                        $bankHolder = \App\Models\SystemSetting::get('bank_account_holder', 'N/A');
                        $bankCode = strtolower(str_replace(' ', '', $bankName));
                        $amount = (int)$invoice->total_amount;
                        $content = "THANH TOAN PHONG " . $invoice->contract->room->room_number . " THANG " . $invoice->month;
                    @endphp
                    <p class="text-sm text-gray-600 font-bold uppercase tracking-tight">{{ $bankName }} - <span class="text-indigo-600 select-all">{{ $bankAccount }}</span></p>
                    <p class="text-sm text-gray-600 font-bold uppercase">{{ $bankHolder }}</p>
                    <div class="mt-3 p-3 bg-white/50 rounded-xl border border-indigo-100">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest block mb-1">Nội dung chuyển khoản</span>
                        <p class="text-[11px] text-indigo-600 font-black uppercase select-all">{{ $content }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-indigo-100">
                    <img src="https://img.vietqr.io/image/{{ $bankCode }}-{{ $bankAccount }}-compact2.jpg?amount={{ $amount }}&addInfo={{ urlencode($content) }}&accountName={{ urlencode($bankHolder) }}" 
                        class="w-48 h-48 md:w-56 md:h-56 object-contain" alt="QR Thanh toán">
                </div>
                <span class="text-[10px] font-black text-indigo-400 uppercase mt-4 tracking-widest animate-pulse">Quét để tự động điền</span>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Status -->
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-50">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] mb-8 text-center">Trạng thái hiện tại</h3>
            <div class="flex flex-col items-center justify-center text-center">
                @if($invoice->status == 'paid')
                <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mb-6 text-2xl text-emerald-600 shadow-lg shadow-emerald-50">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="font-black text-emerald-600 uppercase text-lg tracking-widest mb-1">ĐÃ THANH TOÁN</div>
                <div class="text-[11px] text-gray-400 font-black uppercase">Vào lúc: {{ $invoice->updated_at->format('H:i d/m/Y') }}</div>
                @else
                <div class="w-20 h-20 rounded-full bg-rose-100 flex items-center justify-center mb-6 text-2xl text-rose-600 shadow-lg shadow-rose-50 animate-pulse">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="font-black text-rose-600 uppercase text-lg tracking-widest mb-1">CHỜ THANH TOÁN</div>
                <div class="text-[11px] text-gray-400 font-black uppercase">Hạn cuối: 05/{{ $invoice->month }}/{{ $invoice->year }}</div>
                @endif
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-gradient-to-br from-gray-900 to-indigo-950 rounded-[2.5rem] p-10 text-white">
            <h4 class="font-bold text-lg mb-4">Bạn cần hỗ trợ?</h4>
            <p class="text-indigo-200 text-xs leading-relaxed mb-8 opacity-70">Nếu phát hiện sai sót trong hóa đơn, vui lòng liên hệ quản lý hoặc gửi báo cáo sự cố ngay.</p>
            <a href="{{ route('tenant.issues.create') }}" class="block w-full py-4 bg-white/10 hover:bg-white/20 text-white text-center rounded-2xl font-black text-xs uppercase tracking-widest transition border border-white/5">
                Báo cáo sai sót
            </a>
        </div>
    </div>
</div>
@endsection
