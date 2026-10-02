<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('products')->get();
        $conversions = UnitConversion::with(['fromUnit', 'toUnit'])->get();

        return view('masters.units.index', compact('units', 'conversions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:20', 'unique:units,code'],
            'is_fractional' => ['boolean'],
        ]);

        $validated['is_fractional'] = $request->boolean('is_fractional', true);
        Unit::create($validated);

        return back()->with('success', 'Unit created successfully.');
    }

    public function storeConversion(Request $request)
    {
        $validated = $request->validate([
            'from_unit_id' => ['required', 'exists:units,id'],
            'to_unit_id' => ['required', 'exists:units,id', 'different:from_unit_id'],
            'conversion_factor' => ['required', 'numeric', 'gt:0'],
        ]);

        UnitConversion::updateOrCreate(
            ['from_unit_id' => $validated['from_unit_id'], 'to_unit_id' => $validated['to_unit_id']],
            ['conversion_factor' => $validated['conversion_factor']]
        );

        return back()->with('success', 'Unit conversion saved.');
    }
}
