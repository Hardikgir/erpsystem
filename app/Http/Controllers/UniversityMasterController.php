<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UniversityMasterController extends Controller
{
    /**
     * Display the university master page with form and list
     */
    public function index(): View
    {
        $universities = University::orderBy('id', 'desc')->get();
        
        return view('superadmin.university-master', compact('universities'));
    }

    /**
     * Store a newly created university
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'university_code' => 'required|string|max:255|unique:universities,university_code|regex:/^[A-Z0-9]+$/',
            'university_name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
        ], [
            'university_code.required' => 'University Code is required.',
            'university_code.unique' => 'University Code already exists.',
            'university_code.regex' => 'University Code must be uppercase letters and numbers only.',
            'university_name.required' => 'University Name is required.',
            'url.url' => 'URL must be a valid URL.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate admin username
        $adminUsername = strtolower($request->university_code) . '_admin';
        $adminEmail = $adminUsername . '@erp.com';
        
        // Use default password 123456 for all universities
        $password = '123456';
        $hashedPassword = Hash::make($password);

        // Find or get University Admin role
        $role = Role::where('role_name', 'University Admin')
            ->orWhere('role_name', 'university_admin')
            ->first();
        
        if (!$role) {
            return redirect()->back()
                ->withErrors(['error' => 'University Admin role not found. Please create the role first.'])
                ->withInput();
        }

        // Check if user already exists
        $existingUser = User::where('email', $adminEmail)->first();
        
        if ($existingUser) {
            return redirect()->back()
                ->withErrors(['error' => 'Admin user already exists for this university code.'])
                ->withInput();
        }

        // Create admin user account
        $adminUser = User::create([
            'name' => $request->university_name . ' Admin',
            'email' => $adminEmail,
            'password' => $hashedPassword,
            'role_id' => $role->id,
            'status' => true,
        ]);

        // Create university with admin user reference
        $university = University::create([
            'university_code' => strtoupper($request->university_code),
            'university_name' => $request->university_name,
            'admin_username' => $adminUsername,
            'admin_user_id' => $adminUser->id,
            'admin_password_display' => '******', // Masked password for display
            'url' => $request->url,
            'password' => $hashedPassword,
            'status' => 'active',
        ]);

        return redirect()->route('university.master')
            ->with('success', 'University created successfully. Admin Username: ' . $adminUsername . ' (Email: ' . $adminEmail . '), Password: ' . $password);
    }

    /**
     * Show the form for editing the specified university
     */
    public function edit($id): View
    {
        $university = University::findOrFail($id);
        $universities = University::orderBy('id', 'desc')->get();
        
        return view('superadmin.university-master', compact('universities', 'university'));
    }

    /**
     * Update the specified university
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $university = University::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'university_code' => 'required|string|max:255|unique:universities,university_code,' . $id . '|regex:/^[A-Z0-9]+$/',
            'university_name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
        ], [
            'university_code.required' => 'University Code is required.',
            'university_code.unique' => 'University Code already exists.',
            'university_code.regex' => 'University Code must be uppercase letters and numbers only.',
            'university_name.required' => 'University Name is required.',
            'url.url' => 'URL must be a valid URL.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update admin username if university code changed
        $adminUsername = strtolower($request->university_code) . '_admin';
        $adminEmail = $adminUsername . '@erp.com';

        // Update university
        $university->update([
            'university_code' => strtoupper($request->university_code),
            'university_name' => $request->university_name,
            'admin_username' => $adminUsername,
            'url' => $request->url,
        ]);

        // Update admin user if exists
        if ($university->admin_user_id) {
            $adminUser = User::find($university->admin_user_id);
            if ($adminUser) {
                $adminUser->update([
                    'name' => $request->university_name . ' Admin',
                    'email' => $adminEmail,
                ]);
            }
        }

        return redirect()->route('university.master')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Display the specified university (View)
     */
    public function show($id): View
    {
        $university = University::with('adminUser')->findOrFail($id);
        $universities = University::orderBy('id', 'desc')->get();
        
        return view('superadmin.university-master', compact('universities', 'university'));
    }

    /**
     * Remove the specified university from storage
     */
    public function destroy($id): RedirectResponse
    {
        $university = University::findOrFail($id);
        
        // Delete associated admin user if exists
        if ($university->admin_user_id) {
            $adminUser = User::find($university->admin_user_id);
            if ($adminUser) {
                $adminUser->delete();
            }
        }
        
        // Delete university
        $university->delete();

        return redirect()->route('university.master')
            ->with('success', 'University deleted successfully.');
    }

    /**
     * Toggle university status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $university = University::findOrFail($id);
        
        $newStatus = $university->status === 'active' ? 'inactive' : 'active';
        $university->status = $newStatus;
        $university->save();

        // Also update admin user status
        if ($university->admin_user_id) {
            $adminUser = User::find($university->admin_user_id);
            if ($adminUser) {
                $adminUser->status = $newStatus === 'active';
                $adminUser->save();
            }
        }

        return response()->json([
            'success' => true,
            'status' => $university->status,
            'message' => 'Status updated successfully.'
        ]);
    }
}
