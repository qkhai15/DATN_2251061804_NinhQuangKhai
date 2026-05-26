@extends('layouts.admin')

@section('title', 'Quản lý Dịch vụ')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Danh mục Dịch vụ</h1>
        <p class="text-gray-500 mt-1 text-sm">Quản lý các loại phí dịch vụ, điện nước và tiện ích khác.</p>
    </div>
    <button onclick="document.getElementById('add_service_modal').classList.remove('hidden')" class="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl font-bold transition shadow-xl shadow-gray-200 flex items-center text-sm">
        <i class="fas fa-plus mr-2 text-xs"></i>
        Thêm dịch vụ mới
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse($services as $service)
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-50 shadow-sm hover:shadow-xl transition-all duration-300 relative group overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-bl-[4rem] flex items-center justify-center -mr-4 -mt-4 transition-transform group-hover:scale-110">
            @php
                $icons = [
                    'Điện' => 'fa-bolt text-amber-500',
                    'Nước' => 'fa-tint text-sky-500',
                    'Internet' => 'fa-wifi text-indigo-500',
                    'Rác' => 'fa-trash-alt text-emerald-500',
                ];
                $icon = $icons[$service->name] ?? 'fa-concierge-bell text-gray-400';
            @endphp
            <i class="fas {{ $icon }} text-xl"></i>
        </div>
        
        <h3 class="text-xl font-black text-gray-900 mb-2 mt-4">{{ $service->name }}</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">{{ $service->description ?? 'Dịch vụ tiện ích' }}</p>
        
        <div class="flex items-baseline gap-1 mb-8">
            <span class="text-2xl font-black text-indigo-600">{{ number_format($service->unit_price, 0, ',', '.') }}</span>
            <span class="text-[10px] font-black text-gray-400 uppercase">đ / {{ $service->unit }}</span>
        </div>

        <div class="flex gap-2">
            <button onclick="editService({{ $service->id }}, '{{ $service->name }}', {{ $service->unit_price }}, '{{ $service->unit }}', '{{ $service->description }}')" class="flex-1 py-3 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 font-bold rounded-xl text-[10px] uppercase tracking-widest transition border border-gray-100">Sửa</button>
            <form action="{{ route('services.destroy', $service) }}" method="POST" class="shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Xóa dịch vụ này?')" class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-rose-50 text-gray-400 hover:text-rose-600 rounded-xl transition border border-gray-100">
                    <i class="fas fa-trash-alt text-[10px]"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center">
        <p class="text-gray-400">Chưa có dịch vụ nào được thiết lập.</p>
    </div>
    @endforelse
</div>

@if($services->hasPages())
<div class="mt-12 px-8 py-6 bg-white rounded-[2rem] shadow-sm border border-gray-50">
    {{ $services->links() }}
</div>
@endif

</div>

<!-- Add Service Modal -->
<div id="add_service_modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md p-10 rounded-[2.5rem] shadow-2xl">
        <h3 class="text-2xl font-black text-gray-900 mb-6">Thêm dịch vụ mới</h3>
        <form action="{{ route('services.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tên dịch vụ</label>
                <input type="text" name="name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold" placeholder="VD: Điện, Nước, Internet...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Đơn giá (đ)</label>
                    <input type="number" name="unit_price" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold" placeholder="3500">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Đơn vị</label>
                    <input type="text" name="unit" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-bold" placeholder="kWh, m3...">
                </div>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Mô tả</label>
                <textarea name="description" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-medium" placeholder="Mô tả ngắn gọn về dịch vụ..."></textarea>
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('add_service_modal').classList.add('hidden')" class="flex-1 py-4 bg-gray-100 text-gray-500 font-bold rounded-2xl hover:bg-gray-200 transition text-sm">Hủy bỏ</button>
                <button type="submit" class="flex-1 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition text-sm shadow-xl shadow-gray-200">Lưu dịch vụ</button>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- Edit Service Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-lg w-full shadow-2xl">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Chỉnh sửa Dịch vụ</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-900 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Tên dịch vụ</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Đơn giá (đ)</label>
                    <input type="number" name="unit_price" id="edit_price" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Đơn vị tính</label>
                    <input type="text" name="unit" id="edit_unit" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition" placeholder="VD: kWh, m3, tháng">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Mô tả</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition font-medium"></textarea>
            </div>
            <div class="pt-6">
                <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                    Lưu các thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editService(id, name, price, unit, description) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    
    // Set form action - Ensure it matches the prefix in web.php (admin/services/id)
    form.action = `/admin/services/${id}`;
    
    // Fill data
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_unit').value = unit;
    document.getElementById('edit_description').value = description;
    
    // Show modal
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
