@php
    $user = auth()->user();
@endphp

<li class="nav-item">
    <a href="{{ route('university.admin.dashboard') }}" class="nav-link {{ request()->routeIs('university.admin.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-header">MASTER DATA</li>

<li class="nav-item">
    <a href="{{ route('university.admin.program.master') }}" class="nav-link {{ request()->routeIs('university.admin.program.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-graduation-cap"></i>
        <p>Program Master</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('university.admin.course.master') }}" class="nav-link {{ request()->routeIs('university.admin.course.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-book"></i>
        <p>Course Master</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('university.admin.college.master') }}" class="nav-link {{ request()->routeIs('university.admin.college.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>College Master</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('university.admin.role.master') }}" class="nav-link {{ request()->routeIs('university.admin.role.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-tag"></i>
        <p>Role Master</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('university.admin.session.master') }}" class="nav-link {{ request()->routeIs('university.admin.session.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>Session Master</p>
    </a>
</li>

