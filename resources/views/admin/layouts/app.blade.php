<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ERP System') }} - @yield('title', 'Dashboard')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    @php
        $user = auth()->user();
        $roleColor = $user && $user->role ? ($user->role->role_color ?? '#1F8BFF') : '#1F8BFF';
        $roleHoverColor = $user && $user->role ? ($user->role->role_hover_color ?? '#1A7AE6') : '#1A7AE6';
    @endphp
    
    <style>
        :root {
            --sidebar-bg: {{ $roleColor }};
            --sidebar-hover: {{ $roleHoverColor }};
            --header-bg: {{ $roleColor }};
            --dashboard-card-border: {{ $roleColor }};
        }
        
        /* Sidebar Background */
        .main-sidebar {
            background-color: var(--sidebar-bg) !important;
        }
        
        /* Sidebar Hover Effects */
        .nav-sidebar .nav-link:hover,
        .nav-sidebar .nav-link.active {
            background-color: var(--sidebar-hover) !important;
            color: #fff !important;
        }
        
        /* Sidebar Active Link */
        .nav-sidebar .nav-link.active {
            background-color: var(--sidebar-hover) !important;
        }
        
        /* Header Background */
        .main-header {
            background-color: var(--header-bg) !important;
        }
        
        .main-header .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .main-header .navbar-nav .nav-link:hover {
            color: rgba(255, 255, 255, 1) !important;
        }
        
        .main-header .navbar-nav .nav-link .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }
        
        /* Content Header */
        .content-header {
            background-color: var(--header-bg) !important;
            color: #fff !important;
        }
        
        .content-header h1 {
            color: #fff !important;
        }
        
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .breadcrumb-item.active {
            color: rgba(255, 255, 255, 1) !important;
        }
        
        /* Card Border Colors */
        .card {
            border-top-color: var(--dashboard-card-border) !important;
        }
        
        .card-header {
            background-color: var(--header-bg) !important;
            color: #fff !important;
        }
        
        .card-header .card-title {
            color: #fff !important;
        }
        
        /* Brand Link */
        .brand-link {
            background-color: var(--sidebar-bg) !important;
        }
        
        /* User Panel */
        .user-panel .info a {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .user-panel .info small {
            color: rgba(255, 255, 255, 0.6) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <span class="badge badge-warning navbar-badge">{{ auth()->user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light">ERP System</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    <small class="text-muted">{{ auth()->user()->role->role_name ?? 'No Role' }}</small>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    @include('admin.partials.sidebar-menu')
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} ERP System.</strong>
        All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>

