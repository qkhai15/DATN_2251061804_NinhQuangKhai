@extends('layouts.admin')

@section('title', 'Tạo Hợp đồng Thuê mới')

@section('content')
<div class="mb-8">
    <a href="{{ route('contracts.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 leading-8">Tạo Hợp đồng Mới</h1>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl">
    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Room & Tenant Selection -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 leading-7">Thông tin cơ bản</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phòng trống</label>
                    <select name="room_id" required class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-indigo-500 bg-gray-50 border-gray-200">
                        <option value="">Chọn một phòng</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}">Phòng {{ $room->room_number }} ({{ $room->building->name }}) - {{ number_format($room->price, 0, ',', '.') }} đ</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khách thuê</label>
                    <select name="tenant_id" required class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-indigo-500 bg-gray-50 border-gray-200">
                        <option value="">Chọn khách thuê</option>
                        @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->phone }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Lease Details -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 leading-7">Chi tiết thuê</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-indigo-500 bg-gray-50 border-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc (Tùy chọn)</label>
                        <input type="date" name="end_date" class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-indigo-500 bg-gray-50 border-gray-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiền đặt cọc (VNĐ)</label>
                    <input type="number" name="deposit" required class="w-full px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-indigo-500 bg-gray-50 border-gray-200" placeholder="VD: 5000000">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t">
            <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-0.5">
                Tạo hợp đồng & Nhận phòng
            </button>
        </div>
    </form>
</div>
@endsection
