@extends('layouts.admin')

@section('title', 'Cấp Thẻ Gửi xe Mới')

@section('content')
<div class="mb-8">
    <a href="{{ route('parking.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Cấp Thẻ Gửi xe Mới</h1>
</div>

<div class="max-w-xl bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
    <form action="{{ route('parking.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chọn chủ thẻ (Khách thuê)</label>
            <select name="user_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition appearance-none">
                <option value="">Chọn một khách thuê</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->phone }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Biển số xe</label>
                <input type="text" name="license_plate" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: 29A1-123.45">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Mã số thẻ</label>
                <input type="text" name="card_number" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: PX-001">
            </div>
        </div>
        <div class="pt-6">
            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                Đăng ký cấp thẻ
            </button>
        </div>
    </form>
</div>
@endsection
