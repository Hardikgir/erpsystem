<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\UniversityRole;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class UniversityRoleMasterController extends Controller
{
    /**
     * Display the university role master page (all roles from all universities)
     */
    public function index(): View
    {
        $roles = UniversityRole::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.role-master', compact('roles', 'universities'));
    }

    /**
     * Store a newly created university role
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'role_code' => 'required|string|max:255',
            'role_name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check uniqueness within university
        $exists = UniversityRole::where('university_id', $request->university_id)
            ->where('role_code', strtoupper($request->role_code))
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['role_code' => 'Role Code already exists for this university.'])
                ->withInput();
        }

        UniversityRole::create([
            'university_id' => $request->university_id,
            'role_code' => strtoupper($request->role_code),
            'role_name' => $request->role_name,
        ]);

        return redirect()->route('superadmin.universityrole.master')
            ->with('success', 'University Role created successfully.');
    }

    /**
     * Show the form for editing the specified university role
     */
    public function edit($id): View
    {
        $role = UniversityRole::with('university')->findOrFail($id);
        $roles = UniversityRole::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.role-master', compact('roles', 'role', 'universities'));
    }

    /**
     * Update the specified university role
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $role = UniversityRole::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role_code' => 'required|string|max:255',
            'role_name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check uniqueness within university (excluding current)
        $exists = UniversityRole::where('university_id', $request->university_id)
            ->where('role_code', strtoupper($request->role_code))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['role_code' => 'Role Code already exists for this university.'])
                ->withInput();
        }

        $role->update([
            'university_id' => $request->university_id,
            'role_code' => strtoupper($request->role_code),
            'role_name' => $request->role_name,
        ]);

        return redirect()->route('superadmin.universityrole.master')
            ->with('success', 'University Role updated successfully.');
    }
}

