<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\SalesPayment;
use App\Models\StockMovement;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // 1. Sales metrics
        $todaySalesQuery = SalesInvoice::whereDate('invoice_date', $today)->where('status', 'posted');
        $todayInvoicesCount = (clone $todaySalesQuery)->count();
        $todayGrossSales = (clone $todaySalesQuery)->sum('grand_total');
        $todayTaxableSales = (clone $todaySalesQuery)->sum('taxable_amount');

        // 2. Collections by payment method today
        $todayPayments = SalesPayment::whereDate('payment_date', $today)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // 3. Outstanding receivables & payables
        $totalCustomerReceivables = Customer::sum('current_balance');
        $totalVendorPayables = Vendor::sum('current_balance');

        // 4. Production & PO alerts
        $pendingBatchesCount = ProductionOrder::whereIn('status', ['draft', 'in_progress', 'qc_pending'])->count();
        $pendingPoCount = PurchaseOrder::whereIn('status', ['draft', 'approved', 'partial'])->count();

        // 5. Stock alerts: Products where total stock <= reorder_level
        $lowStockProducts = Product::where('is_active', true)
            ->whereHas('stockBalances')
            ->with(['unit', 'category'])
            ->get()
            ->filter(fn($p) => $p->isLowStock())
            ->take(8);

        // 6. Recent activity lists
        $recentInvoices = SalesInvoice::with('customer')
            ->latest()
            ->take(6)
            ->get();

        $recentBatches = ProductionOrder::with(['outputProduct', 'formula'])
            ->latest()
            ->take(5)
            ->get();

        $recentMovements = StockMovement::with(['product', 'warehouse', 'creator'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'todayInvoicesCount',
            'todayGrossSales',
            'todayTaxableSales',
            'todayPayments',
            'totalCustomerReceivables',
            'totalVendorPayables',
            'pendingBatchesCount',
            'pendingPoCount',
            'lowStockProducts',
            'recentInvoices',
            'recentBatches',
            'recentMovements'
        ));
    }
}
