<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Map of modules and their permissions for easy granular assignment.
     */
    public static function getPermissionGroups(): array
    {
        return [
            'POS & Sales Billing' => [
                'icon' => 'fa-solid fa-cash-register',
                'color' => 'success',
                'permissions' => [
                    'sales.view' => 'View Sales Invoices & Registers',
                    'sales.create' => 'Create Invoices & Access POS Desk',
                    'sales.discount_override' => 'Manual Cart Discount Override',
                    'sales.cancel' => 'Cancel / Void Sales Invoices',
                    'sales.returns.create' => 'Process Sales Returns & Credit Notes',
                    'payments.collect' => 'Tender Payments & Cash Settlements',
                ],
            ],
            'Chemical Manufacturing' => [
                'icon' => 'fa-solid fa-flask-vial',
                'color' => 'info',
                'permissions' => [
                    'production.view' => 'View Formulas (BOM) & Batches',
                    'production.create' => 'Create Formulas & Compounding Orders',
                    'production.finalize' => 'Record Lab QC & Finalize Batches',
                ],
            ],
            'Multi-Tier Inventory' => [
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => 'warning',
                'permissions' => [
                    'inventory.view' => 'View Warehouse Stock & Movement Ledger',
                    'inventory.adjust' => 'Stock Adjustments & Opening Stock',
                ],
            ],
            'Purchasing & Vendors' => [
                'icon' => 'fa-solid fa-cart-shopping',
                'color' => 'primary',
                'permissions' => [
                    'purchases.view' => 'View Purchase Orders & Invoices',
                    'purchases.create' => 'Create Chemical Purchase Orders',
                    'purchases.approve' => 'Authorize & Approve Purchase Orders',
                    'goods_receipts.post' => 'Post Inward Goods Receipt Notes (GRN)',
                ],
            ],
            'Master Data Management' => [
                'icon' => 'fa-solid fa-database',
                'color' => 'secondary',
                'permissions' => [
                    'products.view' => 'View Products, Customers & Vendors',
                    'products.create' => 'Create Products, Categories & Partners',
                    'products.update' => 'Edit Master Records & Price Tiers',
                    'products.delete' => 'Delete Master Records',
                ],
            ],
            'Finance & Reports' => [
                'icon' => 'fa-solid fa-chart-pie',
                'color' => 'danger',
                'permissions' => [
                    'reports.sales' => 'View Sales & Collections Reports',
                    'reports.financial' => 'View GSTR-1, Receivables & Payables',
                    'reports.inventory' => 'View Inventory Asset Valuation',
                    'reports.production' => 'View Production Yield Reports',
                    'expenses.manage' => 'Log & Manage Operating Expenses',
                ],
            ],
            'System & Administration' => [
                'icon' => 'fa-solid fa-gear',
                'color' => 'dark',
                'permissions' => [
                    'settings.update' => 'Manage Company Profile & FY Settings',
                    'users.manage' => 'Manage Staff Users, Roles & Permissions',
                ],
            ],
        ];
    }

    private function isSuperAdmin(?User $user): bool
    {
        if (!$user) return false;
        return $user->hasRole('Super Admin') || $user->role === 'Super Admin' || $user->email === 'admin@gudichemicals.com';
    }

    public function index()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $this->isSuperAdmin($currentUser);

        $rolesQuery = Role::with(['permissions', 'users']);
        if (!$isSuperAdmin) {
            // Hide Super Admin role from regular client/sub-admin view
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

        $permissionGroups = self::getPermissionGroups();
        $allPermissions = Permission::all();

        return view('settings.roles.index', compact('roles', 'permissionGroups', 'allPermissions', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if (strtolower($validated['name']) === 'super admin' && !$this->isSuperAdmin(auth()->user())) {
            return back()->with('error', 'You cannot create another Super Admin role.');
        }

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', "Role '{$role->name}' created successfully with configured permissions.");
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin' && !$this->isSuperAdmin(auth()->user())) {
            return back()->with('error', 'Super Admin role is protected and cannot be modified.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Protect Super Admin role name from being altered
        if ($role->name === 'Super Admin') {
            $role->syncPermissions(Permission::all());
            return back()->with('success', "Super Admin role maintains all system permissions.");
        }

        $role->name = $validated['name'];
        $role->save();

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        return back()->with('success', "Role '{$role->name}' permissions updated successfully.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            return back()->with('error', "System role '{$role->name}' is protected and cannot be deleted.");
        }

        if ($role->users()->count() > 0) {
            $count = $role->users()->count();
            return back()->with('error', "Cannot delete role '{$role->name}' because {$count} staff member(s) are currently assigned to it. Reassign them first.");
        }

        $roleName = $role->name;
        $role->delete();

        return back()->with('success', "Role '{$roleName}' has been removed successfully.");
    }
}
