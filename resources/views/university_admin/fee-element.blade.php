@extends('university_admin.layouts.app')

@section('title', 'Fee Element')
@section('page-title', 'Fee Element')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Fee Element</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #FF8C00; color: white;">
                <h3 class="card-title" style="color: white;">Add Fee Element (University)</h3>
            </div>
            <div class="card-body">
                <form action="{{ isset($feeElement) ? route('university.admin.fee.element.update', $feeElement->id) : route('university.admin.fee.element.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="fee_element_name">Fee Element Name:<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('fee_element_name') is-invalid @enderror" 
                                       id="fee_element_name" 
                                       name="fee_element_name" 
                                       value="{{ old('fee_element_name', isset($feeElement) ? $feeElement->element_name : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('fee_element_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="pattern">Pattern:<span class="text-danger">*</span></label>
                                <select class="form-control @error('pattern') is-invalid @enderror" 
                                        id="pattern" 
                                        name="pattern" 
                                        required>
                                    <option value="">Select</option>
                                    <option value="Annual" {{ old('pattern', isset($feeElement) ? $feeElement->pattern : '') == 'Annual' ? 'selected' : '' }}>Annual</option>
                                    <option value="Semester" {{ old('pattern', isset($feeElement) ? $feeElement->pattern : '') == 'Semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="Quarter" {{ old('pattern', isset($feeElement) ? $feeElement->pattern : '') == 'Quarter' ? 'selected' : '' }}>Quarter</option>
                                    <option value="Monthly" {{ old('pattern', isset($feeElement) ? $feeElement->pattern : '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="One Time" {{ old('pattern', isset($feeElement) ? $feeElement->pattern : '') == 'One Time' ? 'selected' : '' }}>One Time</option>
                                </select>
                                @error('pattern')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block">
                                    SAVE
                                </button>
                            </div>
                        </div>
                    </div>
                    @if(isset($feeElement))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('university.admin.fee.element') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th>Sr. No.</th>
                            <th>Element Name</th>
                            <th>Pattern</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeElements as $index => $feeElement)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $feeElement->element_name }}</td>
                                <td>{{ $feeElement->pattern }}</td>
                                <td>
                                    <a href="{{ route('university.admin.fee.element.edit', $feeElement->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No fee elements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

