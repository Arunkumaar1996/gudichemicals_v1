<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with(['rules.buyProduct', 'rules.getProduct'])
            ->orderBy('priority')
            ->paginate(15);

        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->whereIn('item_type', ['finished_goods', 'trading'])
            ->orderBy('name')
            ->get();

        return view('promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:promotions,code'],
            'type' => ['required', 'in:bogo,b2g1,bxgy,percent_discount,flat_discount,combo,slab_discount'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['required', 'integer', 'min:1'],
            'buy_product_id' => ['nullable', 'exists:products,id'],
            'buy_quantity' => ['nullable', 'numeric', 'min:1'],
            'get_product_id' => ['nullable', 'exists:products,id'],
            'get_quantity' => ['nullable', 'numeric', 'min:1'],
            'discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_flat' => ['nullable', 'numeric', 'min:0'],
            'customer_type' => ['required', 'in:all,retail,wholesale'],
            'description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $promo = Promotion::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'type' => $validated['type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'min_order_value' => $validated['min_order_value'] ?? 0,
                'priority' => $validated['priority'],
                'is_active' => true,
                'description' => $validated['description'] ?? null,
            ]);

            PromotionRule::create([
                'promotion_id' => $promo->id,
                'buy_product_id' => $validated['buy_product_id'] ?? null,
                'buy_quantity' => $validated['buy_quantity'] ?? 1,
                'get_product_id' => $validated['get_product_id'] ?? null,
                'get_quantity' => $validated['get_quantity'] ?? 1,
                'discount_pct' => $validated['discount_pct'] ?? 0,
                'discount_flat' => $validated['discount_flat'] ?? 0,
                'customer_type' => $validated['customer_type'],
            ]);
        });

        return redirect()->route('promotions.index')->with('success', 'Promotion configured and activated for billing.');
    }

    public function toggle(Promotion $promotion)
    {
        $promotion->is_active = !$promotion->is_active;
        $promotion->save();

        return back()->with('success', "Promotion {$promotion->name} status changed.");
    }
}
