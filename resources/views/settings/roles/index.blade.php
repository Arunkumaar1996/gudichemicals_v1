@extends('layouts.app')

@section('title', 'Roles & Permissions Management')

@section('content')
<!-- Header & Navigation Tabs -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Roles & Permissions Management</h4>
        <p class="text-muted small mb-0">Configure role-based access control (RBAC). Control which menus, screens, and actions (View, Create, Edit, Delete) each role can perform.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-users me-1"></i> Staff Users Directory
        </a>
        <button class="btn btn-gudi-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="fa-solid fa-shield-plus me-1"></i> Add New Custom Role
        </button>
    </div>
</div>

<!-- Navigation Pills for Fast Switching -->
<div class="mb-4">
    <ul class="nav nav-pills border-bottom pb-2">
        <li class="nav-item">
            <a class="nav-link text-muted fw-semibold py-1.5 px-3" href="{{ route('users.index') }}">
                <i class="fa-solid fa-user-group me-1.5 text-primary"></i> Staff Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold py-1.5 px-3" href="{{ route('roles.index') }}" style="background-color: #005a9c;">
                <i class="fa-solid fa-user-shield me-1.5"></i> Roles & Permissions Matrix
            </a>
        </li>
    </ul>
</div>

<!-- Info Banner -->
<div class="alert alert-primary bg-primary bg-opacity-10 border-primary border-opacity-25 d-flex align-items-center justify-content-between p-3 mb-4 rounded-3">
    <div class="d-flex align-items-center gap-3">
        <div class="p-2 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <strong class="d-block text-dark small">Granular Menu & Action Visibility</strong>
            <small class="text-muted">Unchecking a module's permissions will automatically hide that menu from the staff member's sidebar navigation and block access to its action buttons.</small>
        </div>
    </div>
    <span class="badge bg-primary text-white py-1 px-2.5">
        <i class="fa-solid fa-key me-1"></i> {{ count($roles) }} Active Roles
    </span>
</div>

<!-- Roles Listing Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i> Configured User Roles</h6>
        <small class="text-muted"><i class="fa-solid fa-lock text-success me-1"></i> Real-time Permission Sync</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 25%;">Role Designation</th>
                        <th style="width: 15%;">Assigned Staff</th>
                        <th style="width: 40%;">Granted Module Access</th>
                        <th class="text-end pe-3" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        @php
                            $isSystemRole = in_array($role->name, ['Super Admin', 'Admin']);
                            $userCount = $role->users->count();
                            $permCount = $role->permissions->count();
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle rounded-circle bg-light border text-dark fw-bold d-flex align-items-center justify-content-center me-2.5 shadow-sm" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-user-shield {{ $role->name === 'Super Admin' ? 'text-warning' : 'text-primary' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                            {{ $role->name }}
                                            @if($role->name === 'Super Admin')
                                                <span class="badge bg-dark text-warning border border-warning-subtle" style="font-size: 0.65rem;">Developer</span>
                                            @elseif($isSystemRole)
                                                <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">System</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $permCount }} permissions assigned</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fa-solid fa-users text-muted me-1"></i> {{ $userCount }} {{ Str::plural('Staff', $userCount) }}
                                </span>
                            </td>
                            <td>
                                @if($role->name === 'Super Admin')
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2.5 py-1">
                                        <i class="fa-solid fa-infinity me-1"></i> Full Access to All Modules & Actions
                                    </span>
                                @elseif($permCount === 0)
                                    <span class="badge bg-secondary-subtle text-muted border px-2 py-1">No Permissions Granted</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($permissionGroups as $groupTitle => $groupData)
                                            @php
                                                $groupPermKeys = array_keys($groupData['permissions']);
                                                $hasGroupPerms = $role->permissions->whereIn('name', $groupPermKeys)->count();
                                            @endphp
                                            @if($hasGroupPerms > 0)
                                                <span class="badge bg-{{ $groupData['color'] }}-subtle text-{{ $groupData['color'] }} border border-{{ $groupData['color'] }}-subtle" style="font-size: 0.68rem;" title="{{ $hasGroupPerms }}/{{ count($groupPermKeys) }} permissions">
                                                    <i class="{{ $groupData['icon'] }} me-1"></i> {{ $groupTitle }} ({{ $hasGroupPerms }})
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($role->name === 'Super Admin')
                                        <span class="badge bg-dark text-muted py-1.5 px-2.5 border" title="Developer Super Admin has unrestricted permissions">
                                            <i class="fa-solid fa-lock text-warning me-1"></i> Protected
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}" title="Edit Permissions">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Permissions
                                        </button>

                                        @if(!$isSystemRole)
                                            <form method="POST" action="{{ route('roles.destroy', $role->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete role {{ $role->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete Role" {{ $userCount > 0 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Role Modal -->
                        @if($role->name !== 'Super Admin')
                            <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark mb-0">
                                                    <i class="fa-solid fa-user-gear text-primary me-2"></i> Edit Role: {{ $role->name }}
                                                </h5>
                                                <small class="text-muted">Configure module view, create, edit, and delete permissions for this role.</small>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('roles.update', $role->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <!-- Role Name Input -->
                                                <div class="mb-4">
                                                    <label class="form-label small fw-bold">Role Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required {{ $isSystemRole ? 'readonly' : '' }}>
                                                    @if($isSystemRole)
                                                        <small class="text-muted">System default role name cannot be renamed, but its permissions can be modified.</small>
                                                    @endif
                                                </div>

                                                <!-- Quick Toggle Buttons -->
                                                <div class="d-flex align-items-center justify-content-between p-2.5 mb-3 bg-light rounded border">
                                                    <span class="small fw-bold text-dark"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Quick Presets:</span>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary py-1" onclick="toggleAllRolePerms('editRoleModal{{ $role->id }}', true)">
                                                            <i class="fa-solid fa-check-double me-1 text-success"></i> Select All
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary py-1" onclick="selectViewOnlyRolePerms('editRoleModal{{ $role->id }}')">
                                                            <i class="fa-solid fa-eye me-1 text-info"></i> View Only
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary py-1" onclick="toggleAllRolePerms('editRoleModal{{ $role->id }}', false)">
                                                            <i class="fa-solid fa-ban me-1 text-danger"></i> Clear All
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Permissions Accordion by Module -->
                                                <div class="accordion" id="accordionPerms{{ $role->id }}">
                                                    @foreach($permissionGroups as $groupTitle => $groupData)
                                                        @php
                                                            $accordionId = 'acc_' . Str::slug($groupTitle) . '_' . $role->id;
                                                        @endphp
                                                        <div class="accordion-item border mb-2 rounded overflow-hidden">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed fw-bold text-dark py-2.5 px-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}">
                                                                    <i class="{{ $groupData['icon'] }} text-{{ $groupData['color'] }} me-2"></i> {{ $groupTitle }}
                                                                </button>
                                                            </h2>
                                                            <div id="{{ $accordionId }}" class="accordion-collapse collapse show">
                                                                <div class="accordion-body p-3">
                                                                    <div class="row g-2">
                                                                        @foreach($groupData['permissions'] as $permKey => $permLabel)
                                                                            <div class="col-md-6">
                                                                                <div class="form-check p-2 rounded border bg-white h-100">
                                                                                    <input class="form-check-input perm-checkbox ms-1 me-2" 
                                                                                           type="checkbox" 
                                                                                           name="permissions[]" 
                                                                                           value="{{ $permKey }}" 
                                                                                           id="perm_{{ $permKey }}_{{ $role->id }}"
                                                                                           data-perm-type="{{ str_contains($permKey, 'view') ? 'view' : 'action' }}"
                                                                                           {{ $role->hasPermissionTo($permKey) ? 'checked' : '' }}>
                                                                                    <label class="form-check-label small fw-semibold text-dark cursor-pointer" for="perm_{{ $permKey }}_{{ $role->id }}">
                                                                                        {{ $permLabel }}
                                                                                        <code class="d-block text-muted" style="font-size: 0.68rem;">{{ $permKey }}</code>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-gudi-primary"><i class="fa-solid fa-save me-1"></i> Save Permissions</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-shield-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No roles configured.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create New Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-shield-plus text-primary me-2"></i> Create New Custom Role
                    </h5>
                    <small class="text-muted">Define a new departmental staff role and select applicable permissions.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Role Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Quality Control Chemist, Billing Supervisor" required>
                    </div>

                    <!-- Quick Presets -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 mb-3 bg-light rounded border">
                        <span class="small fw-bold text-dark"><i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Quick Presets:</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary py-1" onclick="toggleAllRolePerms('createRoleModal', true)">
                                <i class="fa-solid fa-check-double me-1 text-success"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary py-1" onclick="selectViewOnlyRolePerms('createRoleModal')">
                                <i class="fa-solid fa-eye me-1 text-info"></i> View Only
                            </button>
                            <button type="button" class="btn btn-outline-secondary py-1" onclick="toggleAllRolePerms('createRoleModal', false)">
                                <i class="fa-solid fa-ban me-1 text-danger"></i> Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Permissions Accordion -->
                    <div class="accordion" id="accordionCreatePerms">
                        @foreach($permissionGroups as $groupTitle => $groupData)
                            @php
                                $createAccId = 'create_acc_' . Str::slug($groupTitle);
                            @endphp
                            <div class="accordion-item border mb-2 rounded overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold text-dark py-2.5 px-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $createAccId }}">
                                        <i class="{{ $groupData['icon'] }} text-{{ $groupData['color'] }} me-2"></i> {{ $groupTitle }}
                                    </button>
                                </h2>
                                <div id="{{ $createAccId }}" class="accordion-collapse collapse show">
                                    <div class="accordion-body p-3">
                                        <div class="row g-2">
                                            @foreach($groupData['permissions'] as $permKey => $permLabel)
                                                <div class="col-md-6">
                                                    <div class="form-check p-2 rounded border bg-white h-100">
                                                        <input class="form-check-input perm-checkbox ms-1 me-2" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permKey }}" 
                                                               id="new_perm_{{ $permKey }}"
                                                               data-perm-type="{{ str_contains($permKey, 'view') ? 'view' : 'action' }}">
                                                        <label class="form-check-label small fw-semibold text-dark cursor-pointer" for="new_perm_{{ $permKey }}">
                                                            {{ $permLabel }}
                                                            <code class="d-block text-muted" style="font-size: 0.68rem;">{{ $permKey }}</code>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gudi-primary"><i class="fa-solid fa-plus me-1"></i> Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAllRolePerms(modalId, selectAll) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = selectAll;
        });
    }

    function selectViewOnlyRolePerms(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.querySelectorAll('.perm-checkbox').forEach(cb => {
            if (cb.dataset.permType === 'view') {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });
    }
</script>
@endpush
