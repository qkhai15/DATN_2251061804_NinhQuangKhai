@extends('layouts.admin')

@section('title', 'Chi tiết Tòa nhà: ' . $building->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('buildings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại danh sách
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tòa nhà: {{ $building->name }}</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('buildings.edit', $building) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                <i class="fas fa-edit mr-2"></i> Chỉnh sửa
            </a>
            <a href="{{ route('rooms.create', ['building_id' => $building->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center">
                <i class="fas fa-plus mr-2"></i> Thêm phòng
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Building Info Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3 text-indigo-500"></i> Thông tin chung
            </h3>
            <div class="space-y-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Địa chỉ</span>
                    <p class="text-gray-700 font-medium">{{ $building->address }}</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Mô tả</span>
                    <p class="text-gray-600 italic">{{ $building->description ?: 'Chưa có mô tả chi tiết.' }}</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Ngày tạo</span>
                    <p class="text-gray-700 font-medium">{{ $building->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white">
            <h3 class="text-lg font-bold mb-6 flex items-center">
                <i class="fas fa-chart-pie mr-3 text-indigo-300"></i> Thống kê
            </h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-3xl font-black">{{ $building->rooms->count() }}</div>
                    <div class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider">Tổng số phòng</div>
                </div>
                <div>
                    <div class="text-3xl font-black">{{ $building->rooms->where('status', 'rented')->count() }}</div>
                    <div class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider">Đang thuê</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-emerald-400">{{ $building->rooms->where('status', 'empty')->count() }}</div>
                    <div class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider">Còn trống</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-amber-400">{{ $building->rooms->where('status', 'maintenance')->count() }}</div>
                    <div class="text-[10px] uppercase font-bold text-indigo-300 tracking-wider">Bảo trì</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms List Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Danh sách phòng thuộc tòa nhà</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Số phòng</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Giá thuê</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Trạng thái</th>
                            <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($building->rooms as $room)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-6 font-bold text-gray-900">{{ $room->room_number }}</td>
                            <td class="px-8 py-6 font-bold text-indigo-600">{{ number_format($room->price, 0) }}đ</td>
                            <td class="px-8 py-6">
                                @php
                                    $statusMap = [
                                        'empty' => ['label' => 'Còn trống', 'class' => 'text-green-600 bg-green-50'],
                                        'rented' => ['label' => 'Đang thuê', 'class' => 'text-indigo-600 bg-indigo-50'],
                                        'maintenance' => ['label' => 'Bảo trì', 'class' => 'text-orange-600 bg-orange-50'],
                                    ];
                                    $st = $statusMap[$room->status] ?? ['label' => $room->status, 'class' => 'text-gray-600 bg-gray-50'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $st['class'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right space-x-2">
                                <a href="{{ route('rooms.show', $room) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('rooms.edit', $room) }}" class="text-gray-400 hover:text-amber-500 transition"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-gray-400 italic">Chưa có phòng nào trong tòa nhà này.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
