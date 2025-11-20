@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\Role::count() }}</h3>
                <p>Total Roles</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-tag"></i>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\Module::count() }}</h3>
                <p>Total Modules</p>
            </div>
            <div class="icon">
                <i class="fas fa-cube"></i>
            </div>
            <a href="{{ route('admin.modules.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ \App\Models\SubModule::count() }}</h3>
                <p>Total Sub Modules</p>
            </div>
            <div class="icon">
                <i class="fas fa-cubes"></i>
            </div>
            <a href="{{ route('admin.sub-modules.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ \App\Models\User::count() }}</h3>
                <p>Total Users</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Welcome to ERP System</h3>
            </div>
            <div class="card-body">
                <p>Welcome, <strong>{{ auth()->user()->name }}</strong>!</p>
                <p>You are logged in as <strong>{{ auth()->user()->role->role_name ?? 'No Role' }}</strong>.</p>
                @if(auth()->user()->isSuperAdmin())
                    <p class="text-success">You have Super Admin privileges and full system access.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

