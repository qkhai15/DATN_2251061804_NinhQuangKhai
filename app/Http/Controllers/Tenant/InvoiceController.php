<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice; // Added this line

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::whereHas('contract', function ($query) {
            $query->where('tenant_id', auth()->id());
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('tenant.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        // Ensure user can only view their own invoices
        if ($invoice->contract->tenant_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['contract.room', 'details.service']);
        return view('tenant.invoices.show', compact('invoice'));
    }
}
