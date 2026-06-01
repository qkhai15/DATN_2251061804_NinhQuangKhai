@extends('layouts.admin')

@section('title', 'Hóa đơn của tôi')

@section('content')
<div class="mb-8 pl-1">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Lịch sử Hóa đơn</h1>
    <p class="text-gray-500 mt-2 text-sm">Xem và quản lý tất cả các hóa đơn tiền phòng và dịch vụ của bạn.</p>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Kỳ thanh toán</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Tổng tiền</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Ngày xuất</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-8 py-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar-alt text-indigo-600"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 leading-tight">Tháng {{ $invoice->month }}/{{ $invoice->year }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-bold mt-1">Hóa đơn định kỳ</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 font-black text-gray-900 text-lg">
                        {{ number_format($invoice->total_amount, 0, ',', '.') }} <span class="text-xs font-bold text-gray-400 ml-0.5">đ</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase {{ $invoice->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $invoice->status == 'paid' ? 'bg-emerald-500' : 'bg-rose-500' }} animate-pulse"></span>
                            {{ $invoice->status == 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-gray-500 font-medium">
                        {{ $invoice->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-black text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-100/50">
                            Chi tiết
                            <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-invoice-dollar text-3xl text-gray-200"></i>
                        </div>
                        <p class="text-gray-400 font-bold text-sm">Bạn chưa có hóa đơn nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
