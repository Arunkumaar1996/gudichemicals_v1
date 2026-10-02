<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesInvoice::with(['customer', 'warehouse', 'creator'])
            ->latest('invoice_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhereHas('customer', function ($cq) use ($s) {
                        $cq->where('name', 'like', "%{$s}%")
                            ->orWhere('company_name', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->paginate(20)->withQueryString();

        return view('sales.invoices.index', compact('invoices'));
    }

    public function show(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'warehouse', 'creator', 'items.product', 'items.unit', 'payments.receiver']);
        $company = CompanySetting::current();

        return view('sales.invoices.show', compact('invoice', 'company'));
    }

    public function print(SalesInvoice $invoice, Request $request)
    {
        $invoice->load(['customer', 'warehouse', 'creator', 'items.product', 'items.unit', 'payments']);
        $company = CompanySetting::current();
        $format = $request->get('format', 'a4'); // 'a4' or 'thermal'

        if ($format === 'thermal') {
            return view('sales.invoices.thermal_print', compact('invoice', 'company'));
        }

        return view('sales.invoices.print', compact('invoice', 'company'));
    }

    public function downloadPdf(SalesInvoice $invoice)
    {
        $invoice->load(['customer', 'warehouse', 'creator', 'items.product', 'items.unit', 'payments']);
        $company = CompanySetting::current();

        $pdf = Pdf::loadView('sales.invoices.pdf', compact('invoice', 'company'))
            ->setPaper('a4', 'portrait');

        $filename = 'Invoice_' . str_replace('/', '_', $invoice->invoice_number) . '.pdf';
        return $pdf->download($filename);
    }
}
