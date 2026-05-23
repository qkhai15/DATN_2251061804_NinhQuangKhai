<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['contract.room', 'contract.tenant']);

        // Search by room number
        if ($request->has('search')) {
            $query->whereHas('contract.room', function($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by month/year
        if ($request->has('month') && $request->month != '') {
            $query->where('month', $request->month);
        }
        if ($request->has('year') && $request->year != '') {
            $query->where('year', $request->year);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $invoices = $query->paginate(10)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $selected_contract_id = $request->get('contract_id');
        $contracts = Contract::where('status', 'active')->get();
        $services = Service::where('name', 'not like', '%Điện%')
            ->where('name', 'not like', '%Nước%')
            ->get();
        return view('admin.invoices.create', compact('contracts', 'selected_contract_id', 'services'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['contract.room', 'contract.tenant', 'details.service']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['contract.room.building', 'contract.tenant', 'details.service']);
        return view('admin.invoices.print', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $contracts = Contract::where('status', 'active')->get();
        return view('admin.invoices.edit', compact('invoice', 'contracts'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:paid,unpaid,partially_paid',
            'payment_date' => 'nullable|date',
        ]);

        $data = $request->all();
        if ($request->status == 'paid' && $invoice->status != 'paid') {
            $data['payment_date'] = now();
        }

        $invoice->update($data);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Cập nhật hóa đơn thành công.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Xóa hóa đơn thành công.');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'status' => 'required|in:paid,unpaid,partially_paid',
            'services' => 'nullable|array',
        ]);

        $contract = Contract::with(['room.building', 'tenant'])->find($request->contract_id);
        $invoiceData = $this->calculateInvoiceData($contract, $request->month, $request->year, $request->get('services', []));

        return view('admin.invoices.preview', [
            'contract' => $contract,
            'month' => $request->month,
            'year' => $request->year,
            'status' => $request->status,
            'details' => $invoiceData['details'],
            'totalAmount' => $invoiceData['total_amount'],
            'manual_services' => $request->get('services', [])
        ]);
    }

    private function calculateInvoiceData($contract, $month, $year, $manualServices = [])
    {
        $details = [];
        $totalAmount = 0;

        // 1. Room Rent
        $details[] = [
            'name' => 'Tiền phòng',
            'quantity' => 1,
            'unit_price' => $contract->room_price,
            'sub_total' => $contract->room_price,
            'service_id' => null
        ];
        $totalAmount += $contract->room_price;

        // 2. Utility (Electricity & Water) - Always calculated from readings
        $readings = MeterReading::where('room_id', $contract->room_id)
            ->whereMonth('read_date', $month)
            ->whereYear('read_date', $year)
            ->get();

        foreach ($readings as $reading) {
            $serviceName = $reading->type == 'electricity' ? 'Điện' : 'Nước';
            $service = Service::where('name', 'like', '%' . $serviceName . '%')->first();
            
            if ($service) {
                $usage = $reading->new_value - $reading->old_value;
                if ($usage > 0) {
                    $subtotal = $usage * $service->unit_price;
                    $details[] = [
                        'name' => $service->name,
                        'quantity' => $usage,
                        'unit_price' => $service->unit_price,
                        'sub_total' => $subtotal,
                        'service_id' => $service->id
                    ];
                    $totalAmount += $subtotal;
                }
            }
        }

        // 3. Other Services (Internet, Garbage, Laundry, etc.)
        // If provided in manualServices, use that quantity. Otherwise, check all fixed services.
        $otherServices = Service::where('name', 'not like', '%Điện%')
            ->where('name', 'not like', '%Nước%')
            ->get();
        
        foreach ($otherServices as $service) {
            $quantity = 0;
            if (isset($manualServices[$service->id])) {
                $quantity = (float)$manualServices[$service->id];
            } else {
                // If not in manual list, we might want to default to 1 for certain services like Internet?
                // For now, let's assume if it's not in the manual list and not a utility, it might be a fixed monthly fee
                // But the user asked how to handle qty x price, so let's stick to manual input for these.
                // To keep backward compatibility or standard behavior, we can default to 1 for certain services 
                // but let's see if the user wants to specify them.
                // Actually, let's only include them if quantity > 0 or if they are "standard" services.
                // For this project, let's assume any service NOT utilities will be manually entered or default to 1 if not utilities.
                // Wait, if I default to 0, they won't show up. If I default to 1, they always show up.
                // Let's use a simple rule: if it's "Internet" or "Rác", default to 1. Others default to 0 unless specified.
                if (in_array($service->name, ['Internet', 'Rác'])) {
                    $quantity = 1;
                }
            }

            if ($quantity > 0) {
                $subtotal = $quantity * $service->unit_price;
                $details[] = [
                    'name' => $service->name,
                    'quantity' => $quantity,
                    'unit_price' => $service->unit_price,
                    'sub_total' => $subtotal,
                    'service_id' => $service->id
                ];
                $totalAmount += $subtotal;
            }
        }

        return [
            'details' => $details,
            'total_amount' => $totalAmount
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'status' => 'required|in:paid,unpaid,partially_paid',
            'services' => 'nullable|array',
        ]);

        $contract = Contract::find($request->contract_id);
        $invoiceData = $this->calculateInvoiceData($contract, $request->month, $request->year, $request->get('services', []));
        
        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'contract_id' => $contract->id,
                'month' => $request->month,
                'year' => $request->year,
                'total_amount' => $invoiceData['total_amount'],
                'status' => $request->status,
                'payment_date' => $request->status == 'paid' ? now() : null,
            ]);

            foreach ($invoiceData['details'] as $detail) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $detail['service_id'],
                    'name' => $detail['name'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'sub_total' => $detail['sub_total'],
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo hóa đơn: ' . $e->getMessage());
        }
    }
}
