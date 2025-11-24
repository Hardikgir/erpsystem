<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class CourseMasterController extends Controller
{
    /**
     * Display the course master page (all courses from all universities)
     */
    public function index(): View
    {
        $courses = Course::with(['program', 'session', 'university'])
            ->orderBy('id', 'desc')
            ->get();
        
        $programs = Program::with('university')->orderBy('program_name')->get();
        $sessions = Session::with('university')->orderBy('year', 'desc')->orderBy('session_type')->get();
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.course-master', compact('courses', 'programs', 'sessions', 'universities'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|in:Semester,Year',
            'course_duration' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:university_sessions,id',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get university_id from program
        $program = Program::findOrFail($request->program_id);
        $universityId = $program->university_id;

        // Check uniqueness within university
        $exists = Course::where('university_id', $universityId)
            ->where('course_code', strtoupper($request->course_code))
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['course_code' => 'Course Code already exists for this university.'])
                ->withInput();
        }

        Course::create([
            'university_id' => $universityId,
            'program_id' => $request->program_id,
            'session_id' => $request->session_id,
            'course_code' => strtoupper($request->course_code),
            'course_name' => $request->course_name,
            'course_type' => $request->course_type,
            'course_duration' => $request->course_duration,
        ]);

        return redirect()->route('superadmin.course.master')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit($id): View
    {
        $course = Course::with(['program', 'session', 'university'])->findOrFail($id);
        $courses = Course::with(['program', 'session', 'university'])
            ->orderBy('id', 'desc')
            ->get();
        
        $programs = Program::with('university')->orderBy('program_name')->get();
        $sessions = Session::with('university')->orderBy('year', 'desc')->orderBy('session_type')->get();
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.course-master', compact('courses', 'programs', 'sessions', 'course', 'universities'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|in:Semester,Year',
            'course_duration' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:university_sessions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get university_id from program
        $program = Program::findOrFail($request->program_id);
        $universityId = $program->university_id;

        // Check uniqueness within university (excluding current)
        $exists = Course::where('university_id', $universityId)
            ->where('course_code', strtoupper($request->course_code))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['course_code' => 'Course Code already exists for this university.'])
                ->withInput();
        }

        $course->update([
            'university_id' => $universityId,
            'program_id' => $request->program_id,
            'session_id' => $request->session_id,
            'course_code' => strtoupper($request->course_code),
            'course_name' => $request->course_name,
            'course_type' => $request->course_type,
            'course_duration' => $request->course_duration,
        ]);

        return redirect()->route('superadmin.course.master')
            ->with('success', 'Course updated successfully.');
    }
}

