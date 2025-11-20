<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProgramMasterController extends Controller
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
     * Display the program master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $programs = Program::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.program-master', compact('programs'));
    }

    /**
     * Store a newly created program
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
            'program_code' => 'required|string|max:255|unique:programs,program_code,NULL,id,university_id,' . $universityId,
            'program_name' => 'required|string|max:255',
        ], [
            'program_code.required' => 'Program Code is required.',
            'program_code.unique' => 'Program Code already exists for this university.',
            'program_name.required' => 'Program Name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Program::create([
            'university_id' => $universityId,
            'program_code' => strtoupper($request->program_code),
            'program_name' => $request->program_name,
        ]);

        return redirect()->route('university.admin.program.master')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Show the form for editing the specified program
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $program = Program::where('university_id', $universityId)
            ->findOrFail($id);
        $programs = Program::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.program-master', compact('programs', 'program'));
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $program = Program::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'program_code' => 'required|string|max:255|unique:programs,program_code,' . $id . ',id,university_id,' . $universityId,
            'program_name' => 'required|string|max:255',
        ], [
            'program_code.required' => 'Program Code is required.',
            'program_code.unique' => 'Program Code already exists for this university.',
            'program_name.required' => 'Program Name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $program->update([
            'program_code' => strtoupper($request->program_code),
            'program_name' => $request->program_name,
        ]);

        return redirect()->route('university.admin.program.master')
            ->with('success', 'Program updated successfully.');
    }
}
