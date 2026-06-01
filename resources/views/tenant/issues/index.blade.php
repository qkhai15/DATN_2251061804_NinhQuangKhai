@extends('layouts.admin')

@section('title', 'Báo cáo Sự cố')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Vấn đề & Sửa chữa</h1>
        <p class="text-gray-500 mt-2 text-sm font-medium">Theo dõi các yêu cầu bảo trì và sửa chữa của bạn.</p>
    </div>
    <a href="{{ route('tenant.issues.create') }}" class="px-6 py-3.5 bg-gray-900 text-white font-black rounded-[1.2rem] hover:bg-indigo-600 transition-all shadow-xl shadow-gray-200 flex items-center text-sm uppercase tracking-widest">
        <i class="fas fa-plus-circle mr-3"></i>
        Báo sự cố mới
    </a>
</div>

<div class="grid grid-cols-1 gap-6">
    @forelse($issues as $issue)
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 transition-all hover:shadow-xl hover:shadow-gray-100/50 group">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    @php
                        $priorityMap = [
                            'low' => ['label' => 'Thấp', 'class' => 'bg-sky-50 text-sky-600'],
                            'medium' => ['label' => 'Vừa', 'class' => 'bg-amber-50 text-amber-600'],
                            'high' => ['label' => 'Cao', 'class' => 'bg-rose-50 text-rose-600'],
                        ];
                        $p = $priorityMap[$issue->priority] ?? ['label' => $issue->priority, 'class' => 'bg-gray-50 text-gray-500'];
                    @endphp
                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.1em] {{ $p['class'] }}">
                        Ưu tiên: {{ $p['label'] }}
                    </span>
                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                        <i class="far fa-clock mr-1"></i> {{ $issue->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                    {{ $issue->title }}
                </h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-4">
                    {{ $issue->description }}
                </p>
                <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                    <div class="flex items-center text-[10px] font-black text-indigo-500 uppercase tracking-tighter bg-indigo-50 px-3 py-1 rounded-lg">
                        <i class="fas fa-door-open mr-2"></i> Phòng {{ $issue->room->room_number }}
                    </div>
                </div>
            </div>
            
            <div class="shrink-0 flex items-center gap-6 pl-6 lg:border-l lg:border-gray-50">
                <div class="text-center">
                    <div class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-3 text-left">Trạng thái</div>
                    @php
                        $statusMap = [
                            'pending' => ['label' => 'Đang chờ', 'class' => 'text-amber-600 bg-amber-50'],
                            'fixing' => ['label' => 'Đang sửa', 'class' => 'text-indigo-600 bg-indigo-50'],
                            'resolved' => ['label' => 'Đã xử lý', 'class' => 'text-emerald-600 bg-emerald-50'],
                        ];
                        $st = $statusMap[$issue->status] ?? ['label' => $issue->status, 'class' => 'text-gray-500 bg-gray-50'];
                    @endphp
                    <div class="px-6 py-2.5 rounded-2xl font-black text-xs uppercase tracking-widest {{ $st['class'] }} min-w-[140px]">
                        {{ $st['label'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-[3rem] p-20 text-center">
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i class="fas fa-check-double text-3xl text-emerald-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Mọi thứ đều ổn!</h3>
        <p class="text-gray-500 max-w-xs mx-auto text-sm">Bạn hiện không có báo cáo sự cố nào đang chờ xử lý.</p>
    </div>
    @endforelse
</div>

@if($issues->hasPages())
<div class="mt-10">
    {{ $issues->links() }}
</div>
@endif
@endsection
