@extends('layouts.admin')

@section('title', 'Hồ sơ của tôi')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Cấu hình Hồ sơ</h1>
    <p class="text-gray-500 mt-1">Quản lý thông tin cá nhân và chi tiết liên hệ của bạn.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
            <div class="relative inline-block mb-4">
                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff&size=200' }}" class="w-32 h-32 rounded-full border-4 border-indigo-50 shadow-sm mx-auto">
                <button class="absolute bottom-0 right-0 bg-white p-2 rounded-full shadow-lg border text-gray-600 hover:text-indigo-600">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h2 class="text-xl font-bold text-gray-900 leading-6">{{ $user->name }}</h2>
            <p class="text-gray-500 mb-6">{{ $user->role === 'admin' ? 'Quản trị viên' : 'Khách thuê' }}</p>
            
            <div class="text-left space-y-4 border-t pt-6">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-6 text-indigo-400"></i>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-6 text-indigo-400"></i>
                    <span>{{ $user->phone ?? 'Chưa cung cấp' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="lg:col-span-2">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Cập nhật Thông tin</h3>
            <form action="{{ route('tenant.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 leading-6">Họ và Tên</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 leading-6">Số Điện thoại</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Thông tin Bổ sung</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 leading-6">Số CCCD/CMND</label>
                            <input type="text" name="profile[id_card_number]" value="{{ $user->profile->id_card_number ?? '' }}" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 leading-6">Ngày sinh</label>
                            <input type="date" name="profile[dob]" value="{{ $user->profile->dob ?? '' }}" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1 leading-6">Địa chỉ thường trú</label>
                            <textarea name="profile[address]" rows="2" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-indigo-500">{{ $user->profile->address ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg transition">
                        Lưu Thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
