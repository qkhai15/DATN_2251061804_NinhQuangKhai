@extends('layouts.admin')

@section('title', 'Chỉnh sửa Người thuê')

@section('content')
<div class="mb-8">
    <a href="{{ route('tenants.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2 text-xs"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chỉnh sửa Khách thuê</h1>
</div>

<div class="max-w-2xl bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-10">
    <form action="{{ route('tenants.update', $tenant) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Họ và tên</label>
                <input type="text" name="name" value="{{ $tenant->name }}" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Email</label>
                    <input type="email" name="email" value="{{ $tenant->email }}" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ $tenant->phone }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Đổi mật khẩu (Bỏ trống nếu không đổi)</label>
                <input type="password" name="password" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="Tối thiểu 8 ký tự">
            </div>
        </div>
        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-2xl flex items-center justify-center gap-3">
                <i class="fas fa-save text-sm"></i>
                Cập nhật thông tin
            </button>
        </div>
    </form>
</div>
@endsection
