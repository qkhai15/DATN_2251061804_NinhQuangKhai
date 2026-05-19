@extends('layouts.admin')

@section('title', 'Bảng điều khiển Admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 leading-8">Tổng quan hệ thống</h1>
    <p class="text-gray-500 mt-1 leading-5">Tóm tắt tình hình quản lý khu trọ của bạn.</p>
</div>

<!-- Stats / Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                <i class="fas fa-door-closed text-xl md:text-2xl"></i>
            </div>
            <span class="text-[10px] font-black text-indigo-500 bg-indigo-50/50 px-2 py-1 rounded-lg uppercase tracking-wider">Tổng</span>
        </div>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-tight leading-5">Tổng số phòng</p>
        <p class="text-2xl font-black leading-8 text-slate-900">{{ $stats['total_rooms'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                <i class="fas fa-user-check text-xl md:text-2xl"></i>
            </div>
            <span class="text-[10px] font-black text-emerald-500 bg-emerald-50/50 px-2 py-1 rounded-lg uppercase tracking-wider">Đã thuê</span>
        </div>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-tight leading-5">Phòng đang thuê</p>
        <p class="text-2xl font-black leading-8 text-slate-900">{{ $stats['rented_rooms'] }} <span class="text-xs font-medium text-gray-400">({{ number_format($stats['occupancy_rate'], 0) }}%)</span></p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-amber-50 p-3 rounded-xl text-amber-600">
                <i class="fas fa-door-open text-xl md:text-2xl"></i>
            </div>
            <span class="text-[10px] font-black text-amber-500 bg-amber-50/50 px-2 py-1 rounded-lg uppercase tracking-wider">Trống</span>
        </div>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-tight leading-5">Phòng hiện trống</p>
        <p class="text-2xl font-black leading-8 text-slate-900">{{ $stats['empty_rooms'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-rose-50 p-3 rounded-xl text-rose-600">
                <i class="fas fa-file-invoice-dollar text-xl md:text-2xl"></i>
            </div>
            <span class="text-[10px] font-black text-rose-500 bg-rose-50/50 px-2 py-1 rounded-lg uppercase tracking-wider">Chờ thu</span>
        </div>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-tight leading-5">Doanh thu chờ</p>
        <p class="text-2xl font-black leading-8 text-slate-900">{{ number_format($stats['pending_revenue'], 0, ',', '.') }}<span class="text-sm ml-1 text-gray-400">đ</span></p>
    </div>
</div>

{{-- Alert: Hợp đồng đã hết hạn chưa xử lý --}}
@if($stats['expired_contracts_count'] > 0)
<div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
        </div>
        <div>
            <p class="font-black text-red-700 text-base">{{ $stats['expired_contracts_count'] }} hợp đồng đã hết hạn chưa được xử lý!</p>
            <p class="text-red-500 text-sm mt-0.5">Các hợp đồng này vẫn đang ở trạng thái "active" dù đã quá ngày kết thúc. Hãy chạy lệnh cập nhật hoặc xử lý thủ công.</p>
        </div>
    </div>
    <a href="{{ route('contracts.index', ['status' => 'overdue']) }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm whitespace-nowrap transition flex-shrink-0">
        Xem ngay
    </a>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Doanh thu dự kiến -->
    <div class="md:col-span-2 lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900 leading-7">Doanh thu dự kiến</h3>
            <select class="text-sm border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                <option>6 tháng qua</option>
            </select>
        </div>
        <div class="h-64 flex items-end space-x-4 px-4">
            @for($i=0; $i<6; $i++)
            <div class="flex-1 bg-indigo-100 rounded-t-lg relative group" style="height: {{ rand(30, 90) }}%">
                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition shadow-lg whitespace-nowrap z-20">
                    {{ rand(15, 45) }}Tr
                </div>
            </div>
            @endfor
        </div>
        <div class="flex mt-4 text-[10px] font-black text-gray-400 px-4 uppercase tracking-widest">
            <span class="flex-1 text-center">T10</span>
            <span class="flex-1 text-center">T11</span>
            <span class="flex-1 text-center">T12</span>
            <span class="flex-1 text-center">T1</span>
            <span class="flex-1 text-center">T2</span>
            <span class="flex-1 text-center">T3</span>
        </div>
    </div>

    <!-- Hợp đồng sắp hết hạn -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-900 leading-7">Sắp hết hạn <span class="text-sm font-medium text-gray-400">(≤30 ngày)</span></h3>
            @if($stats['expiring_contracts_count'] > 0)
            <span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-0.5 rounded-full font-black">{{ $stats['expiring_contracts_count'] }}</span>
            @endif
        </div>
        <div class="space-y-3 flex-1 overflow-y-auto pr-1 max-h-[320px]">
            @forelse($expiringContracts as $contract)
            @php $daysLeft = now()->startOfDay()->diffInDays($contract->end_date, false); @endphp
            <a href="{{ route('contracts.show', $contract) }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-transparent hover:border-orange-200 hover:bg-orange-50/40 transition-all group">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center mr-4 text-orange-500 group-hover:scale-110 transition-transform flex-shrink-0">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 leading-tight">Phòng {{ $contract->room->room_number }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[140px]">{{ $contract->tenant->name }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <p class="text-[10px] font-black text-orange-500 uppercase tracking-tighter">Còn</p>
                    <p class="text-sm font-black text-gray-900">{{ (int)$daysLeft }} ngày</p>
                    <p class="text-[10px] text-gray-400">{{ $contract->end_date->format('d/m/Y') }}</p>
                </div>
            </a>
            @empty
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-emerald-400 text-xl"></i>
                </div>
                <p class="text-gray-400 font-bold text-xs uppercase tracking-widest leading-loose">Hệ thống ổn định<br>không có hợp đồng sắp hết hạn</p>
            </div>
            @endforelse
        </div>
        @if($stats['expiring_contracts_count'] > 0)
        <a href="{{ route('contracts.index', ['status' => 'expiring']) }}" class="mt-6 text-center text-[11px] font-black text-indigo-600 hover:text-black uppercase tracking-[0.2em] transition-colors pt-4 border-t border-gray-50">Xem tất cả hợp đồng sắp hết hạn</a>
        @endif
    </div>

    <!-- Thao tác nhanh -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-6 leading-7">Thao tác nhanh</h3>
        <div class="space-y-3">
            <a href="{{ route('invoices.create') }}" class="flex items-center p-4 rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition group">
                <div class="bg-indigo-100 p-2.5 rounded-lg text-indigo-600 mr-4">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 leading-5">Tạo hóa đơn tháng</p>
                    <p class="text-xs text-gray-500 leading-4">Cho tất cả hợp đồng còn hiệu lực</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-indigo-400"></i>
            </a>
            <a href="{{ route('rooms.create') }}" class="flex items-center p-4 rounded-xl hover:bg-green-50 border border-transparent hover:border-green-100 transition group">
                <div class="bg-green-100 p-2.5 rounded-lg text-green-600 mr-4">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 leading-5">Thêm phòng mới</p>
                    <p class="text-xs text-gray-500 leading-4">Mở rộng quy mô kinh doanh</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-400"></i>
            </a>
            <a href="{{ route('issues.index') }}" class="flex items-center p-4 rounded-xl hover:bg-orange-50 border border-transparent hover:border-orange-100 transition group">
                <div class="bg-orange-100 p-2.5 rounded-lg text-orange-600 mr-4">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 leading-5">Xem các yêu cầu hỗ trợ</p>
                    <p class="text-xs text-gray-500 leading-4">{{ $stats['active_issues'] }} sự cố đang chờ</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-orange-400"></i>
            </a>
            <a href="{{ route('contracts.index', ['status' => 'expiring']) }}" class="flex items-center p-4 rounded-xl hover:bg-rose-50 border border-transparent hover:border-rose-100 transition group">
                <div class="bg-rose-100 p-2.5 rounded-lg text-rose-600 mr-4">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 leading-5">HĐ sắp hết hạn</p>
                    <p class="text-xs text-gray-500 leading-4">{{ $stats['expiring_contracts_count'] }} hợp đồng cần gia hạn</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 group-hover:text-rose-400"></i>
            </a>
        </div>
    </div>
</div>
@endsection
