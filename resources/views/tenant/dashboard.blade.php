@extends('layouts.admin')

@section('title', 'Bảng điều khiển khách thuê')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 leading-9">Xin chào, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-500 mt-1 leading-5">Dưới đây là tổng quan tình trạng thuê phòng và hóa đơn của bạn.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
        <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 mr-4">
            <i class="fas fa-home text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium leading-5">Phòng đang thuê</p>
            <p class="text-xl font-bold leading-7">{{ $room->room_number ?? 'Chưa có' }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
        <div class="bg-green-100 p-3 rounded-xl text-green-600 mr-4">
            <i class="fas fa-money-bill-wave text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium leading-5">Số tiền chưa thanh toán</p>
            <p class="text-xl font-bold leading-7">{{ number_format($unpaid_amount, 0, ',', '.') }} đ</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
        <div class="bg-yellow-100 p-3 rounded-xl text-yellow-600 mr-4">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium leading-5">Sự cố đang chờ</p>
            <p class="text-xl font-bold leading-7">{{ $pending_issues ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Hóa đơn gần đây -->
    <div class="lg:col-span-2 bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold text-gray-900 leading-7">Hóa đơn gần đây</h3>
            <a href="{{ route('tenant.invoices.index') }}" class="text-sm text-indigo-600 font-black hover:text-black transition-colors uppercase tracking-widest">Xem tất cả</a>
        </div>
        <div class="space-y-4 flex-1">
            @forelse($recent_invoices as $invoice)
            <div class="flex items-center justify-between p-5 bg-gray-50 rounded-2xl border border-transparent hover:border-indigo-100 transition-all group">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mr-4 text-indigo-600 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 leading-tight">Tháng {{ $invoice->month }}/{{ $invoice->year }}</p>
                        <p class="text-[10px] text-gray-400 font-black uppercase mt-1">Hạn: {{ $invoice->created_at->addDays(5)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-black text-indigo-600 text-lg leading-tight">{{ number_format($invoice->total_amount, 0, ',', '.') }}<span class="text-xs ml-0.5">đ</span></p>
                    <span class="inline-block mt-1 text-[9px] px-2 py-0.5 rounded-full font-black uppercase tracking-wider {{ $invoice->status == 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                        {{ $invoice->status == 'paid' ? 'Đã thu' : 'Chờ thu' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-invoice-dollar text-gray-200 text-xl"></i>
                </div>
                <p class="text-gray-400 font-bold text-xs uppercase tracking-widest leading-loose">Bạn chưa có hóa đơn nào</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Thông tin thanh toán & QR -->
    <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl shadow-slate-200 text-white relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
        <h3 class="text-xl font-bold mb-8 relative z-10 flex items-center">
            <i class="fas fa-university mr-3 text-indigo-400"></i>
            Thanh toán nhanh
        </h3>
        
        @php
            $bankName = \App\Models\SystemSetting::get('bank_name', 'vcb');
            $bankAccount = \App\Models\SystemSetting::get('bank_account_number', '0123456789');
            $bankHolder = \App\Models\SystemSetting::get('bank_account_holder', 'N/A');
            // Basic mapping for common banks to VietQR codes if needed, or just use lowercase
            $bankCode = strtolower(str_replace(' ', '', $bankName));
        @endphp
        <div class="bg-white p-4 rounded-3xl mb-8 relative z-10 flex justify-center">
            <img src="https://img.vietqr.io/image/{{ $bankCode }}-{{ $bankAccount }}-compact.jpg?accountName={{ urlencode($bankHolder) }}" class="w-48 h-48 md:w-full md:h-auto aspect-square rounded-2xl grayscale hover:grayscale-0 transition-all duration-500" alt="QR Thanh toán">
        </div>
        <div class="mt-4 text-center">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Quét để chuyển khoản</span>
        </div>

        <div class="space-y-4 relative z-10">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Ngân hàng</span>
                <span class="text-sm font-black text-indigo-400">{{ $bankName }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Số tài khoản</span>
                <span class="text-sm font-black text-white">{{ $bankAccount }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Chủ tài khoản</span>
                <span class="text-sm font-black text-white truncate max-w-[150px]">{{ $bankHolder }}</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Báo cáo sự cố nhanh -->
    <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-900 mb-8 leading-7 flex items-center">
            <i class="fas fa-tools mr-3 text-indigo-500"></i>
            Báo cáo sự cố
        </h3>
        <form action="{{ route('tenant.issues.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Vị trí / Phòng</label>
                        <select name="room_id" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-bold text-gray-800 transition-all">
                            @if($room)
                            <option value="{{ $room->id }}">Phòng {{ $room->room_number }}</option>
                            @else
                            <option value="">Chưa có phòng</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Tiêu đề ngắn</label>
                        <input type="text" name="title" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-bold text-gray-800 transition-all" placeholder="VD: Hỏng vòi nước...">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Mô tả tình trạng</label>
                    <textarea name="description" rows="3" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-medium text-gray-600 transition-all leading-relaxed" placeholder="Hãy mô tả chi tiết vấn đề bạn gặp phải..."></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-black transition-all shadow-xl shadow-indigo-100 uppercase tracking-[0.2em] text-xs">
                    Gửi yêu cầu ngay
                </button>
            </div>
        </form>
    </div>

    <!-- Hỗ trợ & Liên hệ -->
    <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-900 mb-8 leading-7 flex items-center">
            <i class="fas fa-headset mr-3 text-indigo-500"></i>
            Hỗ trợ & Liên hệ
        </h3>
        <div class="space-y-6">
            <div class="flex items-center p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100/50">
                <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-200 mr-5 shrink-0">
                    <i class="fas fa-phone-alt animate-bounce"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Hotline khẩn cấp</p>
                    <p class="text-2xl font-black text-indigo-900">{{ \App\Models\SystemSetting::get('contact_phone', '0123.456.789') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-6 bg-gray-50 rounded-3xl hover:bg-gray-100 transition-colors group cursor-pointer">
                    <i class="fab fa-facebook-messenger text-2xl text-blue-500 mb-4 group-hover:scale-110 transition-transform"></i>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Messenger</p>
                    <p class="text-xs font-bold text-gray-700 mt-1">Chat trực tuyến</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-3xl hover:bg-gray-100 transition-colors group cursor-pointer text-center flex flex-col items-center">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 mb-2">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Điều khoản</p>
                    <p class="text-xs font-bold text-gray-700 mt-1">Quy định phòng</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
