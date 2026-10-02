<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Master Products & Stock
            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            // Purchasing
            'purchases.view',
            'purchases.create',
            'purchases.approve',
            'goods_receipts.post',

            // Production & Formulas
            'production.view',
            'production.create',
            'production.finalize',

            // Inventory
            'inventory.view',
            'inventory.adjust',

            // Sales & POS
            'sales.view',
            'sales.create',
            'sales.discount_override',
            'sales.cancel',
            'sales.returns.create',
            'payments.collect',

            // Accounts & Reports
            'reports.financial',
            'reports.inventory',
            'reports.production',
            'reports.sales',
            'expenses.manage',

            // Administration & Settings
            'settings.update',
            'users.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        $productionManager = Role::firstOrCreate(['name' => 'Production Manager']);
        $productionManager->syncPermissions([
            'products.view',
            'production.view',
            'production.create',
            'production.finalize',
            'inventory.view',
            'reports.production',
            'reports.inventory',
        ]);

        $productionOperator = Role::firstOrCreate(['name' => 'Production Operator']);
        $productionOperator->syncPermissions([
            'production.view',
            'production.create',
            'inventory.view',
        ]);

        $purchaseManager = Role::firstOrCreate(['name' => 'Purchase Manager']);
        $purchaseManager->syncPermissions([
            'products.view',
            'purchases.view',
            'purchases.create',
            'purchases.approve',
            'goods_receipts.post',
            'inventory.view',
            'reports.inventory',
        ]);

        $storekeeper = Role::firstOrCreate(['name' => 'Storekeeper']);
        $storekeeper->syncPermissions([
            'products.view',
            'goods_receipts.post',
            'inventory.view',
            'inventory.adjust',
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'Sales Manager']);
        $salesManager->syncPermissions([
            'products.view',
            'sales.view',
            'sales.create',
            'sales.discount_override',
            'sales.cancel',
            'sales.returns.create',
            'payments.collect',
            'reports.sales',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $cashier->syncPermissions([
            'products.view',
            'sales.view',
            'sales.create',
            'payments.collect',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions([
            'products.view',
            'purchases.view',
            'sales.view',
            'payments.collect',
            'expenses.manage',
            'reports.financial',
            'reports.sales',
            'reports.inventory',
        ]);

        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->syncPermissions([
            'products.view',
            'purchases.view',
            'production.view',
            'inventory.view',
            'sales.view',
            'reports.financial',
            'reports.inventory',
            'reports.production',
            'reports.sales',
        ]);

        // Default Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gudichemicals.com'],
            [
                'name' => 'Gudi Administrator',
                'phone' => '9876543210',
                'role' => 'Super Admin',
                'is_active' => true,
                'password' => Hash::make('Admin@12345'),
            ]
        );
        $adminUser->assignRole($superAdmin);

        // Demo Cashier User
        $cashierUser = User::firstOrCreate(
            ['email' => 'cashier@gudichemicals.com'],
            [
                'name' => 'Counter Cashier',
                'phone' => '9876543211',
                'role' => 'Cashier',
                'is_active' => true,
                'password' => Hash::make('Cashier@12345'),
            ]
        );
        $cashierUser->assignRole($cashier);
    }
}
