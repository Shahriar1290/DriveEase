@extends('layouts.customer')
@section('title', 'My Profile')

@section('content')
{{-- Profile Info --}}
<div class="content-card mb-4">
    <h6 class="fw-bold mb-4">Profile Information</h6>
    @if($errors->any())
    <div class="alert alert-danger small">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif
    @if(session('success'))
    <div class="alert alert-success small">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="text-center mb-4">
            <img src="{{ user_avatar_url($user->avatar, $user->name) }}" class="rounded-circle mb-2" id="avatarPreview"
                 width="90" height="90" style="object-fit:cover;border:3px solid #e5e7eb">
            <div>
                <label for="avatar" class="btn btn-outline-primary btn-sm mt-1" style="cursor:pointer">
                    <i class="fas fa-camera me-1"></i>Change Photo
                </label>
                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*"
                       onchange="previewAvatar(this)">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Change Password --}}
<div class="content-card">
    <h6 class="fw-bold mb-4">Change Password</h6>
    <form method="POST" action="{{ route('customer.password.update') }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Current Password <span class="text-danger">*</span></label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">New Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Confirm New Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-warning px-4">
                    <i class="fas fa-key me-1"></i>Update Password
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
