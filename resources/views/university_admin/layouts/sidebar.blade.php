@php
    $user = auth()->user();
    $modules = $user->getAccessibleModules();
@endphp

<li class="nav-item">
    <a href="{{ route('university.admin.dashboard') }}" class="nav-link {{ request()->routeIs('university.admin.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

@if($modules && $modules->count() > 0)
    <li class="nav-header">MY MODULES</li>
    
    @foreach($modules as $module)
        @php
            $subModules = $module->subModules;
        @endphp

        @if($subModules && $subModules->count() > 0)
            @php
                $validSubModules = $subModules->filter(function($subModule) {
                    return $subModule->hasValidRoute();
                });
            @endphp
            
            @if($subModules->count() > 0)
                <li class="nav-item {{ $subModules->count() > 1 ? 'has-treeview' : '' }}">
                    @if($subModules->count() == 1)
                        @php $singleSubModule = $subModules->first(); @endphp
                        @if($singleSubModule->hasValidRoute())
                            <a href="{{ route($singleSubModule->route) }}" class="nav-link {{ request()->routeIs($singleSubModule->route) ? 'active' : '' }}">
                                <i class="nav-icon {{ $module->icon ?? 'fas fa-circle' }}"></i>
                                <p>{{ $module->module_name }}</p>
                            </a>
                        @else
                            <a href="#" class="nav-link" onclick="return false;" title="Route not configured: {{ $singleSubModule->route }}">
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
                            @foreach($subModules as $subModule)
                                <li class="nav-item">
                                    @if($subModule->hasValidRoute())
                                        <a href="{{ route($subModule->route) }}" class="nav-link {{ request()->routeIs($subModule->route) ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>{{ $subModule->sub_module_name }}</p>
                                        </a>
                                    @else
                                        <a href="#" class="nav-link text-muted" onclick="return false;" title="Route not configured: {{ $subModule->route }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>{{ $subModule->sub_module_name }} <small class="text-danger">(No Route)</small></p>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endif
    @endforeach
@endif

