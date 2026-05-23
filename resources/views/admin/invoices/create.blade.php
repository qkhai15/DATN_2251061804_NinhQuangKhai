@extends('layouts.admin')

@section('title', 'Tạo Hóa đơn')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tạo Hóa đơn Mới</h1>
    <p class="text-gray-500 mt-2 text-sm">Lập hóa đơn thanh toán cho khách thuê dựa trên hợp đồng đang hoạt động.</p>
</div>

<div class="max-w-3xl bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
    <form action="{{ route('invoices.preview') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chọn Hợp đồng / Phòng</label>
                <select name="contract_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none font-bold text-gray-700">
                    <option value="">Chọn hợp đồng đang hoạt động...</option>
                    @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}" {{ (isset($selected_contract_id) && $selected_contract_id == $contract->id) ? 'selected' : '' }}>Phòng {{ $contract->room->room_number }} - {{ $contract->tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Tháng</label>
                <select name="month" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none font-bold text-gray-700">
                    @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Năm</label>
                <select name="year" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition appearance-none font-bold text-gray-700">
                    @for($y=date('Y'); $y>=date('Y')-1; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Trạng thái thanh toán mặc định</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" name="status" value="unpaid" checked class="w-5 h-5 text-rose-600 focus:ring-rose-500 border-gray-300">
                        <span class="ml-3 text-sm text-gray-600 font-bold group-hover:text-rose-600 transition">Chưa thanh toán</span>
                    </label>
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" name="status" value="paid" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <span class="ml-3 text-sm text-gray-600 font-bold group-hover:text-emerald-600 transition">Đã thanh toán</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Additional Services Section -->
        <div class="pt-8 border-t border-gray-50">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Dịch vụ bổ sung & Số lượng</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($services as $service)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-indigo-200 transition">
                    <div class="flex-1">
                        <span class="block text-sm font-black text-gray-700 leading-tight">{{ $service->name }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ number_format($service->unit_price, 0, ',', '.') }} đ / {{ $service->unit }}</span>
                    </div>
                    <div class="w-24">
                        <input type="number" name="services[{{ $service->id }}]" 
                               value="{{ in_array($service->name, ['Internet', 'Rác']) ? 1 : '' }}" 
                               step="0.1"
                               class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-black text-center transition" 
                               placeholder="0">
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-[10px] text-gray-400 mt-4 italic">* Để trống hoặc nhập 0 nếu không sử dụng dịch vụ này trong tháng.</p>
        </div>

        <div class="flex justify-end pt-6 space-x-4 border-t border-gray-50">
            <a href="{{ route('invoices.index') }}" class="px-8 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Hủy bỏ</a>
            <button type="submit" class="px-10 py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition text-sm shadow-xl shadow-indigo-100">
                Xem trước hóa đơn
            </button>
        </div>
    </form>
</div>
@endsection
