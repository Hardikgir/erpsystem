@extends('admin.layouts.app')

@section('title', 'Session Master')
@section('page-title', 'Session Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Session Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Session Master</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>User: Super Admin</strong></p>
                    </div>
                </div>
                <form action="{{ isset($session) ? route('superadmin.session.update', $session->id) : route('superadmin.session.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="university_id">University<span class="text-danger">*</span></label>
                                <select class="form-control @error('university_id') is-invalid @enderror" 
                                        id="university_id" name="university_id" required>
                                    <option value="">Select University</option>
                                    @foreach($universities ?? [] as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id', isset($session) ? $session->university_id : '') == $university->id ? 'selected' : '' }}>
                                            {{ $university->university_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('university_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="session_type">Session Type<span class="text-danger">*</span></label>
                                <select class="form-control @error('session_type') is-invalid @enderror" 
                                        id="session_type" name="session_type" required>
                                    <option value="">Select</option>
                                    <option value="jul-dec" {{ old('session_type', isset($session) ? $session->session_type : '') == 'jul-dec' ? 'selected' : '' }}>Jul-Dec</option>
                                    <option value="jan-jun" {{ old('session_type', isset($session) ? $session->session_type : '') == 'jan-jun' ? 'selected' : '' }}>Jan-Jun</option>
                                </select>
                                @error('session_type')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Year<span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                       id="year" name="year" 
                                       value="{{ old('year', isset($session) ? $session->year : '') }}" 
                                       placeholder="Text Box" min="2000" max="2100" required>
                                @error('year')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block" style="background-color: #1F8BFF;">SAVE</button>
                            </div>
                        </div>
                    </div>
                    @if(isset($session))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('superadmin.session.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th>Session Label</th>
                            <th>Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $session->university->university_name ?? 'N/A' }}</td>
                                <td>{{ $session->session_label }}</td>
                                <td>
                                    <a href="{{ route('superadmin.session.edit', $session->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">Update</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No sessions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

