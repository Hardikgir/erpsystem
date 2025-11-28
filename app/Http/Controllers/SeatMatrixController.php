<?php

namespace App\Http\Controllers;

use App\Models\SeatMatrix;
use App\Models\SeatMatrixCategory;
use App\Models\Program;
use App\Models\Course;
use App\Models\College;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SeatMatrixController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the seat matrix form
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        
        $programs = Program::where('university_id', $universityId)
            ->orderBy('program_name')
            ->get();
        
        $colleges = College::where('university_id', $universityId)
            ->orderBy('college_name')
            ->get();
        
        $sessions = Session::where('university_id', $universityId)
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get();
        
        // Load courses if program is selected in old input
        $courses = collect();
        if (old('program_id')) {
            $courses = Course::where('program_id', old('program_id'))
                ->where('university_id', $universityId)
                ->orderBy('course_name')
                ->get();
        }
        
        return view('university_admin.seat-matrix', compact('programs', 'colleges', 'sessions', 'courses'));
    }

    /**
     * Store a newly created seat matrix
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
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'college_id' => 'required|exists:colleges,id',
            'academic_session_id' => 'required|exists:university_sessions,id',
            'admission_session_id' => 'required|exists:university_sessions,id',
            'mode' => 'required|array|min:1',
            'mode.*' => 'in:direct,counselling,merit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'publish_mode' => 'required|in:public,private',
            'total_seats' => 'required|integer|min:0',
            'define_category' => 'required|in:yes,no',
            'categories' => 'required_if:define_category,yes|array',
            'categories.*.category_name' => 'required_with:define_category|in:GENERAL,OBC,SC,ST',
            'categories.*.direct_seats' => 'required_with:define_category|integer|min:0',
            'categories.*.counselling_seats' => 'required_with:define_category|integer|min:0',
            'categories.*.merit_seats' => 'required_with:define_category|integer|min:0',
        ], [
            'program_id.required' => 'Program is required.',
            'course_id.required' => 'Course is required.',
            'college_id.required' => 'College is required.',
            'academic_session_id.required' => 'Academic Session is required.',
            'admission_session_id.required' => 'Admission Session is required.',
            'mode.required' => 'Please select at least one mode.',
            'start_date.required' => 'Start Date is required.',
            'end_date.required' => 'End Date is required.',
            'end_date.after_or_equal' => 'End Date must be after or equal to Start Date.',
            'publish_mode.required' => 'Publish Mode is required.',
            'total_seats.required' => 'Total number of seats is required.',
            'define_category.required' => 'Please select whether to define seats by category.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify that course belongs to selected program and university
        $course = Course::where('id', $request->course_id)
            ->where('program_id', $request->program_id)
            ->where('university_id', $universityId)
            ->first();

        if (!$course) {
            return redirect()->back()
                ->withErrors(['course_id' => 'Selected course does not belong to the selected program.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $seatMatrix = SeatMatrix::create([
                'university_id' => $universityId,
                'program_id' => $request->program_id,
                'course_id' => $request->course_id,
                'college_id' => $request->college_id,
                'academic_session_id' => $request->academic_session_id,
                'admission_session_id' => $request->admission_session_id,
                'mode' => $request->mode,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'publish_mode' => $request->publish_mode,
                'total_seats' => $request->total_seats,
                'define_category' => $request->define_category,
            ]);

            // If categories are defined, create category records
            if ($request->define_category === 'yes' && $request->has('categories')) {
                foreach ($request->categories as $categoryData) {
                    if (!empty($categoryData['category_name'])) {
                        $total = ($categoryData['direct_seats'] ?? 0) + 
                                 ($categoryData['counselling_seats'] ?? 0) + 
                                 ($categoryData['merit_seats'] ?? 0);
                        
                        SeatMatrixCategory::create([
                            'seat_matrix_id' => $seatMatrix->id,
                            'category_name' => $categoryData['category_name'],
                            'direct_seats' => $categoryData['direct_seats'] ?? 0,
                            'counselling_seats' => $categoryData['counselling_seats'] ?? 0,
                            'merit_seats' => $categoryData['merit_seats'] ?? 0,
                            'total_seats' => $total,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('university.admin.seat.matrix.summary', $seatMatrix->id)
                ->with('success', 'Seat Matrix created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create seat matrix: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the summary page for a seat matrix
     */
    public function summary($id): View
    {
        $universityId = $this->getUniversityId();
        
        $seatMatrix = SeatMatrix::with([
            'university',
            'program',
            'course',
            'college',
            'academicSession',
            'admissionSession',
            'categories'
        ])
        ->where('university_id', $universityId)
        ->findOrFail($id);
        
        return view('university_admin.seat-matrix-summary', compact('seatMatrix'));
    }
}

