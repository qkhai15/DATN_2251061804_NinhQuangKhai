@extends('layouts.admin')

@section('title', 'Thêm phòng mới')

@section('content')
<div class="mb-10">
    <a href="{{ route('rooms.index') }}" class="text-indigo-600 font-bold text-xs uppercase tracking-widest flex items-center mb-6 hover:translate-x-[-4px] transition-transform">
        <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
    </a>
    <h1 class="text-4xl font-black text-gray-900 tracking-tight">Thêm phòng mới</h1>
    <p class="text-gray-500 mt-2 text-sm font-medium">Nhập thông tin chi tiết để khởi tạo phòng mới trong hệ thống.</p>
</div>

<div class="max-w-3xl bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-100/50 border border-gray-50">
    <form action="{{ route('rooms.store') }}" method="POST" class="space-y-10">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Tòa nhà / Khu vực</label>
                <select name="building_id" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition appearance-none font-black text-gray-800">
                    <option value="">Chọn tòa nhà...</option>
                    @foreach($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Số phòng</label>
                <input type="text" name="room_number" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-black text-gray-800" placeholder="VD: 101, A2...">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Giá thuê (VNĐ/tháng)</label>
                <input type="number" name="price" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-black text-gray-800" placeholder="VD: 3000000">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Diện tích (m²)</label>
                <input type="number" step="0.1" name="area" class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-black text-gray-800" placeholder="VD: 25.5">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Số người tối đa</label>
                <input type="number" name="max_people" value="2" class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-black text-gray-800">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Trạng thái khởi tạo</label>
                <select name="status" class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition appearance-none font-black text-gray-800">
                    <option value="empty">Còn trống</option>
                    <option value="rented">Đã thuê</option>
                    <option value="maintenance">Bảo trì</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end pt-8 border-t border-gray-50">
            <button type="submit" class="px-12 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-indigo-600 transition-all text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-200">
                Tạo phòng ngay
            </button>
        </div>
    </form>
</div>
@endsection
