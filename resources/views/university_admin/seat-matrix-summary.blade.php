@extends('university_admin.layouts.app')

@section('title', 'Seat Matrix Summary')
@section('page-title', 'Seat Matrix Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('university.admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('university.admin.seat.matrix') }}">Seat Matrix</a></li>
    <li class="breadcrumb-item active">Summary</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Seat Matrix Summary</h3>
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

                <!-- Program / Course / College Details -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Program</span>
                                <span class="info-box-number">{{ $seatMatrix->program->program_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-book"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Course</span>
                                <span class="info-box-number">{{ $seatMatrix->course->course_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">College</span>
                                <span class="info-box-number">{{ $seatMatrix->college->college_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Session Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Academic Session</h3>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><strong>{{ $seatMatrix->academicSession->session_label ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Admission Session</h3>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><strong>{{ $seatMatrix->admissionSession->session_label ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admission Details -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Admission Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Mode:</strong></p>
                                        <p>
                                            @if(is_array($seatMatrix->mode))
                                                @foreach($seatMatrix->mode as $mode)
                                                    <span class="badge badge-primary">{{ ucfirst($mode) }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-primary">{{ ucfirst($seatMatrix->mode) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Start Date:</strong></p>
                                        <p>{{ $seatMatrix->start_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>End Date:</strong></p>
                                        <p>{{ $seatMatrix->end_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Publish Mode:</strong></p>
                                        <p>
                                            <span class="badge badge-{{ $seatMatrix->publish_mode == 'public' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($seatMatrix->publish_mode) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Seats -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Total Seats</h3>
                            </div>
                            <div class="card-body">
                                <h2 class="mb-0">{{ number_format($seatMatrix->total_seats) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category-wise Allocation -->
                @if($seatMatrix->define_category == 'yes' && $seatMatrix->categories->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Category-wise Seat Allocation</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Direct</th>
                                            <th>Counselling</th>
                                            <th>Merit</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seatMatrix->categories as $category)
                                            <tr>
                                                <td><strong>{{ $category->category_name }}</strong></td>
                                                <td>{{ number_format($category->direct_seats) }}</td>
                                                <td>{{ number_format($category->counselling_seats) }}</td>
                                                <td>{{ number_format($category->merit_seats) }}</td>
                                                <td><strong>{{ number_format($category->total_seats) }}</strong></td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-info">
                                            <td><strong>GRAND TOTAL</strong></td>
                                            <td><strong>{{ number_format($seatMatrix->categories->sum('direct_seats')) }}</strong></td>
                                            <td><strong>{{ number_format($seatMatrix->categories->sum('counselling_seats')) }}</strong></td>
                                            <td><strong>{{ number_format($seatMatrix->categories->sum('merit_seats')) }}</strong></td>
                                            <td><strong>{{ number_format($seatMatrix->categories->sum('total_seats')) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('university.admin.seat.matrix') }}" class="btn btn-primary" style="background-color: #1F8BFF;">
                            Create New Seat Matrix
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


