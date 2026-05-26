@extends('layouts.admin')

@section('title', 'Quản lý Thông báo')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Danh sách Thông báo</h1>
        <p class="text-gray-500 mt-1 text-sm leading-5">Xem và quản lý các thông báo đã gửi cho khách thuê.</p>
    </div>
    <a href="{{ route('notifications.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-sm flex items-center text-sm">
        <i class="fas fa-paper-plane mr-2 text-sm"></i>
        Gửi thông báo mới
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày gửi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Người nhận</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nội dung</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($notifications as $notification)
                <tr class="hover:bg-gray-50 transition group">
                    <td class="px-6 py-4 text-sm text-gray-600 font-bold whitespace-nowrap">
                        {{ $notification->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 leading-5">{{ $notification->user->name }}</div>
                        <div class="text-[10px] text-gray-400">ID: #{{ $notification->user->id }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800">
                        {{ $notification->title }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                        {{ $notification->content }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic text-sm">Chưa có thông báo nào được gửi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
