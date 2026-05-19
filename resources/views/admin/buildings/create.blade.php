@extends('layouts.admin')

@section('title', 'Thêm Tòa nhà Mới')

@section('content')
<div class="mb-8">
    <a href="{{ route('buildings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2 shadow-sm"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Thêm Tòa nhà Mới</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl">
    <form action="{{ route('buildings.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên tòa nhà</label>
                <input type="text" name="name" id="name" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="VD: Chung cư Sunrise">
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                <textarea name="address" id="address" rows="3" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Địa chỉ chi tiết..."></textarea>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả (Không bắt buộc)</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Thông tin chi tiết thêm..."></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                    Lưu tòa nhà
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
