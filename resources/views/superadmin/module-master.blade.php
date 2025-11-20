@extends('admin.layouts.app')

@section('title', 'Module Master')
@section('page-title', 'Module Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Module Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Module Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: {{ auth()->user()->name }}</strong></p>
                    </div>
                </div>
                <form action="{{ isset($module) ? route('module.master.update', $module->id) : route('module.master.store') }}" method="POST" id="moduleForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="module_code">Module Code<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('module_code') is-invalid @enderror" 
                                       id="module_code" 
                                       name="module_code" 
                                       value="{{ old('module_code', isset($module) ? $module->module_code : '') }}" 
                                       placeholder="Text Box"
                                       required
                                       style="text-transform: uppercase;">
                                @error('module_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="module_name">Module Name<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('module_name') is-invalid @enderror" 
                                       id="module_name" 
                                       name="module_name" 
                                       value="{{ old('module_name', isset($module) ? $module->module_name : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('module_name')
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
                    @if(isset($module))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('module.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10%">Sr. no.</th>
                            <th style="width: 30%">Module Code</th>
                            <th style="width: 40%">Module Name</th>
                            <th style="width: 20%">Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $index => $module)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $module->module_code }}</td>
                                <td>{{ $module->module_name }}</td>
                                <td>
                                    <a href="{{ route('module.master.edit', $module->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No modules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-uppercase module code
    document.getElementById('module_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection

