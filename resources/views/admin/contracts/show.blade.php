@extends('layouts.admin')

@section('title', 'Chi tiết Hợp đồng: ' . $contract->tenant->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('contracts.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại danh sách
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Hợp đồng: {{ $contract->tenant->name }}</h1>
            <p class="text-gray-500 mt-1">Phòng: <span class="font-bold text-gray-700">{{ $contract->room->room_number }}</span> ({{ $contract->room->building->name }})</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('contracts.edit', $contract) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                    <i class="fas fa-trash mr-2"></i> Xóa
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Lease Info Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-file-contract mr-3 text-indigo-500"></i> Điều khoản hợp đồng
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Trạng thái</span>
                    @php
                        $statusMap = [
                            'active' => ['label' => 'Đang hiệu lực', 'class' => 'text-emerald-600 bg-emerald-50'],
                            'expired' => ['label' => 'Hết hạn', 'class' => 'text-amber-600 bg-amber-50'],
                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'text-rose-600 bg-rose-50'],
                        ];
                        $st = $statusMap[$contract->status] ?? ['label' => $contract->status, 'class' => 'text-gray-600 bg-gray-50'];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $st['class'] }}">
                        {{ $st['label'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Ngày bắt đầu</span>
                    <span class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Ngày kết thúc</span>
                    <span class="text-gray-900 font-bold">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : 'Không thời hạn' }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Giá thuê tháng</span>
                    <span class="text-indigo-600 font-bold">{{ number_format($contract->room_price, 0) }} đ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium">Tiền đặt cọc</span>
                    <span class="text-gray-900 font-bold">{{ number_format($contract->deposit, 0) }} đ</span>
                </div>
            </div>
        </div>

        <!-- Tenant Contact Card -->
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white">
            <h3 class="text-lg font-bold mb-6 flex items-center">
                <i class="fas fa-user-circle mr-3 text-indigo-300"></i> Thông tin khách thuê
            </h3>
            <div class="space-y-4">
                <div>
                    <span class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider block mb-1">Họ và tên</span>
                    <p class="text-xl font-black">{{ $contract->tenant->name }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider block mb-1">Số điện thoại</span>
                    <p class="text-lg font-bold">{{ $contract->tenant->phone }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider block mb-1">Email</span>
                    <p class="text-sm opacity-80 italic">{{ $contract->tenant->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history mr-3 text-indigo-500"></i> Lịch sử thanh toán
                </h3>
                <a href="{{ route('invoices.create', ['contract_id' => $contract->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition">
                    Xuất hóa đơn mới
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Kỳ hóa đơn</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tổng tiền</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Trạng thái</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Xem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($contract->invoices as $invoice)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-4 font-medium text-gray-900">Tháng {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('m/Y') }}</td>
                            <td class="px-8 py-4 font-bold text-gray-900">{{ number_format($invoice->total_amount, 0) }}đ</td>
                            <td class="px-8 py-4">
                                @if($invoice->status == 'paid')
                                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">Đã thanh toán</span>
                                @else
                                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">Chưa thanh toán</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">Chưa có dữ liệu hóa đơn cho hợp đồng này.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
