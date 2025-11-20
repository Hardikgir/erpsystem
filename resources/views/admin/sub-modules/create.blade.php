@extends('admin.layouts.app')

@section('title', 'Create Sub Module')
@section('page-title', 'Create Sub Module')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub-modules.index') }}">Sub Modules</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Sub Module</h3>
    </div>
    <form action="{{ route('admin.sub-modules.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="module_id">Module <span class="text-danger">*</span></label>
                <select class="form-control @error('module_id') is-invalid @enderror" 
                        id="module_id" name="module_id" required>
                    <option value="">Select Module</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                            {{ $module->module_name }}
                        </option>
                    @endforeach
                </select>
                @error('module_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sub_module_name">Sub Module Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('sub_module_name') is-invalid @enderror" 
                       id="sub_module_name" name="sub_module_name" value="{{ old('sub_module_name') }}" required>
                @error('sub_module_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="route">Route Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('route') is-invalid @enderror" 
                       id="route" name="route" value="{{ old('route') }}" 
                       placeholder="e.g., admin.users.index" required>
                <small class="form-text text-muted">Laravel route name (e.g., admin.users.index)</small>
                @error('route')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" checked>
                    <label class="custom-control-label" for="status">Active</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create Sub Module</button>
            <a href="{{ route('admin.sub-modules.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection

