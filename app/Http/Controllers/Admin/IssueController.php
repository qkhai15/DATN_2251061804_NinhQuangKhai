<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Issue::with(['room.building', 'user'])->latest()->paginate(15);
        return view('admin.issues.index', compact('issues'));
    }

    public function show(Issue $issue)
    {
        $issue->load(['room.building', 'user']);
        return view('admin.issues.show', compact('issue'));
    }

    public function edit(Issue $issue)
    {
        return view('admin.issues.edit', compact('issue'));
    }

    public function update(Request $request, Issue $issue)
    {
        $request->validate([
            'status' => 'required|in:pending,fixing,resolved',
            'responsible_party' => 'nullable|string',
            'repair_cost' => 'nullable|numeric',
            'payer' => 'nullable|in:owner,tenant,shared',
            'admin_note' => 'nullable|string',
        ]);

        $issue->update($request->all());
        return redirect()->route('issues.index')->with('success', 'Cập nhật tình trạng sự cố thành công.');
    }

    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $issues = Issue::with(['room.building', 'user'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $stats = [
            'total' => $issues->count(),
            'resolved' => $issues->where('status', 'resolved')->count(),
            'total_cost' => $issues->sum('repair_cost'),
            'owner_pay' => $issues->where('payer', 'owner')->sum('repair_cost'),
            'tenant_pay' => $issues->where('payer', 'tenant')->sum('repair_cost'),
        ];

        return view('admin.issues.report', compact('issues', 'month', 'year', 'stats'));
    }

    public function destroy(Issue $issue)
    {
        $issue->delete();
        return redirect()->route('issues.index')->with('success', 'Xóa sự cố thành công.');
    }
}
