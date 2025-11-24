<?php

namespace App\Http\Controllers;

use App\Models\UniversityRole;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UniversityRoleMasterController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the role master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $roles = UniversityRole::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.role-master', compact('roles'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        
        if (!$universityId) {
            return redirect()->back()
                ->withErrors(['error' => 'University not found.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'role_code' => 'required|string|max:255|unique:university_roles,role_code,NULL,id,university_id,' . $universityId,
            'role_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        UniversityRole::create([
            'university_id' => $universityId,
            'role_code' => strtoupper($request->role_code),
            'role_name' => $request->role_name,
        ]);

        return redirect()->route('university.admin.role.master')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $role = UniversityRole::where('university_id', $universityId)
            ->findOrFail($id);
        $roles = UniversityRole::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.role-master', compact('roles', 'role'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $role = UniversityRole::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role_code' => 'required|string|max:255|unique:university_roles,role_code,' . $id . ',id,university_id,' . $universityId,
            'role_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'role_code' => strtoupper($request->role_code),
            'role_name' => $request->role_name,
        ]);

        return redirect()->route('university.admin.role.master')
            ->with('success', 'Role updated successfully.');
    }
}
