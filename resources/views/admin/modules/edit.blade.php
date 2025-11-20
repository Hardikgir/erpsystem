@extends('admin.layouts.app')

@section('title', 'Edit Module')
@section('page-title', 'Edit Module')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.modules.index') }}">Modules</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Module</h3>
    </div>
    <form action="{{ route('admin.modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="module_name">Module Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('module_name') is-invalid @enderror" 
                       id="module_name" name="module_name" value="{{ old('module_name', $module->module_name) }}" required>
                @error('module_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="icon">Icon (Font Awesome class)</label>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                       id="icon" name="icon" value="{{ old('icon', $module->icon) }}" 
                       placeholder="e.g., fas fa-cube">
                <small class="form-text text-muted">Use Font Awesome icon classes (e.g., fas fa-cube, fas fa-users)</small>
                @error('icon')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" 
                           {{ old('status', $module->status) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="status">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Module</button>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection

