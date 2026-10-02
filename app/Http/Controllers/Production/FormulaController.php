<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\FormulaItem;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulaController extends Controller
{
    public function index()
    {
        $formulas = Formula::with(['product.unit', 'outputUnit', 'items.ingredient', 'approver'])
            ->orderBy('name')
            ->paginate(15);

        return view('production.formulas.index', compact('formulas'));
    }

    public function create()
    {
        $products = Product::whereIn('item_type', ['semi_finished', 'finished_goods'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $ingredients = Product::whereIn('item_type', ['raw_material', 'semi_finished', 'packaging'])
            ->where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        $units = Unit::all();

        return view('production.formulas.create', compact('products', 'ingredients', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'formula_code' => ['required', 'string', 'max:50', 'unique:formulas,formula_code'],
            'name' => ['required', 'string', 'max:200'],
            'product_id' => ['required', 'exists:products,id'],
            'version' => ['required', 'integer', 'min:1'],
            'standard_batch_qty' => ['required', 'numeric', 'gt:0'],
            'output_unit_id' => ['required', 'exists:units,id'],
            'expected_yield_pct' => ['required', 'numeric', 'min:1', 'max:100'],
            'process_loss_pct' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_id' => ['required', 'exists:units,id'],
            'items.*.wastage_pct' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $formula = Formula::create([
                'formula_code' => $validated['formula_code'],
                'name' => $validated['name'],
                'product_id' => $validated['product_id'],
                'version' => $validated['version'],
                'standard_batch_qty' => $validated['standard_batch_qty'],
                'output_unit_id' => $validated['output_unit_id'],
                'expected_yield_pct' => $validated['expected_yield_pct'],
                'process_loss_pct' => $validated['process_loss_pct'] ?? 0,
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]);

            foreach ($validated['items'] as $item) {
                FormulaItem::create([
                    'formula_id' => $formula->id,
                    'ingredient_product_id' => $item['ingredient_product_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'wastage_pct' => $item['wastage_pct'] ?? 0,
                ]);
            }
        });

        return redirect()->route('production.formulas.index')
            ->with('success', 'Chemical formula / Bill of Materials created successfully.');
    }

    public function show(Formula $formula)
    {
        $formula->load(['product.unit', 'outputUnit', 'items.ingredient.unit', 'items.unit', 'productionOrders']);
        return view('production.formulas.show', compact('formula'));
    }
}
