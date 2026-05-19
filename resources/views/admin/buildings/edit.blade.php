@extends('layouts.admin')

@section('title', 'Chỉnh sửa Tòa nhà')

@section('content')
<div class="mb-8">
    <a href="{{ route('buildings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Tòa nhà: {{ $building->name }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl">
    <form action="{{ route('buildings.update', $building) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1 leading-6">Tên tòa nhà</label>
                <input type="text" name="name" id="name" value="{{ $building->name }}" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1 leading-6">Địa chỉ</label>
                <textarea name="address" id="address" rows="3" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $building->address }}</textarea>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1 leading-6">Mô tả (Không bắt buộc)</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ $building->description }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                    Cập nhật tòa nhà
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
