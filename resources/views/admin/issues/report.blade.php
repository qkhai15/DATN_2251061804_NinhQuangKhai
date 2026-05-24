@extends('layouts.admin')

@section('title', 'Báo cáo Sự cố hàng tháng')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Báo cáo sự cố</h1>
        <p class="text-gray-500 mt-1 text-sm font-medium">Tháng {{ $month }} năm {{ $year }}</p>
    </div>
    
    <form action="{{ route('issues.report') }}" method="GET" class="flex gap-2">
        <select name="month" class="px-4 py-2 bg-white border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-bold">
            @for($m=1; $m<=12; $m++)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
            @endfor
        </select>
        <select name="year" class="px-4 py-2 bg-white border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-bold">
            @for($y=now()->year; $y>=2024; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-black transition">Xem báo cáo</button>
    </form>
</div>

<!-- Thống kê nhanh -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tổng số sự cố</p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</span>
            <span class="text-xs text-emerald-500 font-bold">Đã xong {{ $stats['resolved'] }}</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tổng chi phí sửa chữa</p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-indigo-600">{{ number_format($stats['total_cost'], 0, ',', '.') }}</span>
            <span class="text-xs text-gray-400 font-bold">VNĐ</span>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Trách nhiệm chi trả</p>
        <div class="flex flex-col gap-1">
            <div class="flex justify-between text-xs">
                <span class="text-gray-500">Chủ nhà trả:</span>
                <span class="font-bold text-gray-900">{{ number_format($stats['owner_pay'], 0, ',', '.') }} đ</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-gray-500">Khách thuê trả:</span>
                <span class="font-bold text-rose-500">{{ number_format($stats['tenant_pay'], 0, ',', '.') }} đ</span>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách chi tiết -->
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">
                    <th class="px-8 py-5">Ngày báo / Phòng</th>
                    <th class="px-8 py-5">Nội dung sự cố</th>
                    <th class="px-8 py-5">Nguyên nhân (Do ai)</th>
                    <th class="px-8 py-5 text-right">Chi phí</th>
                    <th class="px-8 py-5">Người trả</th>
                    <th class="px-8 py-5">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($issues as $issue)
                <tr class="hover:bg-gray-50/50 transition group">
                    <td class="px-8 py-6">
                        <div class="font-black text-gray-900 uppercase text-xs tracking-tighter">Phòng {{ $issue->room->room_number }}</div>
                        <div class="text-[10px] text-gray-400 font-bold mt-1">{{ $issue->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-gray-900 leading-tight">{{ $issue->title }}</div>
                        @if($issue->image_proof)
                        <div class="mt-2 group-hover:scale-105 transition-transform origin-left">
                            <a href="{{ asset('storage/' . $issue->image_proof) }}" target="_blank" class="inline-flex items-center">
                                <img src="{{ asset('storage/' . $issue->image_proof) }}" class="w-10 h-10 object-cover rounded-lg border border-indigo-100 shadow-sm" alt="Proof">
                                <span class="ml-2 text-[9px] font-black text-indigo-500 uppercase tracking-widest">Xem ảnh</span>
                            </a>
                        </div>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm text-gray-600 font-medium italic">
                            {{ $issue->responsible_party ?: '— Chưa xác định —' }}
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="font-black text-gray-900 text-sm">{{ number_format($issue->repair_cost, 0, ',', '.') }} đ</div>
                    </td>
                    <td class="px-8 py-6">
                        @php
                            $payerMap = [
                                'owner' => ['label' => 'Chủ nhà', 'class' => 'text-indigo-600'],
                                'tenant' => ['label' => 'Khách thuê', 'class' => 'text-rose-600'],
                                'shared' => ['label' => 'Chia đôi', 'class' => 'text-amber-600'],
                            ];
                            $p = $payerMap[$issue->payer] ?? ['label' => '—', 'class' => 'text-gray-300'];
                        @endphp
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $p['class'] }}">{{ $p['label'] }}</span>
                    </td>
                    <td class="px-8 py-6">
                        @php
                            $statusMap = [
                                'pending' => ['label' => 'Chờ', 'class' => 'bg-gray-100 text-gray-500'],
                                'fixing' => ['label' => 'Sửa', 'class' => 'bg-amber-100 text-amber-600'],
                                'resolved' => ['label' => 'Xong', 'class' => 'bg-emerald-100 text-emerald-600'],
                            ];
                            $s = $statusMap[$issue->status] ?? ['label' => '?', 'class' => 'bg-gray-50 text-gray-400'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $s['class'] }}">
                            {{ $s['label'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center text-gray-400 italic">
                        <i class="fas fa-check-circle text-4xl mb-4 block text-emerald-100"></i>
                        Tháng này không có sự cố nào được ghi nhận.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 flex justify-end">
    <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-600 px-6 py-3 rounded-2xl font-bold hover:bg-gray-50 transition shadow-sm flex items-center">
        <i class="fas fa-print mr-2"></i> Xuất báo cáo (In)
    </button>
</div>

<style>
    @media print {
        .no-print, form, .flex-col, .mt-8 { display: none !important; }
        .bg-white { border: none !important; shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
