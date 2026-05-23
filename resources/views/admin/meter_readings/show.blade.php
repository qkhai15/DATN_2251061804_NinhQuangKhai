@extends('layouts.admin')

@section('title', 'Chi tiết Chỉ số')

@section('content')
<div class="mb-8">
    <a href="{{ route('meter-readings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chi tiết Chỉ số</h1>
</div>

<div class="max-w-2xl bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
    <div class="space-y-8">
        <div class="flex justify-between items-center border-b pb-6">
            <div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Số phòng</span>
                <p class="text-2xl font-black text-gray-900">Phòng {{ $meter_reading->room->room_number }}</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Loại chỉ số</span>
                @if($meter_reading->type == 'electricity')
                    <span class="bg-amber-50 text-amber-600 px-4 py-2 rounded-xl text-xs font-black uppercase"><i class="fas fa-bolt mr-2"></i>Điện</span>
                @else
                    <span class="bg-sky-50 text-sky-600 px-4 py-2 rounded-xl text-xs font-black uppercase"><i class="fas fa-tint mr-2"></i>Nước</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8">
            <div class="bg-gray-50 p-6 rounded-2xl">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Chỉ số cũ</span>
                <p class="text-3xl font-black text-gray-400">{{ number_format($meter_reading->old_value, 0) }}</p>
            </div>
            <div class="bg-indigo-50 p-6 rounded-2xl">
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block mb-2">Chỉ số mới</span>
                <p class="text-3xl font-black text-indigo-600">{{ number_format($meter_reading->new_value, 0) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-6 bg-indigo-900 p-8 rounded-3xl text-white">
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest block mb-1">Mức sử dụng</span>
                <p class="text-3xl font-black">{{ number_format($meter_reading->new_value - $meter_reading->old_value, 0) }} <span class="text-sm font-bold opacity-60">{{ $meter_reading->type == 'electricity' ? 'kWh' : 'm³' }}</span></p>
            </div>
        </div>

        <div class="pt-6 border-t flex justify-between items-center text-sm">
            <span class="text-gray-500">Ngày ghi: <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($meter_reading->read_date)->format('d/m/Y') }}</span></span>
            <div class="flex gap-4">
                <a href="{{ route('meter-readings.edit', $meter_reading) }}" class="text-amber-600 font-bold hover:underline">Chỉnh sửa</a>
                <form action="{{ route('meter-readings.destroy', $meter_reading) }}" method="POST" onsubmit="return confirm('Xóa bản ghi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-600 font-bold hover:underline">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
