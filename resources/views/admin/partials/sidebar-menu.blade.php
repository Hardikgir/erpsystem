@php
    $user = auth()->user();
    $modules = $user->getAccessibleModules();
@endphp

@if($user->isSuperAdmin())
    <!-- Super Admin Menu -->
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <li class="nav-header">SYSTEM MANAGEMENT</li>

    <li class="nav-item">
        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>Role Management</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.modules.index') }}" class="nav-link {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cube"></i>
            <p>Module Management</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.sub-modules.index') }}" class="nav-link {{ request()->routeIs('admin.sub-modules.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cubes"></i>
            <p>Sub Module Management</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>Permission Assignment (Legacy)</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('superadmin.permissions.manage') }}" class="nav-link {{ request()->routeIs('superadmin.permissions.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-shield-alt"></i>
            <p>Permission Management (Spatie)</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>User Management</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.role-colors.index') }}" class="nav-link {{ request()->routeIs('admin.role-colors.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-palette"></i>
            <p>Role Colors</p>
        </a>
    </li>

    <li class="nav-header">MASTER DATA</li>

    <li class="nav-item">
        <a href="{{ route('module.master') }}" class="nav-link {{ request()->routeIs('module.master.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Module Master</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('university.master') }}" class="nav-link {{ request()->routeIs('university.master.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-university"></i>
            <p>University Master</p>
        </a>
    </li>

    <li class="nav-header">UNIVERSITY MASTER DATA</li>

    <li class="nav-item">
        @can('program.master.view')
        <a href="{{ route('superadmin.program.master') }}" class="nav-link {{ request()->routeIs('superadmin.program.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-book"></i>
            <p>Program Master</p>
        </a>
        @endcan
    </li>

    <li class="nav-item">
        @can('course.master.view')
        <a href="{{ route('superadmin.course.master') }}" class="nav-link {{ request()->routeIs('superadmin.course.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>Course Master</p>
        </a>
        @endcan
    </li>

    <li class="nav-item">
        @can('college.master.view')
        <a href="{{ route('superadmin.college.master') }}" class="nav-link {{ request()->routeIs('superadmin.college.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-school"></i>
            <p>College Master</p>
        </a>
        @endcan
    </li>

    <li class="nav-item">
        @can('university_role.master.view')
        <a href="{{ route('superadmin.universityrole.master') }}" class="nav-link {{ request()->routeIs('superadmin.universityrole.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>University Role Master</p>
        </a>
        @endcan
    </li>

    <li class="nav-item">
        @can('session.master.view')
        <a href="{{ route('superadmin.session.master') }}" class="nav-link {{ request()->routeIs('superadmin.session.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-calendar"></i>
            <p>Session Master</p>
        </a>
        @endcan
    </li>
@elseif($user->isUniversityAdmin())
    <!-- University Admin Menu -->
    <li class="nav-item">
        <a href="{{ route('university.admin.dashboard') }}" class="nav-link {{ request()->routeIs('university.admin.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    @php
        // Get all modules with submodules and filter by Spatie permissions
        $allModules = \App\Models\Module::with('subModules')->where('status', true)->get();
        $visibleModules = collect();
        
        // Debug: Get user's roles and permissions
        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        foreach ($allModules as $module) {
            $visibleSubModules = collect();
            
            foreach ($module->subModules->where('status', true) as $subModule) {
                $routeName = trim($subModule->route);
                if (!empty($routeName)) {
                    $viewPermission = "{$routeName}.view";
                    
                    // Check if user has view permission using Spatie
                    if ($user->can($viewPermission)) {
                        $visibleSubModules->push($subModule);
                    }
                }
            }
            
            if ($visibleSubModules->isNotEmpty()) {
                $module->setRelation('subModules', $visibleSubModules);
                $visibleModules->push($module);
            }
        }
    @endphp

    @if($visibleModules && $visibleModules->count() > 0)
        <li class="nav-header">MY MODULES</li>
    @endif

    @foreach($visibleModules as $module)
        @php
            $subModules = $module->subModules;
            $validSubModules = $subModules->filter(function($subModule) {
                return $subModule->hasValidRoute();
            });
        @endphp

        @if($validSubModules->count() > 0)
            <li class="nav-item {{ $validSubModules->count() > 1 ? 'has-treeview' : '' }}">
                @if($validSubModules->count() == 1)
                    @php $singleSubModule = $validSubModules->first(); @endphp
                    @if($singleSubModule->hasValidRoute())
                        <a href="{{ route($singleSubModule->route) }}" class="nav-link {{ request()->routeIs($singleSubModule->route) ? 'active' : '' }}">
                            <i class="nav-icon {{ $module->icon ?? 'fas fa-circle' }}"></i>
                            <p>{{ $module->module_name }}</p>
                        </a>
                    @endif
                @else
                    <a href="#" class="nav-link">
                        <i class="nav-icon {{ $module->icon ?? 'fas fa-circle' }}"></i>
                        <p>
                            {{ $module->module_name }}
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach($validSubModules as $subModule)
                            <li class="nav-item">
                                @if($subModule->hasValidRoute())
                                    <a href="{{ route($subModule->route) }}" class="nav-link {{ request()->routeIs($subModule->route) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ $subModule->sub_module_name }}</p>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
@else
    <!-- Regular User Menu - Dynamic based on Spatie permissions -->
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    @php
        // Get all modules with submodules and filter by Spatie permissions
        $allModules = \App\Models\Module::with('subModules')->where('status', true)->get();
        $visibleModules = collect();
        
        foreach ($allModules as $module) {
            $visibleSubModules = collect();
            
            foreach ($module->subModules->where('status', true) as $subModule) {
                $routeName = $subModule->route;
                $viewPermission = "{$routeName}.view";
                
                // Check if user has view permission using Spatie
                if ($user->can($viewPermission)) {
                    $visibleSubModules->push($subModule);
                }
            }
            
            if ($visibleSubModules->isNotEmpty()) {
                $module->setRelation('subModules', $visibleSubModules);
                $visibleModules->push($module);
            }
        }
    @endphp

    @if($visibleModules && $visibleModules->count() > 0)
        <li class="nav-header">MY MODULES</li>
    @endif

    @foreach($visibleModules as $module)
        @php
            $subModules = $module->subModules;
            $validSubModules = $subModules->filter(function($subModule) {
                return $subModule->hasValidRoute();
            });
        @endphp

        @if($validSubModules->count() > 0)
            <li class="nav-item {{ $validSubModules->count() > 1 ? 'has-treeview' : '' }}">
                @if($validSubModules->count() == 1)
                    @php $singleSubModule = $validSubModules->first(); @endphp
                    @if($singleSubModule->hasValidRoute())
                        <a href="{{ route($singleSubModule->route) }}" class="nav-link {{ request()->routeIs($singleSubModule->route) ? 'active' : '' }}">
                            <i class="nav-icon {{ $module->icon ?? 'fas fa-circle' }}"></i>
                            <p>{{ $module->module_name }}</p>
                        </a>
                    @endif
                @else
                    <a href="#" class="nav-link">
                        <i class="nav-icon {{ $module->icon ?? 'fas fa-circle' }}"></i>
                        <p>
                            {{ $module->module_name }}
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach($validSubModules as $subModule)
                            <li class="nav-item">
                                @if($subModule->hasValidRoute())
                                    <a href="{{ route($subModule->route) }}" class="nav-link {{ request()->routeIs($subModule->route) ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ $subModule->sub_module_name }}</p>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
@endif

