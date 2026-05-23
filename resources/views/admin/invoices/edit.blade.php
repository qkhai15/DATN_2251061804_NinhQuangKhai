@extends('layouts.admin')

@section('title', 'Cập nhật Hóa đơn')

@section('content')
<div class="mb-8">
    <a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Cập nhật Hóa đơn #{{ $invoice->id }}</h1>
    <p class="text-gray-500 mt-1">Phòng: {{ $invoice->contract->room->room_number }} - Kỳ: Tháng {{ $invoice->month }}/{{ $invoice->year }}</p>
</div>

<div class="max-w-2xl bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Trạng thái thanh toán</label>
                <select name="status" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none font-bold text-gray-700">
                    <option value="unpaid" {{ $invoice->status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="partially_paid" {{ $invoice->status == 'partially_paid' ? 'selected' : '' }}>Thanh toán một phần</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Ngày thanh toán (Nếu có)</label>
                <input type="date" name="payment_date" value="{{ $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : '' }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold text-gray-700">
            </div>

            <div class="bg-indigo-50 p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-indigo-600 font-bold">Tổng số tiền:</span>
                    <span class="text-xl font-black text-indigo-900">{{ number_format($invoice->total_amount, 0) }} VNĐ</span>
                </div>
                <p class="text-[10px] text-indigo-400 font-medium italic">* Để thay đổi hạng mục chi tiết, vui lòng xóa và tạo mới hóa đơn.</p>
            </div>
        </div>

        <div class="flex justify-end pt-6 space-x-4 border-t border-gray-50">
            <a href="{{ route('invoices.index') }}" class="px-8 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Hủy bỏ</a>
            <button type="submit" class="px-10 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition text-sm shadow-xl shadow-gray-200">
                Lưu thay đổi
            </button>
        </div>
    </form>
</div>
@endsection
