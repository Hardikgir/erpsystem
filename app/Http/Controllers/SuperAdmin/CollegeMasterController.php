<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class CollegeMasterController extends Controller
{
    /**
     * Display the college master page (all colleges from all universities)
     */
    public function index(): View
    {
        $colleges = College::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.college-master', compact('colleges', 'universities'));
    }

    /**
     * Store a newly created college
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'college_code' => 'required|string|max:255',
            'college_name' => 'required|string|max:255',
            'college_type' => 'required|in:Govt,Private',
            'establish_date' => 'required|date',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check uniqueness within university
        $exists = College::where('university_id', $request->university_id)
            ->where('college_code', strtoupper($request->college_code))
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['college_code' => 'College Code already exists for this university.'])
                ->withInput();
        }

        College::create([
            'university_id' => $request->university_id,
            'college_code' => strtoupper($request->college_code),
            'college_name' => $request->college_name,
            'college_type' => $request->college_type,
            'establish_date' => $request->establish_date,
        ]);

        return redirect()->route('superadmin.college.master')
            ->with('success', 'College created successfully.');
    }

    /**
     * Show the form for editing the specified college
     */
    public function edit($id): View
    {
        $college = College::with('university')->findOrFail($id);
        $colleges = College::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.college-master', compact('colleges', 'college', 'universities'));
    }

    /**
     * Update the specified college
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $college = College::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'college_code' => 'required|string|max:255',
            'college_name' => 'required|string|max:255',
            'college_type' => 'required|in:Govt,Private',
            'establish_date' => 'required|date',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check uniqueness within university (excluding current)
        $exists = College::where('university_id', $request->university_id)
            ->where('college_code', strtoupper($request->college_code))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['college_code' => 'College Code already exists for this university.'])
                ->withInput();
        }

        $college->update([
            'university_id' => $request->university_id,
            'college_code' => strtoupper($request->college_code),
            'college_name' => $request->college_name,
            'college_type' => $request->college_type,
            'establish_date' => $request->establish_date,
        ]);

        return redirect()->route('superadmin.college.master')
            ->with('success', 'College updated successfully.');
    }
}


