@extends('admin.layouts.app')

@section('title', 'Role Color Settings')
@section('page-title', 'Role Based Color Settings Overview')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Role Colors</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Role Color Management</h3>
                <p class="text-muted mb-0">Manage color themes for each role. Colors will be applied to sidebar, header, and dashboard elements.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Current Color</th>
                                <th>Hover Color</th>
                                <th>Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $role->role_name)) }}</strong>
                                        @if($role->description)
                                            <br><small class="text-muted">{{ $role->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->role_color)
                                            <div class="d-flex align-items-center">
                                                <div class="color-box mr-2" style="width: 30px; height: 30px; background-color: {{ $role->role_color }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                                <span>{{ $role->role_color }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->role_hover_color)
                                            <div class="d-flex align-items-center">
                                                <div class="color-box mr-2" style="width: 30px; height: 30px; background-color: {{ $role->role_hover_color }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                                <span>{{ $role->role_hover_color }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->role_color)
                                            <div class="preview-box p-3 rounded" style="background-color: {{ $role->role_color }}; color: white; text-align: center; min-width: 100px;">
                                                <small>Sample</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No preview</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editColorModal{{ $role->id }}">
                                            <i class="fas fa-edit"></i> Update Color
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Color Modal -->
                                <div class="modal fade" id="editColorModal{{ $role->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Color for {{ ucfirst(str_replace('_', ' ', $role->role_name)) }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.role-colors.update', $role) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="role_color{{ $role->id }}">Role Color <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="color" 
                                                                   class="form-control @error('role_color') is-invalid @enderror" 
                                                                   id="role_color{{ $role->id }}" 
                                                                   name="role_color" 
                                                                   value="{{ old('role_color', $role->role_color ?? '#1F8BFF') }}" 
                                                                   required>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   id="role_color_text{{ $role->id }}" 
                                                                   value="{{ old('role_color', $role->role_color ?? '#1F8BFF') }}"
                                                                   readonly>
                                                        </div>
                                                        @error('role_color')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <small class="form-text text-muted">This color will be used for sidebar background and header.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="role_hover_color{{ $role->id }}">Hover Color</label>
                                                        <div class="input-group">
                                                            <input type="color" 
                                                                   class="form-control @error('role_hover_color') is-invalid @enderror" 
                                                                   id="role_hover_color{{ $role->id }}" 
                                                                   name="role_hover_color" 
                                                                   value="{{ old('role_hover_color', $role->role_hover_color ?? '') }}">
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   id="role_hover_color_text{{ $role->id }}" 
                                                                   value="{{ old('role_hover_color', $role->role_hover_color ?? '') }}"
                                                                   readonly>
                                                        </div>
                                                        @error('role_hover_color')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                        <small class="form-text text-muted">Leave blank to auto-generate a darker shade.</small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Live Preview</label>
                                                        <div class="preview-container p-3 rounded" 
                                                             id="preview{{ $role->id }}" 
                                                             style="background-color: {{ $role->role_color ?? '#1F8BFF' }}; color: white; text-align: center; min-height: 60px;">
                                                            <strong>Preview Box</strong><br>
                                                            <small>This is how the color will appear</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Color</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @push('scripts')
                                <script>
                                    $(document).ready(function() {
                                        // Update text input when color picker changes
                                        $('#role_color{{ $role->id }}').on('input', function() {
                                            $('#role_color_text{{ $role->id }}').val($(this).val());
                                            $('#preview{{ $role->id }}').css('background-color', $(this).val());
                                        });

                                        $('#role_hover_color{{ $role->id }}').on('input', function() {
                                            $('#role_hover_color_text{{ $role->id }}').val($(this).val());
                                        });

                                        // Update color picker when text input changes (if manually edited)
                                        $('#role_color_text{{ $role->id }}').on('input', function() {
                                            var color = $(this).val();
                                            if (/^#[0-9A-F]{6}$/i.test(color)) {
                                                $('#role_color{{ $role->id }}').val(color);
                                                $('#preview{{ $role->id }}').css('background-color', color);
                                            }
                                        });
                                    });
                                </script>
                                @endpush
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

