@extends('layouts.admin')

@section('title', 'Xem trước Hóa đơn')

@section('content')
<div class="mb-8">
    <a href="{{ route('invoices.create') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại chỉnh sửa
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Xem trước Hóa đơn</h1>
    <p class="text-gray-500 mt-2 text-sm font-medium">Kỳ hóa đơn: Tháng {{ $month }}/{{ $year }} - Phòng: {{ $contract->room->room_number }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Billing Details Preview -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Chi tiết các hạng mục</h3>
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
                        @foreach($details as $detail)
                        <tr class="group">
                            <td class="py-5 pl-2">
                                <span class="font-bold text-gray-900 block leading-tight">{{ $detail['name'] }}</span>
                            </td>
                            <td class="py-5 text-right font-medium text-gray-600">{{ number_format($detail['unit_price'], 0, ',', '.') }} đ</td>
                            <td class="py-5 text-center font-medium text-gray-400">{{ $detail['quantity'] }}</td>
                            <td class="py-5 pr-2 text-right font-black text-gray-900">{{ number_format($detail['sub_total'], 0, ',', '.') }} đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-indigo-50 bg-indigo-50/20">
                            <td colspan="3" class="py-6 pl-8 text-sm font-black text-indigo-600 uppercase tracking-widest">Tổng cộng</td>
                            <td class="py-6 pr-8 text-right font-black text-indigo-600 text-2xl">
                                {{ number_format($totalAmount, 0, ',', '.') }} <span class="text-xs ml-1">đ</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Summary & Confirmation Card -->
        <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-xl">
            <h3 class="text-xs font-black text-indigo-300 uppercase tracking-[0.2em] mb-6 border-b border-white/10 pb-4">Xác nhận thông tin</h3>
            
            <div class="space-y-6 mb-8">
                <div class="flex justify-between items-center">
                    <span class="text-indigo-300 text-xs font-bold uppercase">Khách thuê:</span>
                    <span class="font-black text-sm uppercase">{{ $contract->tenant->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-indigo-300 text-xs font-bold uppercase">Trạng thái:</span>
                    @if($status == 'paid')
                        <span class="text-[10px] font-black bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-lg uppercase tracking-wider">Đã thanh toán</span>
                    @else
                        <span class="text-[10px] font-black bg-rose-500/20 text-rose-300 px-3 py-1 rounded-lg uppercase tracking-wider">Chưa thanh toán</span>
                    @endif
                </div>
            </div>

            <form action="{{ route('invoices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="status" value="{{ $status }}">
                
                @foreach($manual_services as $service_id => $quantity)
                    @if($quantity > 0)
                        <input type="hidden" name="services[{{ $service_id }}]" value="{{ $quantity }}">
                    @endif
                @endforeach

                <button type="submit" class="w-full py-5 bg-white text-indigo-900 font-black rounded-2xl hover:bg-indigo-50 transition shadow-2xl flex items-center justify-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    Xác nhận & Tạo hóa đơn
                </button>
            </form>
            
            <p class="text-[10px] text-indigo-300 text-center mt-6 font-medium italic">
                * Vui lòng kiểm tra kỹ các hạng mục trước khi tạo. Sau khi tạo, thông số điện nước sẽ được chốt cho kỳ này.
            </p>
        </div>
    </div>
</div>
@endsection
