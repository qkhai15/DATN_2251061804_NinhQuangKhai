<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with([
            'building',
            'contracts' => fn($q) => $q->where('status', 'active')->with('tenant'),
            'meterReadings' => fn($q) => $q->orderBy('read_date', 'desc'),
        ]);

        // Search by room number
        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        // Filter by building
        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort', 'room_number');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        $rooms = $query->paginate(15)->withQueryString();
        $buildings = Building::all();

        return view('admin.rooms.index', compact('rooms', 'buildings'));
    }

    public function create()
    {
        $buildings = Building::all();
        return view('admin.rooms.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|string|max:50',
            'price' => 'required|numeric',
            'status' => 'required|in:empty,rented,maintenance',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function edit(Room $room)
    {
        $buildings = Building::all();
        return view('admin.rooms.edit', compact('room', 'buildings'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|string|max:50',
            'price' => 'required|numeric',
            'status' => 'required|in:empty,rented,maintenance',
        ]);

        $room->update($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['building', 'contracts.tenant', 'meterReadings']);
        return view('admin.rooms.show', compact('room'));
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
