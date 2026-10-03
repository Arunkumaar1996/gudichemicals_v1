<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function isSuperAdmin(?User $user): bool
    {
        if (!$user) return false;
        return $user->hasRole('Super Admin') || $user->role === 'Super Admin' || $user->email === 'admin@gudichemicals.com';
    }

    public function index()
    {
        abort_if(!auth()->user()->can('users.manage'), 403, 'Access Denied: You do not have the required "users.manage" permission to view or manage staff accounts.');

        $currentUser = auth()->user();
        $isSuperAdmin = $this->isSuperAdmin($currentUser);

        $usersQuery = User::with('roles');
        if (!$isSuperAdmin) {
            // Completely hide the Developer / Super Admin account from regular clients/users
            $usersQuery->where('role', '!=', 'Super Admin')
                       ->where('email', '!=', 'admin@gudichemicals.com');
        }
        $users = $usersQuery->get();

        $rolesQuery = Role::query();
        if (!$isSuperAdmin) {
            // Hide Super Admin role from dropdown if not superadmin
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

        return view('settings.users.index', compact('users', 'roles', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('users.manage'), 403, 'Access Denied: You do not have permission to create staff user accounts.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validated['role'] === 'Super Admin' && !$this->isSuperAdmin(auth()->user())) {
            return back()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->assignRole($role);
        }

        return back()->with('success', "User account for {$user->name} created successfully.");
    }

    public function update(Request $request, User $user)
    {
        abort_if(!auth()->user()->can('users.manage'), 403, 'Access Denied: You do not have permission to update staff user accounts.');

        if ($this->isSuperAdmin($user) && !$this->isSuperAdmin(auth()->user())) {
            return back()->with('error', 'Super Admin developer account cannot be modified by other users.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($validated['role'] === 'Super Admin' && !$this->isSuperAdmin(auth()->user())) {
            return back()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $role = Role::where('name', $validated['role'])->first();
        if ($role) {
            $user->syncRoles([$role]);
        }

        return back()->with('success', "User {$user->name} updated successfully.");
    }

    public function toggle(User $user)
    {
        abort_if(!auth()->user()->can('users.manage'), 403, 'Access Denied: You do not have permission to change staff account status.');

        if ($this->isSuperAdmin($user)) {
            return back()->with('error', 'Super Admin developer account cannot be deactivated.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own current administrator account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', "User {$user->name} status updated.");
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->can('users.manage'), 403, 'Access Denied: You do not have permission to remove staff accounts.');

        if ($this->isSuperAdmin($user)) {
            return back()->with('error', 'Super Admin developer account is protected and cannot be deleted.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User account {$name} has been removed.");
    }
}
