@extends('layouts.admin')

@section('title', 'Cập nhật Sự cố')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <a href="{{ route('issues.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay lại danh sách
        </a>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Chi tiết Sự cố #{{ $issue->id }}</h1>
    </div>
    @if($issue->image_proof)
    <a href="{{ asset('storage/' . $issue->image_proof) }}" target="_blank" class="text-xs bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-bold hover:bg-indigo-100 transition">
        <i class="fas fa-image mr-2"></i>Xem ảnh gốc
    </a>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Cột trái: Thông tin sự cố --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-4">Thông tin báo cáo</h3>
            
            @if($issue->image_proof)
            <div class="rounded-2xl overflow-hidden border border-gray-100">
                <img src="{{ asset('storage/' . $issue->image_proof) }}" alt="Bằng chứng sự cố" class="w-full h-48 object-cover">
            </div>
            @endif

            <div>
                <span class="text-[10px] font-bold text-indigo-500 uppercase block mb-1">Loại sự cố</span>
                <p class="text-lg font-black text-gray-900">{{ $issue->title }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Mô tả chi tiết</span>
                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-2xl italic">{{ $issue->description }}</p>
            </div>
            
            <div class="pt-4 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-medium">Phòng:</span>
                    <span class="font-bold text-gray-900">Phòng {{ $issue->room->room_number }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-medium">Người báo:</span>
                    <span class="font-bold text-gray-900">{{ $issue->user->name }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-medium">Thời gian:</span>
                    <span class="font-bold text-gray-900">{{ $issue->created_at->format('H:i d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cột phải: Xử lý và Chi phí --}}
    <div class="lg:col-span-2">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-4 mb-6">Cập nhật xử lý & Chi phí</h3>
            
            <form action="{{ route('issues.update', $issue) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Trạng thái --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Trạng thái xử lý</label>
                        <select name="status" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition appearance-none">
                            <option value="pending" {{ $issue->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="fixing" {{ $issue->status == 'fixing' ? 'selected' : '' }}>Đang sửa chữa</option>
                            <option value="resolved" {{ $issue->status == 'resolved' ? 'selected' : '' }}>Đã hoàn thành</option>
                        </select>
                    </div>

                    {{-- Lỗi do ai --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Lỗi do ai (Trách nhiệm)</label>
                        <input type="text" name="responsible_party" value="{{ $issue->responsible_party }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition" placeholder="Ví dụ: Khách thuê làm hỏng, Hao mòn tự nhiên...">
                    </div>

                    {{-- Chi phí --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chi phí sửa chữa (VNĐ)</label>
                        <input type="number" name="repair_cost" value="{{ (int)$issue->repair_cost }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition" placeholder="Nhập số tiền...">
                    </div>

                    {{-- Người thanh toán --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Ai thanh toán chi phí?</label>
                        <select name="payer" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition appearance-none">
                            <option value="owner" {{ $issue->payer == 'owner' ? 'selected' : '' }}>Chủ nhà trả</option>
                            <option value="tenant" {{ $issue->payer == 'tenant' ? 'selected' : '' }}>Khách thuê trả</option>
                            <option value="shared" {{ $issue->payer == 'shared' ? 'selected' : '' }}>Chia đôi (50/50)</option>
                        </select>
                    </div>
                </div>

                {{-- Ghi chú admin --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Ghi chú chi tiết / Quá trình xử lý</label>
                    <textarea name="admin_note" rows="4" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-medium" placeholder="Cập nhật tiến độ hoặc nguyên nhân cụ thể...">{{ $issue->admin_note }}</textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-black transition shadow-xl shadow-indigo-100">
                        Lưu toàn bộ cập nhật
                    </button>
                    <a href="{{ route('issues.index') }}" class="py-4 px-8 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
