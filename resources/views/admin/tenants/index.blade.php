@extends('layouts.admin')

@section('title', 'Quản lý Người thuê')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Danh sách Người thuê</h1>
        <p class="text-gray-500 mt-1 text-sm">Quản lý tài khoản và thông tin cá nhân của khách thuê phòng.</p>
    </div>
    <a href="{{ route('tenants.create') }}" class="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition shadow-xl shadow-gray-200 flex items-center text-sm">
        <i class="fas fa-plus mr-2 text-xs"></i>
        Thêm khách thuê mới
    </a>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Khách thuê</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Liên hệ</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Phòng đang thuê</th>
                    <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($tenants as $tenant)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-lg mr-4 border border-indigo-100 group-hover:scale-110 transition-transform">
                                {{ substr($tenant->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-black text-gray-900 leading-tight uppercase text-sm">{{ $tenant->name }}</div>
                                <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-tighter">ID: #TEN-{{ str_pad($tenant->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-gray-700">{{ $tenant->email }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $tenant->phone ?? 'Chưa cập nhật SĐT' }}</div>
                    </td>
                    <td class="px-8 py-6">
                        @php $activeContract = $tenant->contracts->where('status', 'active')->first(); @endphp
                        @if($activeContract)
                            <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">PHÒNG {{ $activeContract->room->room_number }}</span>
                        @else
                            <span class="px-4 py-1.5 bg-gray-50 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest">CHƯA THUÊ</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('tenants.edit', $tenant) }}" class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 hover:bg-amber-100 transition shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" class="inline-block" onsubmit="return confirm('Xóa tài khoản khách thuê này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 hover:bg-rose-100 transition shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center text-gray-400 font-medium">Chưa có người thuê nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection
