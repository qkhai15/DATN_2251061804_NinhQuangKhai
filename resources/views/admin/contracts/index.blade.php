@extends('layouts.admin')

@section('title', 'Quản lý Hợp đồng')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Danh sách Hợp đồng</h1>
        <p class="text-gray-500 mt-1 text-sm leading-5">Quản lý các thỏa thuận thuê phòng, tiền cọc và thời hạn hợp đồng.</p>
    </div>
    <a href="{{ route('contracts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
        <i class="fas fa-plus mr-2 text-sm"></i>
        Tạo hợp đồng mới
    </a>
</div>

<!-- Search & Filter Card -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
    <form action="{{ route('contracts.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Tìm kiếm khách thuê</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition" placeholder="Nhập tên khách thuê...">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1 leading-5">Trạng thái</label>
            <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none">
                <option value="">Tất cả trạng thái</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Đang hiệu lực</option>
                <option value="expiring" {{ request('status') == 'expiring' ? 'selected' : '' }}>⚠ Sắp hết hạn (≤30 ngày)</option>
                <option value="overdue"  {{ request('status') == 'overdue'  ? 'selected' : '' }}>🔴 Đã hết hạn chưa xử lý</option>
                <option value="expired"  {{ request('status') == 'expired'  ? 'selected' : '' }}>Hết hạn</option>
                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 bg-gray-900 text-white font-bold py-2 rounded-xl hover:bg-black transition text-sm">Lọc dữ liệu</button>
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition text-sm flex items-center justify-center">
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
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Phòng</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Khách thuê</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'end_date', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                            Thời hạn hợp đồng <i class="fas fa-sort ml-2 opacity-30"></i>
                        </a>
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Còn lại</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Tiền cọc</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contracts as $contract)
                @php
                    $today    = now()->startOfDay();
                    $endDate  = $contract->end_date;
                    $daysLeft = $endDate ? $today->diffInDays($endDate, false) : null;

                    // Badge trạng thái
                    $statusMap = [
                        'active'   => ['label' => 'Đang hiệu lực', 'class' => 'text-green-600 bg-green-50'],
                        'expired'  => ['label' => 'Hết hạn',        'class' => 'text-red-600 bg-red-50'],
                        'canceled' => ['label' => 'Đã hủy',         'class' => 'text-gray-600 bg-gray-50'],
                    ];
                    $st = $statusMap[$contract->status] ?? ['label' => $contract->status, 'class' => 'text-gray-600 bg-gray-50'];

                    // Badge cảnh báo hạn còn lại
                    if ($daysLeft === null) {
                        $daysLabel = '—'; $daysClass = 'text-gray-400';
                    } elseif ($daysLeft < 0) {
                        $daysLabel = 'Quá hạn ' . abs((int)$daysLeft) . ' ngày'; $daysClass = 'text-red-600 bg-red-50 font-bold px-2 py-0.5 rounded-lg text-[10px] border border-red-200';
                    } elseif ($daysLeft <= 30) {
                        $daysLabel = 'Còn ' . (int)$daysLeft . ' ngày'; $daysClass = 'text-orange-600 bg-orange-50 font-bold px-2 py-0.5 rounded-lg text-[10px] border border-orange-200';
                    } else {
                        $daysLabel = 'Còn ' . (int)$daysLeft . ' ngày'; $daysClass = 'text-gray-500 text-xs';
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition group {{ $daysLeft !== null && $daysLeft < 0 ? 'bg-red-50/30' : ($daysLeft !== null && $daysLeft <= 30 ? 'bg-orange-50/20' : '') }}">
                    {{-- Phòng --}}
                    <td class="px-5 py-4">
                        <div class="font-bold text-gray-900">{{ $contract->room->room_number }}</div>
                        <div class="text-[10px] text-gray-400">{{ $contract->room->building->name ?? '' }}</div>
                    </td>

                    {{-- Khách thuê --}}
                    <td class="px-5 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $contract->tenant->name }}</div>
                        <div class="text-[10px] text-gray-400">{{ $contract->tenant->phone }}</div>
                    </td>

                    {{-- Thời hạn --}}
                    <td class="px-5 py-4">
                        <div class="text-xs font-bold text-gray-700">{{ $contract->start_date->format('d/m/Y') }}</div>
                        <div class="text-[10px] text-gray-400">đến {{ $endDate ? $endDate->format('d/m/Y') : 'Không thời hạn' }}</div>
                    </td>

                    {{-- Còn lại --}}
                    <td class="px-5 py-4">
                        @if($contract->status === 'active')
                            <span class="{{ $daysClass }}">{{ $daysLabel }}</span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Tiền cọc --}}
                    <td class="px-5 py-4 text-right font-bold text-amber-600 text-sm whitespace-nowrap">
                        {{ number_format($contract->deposit, 0, ',', '.') }} đ
                    </td>

                    {{-- Trạng thái --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                    </td>

                    {{-- Thao tác --}}
                    <td class="px-5 py-4 text-right space-x-1">
                        <a href="{{ route('contracts.show', $contract) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition inline-block" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('contracts.edit', $contract) }}" class="text-amber-500 hover:bg-amber-50 p-2 rounded-lg transition inline-block" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:bg-rose-50 p-2 rounded-lg transition inline-block" onclick="return confirm('Xóa hợp đồng này?')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">Không tìm thấy hợp đồng nào phù hợp.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contracts->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $contracts->links() }}
    </div>
    @endif
</div>
@endsection
