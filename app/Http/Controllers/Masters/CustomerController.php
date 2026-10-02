<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('company_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('gstin', 'like', "%{$s}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('masters.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('masters.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:retail,wholesale'],
            'name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:100'],
            'gstin' => ['nullable', 'string', 'max:15'],
            'pan' => ['nullable', 'string', 'max:10'],
            'billing_address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state_code' => ['required', 'string', 'max:5'],
            'state_name' => ['required', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'opening_balance' => ['nullable', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        Customer::create($validated);

        return redirect()->route('masters.customers.index')->with('success', 'Customer registered successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['salesInvoices' => function ($q) {
            $q->latest()->take(20);
        }, 'payments' => function ($q) {
            $q->latest()->take(20);
        }]);

        return view('masters.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('masters.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:retail,wholesale'],
            'name' => ['required', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:100'],
            'gstin' => ['nullable', 'string', 'max:15'],
            'pan' => ['nullable', 'string', 'max:10'],
            'billing_address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state_code' => ['required', 'string', 'max:5'],
            'state_name' => ['required', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $customer->update($validated);

        return redirect()->route('masters.customers.index')->with('success', 'Customer updated successfully.');
    }
}
