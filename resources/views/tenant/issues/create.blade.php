@extends('layouts.admin')

@section('title', 'Báo cáo sự cố mới')

@section('content')
<div class="mb-10">
    <a href="{{ route('tenant.issues.index') }}" class="text-indigo-600 font-bold text-xs uppercase tracking-widest flex items-center mb-6 hover:translate-x-[-4px] transition-transform">
        <i class="fas fa-arrow-left mr-2"></i> Quay lại
    </a>
    <h1 class="text-4xl font-black text-gray-900 tracking-tight">Báo cáo sự cố mới</h1>
    <p class="text-gray-500 mt-2 text-sm font-medium">Vui lòng mô tả chi tiết vấn đề bạn đang gặp phải để chúng tôi hỗ trợ sớm nhất.</p>
</div>

<div class="max-w-3xl bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-100/50 border border-gray-50">
    <form action="{{ route('tenant.issues.store') }}" method="POST" class="space-y-10">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Vị trí / Phòng</label>
                <select name="room_id" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition appearance-none font-black text-gray-800">
                    <option value="">Chọn phòng của bạn...</option>
                    @foreach($rooms as $room)
                    <option value="{{ $room->id }}">Phòng {{ $room->room_number }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Mức độ ưu tiên</label>
                <div class="flex items-center bg-gray-50 p-2 rounded-2xl border border-gray-100">
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="priority" value="low" class="sr-only peer" checked>
                        <div class="py-3 rounded-xl peer-checked:bg-white peer-checked:shadow-sm peer-checked:text-indigo-600 text-gray-400 font-black text-[10px] uppercase tracking-widest transition-all">Thấp</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="priority" value="medium" class="sr-only peer">
                        <div class="py-3 rounded-xl peer-checked:bg-white peer-checked:shadow-sm peer-checked:text-amber-600 text-gray-400 font-black text-[10px] uppercase tracking-widest transition-all">Vừa</div>
                    </label>
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="priority" value="high" class="sr-only peer">
                        <div class="py-3 rounded-xl peer-checked:bg-white peer-checked:shadow-sm peer-checked:text-rose-600 text-gray-400 font-black text-[10px] uppercase tracking-widest transition-all">Cao</div>
                    </label>
                </div>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Tiêu đề sự cố</label>
                <input type="text" name="title" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-black text-gray-800" placeholder="VD: Hỏng vòi nước, Mất điện...">
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4 pl-1">Mô tả chi tiết</label>
                <textarea name="description" rows="6" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm transition font-medium text-gray-600 leading-relaxed" placeholder="Vui lòng mô tả chi tiết tình trạng bên dưới..."></textarea>
            </div>
        </div>

        <div class="flex justify-end pt-8 border-t border-gray-50">
            <button type="submit" class="px-12 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-indigo-600 transition-all text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-200">
                Gửi yêu cầu ngay
            </button>
        </div>
    </form>
</div>
@endsection
