<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Module;
use App\Models\SubModule;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return view('admin.dashboard');
        }
        
        // For regular users, show role-specific dashboard
        $modules = $user->getAccessibleModules();
        $accessibleModulesCount = $modules->count();
        $accessibleSubModulesCount = $modules->sum(function($module) {
            return $module->subModules->count();
        });
        
        return view('dashboard', compact('modules', 'accessibleModulesCount', 'accessibleSubModulesCount'));
    }
}
