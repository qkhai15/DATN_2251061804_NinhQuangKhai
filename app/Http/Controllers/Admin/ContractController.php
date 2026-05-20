<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with(['room.building', 'tenant']);

        // Search by tenant name
        if ($request->filled('search')) {
            $query->whereHas('tenant', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'expiring') {
                // Hợp đồng active sắp hết hạn trong 30 ngày
                $query->where('status', 'active')
                      ->where('end_date', '>=', now()->startOfDay())
                      ->where('end_date', '<=', now()->addDays(30));
            } elseif ($request->status === 'overdue') {
                // Hợp đồng active đã quá hạn chưa cập nhật
                $query->where('status', 'active')
                      ->where('end_date', '<', now()->startOfDay());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'end_date');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        $contracts = $query->paginate(15)->withQueryString();

        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'empty')->get();
        $tenants = User::where('role', 'tenant')->get();
        return view('admin.contracts.create', compact('rooms', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'deposit' => 'required|numeric',
        ]);

        $contract = Contract::create($request->all());
        
        // Update room status
        $contract->room->update(['status' => 'rented']);

        return redirect()->route('contracts.index')->with('success', 'Tạo hợp đồng thành công.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['room.building', 'tenant', 'invoices']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $rooms = Room::all();
        $tenants = User::where('role', 'tenant')->get();
        return view('admin.contracts.edit', compact('contract', 'rooms', 'tenants'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'tenant_id'  => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'room_price' => 'required|numeric|min:0',
            'deposit'    => 'required|numeric|min:0',
            'status'     => 'required|in:active,expired,canceled',
        ]);

        $data = $request->only(['room_id', 'tenant_id', 'start_date', 'end_date', 'room_price', 'deposit', 'status']);
        $contract->update($data);

        // Nếu admin đánh dấu expired/canceled thủ công → cập nhật trạng thái phòng
        if (in_array($data['status'], ['expired', 'canceled'])) {
            $contract->room->update(['status' => 'empty']);
        } elseif ($data['status'] === 'active') {
            $contract->room->update(['status' => 'rented']);
        }

        return redirect()->route('contracts.index')->with('success', 'Cập nhật hợp đồng thành công.');
    }

    public function destroy(Contract $contract)
    {
        // Revert room status if active
        if ($contract->status == 'active') {
            $contract->room->update(['status' => 'empty']);
        }
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Xóa hợp đồng thành công.');
    }
}
