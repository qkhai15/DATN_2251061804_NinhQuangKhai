@extends('layouts.admin')

@section('title', 'Chỉ số Điện Nước')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Quản lý Chỉ số Điện & Nước</h1>
        <p class="text-gray-500 mt-1 text-sm">Ghi chép và theo dõi chỉ số sử dụng hàng tháng của từng phòng.</p>
    </div>
    <a href="{{ route('meter-readings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition shadow-xl shadow-indigo-100/50 flex items-center text-sm">
        <i class="fas fa-plus mr-2"></i>
        Ghi chỉ số mới
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Phòng</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Loại</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Chỉ số cũ</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Chỉ số mới</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Sử dụng</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Ngày ghi</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($readings as $reading)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-8 py-6">
                        <div class="font-bold text-gray-900">Phòng {{ $reading->room->room_number }}</div>
                    </td>
                    <td class="px-8 py-6">
                        @if($reading->type == 'electricity')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight bg-amber-50 text-amber-600">
                            <i class="fas fa-bolt mr-1.5"></i> Điện
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight bg-sky-50 text-sky-600">
                            <i class="fas fa-tint mr-1.5"></i> Nước
                        </span>
                        @endif
                    </td>
                    <td class="px-8 py-6 font-medium text-gray-400 italic">{{ number_format($reading->old_value, 0) }}</td>
                    <td class="px-8 py-6 font-black text-gray-900">{{ number_format($reading->new_value, 0) }}</td>
                    <td class="px-8 py-6">
                        <span class="font-black text-indigo-600">{{ number_format($reading->new_value - $reading->old_value, 0) }}</span>
                        <span class="text-[10px] text-gray-400 font-bold ml-1 uppercase">{{ $reading->type == 'electricity' ? 'kWh' : 'm3' }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($reading->read_date)->format('d/m/Y') }}
                    </td>
                    <td class="px-8 py-6 text-right">
                        <form action="{{ route('meter-readings.destroy', $reading) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:bg-rose-50 p-3 rounded-xl transition" onclick="return confirm('Xóa bản ghi này?')">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-20 text-center">
                        <p class="text-gray-400 font-bold italic">Chưa có chỉ số nào được ghi nhận.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
