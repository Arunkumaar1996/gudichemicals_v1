<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\SalesInvoice;
use App\Models\SalesPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $invoices = SalesInvoice::with('customer')
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->where('status', 'posted')
            ->latest('invoice_date')
            ->get();

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

        return view('reports.sales', compact('invoices', 'summary', 'collections', 'fromDate', 'toDate'));
    }

    public function inventory()
    {
        $products = Product::where('is_active', true)
            ->with(['unit', 'category', 'stockBalances.warehouse', 'stockBalances.batch'])
            ->get();

        $totalValuation = 0.0;
        foreach ($products as $p) {
            $totalValuation += $p->total_stock * (float)$p->purchase_cost;
        }

        return view('reports.inventory', compact('products', 'totalValuation'));
    }

    public function production()
    {
        $batches = ProductionOrder::with(['outputProduct.unit', 'formula', 'operator'])
            ->latest('order_date')
            ->paginate(20);

        return view('reports.production', compact('batches'));
    }
}
