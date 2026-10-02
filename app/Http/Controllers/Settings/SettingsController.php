<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = CompanySetting::current();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'gstin' => ['required', 'string', 'max:15'],
            'pan' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state_name' => ['required', 'string', 'max:100'],
            'state_code' => ['required', 'string', 'max:5'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'invoice_prefix' => ['required', 'string', 'max:10'],
            'fy_code' => ['required', 'string', 'max:10'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_no' => ['nullable', 'string', 'max:50'],
            'bank_ifsc' => ['nullable', 'string', 'max:20'],
            'bank_branch' => ['nullable', 'string', 'max:100'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'terms_and_conditions' => ['nullable', 'string'],
        ]);

        $setting = CompanySetting::current();
        $setting->update($validated);

        return back()->with('success', 'Company profile and GST configuration saved successfully.');
    }
}
