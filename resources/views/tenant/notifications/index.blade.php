@extends('layouts.admin')

@section('title', 'Thông báo của tôi')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Thông báo của tôi</h1>
        <p class="text-gray-500 mt-1 text-sm leading-5">Cập nhật những tin tức mới nhất từ ban quản lý.</p>
    </div>
    <form action="{{ route('tenant.notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm bg-indigo-50 px-4 py-2 rounded-xl transition flex items-center">
            <i class="fas fa-check-double mr-2"></i>
            Đánh dấu tất cả là đã đọc
        </button>
    </form>
</div>

<div class="space-y-4">
    @forelse($notifications as $notification)
    <div class="bg-white p-6 rounded-2xl shadow-sm border {{ $notification->is_read ? 'border-gray-100 opacity-75' : 'border-indigo-100 ring-1 ring-indigo-50 shadow-indigo-50/50' }} transition group relative overflow-hidden">
        @if(!$notification->is_read)
        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-600"></div>
        @endif
        
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                    <i class="far fa-clock mr-1.5 text-xs"></i>
                    {{ $notification->created_at->diffForHumans() }}
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 {{ $notification->is_read ? 'font-medium' : 'font-extrabold' }}">
                    {{ $notification->title }}
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                    {{ $notification->content }}
                </p>
            </div>
            
            <div class="flex flex-col items-end gap-2 shrink-0">
                @if(!$notification->is_read)
                <form action="{{ route('tenant.notifications.update', $notification) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl hover:bg-black transition shadow-md shadow-indigo-100" title="Đánh dấu đã đọc">
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </form>
                @endif
                <span class="text-[10px] font-bold {{ $notification->is_read ? 'text-gray-400' : 'text-indigo-600' }} uppercase tracking-tighter">
                    {{ $notification->is_read ? 'Đã đọc' : 'Chưa đọc' }}
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white p-12 text-center rounded-3xl border border-dashed border-gray-200">
        <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="far fa-bell text-2xl"></i>
        </div>
        <p class="text-gray-500 italic">Bạn hiện không có thông báo nào.</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-8">
    {{ $notifications->links() }}
</div>
@endif
@endsection
