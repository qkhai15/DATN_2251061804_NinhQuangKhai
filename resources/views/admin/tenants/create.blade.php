@extends('layouts.admin')

@section('title', 'Thêm Người thuê mới')

@section('content')
<div class="mb-8">
    <a href="{{ route('tenants.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2 text-xs"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Thêm Khách thuê mới</h1>
</div>

<div class="max-w-2xl bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-10">
    <form action="{{ route('tenants.store') }}" method="POST" class="space-y-8">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Họ và tên</label>
                <input type="text" name="name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: Nguyễn Văn A">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Email (Tài khoản đăng nhập)</label>
                    <input type="email" name="email" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: tenant@example.com">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Số điện thoại</label>
                    <input type="text" name="phone" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: 0912345678">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Mật khẩu ban đầu</label>
                <input type="password" name="password" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="Tối thiểu 8 ký tự">
                <p class="text-[10px] text-gray-400 mt-2 italic">* Cung cấp mật khẩu này cho khách thuê để họ đăng nhập.</p>
            </div>
        </div>
        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-black transition shadow-2xl flex items-center justify-center gap-3">
                <i class="fas fa-user-plus text-sm"></i>
                Tạo tài khoản khách thuê
            </button>
        </div>
    </form>
</div>
@endsection
