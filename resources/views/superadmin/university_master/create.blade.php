@extends('admin.layouts.app')

@section('title', 'Add University')
@section('page-title', 'Add University')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('university.master') }}">University Master</a></li>
    <li class="breadcrumb-item active">Add University</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add University</h3>
            </div>
            <form action="{{ route('university.master.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="university_code">University Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('university_code') is-invalid @enderror" 
                               id="university_code" 
                               name="university_code" 
                               value="{{ old('university_code') }}" 
                               placeholder="Enter University Code"
                               required
                               style="text-transform: uppercase;">
                        @error('university_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="university_name">University Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('university_name') is-invalid @enderror" 
                               id="university_name" 
                               name="university_name" 
                               value="{{ old('university_name') }}" 
                               placeholder="Enter University Name"
                               required>
                        @error('university_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <a href="{{ route('university.master') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-uppercase university code
    document.getElementById('university_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection

