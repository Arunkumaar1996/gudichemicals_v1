<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Display the comprehensive interactive user guide and video walkthrough center.
     */
    public function index()
    {
        $productsCount = Product::count();
        $invoicesCount = SalesInvoice::count();
        $batchesCount = ProductionOrder::count();

        return view('guide.index', compact(
            'productsCount',
            'invoicesCount',
            'batchesCount'
        ));
    }
}
