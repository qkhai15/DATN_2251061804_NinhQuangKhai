<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Auth::user()->issues()->with('room')->latest()->paginate(10);
        return view('tenant.issues.index', compact('issues'));
    }

    public function create()
    {
        $rooms = Auth::user()->contracts()->where('status', 'active')->with('room')->get()->pluck('room');
        return view('tenant.issues.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        Issue::create($data);
        return redirect()->route('tenant.issues.index')->with('success', 'Yêu cầu sửa chữa đã được gửi thành công.');
    }
}
