@extends('layouts.admin')

@section('title', 'Ghi Chỉ số mới')

@section('content')
<div class="mb-8">
    <a href="{{ route('meter-readings.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4 transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Ghi Chỉ số Điện & Nước</h1>
</div>

<div class="max-w-2xl bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
    <form action="{{ route('meter-readings.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chọn phòng</label>
            <select name="room_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition appearance-none">
                <option value="">Chọn một phòng</option>
                @foreach($rooms as $room)
                <option value="{{ $room->id }}">Phòng {{ $room->room_number }} ({{ $room->building->name }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Loại chỉ số</label>
                <select name="type" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-700 transition appearance-none">
                    <option value="electricity">Điện (kWh)</option>
                    <option value="water">Nước (m³)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Ngày ghi nhận</label>
                <input type="date" name="read_date" required value="{{ date('Y-m-d') }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-gray-900 transition">
            </div>
        </div>
        <div class="relative">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">Chỉ số mới</label>
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="number" id="new_value" name="new_value" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none text-base font-black text-gray-900 transition" placeholder="VD: 1250">
                </div>
                <button type="button" id="scan-ocr-btn" class="flex-shrink-0 p-4 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-100 transition border border-indigo-100 group relative" title="Quét số từ ảnh (OCR)">
                    <i class="fas fa-camera text-xl group-hover:scale-110 transition-transform"></i>
                    <span id="ocr-loading" class="hidden absolute inset-0 flex items-center justify-center bg-indigo-100 rounded-2xl">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </span>
                </button>
            </div>
            <input type="file" id="ocr-image-input" accept="image/*" capture="environment" class="hidden">
            <p class="text-[10px] text-gray-400 font-medium italic mt-2 pl-1">* Hệ thống sẽ tự động tính toán dựa trên chỉ số cũ gần nhất.</p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const scanBtn = document.getElementById('scan-ocr-btn');
                const imageInput = document.getElementById('ocr-image-input');
                const newValueInput = document.getElementById('new_value');
                const loadingIcon = document.getElementById('ocr-loading');

                scanBtn.addEventListener('click', () => imageInput.click());

                imageInput.addEventListener('change', async function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const formData = new FormData();
                        formData.append('image', file);

                        loadingIcon.classList.remove('hidden');
                        scanBtn.disabled = true;

                        try {
                            const response = await fetch('http://127.0.0.1:5000/ocr', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();

                            if (result.success && result.data) {
                                newValueInput.value = result.data.value;
                                // Visual feedback for success
                                newValueInput.classList.add('ring-2', 'ring-emerald-500');
                                setTimeout(() => newValueInput.classList.remove('ring-2', 'ring-emerald-500'), 2000);
                            } else {
                                alert(result.message || 'Không thể nhận diện được số. Vui lòng chụp rõ hơn hoặc nhập tay.');
                            }
                        } catch (error) {
                            console.error('OCR Error:', error);
                            alert('Không kết nối được với máy chủ OCR. Hãy đảm bảo terminal AI đang chạy trên cổng 5000.');
                        } finally {
                            loadingIcon.classList.add('hidden');
                            scanBtn.disabled = false;
                            imageInput.value = ''; // Reset input
                        }
                    }
                });
            });
        </script>
        <div class="pt-6">
            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                Ghi và lưu chỉ số
            </button>
        </div>
    </form>
</div>
@endsection
