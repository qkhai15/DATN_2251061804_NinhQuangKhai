@extends('layouts.admin')

@section('title', 'Quản lý Gửi xe')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Danh sách Thẻ gửi xe</h1>
        <p class="text-gray-500 mt-1 text-sm">Quản lý thẻ xe và thông tin phương tiện của khách thuê.</p>
    </div>
    <button onclick="document.getElementById('add_parking_modal').classList.remove('hidden')" class="bg-gray-900 hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold transition shadow-lg flex items-center text-sm">
        <i class="fas fa-plus mr-2 text-xs"></i>
        Cấp thẻ mới
    </button>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Khách thuê</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Mã thẻ</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Biển số xe</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Loại xe</th>
                    <th class="px-8 py-5 text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($parkingCards as $card)
                <tr class="hover:bg-gray-50/80 transition-all duration-300 group">
                    <td class="px-8 py-6">
                        <div class="font-bold text-gray-900 text-sm">{{ $card->user->name }}</div>
                        <div class="text-[10px] text-gray-400 font-medium uppercase mt-1">{{ $card->user->phone }}</div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 font-black rounded-lg text-xs leading-none">
                            {{ $card->card_number }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="w-24 px-2 py-1 bg-white border-2 border-gray-800 rounded-md text-center">
                            <div class="text-[8px] font-bold text-gray-400 uppercase leading-none border-b border-gray-100 pb-0.5 mb-0.5">Việt Nam</div>
                            <div class="text-xs font-black text-gray-800 tracking-tighter">{{ $card->license_plate }}</div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-sm font-bold text-gray-600">
                            {{ $card->vehicle_type ?? 'Chưa xác định' }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <button class="text-gray-400 hover:text-rose-600 p-2 transition">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <p class="text-gray-400 italic">Chưa có dữ liệu thẻ gửi xe.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($parkingCards->hasPages())
    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
        {{ $parkingCards->links() }}
    </div>
    @endif
</div>

<!-- Simple Modal for Adding -->
<div id="add_parking_modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md p-10 rounded-[2.5rem] shadow-2xl">
        <h3 class="text-2xl font-black text-gray-900 mb-6">Cấp thẻ gửi xe mới</h3>
        <form action="{{ route('parking.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Khách thuê</label>
                <select name="user_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold">
                    <option value="">Chọn khách thuê...</option>
                    @foreach(\App\Models\User::where('role', 'tenant')->get() as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Mã thẻ</label>
                <input type="text" name="card_number" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold" placeholder="VD: CARD-001">
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Biển số xe</label>
                <input type="text" name="license_plate" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold text-center uppercase" placeholder="VD: 59-X1 12345">
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('add_parking_modal').classList.add('hidden')" class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Hủy bỏ</button>
                <button type="submit" class="flex-1 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition text-sm shadow-xl shadow-gray-200">Lưu lại</button>
            </div>
        </form>
    </div>
</div>
@endsection
