@extends('layouts.admin')

@section('title', 'Chi tiết Hóa đơn')

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chi tiết Hóa đơn #{{ $invoice->id }}</h1>
        <p class="text-gray-500 mt-2 text-sm font-medium">Kỳ hóa đơn: Tháng {{ $invoice->month }}/{{ $invoice->year }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm flex items-center shadow-sm">
            <i class="fas fa-print mr-2 text-xs"></i> Mẫu in hóa đơn
        </a>
        @if($invoice->status != 'paid')
        <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="paid">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-black transition text-sm flex items-center shadow-md shadow-indigo-100">
                <i class="fas fa-check mr-2 text-xs"></i> Đánh dấu đã thu
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Billing Details -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Thông tin thanh toán</h3>
            </div>
            <div class="p-8">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="pb-4 pl-2">Hạng mục</th>
                            <th class="pb-4 text-right">Đơn giá</th>
                            <th class="pb-4 text-center">Số lượng</th>
                            <th class="pb-4 pr-2 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <!-- Example: Room Rent (should come from invoice details if implemented) -->
                        @foreach($invoice->details as $detail)
                        <tr class="group">
                            <td class="py-5 pl-2">
                                <span class="font-bold text-gray-900 block leading-tight">{{ $detail->name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase mt-1 italic">
                                    {{ $detail->service_id ? 'Dịch vụ sử dụng' : 'Hợp đồng định kỳ' }}
                                </span>
                            </td>
                            <td class="py-5 text-right font-medium text-gray-600">{{ number_format($detail->unit_price, 0, ',', '.') }} đ</td>
                            <td class="py-5 text-center font-medium text-gray-400">{{ $detail->quantity }}</td>
                            <td class="py-5 pr-2 text-right font-black text-gray-900">{{ number_format($detail->sub_total, 0, ',', '.') }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-indigo-50 bg-indigo-50/20">
                            <td colspan="3" class="py-6 pl-8 text-sm font-black text-indigo-600 uppercase tracking-widest">Tổng cộng</td>
                            <td class="py-6 pr-8 text-right font-black text-indigo-600 text-2xl">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }} <span class="text-xs ml-1">đ</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Note Section -->
        <div class="bg-indigo-900 rounded-[2rem] p-8 text-white relative overflow-hidden">
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fas fa-info-circle text-xl text-indigo-200"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-2">Ghi chú quan trọng</h4>
                    <p class="text-indigo-200 text-sm leading-relaxed">Vui lòng thanh toán hóa đơn trước ngày 05 hàng tháng. Các trường hợp chậm trễ sẽ bị tính phí phạt 5% giá trị hóa đơn.</p>
                </div>
            </div>
            <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Status Card -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-50">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Trạng thái</h3>
            <div class="flex items-center">
                @if($invoice->status == 'paid')
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mr-4">
                    <i class="fas fa-check text-emerald-600"></i>
                </div>
                <div>
                    <div class="font-black text-emerald-600 uppercase text-sm tracking-wider">Đã thanh toán</div>
                    <div class="text-[10px] text-gray-400 font-bold mt-1">Ngày: {{ $invoice->payment_date ? $invoice->payment_date->format('d/m/Y') : $invoice->updated_at->format('d/m/Y') }}</div>
                </div>
                @else
                <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-rose-600"></i>
                </div>
                <div>
                    <div class="font-black text-rose-600 uppercase text-sm tracking-wider">Chưa thanh toán</div>
                    <div class="text-[10px] text-gray-400 font-bold mt-1">Hạn: 05/{{ $invoice->month }}/{{ $invoice->year }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tenant Info -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-50">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Khách thuê</h3>
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-lg mr-4 border border-gray-200">
                    {{ substr($invoice->contract->tenant->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-black text-gray-900 leading-tight uppercase text-sm">{{ $invoice->contract->tenant->name }}</div>
                    <div class="text-[10px] text-indigo-500 font-bold mt-1">Phòng {{ $invoice->contract->room->room_number }}</div>
                </div>
            </div>
            <div class="space-y-3 pt-6 border-t border-gray-50">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold">Số điện thoại:</span>
                    <span class="text-gray-900 font-black">{{ $invoice->contract->tenant->phone }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-bold">Ngày thuê:</span>
                    <span class="text-gray-900 font-black">{{ $invoice->contract->start_date->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
