@extends('university_admin.layouts.app')

@section('title', 'Eligibility Criteria Summary')
@section('page-title', 'Eligibility Criteria Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('university.admin.eligibility.criteria') }}">Eligibility Criteria</a></li>
    <li class="breadcrumb-item active">Summary</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Eligibility Criteria Summary</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>Program & Course Details</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">Program</th>
                                <td>{{ $eligibilityCriteria->program->program_name }}</td>
                                <th style="width: 200px;">Course</th>
                                <td>{{ $eligibilityCriteria->course->course_name }}</td>
                            </tr>
                            <tr>
                                <th>Semester/Year</th>
                                <td>{{ $eligibilityCriteria->semester_year }}</td>
                                <th>Category</th>
                                <td>{{ $eligibilityCriteria->category_id ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>Gender & Age Eligibility</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">Gender</th>
                                <td>{{ ucfirst($eligibilityCriteria->gender) }}</td>
                                <th style="width: 200px;">Minimum Age</th>
                                <td>{{ $eligibilityCriteria->min_age ?? 'N/A' }}</td>
                                <th style="width: 200px;">Maximum Age</th>
                                <td>{{ $eligibilityCriteria->max_age ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>Minimum Qualification Requirements</h4>
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: #E3F2FD;">
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Minimum Qualification</th>
                                    <th>Minimum Marks (%)</th>
                                    <th>Board</th>
                                    <th>Competitive Exam</th>
                                    <th>Minimum Percentile</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($eligibilityCriteria->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->minimumQualification->qualification_name ?? 'N/A' }}</td>
                                        <td>{{ $item->min_marks ?? 'N/A' }}</td>
                                        <td>{{ $item->board->board_name ?? 'N/A' }}</td>
                                        <td>{{ $item->competitiveExam->exam_name ?? 'N/A' }}</td>
                                        <td>{{ $item->min_percentile ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No eligibility items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('university.admin.eligibility.criteria') }}" class="btn btn-primary" style="background-color: #1F8BFF;">
                            Add New Eligibility Criteria
                        </a>
                        <a href="{{ route('university.admin.eligibility.criteria.view') }}" class="btn btn-secondary">
                            View All Eligibility Criteria
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

