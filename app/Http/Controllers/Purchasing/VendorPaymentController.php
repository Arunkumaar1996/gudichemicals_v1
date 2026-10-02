<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\SupplierInvoice;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\Purchasing\PurchasingService;
use Illuminate\Http\Request;

class VendorPaymentController extends Controller
{
    public function __construct(
        protected PurchasingService $purchasingService
    ) {}

    public function index()
    {
        $payments = VendorPayment::with(['vendor', 'supplierInvoice', 'creator'])
            ->latest('payment_date')
            ->paginate(15);

        return view('purchases.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $invoices = SupplierInvoice::whereIn('status', ['unpaid', 'partially_paid'])
            ->with('vendor')
            ->get();

        return view('purchases.payments.create', compact('vendors', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'supplier_invoice_id' => ['nullable', 'exists:supplier_invoices,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,cheque,upi'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = $this->purchasingService->recordPayment(
            vendorId: $validated['vendor_id'],
            amount: (float)$validated['amount'],
            paymentMethod: $validated['payment_method'],
            paymentDate: $validated['payment_date'],
            supplierInvoiceId: $validated['supplier_invoice_id'] ?? null,
            referenceNo: $validated['reference_no'] ?? null,
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('purchases.payments.index')
            ->with('success', "Payment {$payment->payment_number} of ₹" . number_format($payment->amount, 2) . " recorded successfully.");
    }
}
