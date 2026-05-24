@extends('layouts.admin')

@section('title', 'Quản lý Sự cố')

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Danh sách Sự cố & Sửa chữa</h1>
        <p class="text-gray-500 mt-2 text-sm">Xem và xử lý các yêu cầu sửa chữa từ khách thuê.</p>
    </div>
    <a href="{{ route('issues.report') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition shadow-xl shadow-indigo-100/50 flex items-center text-sm">
        <i class="fas fa-chart-bar mr-2"></i>
        Báo cáo hàng tháng
    </a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Khách thuê / Phòng</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Nội dung sự cố</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Ưu tiên</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($issues as $issue)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-8 py-6">
                        <div class="font-bold text-gray-900 text-sm">{{ $issue->user->name }}</div>
                        <div class="text-[11px] font-bold text-indigo-500 uppercase mt-1">Phòng {{ $issue->room->room_number }}</div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-gray-800 font-bold text-sm">{{ $issue->title }}</div>
                        <div class="text-[11px] text-gray-400 truncate w-48 mt-1 italic">{{ $issue->description }}</div>
                        @if($issue->image_proof)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $issue->image_proof) }}" target="_blank" class="inline-flex items-center group/img">
                                <img src="{{ asset('storage/' . $issue->image_proof) }}" class="w-8 h-8 object-cover rounded-lg border border-gray-100 shadow-sm group-hover/img:border-indigo-300 transition-all" alt="Proof">
                                <span class="ml-2 text-[9px] font-bold text-indigo-500 uppercase tracking-widest opacity-0 group-hover/img:opacity-100 transition-opacity">Xem ảnh</span>
                            </a>
                        </div>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @php
                            $priorityMap = [
                                'low' => ['label' => 'Thấp', 'class' => 'bg-sky-100 text-sky-700'],
                                'medium' => ['label' => 'Vừa', 'class' => 'bg-amber-100 text-amber-700'],
                                'high' => ['label' => 'Cao', 'class' => 'bg-rose-100 text-rose-700'],
                            ];
                            $p = $priorityMap[$issue->priority] ?? ['label' => $issue->priority, 'class' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $p['class'] }}">
                            {{ $p['label'] }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <form action="{{ route('issues.update', $issue) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="text-[11px] font-bold uppercase tracking-tight py-2 px-4 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer">
                                <option value="pending" {{ $issue->status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                <option value="fixing" {{ $issue->status == 'fixing' ? 'selected' : '' }}>Đang sửa</option>
                                <option value="resolved" {{ $issue->status == 'resolved' ? 'selected' : '' }}>Đã xong</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('issues.show', $issue) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm border border-gray-100">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                            <i class="fas fa-tools text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-bold text-sm italic">Không có yêu cầu sửa chữa nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
