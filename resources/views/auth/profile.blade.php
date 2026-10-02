@extends('layouts.app')

@section('title', 'My Profile & Security')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> User Profile</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address (Immutable)</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">System Role</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->role }}" disabled>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-key text-warning me-2"></i> Change Password</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimum 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat new password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gudi-primary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
