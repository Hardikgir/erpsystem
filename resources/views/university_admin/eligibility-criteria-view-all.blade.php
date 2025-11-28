@extends('university_admin.layouts.app')

@section('title', 'View Eligibility Criteria')
@section('page-title', 'View Eligibility Criteria')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('university.admin.eligibility.criteria') }}">Eligibility Criteria</a></li>
    <li class="breadcrumb-item active">View All</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title" style="color: white;">All Eligibility Criteria</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('university.admin.eligibility.criteria') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead style="background-color: #E3F2FD;">
                        <tr>
                            <th style="width: 5%">Sr. No.</th>
                            <th style="width: 15%">Program</th>
                            <th style="width: 15%">Course</th>
                            <th style="width: 10%">Semester/Year</th>
                            <th style="width: 10%">Category</th>
                            <th style="width: 10%">Gender</th>
                            <th style="width: 10%">Age Range</th>
                            <th style="width: 10%">Items Count</th>
                            <th style="width: 15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eligibilityCriteria as $index => $criteria)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $criteria->program->program_name }}</td>
                                <td>{{ $criteria->course->course_name }}</td>
                                <td>{{ $criteria->semester_year }}</td>
                                <td>{{ $criteria->category_id ?? 'N/A' }}</td>
                                <td>{{ ucfirst($criteria->gender) }}</td>
                                <td>
                                    @if($criteria->min_age || $criteria->max_age)
                                        {{ $criteria->min_age ?? 'N/A' }} - {{ $criteria->max_age ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $criteria->items->count() }}</td>
                                <td>
                                    <a href="{{ route('university.admin.eligibility.criteria.summary', $criteria->id) }}" 
                                       class="btn btn-sm btn-primary" style="background-color: #1F8BFF;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No eligibility criteria found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

