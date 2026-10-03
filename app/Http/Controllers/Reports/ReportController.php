<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesPayment;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Sales Register with Excel/CSV & DomPDF options
     */
    public function sales(Request $request)
    {
        abort_if(!auth()->user()->can('reports.sales'), 403, 'Access Denied: You do not have permission to view Sales Registers and Revenue Reports.');

        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $invoices = SalesInvoice::with('customer')
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->where('status', 'posted')
            ->latest('invoice_date')
            ->get();

        if ($request->get('export') === 'csv') {
            return $this->exportSalesCsv($invoices, $fromDate, $toDate);
        }

        $summary = [
            'total_invoices' => $invoices->count(),
            'gross_sales' => $invoices->sum('grand_total'),
            'taxable_sales' => $invoices->sum('taxable_amount'),
            'cgst_total' => $invoices->sum('cgst_amount'),
            'sgst_total' => $invoices->sum('sgst_amount'),
            'igst_total' => $invoices->sum('igst_amount'),
            'total_gst' => $invoices->sum('cgst_amount') + $invoices->sum('sgst_amount') + $invoices->sum('igst_amount'),
            'discount_total' => $invoices->sum('discount_total'),
        ];

        // Collections by method
        $collections = SalesPayment::whereBetween('payment_date', [$fromDate, $toDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.sales', compact('invoices', 'summary', 'collections', 'fromDate', 'toDate', 'company'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("sales_register_{$fromDate}_to_{$toDate}.pdf");
        }

        return view('reports.sales', compact('invoices', 'summary', 'collections', 'fromDate', 'toDate'));
    }

    /**
     * GSTR-1 Tax Summary (B2B, B2C, and HSN Table)
     */
    public function gst(Request $request)
    {
        abort_if(!auth()->user()->can('reports.financial') && !auth()->user()->can('reports.gst'), 403, 'Access Denied: You do not have permission to view GSTR-1 Tax Reports.');

        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $invoices = SalesInvoice::with(['customer', 'items'])
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->where('status', 'posted')
            ->latest('invoice_date')
            ->get();

        $b2bInvoices = $invoices->filter(fn($inv) => !empty($inv->customer?->gstin));
        $b2cInvoices = $invoices->filter(fn($inv) => empty($inv->customer?->gstin));

        // HSN Summary aggregation
        $hsnSummary = SalesInvoiceItem::join('sales_invoices', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->whereBetween('sales_invoices.invoice_date', [$fromDate, $toDate])
            ->where('sales_invoices.status', 'posted')
            ->select(
                'sales_invoice_items.hsn_code',
                'sales_invoice_items.product_name',
                DB::raw('SUM(sales_invoice_items.quantity) as total_qty'),
                DB::raw('SUM(sales_invoice_items.taxable_amount) as total_taxable'),
                DB::raw('SUM(sales_invoice_items.cgst_amount) as total_cgst'),
                DB::raw('SUM(sales_invoice_items.sgst_amount) as total_sgst'),
                DB::raw('SUM(sales_invoice_items.igst_amount) as total_igst'),
                DB::raw('SUM(sales_invoice_items.line_total) as total_val')
            )
            ->groupBy('sales_invoice_items.hsn_code', 'sales_invoice_items.product_name')
            ->get();

        if ($request->get('export') === 'csv') {
            return $this->exportGstCsv($invoices, $hsnSummary, $fromDate, $toDate);
        }

        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.gst', compact('invoices', 'b2bInvoices', 'b2cInvoices', 'hsnSummary', 'fromDate', 'toDate', 'company'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("gstr1_summary_{$fromDate}_to_{$toDate}.pdf");
        }

        return view('reports.gst', compact('invoices', 'b2bInvoices', 'b2cInvoices', 'hsnSummary', 'fromDate', 'toDate'));
    }

    /**
     * Customer Receivables & Outstanding Aging Report
     */
    public function receivables(Request $request)
    {
        abort_if(!auth()->user()->can('reports.financial') && !auth()->user()->can('reports.sales'), 403, 'Access Denied: You do not have permission to view Customer Receivables & Aging Reports.');

        $customers = Customer::withSum('salesInvoices as total_billed', 'grand_total')
            ->get()
            ->map(function ($c) {
                $totalPaid = (float)SalesPayment::whereIn('sales_invoice_id', function ($q) use ($c) {
                    $q->select('id')->from('sales_invoices')->where('customer_id', $c->id)->where('status', 'posted');
                })->sum('amount');

                $billed = (float)$c->total_billed;
                $balance = $billed - $totalPaid;

                $c->total_paid = $totalPaid;
                $c->outstanding = max(0, $balance);
                return $c;
            })
            ->sortByDesc('outstanding');

        if ($request->get('export') === 'csv') {
            return $this->exportReceivablesCsv($customers);
        }

        $totalReceivable = $customers->sum('outstanding');
        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.receivables', compact('customers', 'totalReceivable', 'company'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('customer_receivables_' . date('Y-m-d') . '.pdf');
        }

        return view('reports.receivables', compact('customers', 'totalReceivable'));
    }

    /**
     * Vendor Payables Report
     */
    public function payables(Request $request)
    {
        abort_if(!auth()->user()->can('reports.financial') && !auth()->user()->can('purchases.view'), 403, 'Access Denied: You do not have permission to view Vendor Payables Reports.');

        $vendors = Vendor::all()->map(function ($v) {
            $totalPurchased = (float)DB::table('supplier_invoices')
                ->where('vendor_id', $v->id)
                ->sum('total_amount');

            $totalPaid = (float)DB::table('vendor_payments')
                ->where('vendor_id', $v->id)
                ->sum('amount');

            $v->total_purchased = $totalPurchased;
            $v->total_paid = $totalPaid;
            $v->outstanding = max(0, $totalPurchased - $totalPaid);
            return $v;
        })->sortByDesc('outstanding');

        if ($request->get('export') === 'csv') {
            return $this->exportPayablesCsv($vendors);
        }

        $totalPayable = $vendors->sum('outstanding');
        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.payables', compact('vendors', 'totalPayable', 'company'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('vendor_payables_' . date('Y-m-d') . '.pdf');
        }

        return view('reports.payables', compact('vendors', 'totalPayable'));
    }

    /**
     * Daily Collection Register
     */
    public function collections(Request $request)
    {
        abort_if(!auth()->user()->can('reports.sales') && !auth()->user()->can('reports.financial') && !auth()->user()->can('payments.collect'), 403, 'Access Denied: You do not have permission to view Daily Collections Registers.');

        $fromDate = $request->get('from_date', now()->subDays(7)->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $payments = SalesPayment::with(['salesInvoice.customer'])
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->latest('payment_date')
            ->get();

        $byMode = $payments->groupBy('payment_method')->map->sum('amount');

        if ($request->get('export') === 'csv') {
            return $this->exportCollectionsCsv($payments, $fromDate, $toDate);
        }

        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.collections', compact('payments', 'byMode', 'fromDate', 'toDate', 'company'))
                ->setPaper('a4', 'portrait');
            return $pdf->download("daily_collections_{$fromDate}_to_{$toDate}.pdf");
        }

        return view('reports.collections', compact('payments', 'byMode', 'fromDate', 'toDate'));
    }

    /**
     * Inventory Valuation Report
     */
    public function inventory(Request $request)
    {
        abort_if(!auth()->user()->can('reports.inventory'), 403, 'Access Denied: You do not have permission to view Inventory Valuation Reports.');

        $products = Product::where('is_active', true)
            ->with(['unit', 'category', 'stockBalances.warehouse', 'stockBalances.batch'])
            ->get();

        $totalValuation = 0.0;
        foreach ($products as $p) {
            $totalValuation += $p->total_stock * (float)$p->purchase_cost;
        }

        if ($request->get('export') === 'csv') {
            return $this->exportInventoryCsv($products);
        }

        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.inventory', compact('products', 'totalValuation', 'company'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('inventory_valuation_' . date('Y-m-d') . '.pdf');
        }

        return view('reports.inventory', compact('products', 'totalValuation'));
    }

    /**
     * Production Yield & Costing Report
     */
    public function production(Request $request)
    {
        abort_if(!auth()->user()->can('reports.production'), 403, 'Access Denied: You do not have permission to view Production Yield & Costing Reports.');

        $batches = ProductionOrder::with(['outputProduct.unit', 'formula', 'operator'])
            ->latest('order_date')
            ->paginate(20);

        if ($request->get('export') === 'csv') {
            return $this->exportProductionCsv(ProductionOrder::with(['outputProduct.unit', 'formula', 'operator'])->latest('order_date')->get());
        }

        $company = CompanySetting::current();

        if ($request->get('export') === 'pdf') {
            $allBatches = ProductionOrder::with(['outputProduct.unit', 'formula', 'operator'])
                ->latest('order_date')
                ->get();

            $pdf = Pdf::loadView('reports.pdf.production', ['batches' => $allBatches, 'company' => $company])
                ->setPaper('a4', 'landscape');
            return $pdf->download('production_yield_' . date('Y-m-d') . '.pdf');
        }

        return view('reports.production', compact('batches'));
    }

    /* -------------------------------------------------------------
     * STREAMED CSV EXPORTERS (RFC 4180 with UTF-8 BOM for MS Excel)
     * ------------------------------------------------------------- */

    protected function exportSalesCsv($invoices, $fromDate, $toDate): StreamedResponse
    {
        $filename = "sales_register_{$fromDate}_to_{$toDate}.csv";
        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, ['Invoice #', 'Date', 'Customer Name', 'GSTIN', 'Taxable Amount (Rs)', 'CGST (Rs)', 'SGST (Rs)', 'IGST (Rs)', 'Discount (Rs)', 'Grand Total (Rs)', 'Payment Status']);
            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->customer?->name ?: 'Walk-in Customer',
                    $inv->customer?->gstin ?: 'Unregistered',
                    number_format($inv->taxable_amount, 2, '.', ''),
                    number_format($inv->cgst_amount, 2, '.', ''),
                    number_format($inv->sgst_amount, 2, '.', ''),
                    number_format($inv->igst_amount, 2, '.', ''),
                    number_format($inv->discount_total, 2, '.', ''),
                    number_format($inv->grand_total, 2, '.', ''),
                    strtoupper($inv->payment_status),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportGstCsv($invoices, $hsnSummary, $fromDate, $toDate): StreamedResponse
    {
        $filename = "gstr1_summary_{$fromDate}_to_{$toDate}.csv";
        return response()->streamDownload(function () use ($invoices, $hsnSummary) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['--- GSTR-1 INVOICE DETAILS ---']);
            fputcsv($handle, ['Invoice #', 'Date', 'Recipient Name', 'Recipient GSTIN', 'Place of Supply', 'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Invoice Value']);
            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->customer?->name ?: 'Consumer',
                    $inv->customer?->gstin ?: 'B2C-Others',
                    $inv->place_of_supply,
                    number_format($inv->taxable_amount, 2, '.', ''),
                    number_format($inv->cgst_amount, 2, '.', ''),
                    number_format($inv->sgst_amount, 2, '.', ''),
                    number_format($inv->igst_amount, 2, '.', ''),
                    number_format($inv->grand_total, 2, '.', ''),
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['--- TABLE 12: HSN SUMMARY ---']);
            fputcsv($handle, ['HSN Code', 'Description', 'Total Qty', 'Total Taxable Value', 'Central Tax', 'State Tax', 'Integrated Tax', 'Total Value']);
            foreach ($hsnSummary as $hsn) {
                fputcsv($handle, [
                    $hsn->hsn_code,
                    $hsn->product_name,
                    $hsn->total_qty,
                    number_format($hsn->total_taxable, 2, '.', ''),
                    number_format($hsn->total_cgst, 2, '.', ''),
                    number_format($hsn->total_sgst, 2, '.', ''),
                    number_format($hsn->total_igst, 2, '.', ''),
                    number_format($hsn->total_val, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportReceivablesCsv($customers): StreamedResponse
    {
        $filename = "customer_receivables_" . date('Y-m-d') . ".csv";
        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Customer Name', 'Phone', 'GSTIN', 'Customer Type', 'Credit Limit (Rs)', 'Total Billed (Rs)', 'Total Paid (Rs)', 'Outstanding Due (Rs)']);
            foreach ($customers as $c) {
                fputcsv($handle, [
                    $c->name,
                    $c->phone ?: '-',
                    $c->gstin ?: 'Unregistered',
                    strtoupper($c->customer_type),
                    number_format($c->credit_limit, 2, '.', ''),
                    number_format($c->total_billed, 2, '.', ''),
                    number_format($c->total_paid, 2, '.', ''),
                    number_format($c->outstanding, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportPayablesCsv($vendors): StreamedResponse
    {
        $filename = "vendor_payables_" . date('Y-m-d') . ".csv";
        return response()->streamDownload(function () use ($vendors) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Vendor Name', 'GSTIN', 'Phone', 'Total Invoiced (Rs)', 'Total Paid (Rs)', 'Outstanding Payable (Rs)']);
            foreach ($vendors as $v) {
                fputcsv($handle, [
                    $v->name,
                    $v->gstin ?: '-',
                    $v->phone ?: '-',
                    number_format($v->total_purchased, 2, '.', ''),
                    number_format($v->total_paid, 2, '.', ''),
                    number_format($v->outstanding, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportCollectionsCsv($payments, $fromDate, $toDate): StreamedResponse
    {
        $filename = "daily_collections_{$fromDate}_to_{$toDate}.csv";
        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Payment #', 'Date', 'Invoice #', 'Customer', 'Payment Mode', 'Reference / UTR', 'Amount (Rs)']);
            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->payment_number,
                    $p->payment_date->format('Y-m-d'),
                    $p->salesInvoice?->invoice_number ?: '-',
                    $p->salesInvoice?->customer?->name ?: 'Walk-in Customer',
                    strtoupper($p->payment_method),
                    $p->reference_number ?: '-',
                    number_format($p->amount, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportInventoryCsv($products): StreamedResponse
    {
        $filename = "inventory_valuation_" . date('Y-m-d') . ".csv";
        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['SKU', 'Chemical Name', 'Item Type', 'Category', 'Unit', 'Stock On Hand', 'Reorder Level', 'Purchase Cost (Rs)', 'Valuation Amount (Rs)', 'Stock Status']);
            foreach ($products as $p) {
                $val = $p->total_stock * (float)$p->purchase_cost;
                $status = $p->total_stock <= $p->reorder_level ? 'LOW STOCK' : 'OPTIMAL';
                fputcsv($handle, [
                    $p->sku,
                    $p->name,
                    strtoupper($p->item_type),
                    $p->category?->name ?: '-',
                    $p->unit?->code ?: '-',
                    number_format($p->total_stock, 2, '.', ''),
                    number_format($p->reorder_level, 2, '.', ''),
                    number_format($p->purchase_cost, 2, '.', ''),
                    number_format($val, 2, '.', ''),
                    $status,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportProductionCsv($batches): StreamedResponse
    {
        $filename = "production_yield_" . date('Y-m-d') . ".csv";
        return response()->streamDownload(function () use ($batches) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Batch Order #', 'Date', 'Formula', 'Output Chemical', 'Planned Qty', 'Actual Output', 'Process Loss %', 'Unit Cost (Rs)', 'Total Batch Cost (Rs)', 'Status', 'QC Status']);
            foreach ($batches as $b) {
                fputcsv($handle, [
                    $b->order_number,
                    $b->order_date->format('Y-m-d'),
                    $b->formula?->name ?: '-',
                    $b->outputProduct?->name ?: '-',
                    number_format($b->planned_quantity, 2, '.', ''),
                    number_format($b->actual_output_quantity, 2, '.', ''),
                    number_format($b->loss_percentage, 2, '.', ''),
                    number_format($b->actual_unit_cost, 2, '.', ''),
                    number_format($b->total_cost, 2, '.', ''),
                    strtoupper($b->status),
                    strtoupper($b->qc_status),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
