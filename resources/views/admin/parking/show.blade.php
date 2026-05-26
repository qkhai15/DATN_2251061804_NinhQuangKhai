@extends('layouts.admin')

@section('title', 'Thông tin Thẻ Gửi xe')

@section('content')
<div class="mb-8">
    <a href="{{ route('parking.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chi tiết Thẻ Gửi xe</h1>
</div>

<div class="max-w-xl bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
    <div class="space-y-8">
        <div class="flex items-center gap-4 border-b pb-6">
            <div class="w-16 h-16 bg-indigo-900 rounded-2xl flex items-center justify-center text-white text-2xl">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Số thẻ</span>
                <p class="text-2xl font-black text-gray-900">{{ $parking->card_number }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500 font-bold uppercase tracking-wider text-[10px]">Chủ sở hữu:</span>
                <span class="text-gray-900 font-black uppercase tracking-tight">{{ $parking->user->name }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500 font-bold uppercase tracking-wider text-[10px]">Biển số xe:</span>
                <span class="px-3 py-1 bg-gray-100 rounded-lg text-gray-900 font-black">{{ $parking->license_plate }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500 font-bold uppercase tracking-wider text-[10px]">Ngày cấp:</span>
                <span class="text-gray-900 font-medium">{{ $parking->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="pt-8 border-t flex gap-4">
            <a href="{{ route('parking.edit', $parking) }}" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-center py-4 rounded-2xl font-bold transition shadow-lg shadow-amber-100">
                Chỉnh sửa
            </a>
            <form action="{{ route('parking.destroy', $parking) }}" method="POST" class="flex-1" onsubmit="return confirm('Hủy thẻ này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-2xl font-bold transition shadow-lg shadow-rose-100">
                    Thu hồi thẻ
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
