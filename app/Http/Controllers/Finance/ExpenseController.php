<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\DocumentSequence;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('expenses.manage'), 403, 'Access Denied: You do not have permission to view operating expenses.');

        $query = Expense::with(['category', 'creator'])
            ->latest('expense_date');

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::all();
        $totalAmount = (clone $query)->sum('amount');

        return view('finance.expenses.index', compact('expenses', 'categories', 'totalAmount'));
    }

    public function create()
    {
        abort_if(!auth()->user()->can('expenses.manage'), 403, 'Access Denied: You do not have permission to log operating expenses.');

        $categories = ExpenseCategory::all();
        return view('finance.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('expenses.manage'), 403, 'Access Denied: You do not have permission to record operating expenses.');
        $validated = $request->validate([
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'in:cash,bank_transfer,upi,card'],
            'paid_to' => ['required', 'string', 'max:150'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $company = CompanySetting::current();
        $expNumber = DocumentSequence::getNextSequence('receipt', $company->fy_code ?? '2026-27', 'EXP');

        $validated['expense_number'] = $expNumber;
        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', "Expense voucher {$expNumber} recorded.");
    }
}
