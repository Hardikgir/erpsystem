@extends('admin.layouts.app')

@section('title', 'Program Master')
@section('page-title', 'Program Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Program Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Program Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: Super Admin</strong></p>
                    </div>
                </div>
                <form action="{{ isset($program) ? route('superadmin.program.update', $program->id) : route('superadmin.program.store') }}" method="POST" id="programForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="university_id">University<span class="text-danger">*</span></label>
                                <select class="form-control @error('university_id') is-invalid @enderror" 
                                        id="university_id" name="university_id" required>
                                    <option value="">Select University</option>
                                    @foreach($universities ?? [] as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id', isset($program) ? $program->university_id : '') == $university->id ? 'selected' : '' }}>
                                            {{ $university->university_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_code">Program Code<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('program_code') is-invalid @enderror" 
                                       id="program_code" 
                                       name="program_code" 
                                       value="{{ old('program_code', isset($program) ? $program->program_code : '') }}" 
                                       placeholder="Text Box"
                                       required
                                       style="text-transform: uppercase;">
                                @error('program_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_name">Program Name<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('program_name') is-invalid @enderror" 
                                       id="program_name" 
                                       name="program_name" 
                                       value="{{ old('program_name', isset($program) ? $program->program_name : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('program_name')
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
                    @if(isset($program))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('superadmin.program.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th style="width: 5%">Sr. no.</th>
                            <th style="width: 15%">University</th>
                            <th style="width: 20%">Program Code</th>
                            <th style="width: 40%">Program Name</th>
                            <th style="width: 20%">Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $index => $program)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $program->university->university_name ?? 'N/A' }}</td>
                                <td>{{ $program->program_code }}</td>
                                <td>{{ $program->program_name }}</td>
                                <td>
                                    <a href="{{ route('superadmin.program.edit', $program->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No programs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('program_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection






