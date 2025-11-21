@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User</h3>
    </div>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" 
                       id="password_confirmation" name="password_confirmation">
            </div>

            <div class="form-group">
                <label for="role_id">Role <span class="text-danger">*</span></label>
                <select class="form-control @error('role_id') is-invalid @enderror" 
                        id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group" id="university-section" style="display: none;">
                <label for="university_id">Select University <span class="text-danger">*</span></label>
                <select name="university_id" id="university_id" class="form-control @error('university_id') is-invalid @enderror">
                    <option value="">Search University...</option>
                    @foreach($universities as $uni)
                        <option value="{{ $uni->id }}" {{ old('university_id', $user->university_id) == $uni->id ? 'selected' : '' }}>
                            {{ $uni->university_code }} - {{ $uni->university_name }}
                        </option>
                    @endforeach
                </select>
                @error('university_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select class="form-control @error('status') is-invalid @enderror" 
                        id="status" name="status" required>
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for university dropdown
    $('#university_id').select2({
        width: '100%',
        placeholder: "Search University...",
        allowClear: true
    });

    // Function to check role and show/hide university section
    function toggleUniversitySection() {
        var roleId = $('#role_id').val();
        if (roleId) {
            // Get role name from selected option and normalize it
            var roleName = $('#role_id option:selected').text().toLowerCase().trim();
            // Remove spaces and underscores for comparison
            var normalizedRoleName = roleName.replace(/[\s_]/g, '');
            
            if (normalizedRoleName === 'universityadmin') {
                $('#university-section').show();
                $('#university_id').prop('required', true);
            } else {
                $('#university-section').hide();
                $('#university_id').prop('required', false);
                $('#university_id').val(null).trigger('change');
            }
        } else {
            $('#university-section').hide();
            $('#university_id').prop('required', false);
            $('#university_id').val(null).trigger('change');
        }
    }

    // Check on page load based on current user role
    @php
        $userRole = $user->role;
        $roleNameNormalized = $userRole ? strtolower(str_replace([' ', '_'], '', $userRole->role_name)) : '';
        $isUniversityAdmin = $roleNameNormalized === 'universityadmin';
    @endphp
    
    @if($isUniversityAdmin || old('role_id'))
        toggleUniversitySection();
    @endif

    // Listen for role changes
    $('#role_id').on('change', function() {
        toggleUniversitySection();
    });
});
</script>
@endpush
@endsection

