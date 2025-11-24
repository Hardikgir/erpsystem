@extends('admin.layouts.app')

@section('title', 'Course Master')
@section('page-title', 'Course Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Course Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Course Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: Super Admin</strong></p>
                    </div>
                </div>
                <form action="{{ isset($course) ? route('superadmin.course.update', $course->id) : route('superadmin.course.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="course_code">Course Code<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('course_code') is-invalid @enderror" 
                                       id="course_code" name="course_code" 
                                       value="{{ old('course_code', isset($course) ? $course->course_code : '') }}" 
                                       placeholder="Text Box" required style="text-transform: uppercase;">
                                @error('course_code')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="course_name">Course Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('course_name') is-invalid @enderror" 
                                       id="course_name" name="course_name" 
                                       value="{{ old('course_name', isset($course) ? $course->course_name : '') }}" 
                                       placeholder="Text Box" required>
                                @error('course_name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="course_type">Course Type<span class="text-danger">*</span></label>
                                <select class="form-control @error('course_type') is-invalid @enderror" 
                                        id="course_type" name="course_type" required>
                                    <option value="">Select</option>
                                    <option value="Semester" {{ old('course_type', isset($course) ? $course->course_type : '') == 'Semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="Year" {{ old('course_type', isset($course) ? $course->course_type : '') == 'Year' ? 'selected' : '' }}>Year</option>
                                </select>
                                @error('course_type')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="course_duration">Duration<span class="text-danger">*</span></label>
                                <select class="form-control @error('course_duration') is-invalid @enderror" 
                                        id="course_duration" name="course_duration" required>
                                    <option value="">Select</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('course_duration', isset($course) ? $course->course_duration : '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('course_duration')
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_id">Program<span class="text-danger">*</span></label>
                                <select class="form-control @error('program_id') is-invalid @enderror" 
                                        id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs ?? [] as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id', isset($course) ? $course->program_id : '') == $program->id ? 'selected' : '' }}>
                                            {{ $program->program_name }} ({{ $program->university->university_name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="session_id">Session<span class="text-danger">*</span></label>
                                <select class="form-control @error('session_id') is-invalid @enderror" 
                                        id="session_id" name="session_id" required>
                                    <option value="">Select Session</option>
                                    @foreach($sessions ?? [] as $session)
                                        <option value="{{ $session->id }}" {{ old('session_id', isset($course) ? $course->session_id : '') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_label }} ({{ $session->university->university_name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('session_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @if(isset($course))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('superadmin.course.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $index => $course)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_name }}</td>
                                <td>{{ $course->course_type }}</td>
                                <td>{{ $course->course_duration }}</td>
                                <td>
                                    <a href="{{ route('superadmin.course.edit', $course->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">Update</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No courses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('course_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection

