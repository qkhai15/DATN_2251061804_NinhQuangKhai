@extends('layouts.admin')

@section('title', 'Gửi Thông báo')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Gửi Thông báo Mới</h1>
    <p class="text-gray-500 mt-1">Gửi thông báo đến một hoặc toàn bộ khách thuê.</p>
</div>

<div class="max-w-2xl bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
    <form action="{{ route('notifications.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Người nhận</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="recipient_type" value="all" checked class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 font-bold">Tất cả khách thuê</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="recipient_type" value="specific" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 font-bold">Khách thuê cụ thể</span>
                    </label>
                </div>
            </div>

            <div id="specific_user_field" class="col-span-2 hidden">
                <label class="block text-sm font-bold text-gray-700 mb-2">Chọn khách thuê</label>
                <select name="user_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none font-medium">
                    <option value="">Chọn khách thuê...</option>
                    @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề thông báo</label>
                <input type="text" name="title" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-medium" placeholder="VD: Thông báo bảo trì điện...">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung thông báo</label>
                <textarea name="content" rows="6" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-medium" placeholder="Nhập nội dung chi tiết..."></textarea>
            </div>
        </div>

        <div class="flex justify-end pt-4 space-x-4">
            <a href="{{ route('notifications.index') }}" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Hủy bỏ</a>
            <button type="submit" class="px-8 py-3 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition text-sm shadow-lg shadow-gray-200">Gửi thông báo</button>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const field = document.getElementById('specific_user_field');
            if (e.target.value === 'specific') {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
            }
        });
    });
</script>
@endsection
