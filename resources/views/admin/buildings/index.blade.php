@extends('layouts.admin')

@section('title', 'Quản lý Tòa nhà')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hệ thống Tòa nhà & Khu trọ</h1>
        <p class="text-gray-500 mt-1 text-sm">Quản lý các địa điểm, tòa nhà và khu vực phòng trọ của bạn.</p>
    </div>
    <a href="{{ route('buildings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition shadow-xl shadow-indigo-100/50 flex items-center text-sm">
        <i class="fas fa-plus mr-2"></i>
        Thêm tòa nhà
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($buildings as $building)
    <div class="bg-white rounded-[2.5rem] border border-gray-50 shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
        <div class="h-40 bg-gradient-to-br from-indigo-500 to-indigo-800 relative">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute bottom-6 left-8 right-8">
                <h3 class="text-xl font-black text-white leading-tight mb-1">{{ $building->name }}</h3>
                <div class="flex items-center text-indigo-100 text-[10px] uppercase font-bold tracking-widest">
                    <i class="fas fa-map-marker-alt mr-2"></i> {{ Str::limit($building->address, 30) }}
                </div>
            </div>
        </div>
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <div class="text-center">
                    <div class="text-2xl font-black text-gray-900 leading-none">{{ $building->rooms->count() }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">Tổng phòng</div>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="text-center">
                    <div class="text-2xl font-black text-indigo-600 leading-none">{{ $building->rooms->where('status', 'rented')->count() }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">Đã thuê</div>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="text-center">
                    <div class="text-2xl font-black text-emerald-500 leading-none">{{ $building->rooms->where('status', 'empty')->count() }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">Phòng trống</div>
                </div>
            </div>
            
            <p class="text-gray-500 text-sm mb-8 leading-relaxed italic">
                {{ Str::limit($building->description, 80) ?? 'Chưa có mô tả chi tiết cho tòa nhà này.' }}
            </p>

            <div class="flex items-center gap-3">
                <a href="{{ route('buildings.edit', $building) }}" class="flex-1 py-3 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 font-bold rounded-xl text-xs text-center transition border border-gray-100 uppercase tracking-widest">
                    Chỉnh sửa
                </a>
                <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa tòa nhà này?')" class="w-12 h-12 flex items-center justify-center bg-gray-50 hover:bg-rose-50 text-gray-400 hover:text-rose-600 rounded-xl transition border border-gray-100">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200 text-center">
        <i class="fas fa-city text-5xl text-gray-300 mb-6 block"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có tòa nhà nào</h3>
        <p class="text-gray-500">Hãy bắt đầu bằng việc thêm tòa nhà đầu tiên của bạn.</p>
    </div>
    @endforelse
</div>

@if($buildings->hasPages())
<div class="mt-12 px-8 py-6 bg-white rounded-[2rem] shadow-sm border border-gray-50">
    {{ $buildings->links() }}
</div>
@endif
@endsection
