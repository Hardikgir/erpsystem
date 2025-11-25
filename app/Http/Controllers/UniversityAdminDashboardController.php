<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\University;

class UniversityAdminDashboardController extends Controller
{
    /**
     * Display the university admin dashboard
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get university associated with this admin user via university_id relationship
        $university = $user->university;
        
        // Get accessible modules for this user
        $modules = $user->getAccessibleModules();
        
        return view('university_admin.dashboard', compact('user', 'university', 'modules'));
    }
}
