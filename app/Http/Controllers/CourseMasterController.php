<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use App\Models\Session;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseMasterController extends Controller
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
     * Display the course master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $courses = Course::where('university_id', $universityId)
            ->with(['program', 'session'])
            ->orderBy('id', 'desc')
            ->get();
        
        $programs = Program::where('university_id', $universityId)->get();
        $sessions = Session::where('university_id', $universityId)->get();
        
        return view('university_admin.course-master', compact('courses', 'programs', 'sessions'));
    }

    /**
     * Store a newly created course
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
            'course_code' => 'required|string|max:255|unique:courses,course_code,NULL,id,university_id,' . $universityId,
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|in:Semester,Yearly',
            'course_duration' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
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

        return redirect()->route('university.admin.course.master')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $course = Course::where('university_id', $universityId)
            ->findOrFail($id);
        $courses = Course::where('university_id', $universityId)
            ->with(['program', 'session'])
            ->orderBy('id', 'desc')
            ->get();
        
        $programs = Program::where('university_id', $universityId)->get();
        $sessions = Session::where('university_id', $universityId)->get();
        
        return view('university_admin.course-master', compact('courses', 'programs', 'sessions', 'course'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $course = Course::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $id . ',id,university_id,' . $universityId,
            'course_name' => 'required|string|max:255',
            'course_type' => 'required|in:Semester,Yearly',
            'course_duration' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course->update([
            'program_id' => $request->program_id,
            'session_id' => $request->session_id,
            'course_code' => strtoupper($request->course_code),
            'course_name' => $request->course_name,
            'course_type' => $request->course_type,
            'course_duration' => $request->course_duration,
        ]);

        return redirect()->route('university.admin.course.master')
            ->with('success', 'Course updated successfully.');
    }
}
