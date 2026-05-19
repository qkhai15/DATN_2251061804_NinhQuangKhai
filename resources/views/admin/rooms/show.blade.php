@extends('layouts.admin')

@section('title', 'Chi tiết Phòng: ' . $room->room_number)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('rooms.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại danh sách
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Phòng: {{ $room->room_number }}</h1>
            <p class="text-gray-500 mt-1">Thuộc tòa nhà: <span class="font-bold text-gray-700">{{ $room->building->name }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('rooms.edit', $room) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            @if($room->status == 'empty')
            <a href="{{ route('contracts.create', ['room_id' => $room->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                <i class="fas fa-file-contract mr-2"></i> Tạo hợp đồng
            </a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Room Info Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-door-open mr-3 text-indigo-500"></i> Thông tin phòng
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Trạng thái</span>
                    @php
                        $statusMap = [
                            'empty' => ['label' => 'Còn trống', 'class' => 'text-green-600 bg-green-50'],
                            'rented' => ['label' => 'Đang thuê', 'class' => 'text-indigo-600 bg-indigo-50'],
                            'maintenance' => ['label' => 'Bảo trì', 'class' => 'text-orange-600 bg-orange-50'],
                        ];
                        $st = $statusMap[$room->status] ?? ['label' => $room->status, 'class' => 'text-gray-600 bg-gray-50'];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $st['class'] }}">
                        {{ $st['label'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Giá thuê</span>
                    <span class="text-indigo-600 font-bold">{{ number_format($room->price, 0) }} đ/tháng</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Diện tích</span>
                    <span class="text-gray-900 font-bold">{{ $room->area }} m²</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Số người tối đa</span>
                    <span class="text-gray-900 font-bold">{{ $room->max_people }} người</span>
                </div>
            </div>
        </div>

        <!-- Meter Readings Quick View -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-bolt mr-3 text-amber-500"></i> Chỉ số gần nhất
            </h3>
            @php
                $latestElec = $room->meterReadings->where('type', 'electricity')->sortByDesc('read_date')->first();
                $latestWater = $room->meterReadings->where('type', 'water')->sortByDesc('read_date')->first();
            @endphp
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Điện</span>
                    <span class="font-bold text-gray-900">{{ $latestElec ? number_format($latestElec->new_value, 0) . ' kWh' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Nước</span>
                    <span class="font-bold text-gray-900">{{ $latestWater ? number_format($latestWater->new_value, 0) . ' m³' : 'N/A' }}</span>
                </div>
                <div class="pt-4">
                    <a href="{{ route('meter-readings.create', ['room_id' => $room->id]) }}" class="text-indigo-600 font-bold text-sm hover:underline flex items-center">
                        Ghi chỉ số mới <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant & Contract Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-8 border-b border-gray-50">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-tie mr-3 text-indigo-500"></i> Thông tin khách thuê & Hợp đồng
                </h3>
            </div>
            <div class="p-8">
                @php $activeContract = $room->contracts->where('status', 'active')->first(); @endphp
                @if($activeContract)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest block mb-1">Khách thuê hiện tại</span>
                            <p class="text-lg font-bold text-gray-900">{{ $activeContract->tenant->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $activeContract->tenant->phone }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest block mb-1">Thời hạn hợp đồng</span>
                            <p class="text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($activeContract->start_date)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($activeContract->end_date)->format('d/m/Y') }}
                            </p>
                            <span class="text-xs text-indigo-600 font-bold">Đang hiệu lực</span>
                        </div>
                    </div>
                    <div class="mt-8 pt-8 border-t border-gray-50 flex gap-4">
                        <a href="{{ route('contracts.show', $activeContract) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
                            <i class="fas fa-file-invoice mr-2"></i> Xem chi tiết hợp đồng
                        </a>
                        <a href="{{ route('invoices.create', ['contract_id' => $activeContract->id]) }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-800 transition">
                            <i class="fas fa-plus-circle mr-2"></i> Xuất hóa đơn tháng
                        </a>
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-file-contract text-4xl text-gray-100 mb-4 block"></i>
                        <p class="text-gray-400 font-medium italic">Phòng này hiện không có hợp đồng hoạt động.</p>
                        <a href="{{ route('contracts.create', ['room_id' => $room->id]) }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">Tạo hợp đồng ngay</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Hóa đơn gần đây</h3>
                <a href="{{ route('invoices.index', ['room_id' => $room->id]) }}" class="text-sm font-bold text-indigo-600 hover:underline">Tất cả hóa đơn</a>
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
                        @php
                            $invoices = collect();
                            if($activeContract) {
                                $invoices = $activeContract->invoices()->latest()->take(5)->get();
                            }
                        @endphp
                        @forelse($invoices as $invoice)
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
                            <td colspan="4" class="px-8 py-8 text-center text-gray-400 italic">Chưa có dữ liệu hóa đơn.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
