@extends('admin.layouts.app')

@section('title', 'College Master')
@section('page-title', 'College Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">College Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">College Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: Super Admin</strong></p>
                    </div>
                </div>
                <form action="{{ isset($college) ? route('superadmin.college.update', $college->id) : route('superadmin.college.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="university_id">University<span class="text-danger">*</span></label>
                                <select class="form-control @error('university_id') is-invalid @enderror" 
                                        id="university_id" name="university_id" required>
                                    <option value="">Select University</option>
                                    @foreach($universities ?? [] as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id', isset($college) ? $college->university_id : '') == $university->id ? 'selected' : '' }}>
                                            {{ $university->university_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="college_code">College Code<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('college_code') is-invalid @enderror" 
                                       id="college_code" name="college_code" 
                                       value="{{ old('college_code', isset($college) ? $college->college_code : '') }}" 
                                       placeholder="Text Box" required style="text-transform: uppercase;">
                                @error('college_code')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="college_name">College Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('college_name') is-invalid @enderror" 
                                       id="college_name" name="college_name" 
                                       value="{{ old('college_name', isset($college) ? $college->college_name : '') }}" 
                                       placeholder="Text Box" required>
                                @error('college_name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="college_type">College Type<span class="text-danger">*</span></label>
                                <select class="form-control @error('college_type') is-invalid @enderror" 
                                        id="college_type" name="college_type" required>
                                    <option value="">Select</option>
                                    <option value="Govt" {{ old('college_type', isset($college) ? $college->college_type : '') == 'Govt' ? 'selected' : '' }}>Govt</option>
                                    <option value="Private" {{ old('college_type', isset($college) ? $college->college_type : '') == 'Private' ? 'selected' : '' }}>Private</option>
                                </select>
                                @error('college_type')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="establish_date">Establishment Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('establish_date') is-invalid @enderror" 
                                       id="establish_date" name="establish_date" 
                                       value="{{ old('establish_date', isset($college) ? $college->establish_date->format('Y-m-d') : '') }}" 
                                       required>
                                @error('establish_date')
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
                    @if(isset($college))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('superadmin.college.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th>College Code</th>
                            <th>College Name</th>
                            <th>College Type</th>
                            <th>Est. Date</th>
                            <th>Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($colleges as $index => $college)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $college->university->university_name ?? 'N/A' }}</td>
                                <td>{{ $college->college_code }}</td>
                                <td>{{ $college->college_name }}</td>
                                <td>{{ $college->college_type }}</td>
                                <td>{{ $college->establish_date->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.college.edit', $college->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">Update</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No colleges found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('college_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection



