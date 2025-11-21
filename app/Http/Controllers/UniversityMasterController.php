<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class UniversityMasterController extends Controller
{
    /**
     * Display a listing of universities
     */
    public function index(): View
    {
        $universities = University::orderBy('id', 'desc')->paginate(10);
        
        return view('superadmin.university_master.index', compact('universities'));
    }

    /**
     * Show the form for creating a new university
     */
    public function create(): View
    {
        return view('superadmin.university_master.create');
    }

    /**
     * Store a newly created university
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'university_code' => 'required|string|max:255|unique:universities,university_code',
            'university_name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [
            'university_code.required' => 'University Code is required.',
            'university_code.unique' => 'University Code already exists.',
            'university_name.required' => 'University Name is required.',
            'status.required' => 'Status is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('university.master.create')
                ->withErrors($validator)
                ->withInput();
        }

        University::create([
            'university_code' => strtoupper($request->university_code),
            'university_name' => $request->university_name,
            'status' => (bool) $request->status,
        ]);

        return redirect()->route('university.master')
            ->with('success', 'University created successfully.');
    }

    /**
     * Display the specified university
     */
    public function view($id): View
    {
        $university = University::findOrFail($id);
        
        return view('superadmin.university_master.view', compact('university'));
    }

    /**
     * Show the form for editing the specified university
     */
    public function edit($id): View
    {
        $university = University::findOrFail($id);
        
        return view('superadmin.university_master.edit', compact('university'));
    }

    /**
     * Update the specified university
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $university = University::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'university_code' => 'required|string|max:255|unique:universities,university_code,' . $id,
            'university_name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ], [
            'university_code.required' => 'University Code is required.',
            'university_code.unique' => 'University Code already exists.',
            'university_name.required' => 'University Name is required.',
            'status.required' => 'Status is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('university.master.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $university->update([
            'university_code' => strtoupper($request->university_code),
            'university_name' => $request->university_name,
            'status' => (bool) $request->status,
        ]);

        return redirect()->route('university.master')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Remove the specified university from storage
     */
    public function destroy($id): RedirectResponse
    {
        $university = University::findOrFail($id);
        $university->delete();

        return redirect()->route('university.master')
            ->with('success', 'University deleted successfully.');
    }
}
