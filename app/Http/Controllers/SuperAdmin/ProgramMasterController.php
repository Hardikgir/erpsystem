<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class ProgramMasterController extends Controller
{
    /**
     * Constructor - Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:program.master.view')->only('index');
        $this->middleware('permission:program.master.create')->only('store');
        $this->middleware('permission:program.master.edit')->only('edit', 'update');
    }

    /**
     * Display the program master page (all programs from all universities)
     */
    public function index(): View
    {
        $programs = Program::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.program-master', compact('programs', 'universities'));
    }

    /**
     * Store a newly created program
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'program_code' => 'required|string|max:255|unique:programs,program_code',
            'program_name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
        ], [
            'program_code.required' => 'Program Code is required.',
            'program_code.unique' => 'Program Code already exists.',
            'program_name.required' => 'Program Name is required.',
            'university_id.required' => 'University is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Program::create([
            'university_id' => $request->university_id,
            'program_code' => strtoupper($request->program_code),
            'program_name' => $request->program_name,
        ]);

        return redirect()->route('superadmin.program.master')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Show the form for editing the specified program
     */
    public function edit($id): View
    {
        $program = Program::with('university')->findOrFail($id);
        $programs = Program::with('university')
            ->orderBy('id', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.program-master', compact('programs', 'program', 'universities'));
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $program = Program::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'program_code' => 'required|string|max:255|unique:programs,program_code,' . $id,
            'program_name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
        ], [
            'program_code.required' => 'Program Code is required.',
            'program_code.unique' => 'Program Code already exists.',
            'program_name.required' => 'Program Name is required.',
            'university_id.required' => 'University is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $program->update([
            'university_id' => $request->university_id,
            'program_code' => strtoupper($request->program_code),
            'program_name' => $request->program_name,
        ]);

        return redirect()->route('superadmin.program.master')
            ->with('success', 'Program updated successfully.');
    }
}

