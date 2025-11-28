<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Program;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseMasterController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
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
            'course_type' => 'required|in:Semester,Year',
            'course_duration' => 'required|integer|min:1|max:10',
            'program_id' => 'required|exists:programs,id',
            'session_id' => 'required|exists:university_sessions,id',
        ], [
            'course_type.in' => 'Course type must be either "Semester" or "Year".',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure course_type is exactly one of the valid values
        $courseType = trim($request->course_type);
        if (!in_array($courseType, ['Semester', 'Year'])) {
            return redirect()->back()
                ->withErrors(['course_type' => 'Invalid course type. Must be "Semester" or "Year".'])
                ->withInput();
        }

        try {
            Course::create([
                'university_id' => $universityId,
                'program_id' => $request->program_id,
                'session_id' => $request->session_id,
                'course_code' => strtoupper(trim($request->course_code)),
                'course_name' => trim($request->course_name),
                'course_type' => $courseType,
                'course_duration' => (int)$request->course_duration,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // If there's still a database error, try to fix the column and retry
            if (strpos($e->getMessage(), 'course_type') !== false || strpos($e->getMessage(), 'Data truncated') !== false) {
                try {
                    // First update any invalid values
                    DB::statement("UPDATE courses SET course_type = 'Semester' WHERE course_type NOT IN ('Semester', 'Year') OR course_type IS NULL");
                    
                    // Then fix the column - ensure exact match
                    DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
                    
                    // Retry the creation
                    Course::create([
                        'university_id' => $universityId,
                        'program_id' => $request->program_id,
                        'session_id' => $request->session_id,
                        'course_code' => strtoupper(trim($request->course_code)),
                        'course_name' => trim($request->course_name),
                        'course_type' => $courseType,
                        'course_duration' => (int)$request->course_duration,
                    ]);
                } catch (\Exception $retryException) {
                    \Log::error('Course creation error after fix attempt: ' . $retryException->getMessage());
                    return redirect()->back()
                        ->withErrors(['error' => 'Database error. Please run: php artisan fix:course-type-enum or contact administrator. Error: ' . $retryException->getMessage()])
                        ->withInput();
                }
            } else {
                return redirect()->back()
                    ->withErrors(['error' => 'Failed to create course: ' . $e->getMessage()])
                    ->withInput();
            }
        }

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

        $course->update([
            'program_id' => $request->program_id,
            'session_id' => $request->session_id,
            'course_code' => strtoupper(trim($request->course_code)),
            'course_name' => trim($request->course_name),
            'course_type' => trim($request->course_type), // Ensure trimmed value
            'course_duration' => (int)$request->course_duration,
        ]);

        return redirect()->route('university.admin.course.master')
            ->with('success', 'Course updated successfully.');
    }
}
