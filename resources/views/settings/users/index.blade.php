@extends('layouts.app')

@section('title', 'Staff Users & Role Permissions')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Staff Users & System Roles</h4>
        <p class="text-muted small mb-0">Manage ERP access credentials, assign departmental roles, and configure staff login security.</p>
    </div>
    <button class="btn btn-gudi-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fa-solid fa-user-plus me-1"></i> Add New Staff User
    </button>
</div>

<div class="row g-4">
    <!-- User Listing Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-semibold">
                        <i class="fa-solid fa-users me-1"></i> {{ count($users) }} Registered Staff
                    </span>
                </div>
                <div class="small text-muted">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Role-Based Access Control (RBAC) Active
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Staff Member</th>
                                <th>Contact Information</th>
                                <th>System Role</th>
                                <th>Last Active</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px; font-size: 0.95rem; background: linear-gradient(135deg, #005a9c 0%, #003e6b 100%);">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                                <small class="text-muted"><i class="fa-regular fa-envelope me-1"></i>{{ $u->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($u->phone)
                                            <span class="text-dark small"><i class="fa-solid fa-phone text-muted me-1"></i>{{ $u->phone }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $roleName = $u->roles->first()?->name ?: ($u->role ?: 'Operator');
                                            $badgeColor = match($roleName) {
                                                'Super Admin', 'Admin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'Production Manager', 'Production Operator' => 'bg-info-subtle text-info border-info-subtle',
                                                'Purchase Manager', 'Storekeeper' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                                'Sales Manager', 'Cashier' => 'bg-success-subtle text-success border-success-subtle',
                                                default => 'bg-primary-subtle text-primary border-primary-subtle'
                                            };
                                        @endphp
                                        <span class="badge border {{ $badgeColor }} px-2.5 py-1 fw-semibold">
                                            <i class="fa-solid fa-user-shield me-1"></i> {{ $roleName }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if($u->last_login_at)
                                                <i class="fa-regular fa-clock me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($u->last_login_at)->format('d M Y, h:i A') }}
                                            @else
                                                <span class="badge bg-light text-muted border">Never Logged In</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1">
                                            <i class="fa-solid {{ $u->is_active ? 'fa-check' : 'fa-ban' }} me-1"></i>
                                            {{ $u->is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal{{ $u->id }}" 
                                                    title="Edit User">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            @if($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.toggle', $u->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                            title="{{ $u->is_active ? 'Deactivate User' : 'Activate User' }}">
                                                        <i class="fa-solid {{ $u->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete staff user {{ $u->name }}? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete User">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn btn-outline-light text-muted disabled border" title="Current Active Session">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal for this user -->
                                <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Staff User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('users.update', $u->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Login Email <span class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Phone Number</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $u->phone }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Role Designation <span class="text-danger">*</span></label>
                                                        <select name="role" class="form-select" required>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->name }}" {{ ($u->roles->first()?->name ?: $u->role) === $role->name ? 'selected' : '' }}>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Reset Password (Leave blank to keep current)</label>
                                                        <input type="password" name="password" class="form-control" placeholder="New password (min 8 chars)">
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-gudi-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-user-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0">No staff members found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Register New Staff User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="staff@gudichemicals.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Assigned System Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Determines access permissions across Billing, Production, Purchasing, and Reports.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Initial Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gudi-primary"><i class="fa-solid fa-check me-1"></i> Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
