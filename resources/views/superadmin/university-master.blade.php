@extends('admin.layouts.app')

@section('title', 'University Master')
@section('page-title', 'University Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">University Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">University Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: {{ auth()->user()->name }}</strong></p>
                    </div>
                </div>

                @if(isset($university) && request()->routeIs('university.master.show'))
                    <!-- View Mode -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">University Details</h3>
                                    <div class="card-tools">
                                        <a href="{{ route('university.master') }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="width: 40%">University Code:</th>
                                                    <td><strong>{{ $university->university_code }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>University Name:</th>
                                                    <td><strong>{{ $university->university_name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>URL:</th>
                                                    <td>
                                                        @if($university->url)
                                                            <a href="{{ $university->url }}" target="_blank">{{ $university->url }}</a>
                                                        @else
                                                            <span class="text-muted">Not provided</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Status:</th>
                                                    <td>
                                                        @if($university->status === 'active')
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th style="width: 40%">Admin Username:</th>
                                                    <td><strong>{{ $university->admin_username }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>Admin Email:</th>
                                                    <td>
                                                        @if($university->adminUser)
                                                            {{ $university->adminUser->email }}
                                                        @else
                                                            {{ $university->admin_username }}@erp.com
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Password:</th>
                                                    <td>{{ $university->admin_password_display ?? '123456' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Created At:</th>
                                                    <td>{{ $university->created_at->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Updated At:</th>
                                                    <td>{{ $university->updated_at->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <a href="{{ route('university.master.edit', $university->id) }}" class="btn btn-info">
                                                <i class="fas fa-edit"></i> Edit University
                                            </a>
                                            <a href="{{ route('university.master') }}" class="btn btn-secondary">
                                                <i class="fas fa-list"></i> Back to List
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Form Mode (Create/Edit) -->
                    <form action="{{ isset($university) ? route('university.master.update', $university->id) : route('university.master.store') }}" method="POST" id="universityForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="university_code">University Code<span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('university_code') is-invalid @enderror" 
                                           id="university_code" 
                                           name="university_code" 
                                           value="{{ old('university_code', isset($university) ? $university->university_code : '') }}" 
                                           placeholder="Text Box"
                                           {{ isset($university) && request()->routeIs('university.master.edit') ? '' : 'required' }}
                                           style="text-transform: uppercase;">
                                    @error('university_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="university_name">University Name<span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('university_name') is-invalid @enderror" 
                                           id="university_name" 
                                           name="university_name" 
                                           value="{{ old('university_name', isset($university) ? $university->university_name : '') }}" 
                                           placeholder="Text Box"
                                           {{ isset($university) && request()->routeIs('university.master.edit') ? '' : 'required' }}>
                                    @error('university_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block" style="background-color: #1F8BFF;">
                                        SAVE
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="url">URL</label>
                                    <input type="url" 
                                           class="form-control @error('url') is-invalid @enderror" 
                                           id="url" 
                                           name="url" 
                                           value="{{ old('url', isset($university) ? $university->url : '') }}" 
                                           placeholder="Text Box">
                                    @error('url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if(isset($university))
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('university.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
                                </div>
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%">Sr. no.</th>
                            <th style="width: 12%">University Code</th>
                            <th style="width: 18%">University Name</th>
                            <th style="width: 15%">University Admin User Id</th>
                            <th style="width: 15%">URL</th>
                            <th style="width: 10%">Password</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($universities as $index => $university)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $university->university_code }}</td>
                                <td>{{ $university->university_name }}</td>
                                <td>
                                    @if($university->adminUser)
                                        <a href="#" style="color: #1F8BFF; text-decoration: underline;" title="{{ $university->adminUser->email }}">
                                            {{ $university->admin_username }}<br>
                                            <small style="color: #666;">{{ $university->adminUser->email }}</small>
                                        </a>
                                    @else
                                        <span style="color: #1F8BFF; text-decoration: underline;">
                                            {{ $university->admin_username }}@erp.com
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($university->url)
                                        <a href="{{ $university->url }}" target="_blank" style="color: #1F8BFF; text-decoration: underline;">
                                            {{ $university->url }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $university->admin_password_display ?? '123456' }}</td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input status-toggle" 
                                               id="status_{{ $university->id }}" 
                                               data-id="{{ $university->id }}"
                                               {{ $university->status === 'active' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status_{{ $university->id }}">
                                            {{ $university->status === 'active' ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Action buttons">
                                        <a href="{{ route('university.master.edit', $university->id) }}" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('university.master.show', $university->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('university.master.destroy', $university->id) }}" method="POST" class="d-inline" id="deleteForm{{ $university->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete({{ $university->id }}, '{{ addslashes($university->university_name) }}');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No universities found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Auto-uppercase university code
    var universityCodeInput = document.getElementById('university_code');
    if (universityCodeInput) {
        universityCodeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    }

    // Double confirmation for delete
    function confirmDelete(id, universityName) {
        // First confirmation
        var firstConfirm = confirm('Are you sure you want to delete "' + universityName + '"?\n\nThis action will also delete the associated admin user account.');
        
        if (!firstConfirm) {
            return false;
        }
        
        // Second confirmation
        var secondConfirm = confirm('⚠️ WARNING: This action cannot be undone!\n\nAre you absolutely sure you want to delete "' + universityName + '"?\n\nThis will permanently delete:\n- The university record\n- The admin user account\n- All associated data\n\nClick OK to confirm deletion.');
        
        if (!secondConfirm) {
            return false;
        }
        
        return true;
    }

    // AJAX status toggle
    $(document).ready(function() {
        $('.status-toggle').on('change', function() {
            var universityId = $(this).data('id');
            var isActive = $(this).is(':checked');
            var status = isActive ? 'active' : 'inactive';
            var label = $(this).next('label');
            
            $.ajax({
                url: '{{ url("university-master/status") }}/' + universityId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        label.text(response.status === 'active' ? 'Active' : 'Inactive');
                    } else {
                        alert('Failed to update status');
                        // Revert checkbox
                        $('.status-toggle[data-id="' + universityId + '"]').prop('checked', !isActive);
                    }
                },
                error: function() {
                    alert('Error updating status');
                    // Revert checkbox
                    $('.status-toggle[data-id="' + universityId + '"]').prop('checked', !isActive);
                }
            });
        });
    });
</script>
@endsection

