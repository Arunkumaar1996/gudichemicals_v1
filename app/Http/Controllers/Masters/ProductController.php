<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'stockBalances']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = ProductCategory::where('is_active', true)->get();

        return view('masters.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $units = Unit::all();

        return view('masters.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'item_type' => ['required', 'in:raw_material,semi_finished,finished_goods,packaging,trading,service'],
            'unit_id' => ['required', 'exists:units,id'],
            'hsn_code' => ['required', 'string', 'max:20'],
            'gst_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'retail_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'has_batch_tracking' => ['boolean'],
            'has_expiry_tracking' => ['boolean'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['has_batch_tracking'] = $request->boolean('has_batch_tracking');
        $validated['has_expiry_tracking'] = $request->boolean('has_expiry_tracking');
        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('masters.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $units = Unit::all();

        return view('masters.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'item_type' => ['required', 'in:raw_material,semi_finished,finished_goods,packaging,trading,service'],
            'unit_id' => ['required', 'exists:units,id'],
            'hsn_code' => ['required', 'string', 'max:20'],
            'gst_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'retail_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'numeric', 'min:0'],
            'has_batch_tracking' => ['boolean'],
            'has_expiry_tracking' => ['boolean'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['has_batch_tracking'] = $request->boolean('has_batch_tracking');
        $validated['has_expiry_tracking'] = $request->boolean('has_expiry_tracking');
        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('masters.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $hasMovements = $product->stockMovements()->exists();
        if ($hasMovements) {
            return back()->with('error', 'Cannot delete this product because posted stock ledger history references it. You can set it to inactive instead.');
        }

        $product->delete();
        return redirect()->route('masters.products.index')->with('success', 'Product deleted successfully.');
    }
}
