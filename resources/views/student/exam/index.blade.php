@extends('admin.layouts.app')

@section('title', 'Exam Details')
@section('page-title', 'Exam Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Exam Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book mr-2"></i>
                    Exam Details
                </h3>
            </div>
            <div class="card-body">
                <h1>Exam Details</h1>
                <p class="lead">Welcome to the Exam Details page!</p>
                <p>This page is accessible only to students with the appropriate permissions.</p>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> This is a placeholder page. You can add exam details, schedules, results, and other exam-related information here.
                </div>

                <!-- Example Exam Details Table -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No exam details available at the moment.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

