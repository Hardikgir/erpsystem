@extends('admin.layouts.app')

@section('title', 'Permission Assignment')
@section('page-title', 'Permission Assignment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assign Permissions to Roles</h3>
    </div>
    <div class="card-body">
        <form id="permissionForm" action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="role_id">Select Role <span class="text-danger">*</span></label>
                <select class="form-control @error('role_id') is-invalid @enderror" 
                        id="role_id" name="role_id" required>
                    <option value="">Select a Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" 
                                {{ (old('role_id', session('selected_role_id')) == $role->id) ? 'selected' : '' }}>
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

            <div id="permissionsContainer" style="display: none;">
                @php
                    $selectedRoleId = old('role_id', session('selected_role_id'));
                    $permissions = [];
                    if ($selectedRoleId) {
                        $permissions = \App\Models\RoleModulePermission::where('role_id', $selectedRoleId)
                            ->get()
                            ->keyBy(function ($item) {
                                return $item->module_id . '_' . $item->sub_module_id;
                            });
                    }
                @endphp
                @foreach($modules as $module)
                    <div class="card card-primary card-outline module-card" data-module-id="{{ $module->id }}">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="{{ $module->icon ?? 'fas fa-circle' }}"></i>
                                {{ $module->module_name }}
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($module->subModules->count() > 0)
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sub Module</th>
                                            <th>Route</th>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Add</th>
                                            <th class="text-center">Edit</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($module->subModules as $subModule)
                                            @php
                                                $permissionKey = $module->id . '_' . $subModule->id;
                                                $permission = $permissions[$permissionKey] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ $subModule->sub_module_name }}</td>
                                                <td><code>{{ $subModule->route }}</code></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="permissions[{{ $module->id }}][{{ $subModule->id }}][module_id]" value="{{ $module->id }}">
                                                    <input type="hidden" name="permissions[{{ $module->id }}][{{ $subModule->id }}][sub_module_id]" value="{{ $subModule->id }}">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input permission-checkbox" 
                                                               name="permissions[{{ $module->id }}][{{ $subModule->id }}][can_view]"
                                                               id="view_{{ $module->id }}_{{ $subModule->id }}"
                                                               value="1"
                                                               {{ $permission && ($permission->can_view == true || $permission->can_view == 1) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" 
                                                               for="view_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input permission-checkbox" 
                                                               name="permissions[{{ $module->id }}][{{ $subModule->id }}][can_add]"
                                                               id="add_{{ $module->id }}_{{ $subModule->id }}"
                                                               value="1"
                                                               {{ $permission && ($permission->can_add == true || $permission->can_add == 1) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" 
                                                               for="add_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input permission-checkbox" 
                                                               name="permissions[{{ $module->id }}][{{ $subModule->id }}][can_edit]"
                                                               id="edit_{{ $module->id }}_{{ $subModule->id }}"
                                                               value="1"
                                                               {{ $permission && ($permission->can_edit == true || $permission->can_edit == 1) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" 
                                                               for="edit_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input permission-checkbox" 
                                                               name="permissions[{{ $module->id }}][{{ $subModule->id }}][can_delete]"
                                                               id="delete_{{ $module->id }}_{{ $subModule->id }}"
                                                               value="1"
                                                               {{ $permission && ($permission->can_delete == true || $permission->can_delete == 1) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" 
                                                               for="delete_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No sub modules available for this module.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary" id="savePermissionsBtn">
                        <i class="fas fa-save"></i> Save Permissions
                    </button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-default">Cancel</a>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> After saving permissions, the assigned modules and submodules will appear in the sidebar for users with this role. Only modules/submodules with "View" permission checked will be visible.
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load permissions if role is already selected (after save redirect)
    var selectedRoleId = $('#role_id').val();
    if (selectedRoleId) {
        $('#permissionsContainer').show();
        loadPermissions(selectedRoleId);
    }

    $('#role_id').on('change', function() {
        var roleId = $(this).val();
        if (roleId) {
            $('#permissionsContainer').show();
            loadPermissions(roleId);
        } else {
            $('#permissionsContainer').hide();
            // Reset all checkboxes when no role selected
            $('.permission-checkbox').prop('checked', false);
        }
    });

    function loadPermissions(roleId) {
        // Reset all checkboxes first
        $('.permission-checkbox').prop('checked', false);
        
        $.ajax({
            url: '/admin/permissions/' + roleId + '/get',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Set checkboxes based on loaded permissions
                $.each(data, function(key, permissions) {
                    var parts = key.split('_');
                    var moduleId = parts[0];
                    var subModuleId = parts[1];
                    
                    // Check for boolean true or integer 1
                    if (permissions.can_view === true || permissions.can_view === 1 || permissions.can_view === '1') {
                        $('#view_' + moduleId + '_' + subModuleId).prop('checked', true);
                    }
                    if (permissions.can_add === true || permissions.can_add === 1 || permissions.can_add === '1') {
                        $('#add_' + moduleId + '_' + subModuleId).prop('checked', true);
                    }
                    if (permissions.can_edit === true || permissions.can_edit === 1 || permissions.can_edit === '1') {
                        $('#edit_' + moduleId + '_' + subModuleId).prop('checked', true);
                    }
                    if (permissions.can_delete === true || permissions.can_delete === 1 || permissions.can_delete === '1') {
                        $('#delete_' + moduleId + '_' + subModuleId).prop('checked', true);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading permissions:', error);
                alert('Error loading permissions. Please try again.');
            }
        });
    }
    
    // Handle form submission
    $('#permissionForm').on('submit', function(e) {
        var roleId = $('#role_id').val();
        if (!roleId) {
            e.preventDefault();
            alert('Please select a role first.');
            return false;
        }
        
        // Show loading state
        $('#savePermissionsBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Form will submit normally with nested array structure
        // Laravel will handle it automatically
    });
});
</script>
@endpush

