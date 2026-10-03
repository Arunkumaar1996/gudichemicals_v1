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

        // 5. Stock alerts: Products where reorder_level > 0 and total stock <= reorder_level
        $allLowStock = Product::where('is_active', true)
            ->where('reorder_level', '>', 0)
            ->with(['unit', 'category', 'stockBalances'])
            ->get()
            ->filter(fn($p) => $p->isLowStock())
            ->values();

        $lowStockTotalCount = $allLowStock->count();
        $lowStockProducts = $allLowStock->take(15);

        // 6. Recent activity lists
        $recentInvoices = SalesInvoice::with('customer')
            ->latest()
            ->take(15)
            ->get();

        $recentBatches = ProductionOrder::with(['outputProduct', 'formula'])
            ->latest()
            ->take(15)
            ->get();

        $recentMovements = StockMovement::with(['product', 'warehouse', 'creator'])
            ->latest()
            ->take(8)
            ->get();

        // 7. Interactive Charts Data: 14-Day Sales Revenue & Invoices Trend
        $trendDays = [];
        for ($i = 13; $i >= 0; $i--) {
            $dt = now()->subDays($i);
            $key = $dt->format('Y-m-d');
            $trendDays[$key] = [
                'label' => $dt->format('d M'),
                'revenue' => 0.0,
                'count' => 0,
            ];
        }

        $dailyStats = SalesInvoice::where('status', 'posted')
            ->whereDate('invoice_date', '>=', now()->subDays(13)->toDateString())
            ->select(
                DB::raw('DATE(invoice_date) as inv_date'),
                DB::raw('SUM(grand_total) as daily_revenue'),
                DB::raw('COUNT(*) as inv_count')
            )
            ->groupBy('inv_date')
            ->get();

        foreach ($dailyStats as $stat) {
            $dateKey = is_string($stat->inv_date) ? substr($stat->inv_date, 0, 10) : $stat->inv_date;
            if (isset($trendDays[$dateKey])) {
                $trendDays[$dateKey]['revenue'] = (float)$stat->daily_revenue;
                $trendDays[$dateKey]['count'] = (int)$stat->inv_count;
            }
        }

        $chartLabels = array_column($trendDays, 'label');
        $chartRevenues = array_column($trendDays, 'revenue');
        $chartCounts = array_column($trendDays, 'count');

        // 8. Interactive Charts Data: Sales by Product Category (Top Categories)
        $categorySales = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
            ->join('products', 'products.id', '=', 'sales_invoice_items.product_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->where('sales_invoices.status', 'posted')
            ->select(
                DB::raw('COALESCE(product_categories.name, "General Chemicals") as cat_name'),
                DB::raw('SUM(sales_invoice_items.line_total) as cat_total')
            )
            ->groupBy('cat_name')
            ->orderByDesc('cat_total')
            ->pluck('cat_total', 'cat_name')
            ->toArray();

        // 9. Payment Methods Split (Past 30 Days)
        $paymentMethodsSplit = SalesPayment::whereDate('payment_date', '>=', now()->subDays(30)->toDateString())
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

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
            'lowStockTotalCount',
            'recentInvoices',
            'recentBatches',
            'recentMovements',
            'chartLabels',
            'chartRevenues',
            'chartCounts',
            'categorySales',
            'paymentMethodsSplit'
        ));
    }
}
