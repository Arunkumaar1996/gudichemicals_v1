@extends('layouts.app')

@section('title', 'Staff Users & Roles')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-user-plus text-primary me-2"></i> Create Staff User</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Patil" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Login Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="staff@gudichemicals.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit mobile">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">System Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>

                    <button type="submit" class="btn btn-gudi-primary w-100 fw-semibold">
                        <i class="fa-solid fa-user-check me-1"></i> Create Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-user-shield text-primary me-2"></i> System Users & Role Permissions</h6>
                <span class="badge bg-secondary">{{ count($users) }} Users</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Name & Email</th>
                                <th>Phone</th>
                                <th>Assigned Role</th>
                                <th>Last Login</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                                <tr>
                                    <td>
                                        <strong>{{ $u->name }}</strong>
                                        <div class="text-muted small">{{ $u->email }}</div>
                                    </td>
                                    <td>{{ $u->phone ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $u->roles->first()?->name ?: $u->role }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $u->last_login_at ? $u->last_login_at->format('d M Y, h:i A') : 'Never' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $u->is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.toggle', $u->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-2">
                                                    {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-light text-muted border">You</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
