@extends('admin.layouts.app')

@section('title', 'Create Role')
@section('page-title', 'Create Role')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Role</h3>
    </div>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="role_name">Role Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('role_name') is-invalid @enderror" 
                       id="role_name" name="role_name" value="{{ old('role_name') }}" required>
                @error('role_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection

