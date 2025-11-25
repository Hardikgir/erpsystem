@extends('university_admin.layouts.app')

@section('title', 'Bank Master')
@section('page-title', 'Bank Master')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Bank Master</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="background-color: #FF8C00; color: white;">
                <h3 class="card-title" style="color: white;">Add Bank (University)</h3>
            </div>
            <div class="card-body">
                <form action="{{ isset($bank) ? route('university.admin.bank.update', $bank->id) : route('university.admin.bank.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="bank_name">Bank Name:<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" 
                                       name="bank_name" 
                                       value="{{ old('bank_name', isset($bank) ? $bank->bank_name : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('bank_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="account_no">Account No.:<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('account_no') is-invalid @enderror" 
                                       id="account_no" 
                                       name="account_no" 
                                       value="{{ old('account_no', isset($bank) ? $bank->account_no : '') }}" 
                                       placeholder="Text Box"
                                       required>
                                @error('account_no')
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
                    @if(isset($bank))
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('university.admin.bank.master') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
                            <th>Bank Name</th>
                            <th>Account No</th>
                            <th>Edit/Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banks as $index => $bank)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->account_no }}</td>
                                <td>
                                    <a href="{{ route('university.admin.bank.edit', $bank->id) }}" class="btn btn-link" style="color: #1F8BFF; text-decoration: underline;">
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No banks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

