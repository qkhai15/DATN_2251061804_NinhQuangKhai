<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get active contract and room
        $activeContract = $user->contracts()->where('status', 'active')->with('room.building')->first();
        $room = $activeContract ? $activeContract->room : null;

        // Stats
        $unpaid_amount = Invoice::whereHas('contract', function($q) use ($user) {
            $q->where('tenant_id', $user->id);
        })->where('status', 'unpaid')->sum('total_amount');

        $pending_issues = $user->issues()->where('status', 'pending')->count();

        $recent_invoices = Invoice::whereHas('contract', function($q) use ($user) {
            $q->where('tenant_id', $user->id);
        })->latest()->take(5)->get();

        return view('tenant.dashboard', compact('room', 'unpaid_amount', 'pending_issues', 'recent_invoices'));
    }
}
