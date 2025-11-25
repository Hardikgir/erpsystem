@extends('admin.layouts.app')

@section('title', 'University Role Master')
@section('page-title', 'University Role Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">University Role Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">University Role Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: Super Admin</strong></p>
                    </div>
                </div>
                <form action="{{ isset($role) ? route('superadmin.universityrole.update', $role->id) : route('superadmin.universityrole.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="university_id">University<span class="text-danger">*</span></label>
                                <select class="form-control @error('university_id') is-invalid @enderror" 
                                        id="university_id" name="university_id" required>
                                    <option value="">Select University</option>
                                    @foreach($universities ?? [] as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id', isset($role) ? $role->university_id : '') == $university->id ? 'selected' : '' }}>
                                            {{ $university->university_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role_code">Role Code<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('role_code') is-invalid @enderror" 
                                       id="role_code" name="role_code" 
                                       value="{{ old('role_code', isset($role) ? $role->role_code : '') }}" 
                                       placeholder="Text Box" required style="text-transform: uppercase;">
                                @error('role_code')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role_name">Role Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('role_name') is-invalid @enderror" 
                                       id="role_name" name="role_name" 
                                       value="{{ old('role_name', isset($role) ? $role->role_name : '') }}" 
                                       placeholder="Text Box" required>
                                @error('role_name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" style="background-color: #1F8BFF;">SAVE</button>
                            </div>
                        </div>
                    </div>
                    @if(isset($role))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('superadmin.universityrole.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sr. no.</th>
                            <th>University</th>
                            <th>Role Code</th>
                            <th>Role Name</th>
                            <th>Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $role->university->university_name ?? 'N/A' }}</td>
                                <td>{{ $role->role_code }}</td>
                                <td>{{ $role->role_name }}</td>
                                <td>
                                    <a href="{{ route('superadmin.universityrole.edit', $role->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">Update</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No roles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('role_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection


