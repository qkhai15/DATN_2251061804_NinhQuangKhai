@extends('layouts.admin')

@section('title', 'Quản lý Hóa đơn')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Danh sách Hóa đơn</h1>
        <p class="text-gray-500 mt-1 text-sm leading-5">Theo dõi tình trạng thanh toán, tiền điện nước và phí dịch vụ hàng tháng.</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center text-sm">
        <i class="fas fa-plus mr-2 text-sm"></i>
        Tạo hóa đơn mới
    </a>
</div>

<!-- Search & Filter Card -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
    <form action="{{ route('invoices.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Tìm kiếm số phòng</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition" placeholder="Số phòng...">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Trạng thái</label>
            <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none">
                <option value="">Tất cả</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Thanh toán một phần</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Tháng</label>
            <select name="month" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none">
                <option value="">Tất cả</option>
                @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Năm</label>
            <select name="year" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none">
                <option value="">Tất cả</option>
                @for($y=date('Y'); $y>=date('Y')-2; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 bg-gray-900 text-white font-bold py-2 rounded-xl hover:bg-black transition text-sm">Lọc</button>
            <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition text-sm flex items-center justify-center">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kỳ hóa đơn</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Khách thuê / Phòng</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Tổng tiền</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 transition group">
                    <td class="px-6 py-4 font-bold text-gray-900 text-sm leading-5">Tháng {{ $invoice->month }}/{{ $invoice->year }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 leading-5">{{ $invoice->contract->tenant->name }}</div>
                        <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-tight">Phòng {{ $invoice->contract->room->room_number }}</div>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-indigo-600 text-sm">
                        {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusMap = [
                                'paid' => ['label' => 'Đã thanh toán', 'class' => 'text-green-600 bg-green-50'],
                                'unpaid' => ['label' => 'Chưa thanh toán', 'class' => 'text-red-600 bg-red-50'],
                                'partially_paid' => ['label' => 'Thanh toán một phần', 'class' => 'text-orange-600 bg-orange-50'],
                            ];
                            $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'class' => 'text-gray-600 bg-gray-50'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition inline-block" title="Xem chi tiết">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('invoices.edit', $invoice) }}" class="text-amber-600 hover:bg-amber-50 p-2 rounded-lg transition inline-block" title="Chỉnh sửa">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:bg-rose-50 p-2 rounded-lg transition inline-block" onclick="return confirm('Xóa hóa đơn này?')" title="Xóa">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic text-sm">Không tìm thấy hóa đơn nào phù hợp.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
