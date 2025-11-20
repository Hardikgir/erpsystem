@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@php
    $user = auth()->user();
    $roleName = $user->role->role_name ?? 'No Role';
@endphp

<!-- Welcome Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Welcome to ERP System</h3>
            </div>
            <div class="card-body">
                <p>Welcome, <strong>{{ $user->name }}</strong>!</p>
                <p>You are logged in as <strong>{{ ucfirst(str_replace('_', ' ', $roleName)) }}</strong>.</p>
                @if($user->isSuperAdmin())
                    <p class="text-success">You have Super Admin privileges and full system access.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($user->isSuperAdmin())
    @include('admin.dashboard')
@else
    <!-- Role-Specific Dashboard Content -->
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $accessibleModulesCount ?? 0 }}</h3>
                    <p>My Modules</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="small-box-footer">
                    <span>Accessible Modules</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $accessibleSubModulesCount ?? 0 }}</h3>
                    <p>My Sub Modules</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="small-box-footer">
                    <span>Accessible Sub Modules</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $modules->sum(function($m) { return $m->subModules->count(); }) }}</h3>
                    <p>Total Access</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="small-box-footer">
                    <span>Total Permissions</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \Carbon\Carbon::now()->format('d') }}</h3>
                    <p>Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="small-box-footer">
                    <span>{{ \Carbon\Carbon::now()->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Module Distribution Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Module Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="moduleChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Sub Module Access Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Sub Module Access
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="subModuleChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        My Accessible Modules
                    </h3>
                </div>
                <div class="card-body">
                    @if($modules && $modules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Module Name</th>
                                        <th>Icon</th>
                                        <th>Sub Modules</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td><strong>{{ $module->module_name }}</strong></td>
                                            <td><i class="{{ $module->icon ?? 'fas fa-circle' }}"></i></td>
                                            <td>
                                                @if($module->subModules->count() > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($module->subModules as $subModule)
                                                            <li>
                                                                <i class="fas fa-arrow-right text-primary mr-1"></i>
                                                                {{ $subModule->sub_module_name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No sub modules</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($module->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            You don't have access to any modules yet. Please contact your administrator.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
@if(!$user->isSuperAdmin() && $modules && $modules->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Module Distribution Pie Chart
    var moduleCtx = document.getElementById('moduleChart');
    if (moduleCtx) {
        moduleCtx = moduleCtx.getContext('2d');
        var moduleData = @json($modules->map(function($m) { return $m->module_name; })->toArray());
        var moduleSubCounts = @json($modules->map(function($m) { return $m->subModules->count(); })->toArray());
        
        var moduleChart = new Chart(moduleCtx, {
            type: 'pie',
            data: {
                labels: moduleData,
                datasets: [{
                    data: moduleSubCounts,
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#20c997',
                        '#fd7e14',
                        '#e83e8c'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Modules by Sub Module Count'
                    }
                }
            }
        });

        // Sub Module Access Bar Chart
        var subModuleCtx = document.getElementById('subModuleChart');
        if (subModuleCtx) {
            subModuleCtx = subModuleCtx.getContext('2d');
            var subModuleLabels = [];
            var subModuleData = [];
            
            @foreach($modules as $module)
                @foreach($module->subModules as $subModule)
                    subModuleLabels.push('{{ $subModule->sub_module_name }}');
                    subModuleData.push(1);
                @endforeach
            @endforeach
            
            var subModuleChart = new Chart(subModuleCtx, {
                type: 'bar',
                data: {
                    labels: subModuleLabels,
                    datasets: [{
                        label: 'Access Count',
                        data: subModuleData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Sub Module Access Overview'
                        }
                    }
                }
            });
        }
    }
</script>
@endif
@endpush
@endsection
