<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingCard;
use App\Models\User;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function index()
    {
        $parkingCards = ParkingCard::with('user')->paginate(10);
        return view('admin.parking.index', compact('parkingCards'));
    }

    public function create()
    {
        $users = User::where('role', 'tenant')->get();
        return view('admin.parking.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_plate' => 'required|string|max:20',
            'card_number' => 'required|string|max:50|unique:parking_cards',
        ]);

        ParkingCard::create($request->all());
        return redirect()->route('parking.index')->with('success', 'Cấp thẻ gửi xe thành công.');
    }

    public function show(ParkingCard $parking)
    {
        $parking->load('user');
        return view('admin.parking.show', compact('parking'));
    }

    public function edit(ParkingCard $parking)
    {
        $users = User::where('role', 'tenant')->get();
        return view('admin.parking.edit', compact('parking', 'users'));
    }

    public function update(Request $request, ParkingCard $parking)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_plate' => 'required|string|max:20',
            'card_number' => 'required|string|max:50|unique:parking_cards,card_number,' . $parking->id,
        ]);

        $parking->update($request->all());
        return redirect()->route('parking.index')->with('success', 'Cập nhật thẻ gửi xe thành công.');
    }

    public function destroy(ParkingCard $parking)
    {
        $parking->delete();
        return redirect()->route('parking.index')->with('success', 'Xóa thẻ gửi xe thành công.');
    }
}
