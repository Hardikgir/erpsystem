<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CollegeMasterController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        $user = Auth::user();
        $university = University::where('admin_user_id', $user->id)->first();
        return $university ? $university->id : null;
    }

    /**
     * Display the college master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $colleges = College::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.college-master', compact('colleges'));
    }

    /**
     * Store a newly created college
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
            'college_code' => 'required|string|max:255|unique:colleges,college_code,NULL,id,university_id,' . $universityId,
            'college_name' => 'required|string|max:255',
            'college_type' => 'required|in:Govt,Private',
            'establish_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        College::create([
            'university_id' => $universityId,
            'college_code' => strtoupper($request->college_code),
            'college_name' => $request->college_name,
            'college_type' => $request->college_type,
            'establish_date' => $request->establish_date,
        ]);

        return redirect()->route('university.admin.college.master')
            ->with('success', 'College created successfully.');
    }

    /**
     * Show the form for editing the specified college
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $college = College::where('university_id', $universityId)
            ->findOrFail($id);
        $colleges = College::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.college-master', compact('colleges', 'college'));
    }

    /**
     * Update the specified college
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $college = College::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'college_code' => 'required|string|max:255|unique:colleges,college_code,' . $id . ',id,university_id,' . $universityId,
            'college_name' => 'required|string|max:255',
            'college_type' => 'required|in:Govt,Private',
            'establish_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $college->update([
            'college_code' => strtoupper($request->college_code),
            'college_name' => $request->college_name,
            'college_type' => $request->college_type,
            'establish_date' => $request->establish_date,
        ]);

        return redirect()->route('university.admin.college.master')
            ->with('success', 'College updated successfully.');
    }
}
