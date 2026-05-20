<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = User::where('role', 'tenant')->latest()->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'tenant',
        ]);

        return redirect()->route('tenants.index')->with('success', 'Thêm người thuê mới thành công.');
    }

    public function show(User $tenant)
    {
        if ($tenant->role !== 'tenant') abort(404);
        $tenant->load('contracts.room');
        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(User $tenant)
    {
        if ($tenant->role !== 'tenant') abort(404);
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, User $tenant)
    {
        if ($tenant->role !== 'tenant') abort(404);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        $tenant->name = $request->name;
        $tenant->email = $request->email;
        $tenant->phone = $request->phone;
        
        if ($request->password) {
            $tenant->password = Hash::make($request->password);
        }

        $tenant->save();

        return redirect()->route('tenants.index')->with('success', 'Cập nhật thông tin người thuê thành công.');
    }

    public function destroy(User $tenant)
    {
        if ($tenant->role !== 'tenant') abort(404);
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Xóa tài khoản người thuê thành công.');
    }
}
