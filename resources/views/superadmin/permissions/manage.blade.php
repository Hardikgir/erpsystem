@extends('admin.layouts.app')

@section('title', 'Permission Management')
@section('page-title', 'Permission Management - Spatie')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Permission Management</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-key"></i> Assign Permissions to Roles
        </h3>
        <div class="card-tools">
            <form action="{{ route('superadmin.permissions.sync') }}" method="POST" style="display: inline-block;" id="syncForm">
                @csrf
                <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('This will sync all permissions from modules. Continue?');">
                    <i class="fas fa-sync"></i> Sync Permissions
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="permissionForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="role_id" id="hidden_role_id">
            <div class="form-group">
                <label for="role_id">Select Role <span class="text-danger">*</span></label>
                <select class="form-control @error('role_id') is-invalid @enderror" 
                        id="role_id" name="role_id_display" required>
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
                @foreach($modules as $module)
                    @php
                        // Use the same slugify logic as SyncModulePermissions command
                        $moduleName = $module->module_code ?? $module->module_name;
                        $moduleSlug = strtolower(trim($moduleName));
                        $moduleSlug = preg_replace('/[^a-z0-9]+/', '_', $moduleSlug);
                        $moduleSlug = preg_replace('/_+/', '_', $moduleSlug);
                        $moduleSlug = trim($moduleSlug, '_');
                    @endphp
                    <div class="card card-primary card-outline module-card mb-3" data-module-id="{{ $module->id }}">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="{{ $module->icon ?? 'fas fa-circle' }}"></i>
                                {{ $module->module_name }}
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($module->subModules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Sub Module</th>
                                                <th>Route</th>
                                                <th class="text-center">View</th>
                                                <th class="text-center">Create</th>
                                                <th class="text-center">Edit</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($module->subModules->where('status', true) as $subModule)
                                                @php
                                                    // Use route name for permissions (route.view, route.create, etc.)
                                                    $routeName = $subModule->route;
                                                    
                                                    // Generate permission names based on route
                                                    $permissionView = "{$routeName}.view";
                                                    $permissionCreate = "{$routeName}.create";
                                                    $permissionEdit = "{$routeName}.edit";
                                                    $permissionDelete = "{$routeName}.delete";
                                                    
                                                    // Get selected role to check current permissions
                                                    $selectedRoleId = old('role_id', session('selected_role_id'));
                                                    $selectedRole = $selectedRoleId ? \App\Models\Role::find($selectedRoleId) : null;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $subModule->sub_module_name }}</strong>
                                                    </td>
                                                    <td>
                                                        <code class="text-sm">{{ $subModule->route }}</code>
                                                        <br>
                                                        <small class="text-muted">View: {{ $permissionView }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" 
                                                                   class="custom-control-input permission-checkbox" 
                                                                   name="permissions[]"
                                                                   id="view_{{ $module->id }}_{{ $subModule->id }}"
                                                                   value="{{ $permissionView }}"
                                                                   data-action="view"
                                                                   data-route="{{ $subModule->route }}">
                                                            <label class="custom-control-label" 
                                                                   for="view_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" 
                                                                   class="custom-control-input permission-checkbox" 
                                                                   name="permissions[]"
                                                                   id="create_{{ $module->id }}_{{ $subModule->id }}"
                                                                   value="{{ $permissionCreate }}"
                                                                   data-action="create"
                                                                   data-route="{{ $subModule->route }}">
                                                            <label class="custom-control-label" 
                                                                   for="create_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" 
                                                                   class="custom-control-input permission-checkbox" 
                                                                   name="permissions[]"
                                                                   id="edit_{{ $module->id }}_{{ $subModule->id }}"
                                                                   value="{{ $permissionEdit }}"
                                                                   data-action="edit"
                                                                   data-route="{{ $subModule->route }}">
                                                            <label class="custom-control-label" 
                                                                   for="edit_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" 
                                                                   class="custom-control-input permission-checkbox" 
                                                                   name="permissions[]"
                                                                   id="delete_{{ $module->id }}_{{ $subModule->id }}"
                                                                   value="{{ $permissionDelete }}"
                                                                   data-action="delete"
                                                                   data-route="{{ $subModule->route }}">
                                                            <label class="custom-control-label" 
                                                                   for="delete_{{ $module->id }}_{{ $subModule->id }}"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No sub modules available for this module.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary btn-lg" id="savePermissionsBtn">
                        <i class="fas fa-save"></i> Save Permissions
                    </button>
                    <a href="{{ route('superadmin.permissions.manage') }}" class="btn btn-default btn-lg">Cancel</a>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> After saving permissions, the assigned modules and submodules will appear in the sidebar for users with this role. Only modules/submodules with "View" permission checked will be visible.
                </div>
                
                <div class="alert alert-warning mt-3" id="debugInfo" style="display: none;">
                    <i class="fas fa-bug mr-2"></i>
                    <strong>Debug Info:</strong>
                    <div id="debugContent" class="mt-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var selectedRoleId = $('#role_id').val();
    if (selectedRoleId) {
        $('#permissionsContainer').show();
        updateFormAction(selectedRoleId);
        loadPermissions(selectedRoleId);
    }

    $('#role_id').on('change', function() {
        var roleId = $(this).val();
        if (roleId) {
            $('#permissionsContainer').show();
            updateFormAction(roleId);
            loadPermissions(roleId);
        } else {
            $('#permissionsContainer').hide();
            $('.permission-checkbox').prop('checked', false);
            $('#permissionForm').attr('action', '');
        }
    });

    function updateFormAction(roleId) {
        var form = $('#permissionForm');
        var actionUrl = '{{ url("/superadmin/permissions") }}/' + roleId + '/update';
        form.attr('action', actionUrl);
        $('#hidden_role_id').val(roleId);
    }

    function loadPermissions(roleId) {
        // Reset all checkboxes first
        $('.permission-checkbox').prop('checked', false);
        
        $.ajax({
            url: '{{ url("/superadmin/permissions") }}/' + roleId + '/get',
            type: 'GET',
            dataType: 'json',
            success: function(permissions) {
                // permissions is an array of permission names
                $.each(permissions, function(index, permissionName) {
                    $('.permission-checkbox[value="' + permissionName + '"]').prop('checked', true);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading permissions:', error);
                // If role has no permissions, that's okay - just show empty checkboxes
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
        
        // Ensure form action is set
        if (!$('#permissionForm').attr('action')) {
            updateFormAction(roleId);
        }
        
        // Collect all checked permissions
        var checkedPermissions = [];
        $('.permission-checkbox:checked').each(function() {
            var permValue = $(this).val();
            if (permValue && permValue.trim() !== '') {
                checkedPermissions.push(permValue);
            }
        });
        
        // Show debug info
        $('#debugInfo').show();
        $('#debugContent').html(
            '<strong>Role ID:</strong> ' + roleId + '<br>' +
            '<strong>Form Action:</strong> ' + $('#permissionForm').attr('action') + '<br>' +
            '<strong>Permissions Count:</strong> ' + checkedPermissions.length + '<br>' +
            '<strong>Permissions:</strong> ' + (checkedPermissions.length > 0 ? checkedPermissions.join(', ') : 'None selected')
        );
        
        console.log('Submitting permissions:', checkedPermissions);
        console.log('Form action:', $('#permissionForm').attr('action'));
        console.log('Form method:', $('#permissionForm').attr('method'));
        
        // Show loading state
        $('#savePermissionsBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Form will submit normally - don't prevent default
    });
});
</script>
@endpush

