@extends('university_admin.layouts.app')

@section('title', 'University Admin Dashboard')
@section('page-title', 'University Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color: #1F8BFF; color: white;">
                <h3 class="card-title" style="color: white;">Welcome, University Admin</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Welcome, <strong>{{ $user->name }}</strong>!</h4>
                        <p>You are logged in as <strong>{{ $user->role->role_name ?? 'University Admin' }}</strong>.</p>
                    </div>
                </div>

                @if($university)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-university"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">University Name</span>
                                    <span class="info-box-number">{{ $university->university_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-code"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">University Code</span>
                                    <span class="info-box-number">{{ $university->university_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($university->url)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-link"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">University URL</span>
                                        <span class="info-box-number">
                                            <a href="{{ $university->url }}" target="_blank">{{ $university->url }}</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No university assigned to this admin account.
                    </div>
                @endif

                @if($modules && $modules->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Assigned Modules</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($modules as $module)
                                            <div class="col-md-4 mb-3">
                                                <div class="card card-primary card-outline">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            <i class="{{ $module->icon ?? 'fas fa-cube' }}"></i>
                                                            {{ $module->module_name }}
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($module->subModules && $module->subModules->count() > 0)
                                                            <p class="mb-2"><strong>Sub Modules:</strong></p>
                                                            <ul class="list-unstyled">
                                                                @foreach($module->subModules as $subModule)
                                                                    <li>
                                                                        <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                                                        {{ $subModule->sub_module_name }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted">No sub-modules assigned</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> No modules assigned yet. Please contact Super Admin to assign modules.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

