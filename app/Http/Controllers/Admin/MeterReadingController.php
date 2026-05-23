<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use App\Models\Room;
use Illuminate\Http\Request;

class MeterReadingController extends Controller
{
    public function index()
    {
        $readings = MeterReading::with('room')->orderBy('read_date', 'desc')->paginate(15);
        return view('admin.meter_readings.index', compact('readings'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('admin.meter_readings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'type' => 'required|in:electricity,water',
            'new_value' => 'required|numeric',
            'read_date' => 'required|date',
        ]);

        // Find previous reading to get old_value
        $previousReading = MeterReading::where('room_id', $request->room_id)
            ->where('type', $request->type)
            ->orderBy('read_date', 'desc')
            ->first();

        $data = $request->all();
        $data['old_value'] = $previousReading ? $previousReading->new_value : 0;

        MeterReading::create($data);

        return redirect()->route('meter-readings.index')->with('success', 'Đã ghi nhận chỉ số mới.');
    }

    public function show(MeterReading $meter_reading)
    {
        $meter_reading->load('room.building');
        return view('admin.meter_readings.show', compact('meter_reading'));
    }

    public function edit(MeterReading $meter_reading)
    {
        $rooms = Room::all();
        return view('admin.meter_readings.edit', compact('meter_reading', 'rooms'));
    }

    public function update(Request $request, MeterReading $meter_reading)
    {
        $request->validate([
            'new_value' => 'required|numeric',
            'read_date' => 'required|date',
        ]);

        $meter_reading->update($request->all());
        return redirect()->route('meter-readings.index')->with('success', 'Cập nhật chỉ số thành công.');
    }

    public function destroy(MeterReading $meter_reading)
    {
        $meter_reading->delete();
        return redirect()->route('meter-readings.index')->with('success', 'Xóa bản ghi chỉ số thành công.');
    }
}
