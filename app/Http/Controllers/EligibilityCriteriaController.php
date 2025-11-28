<?php

namespace App\Http\Controllers;

use App\Models\EligibilityCriteria;
use App\Models\EligibilityItem;
use App\Models\Program;
use App\Models\Course;
use App\Models\Board;
use App\Models\CompetitiveExam;
use App\Models\MinimumQualification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class EligibilityCriteriaController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the eligibility criteria form
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        
        $programs = Program::where('university_id', $universityId)
            ->orderBy('program_name')
            ->get();
        
        $boards = Board::where('university_id', $universityId)
            ->where('status', true)
            ->orderBy('board_name')
            ->get();
        
        $exams = CompetitiveExam::where('university_id', $universityId)
            ->where('status', true)
            ->orderBy('exam_name')
            ->get();
        
        $qualifications = MinimumQualification::where('university_id', $universityId)
            ->where('status', true)
            ->orderBy('qualification_name')
            ->get();
        
        // Load courses if program is selected in old input
        $courses = collect();
        if (old('program_id')) {
            $courses = Course::where('program_id', old('program_id'))
                ->where('university_id', $universityId)
                ->orderBy('course_name')
                ->get();
        }
        
        return view('university_admin.eligibility-criteria', compact(
            'programs',
            'boards',
            'exams',
            'qualifications',
            'courses'
        ));
    }

    /**
     * Store eligibility criteria
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
            'semester_year' => 'required|string',
            'category_id' => 'nullable|string',
            'gender' => 'required|in:male,female,both',
            'min_age' => 'nullable|integer|min:0|max:100',
            'max_age' => 'nullable|integer|min:0|max:100|gte:min_age',
            'items' => 'required|array|min:1',
            'items.*.min_qualification_id' => 'required_without:items.*.min_marks|nullable|exists:minimum_qualifications,id',
            'items.*.min_marks' => 'required_without:items.*.min_qualification_id|nullable|numeric|min:0|max:100',
            'items.*.board_id' => 'nullable|exists:boards,id',
            'items.*.exam_id' => 'nullable|exists:competitive_exams,id',
            'items.*.min_percentile' => 'nullable|numeric|min:0|max:100',
        ], [
            'program_id.required' => 'Program is required.',
            'course_id.required' => 'Course is required.',
            'semester_year.required' => 'Semester/Year is required.',
            'gender.required' => 'Gender eligibility is required.',
            'items.required' => 'At least one eligibility item is required.',
            'items.min' => 'At least one eligibility item is required.',
            'max_age.gte' => 'Maximum age must be greater than or equal to minimum age.',
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
            $eligibilityCriteria = EligibilityCriteria::create([
                'university_id' => $universityId,
                'program_id' => $request->program_id,
                'course_id' => $request->course_id,
                'semester_year' => $request->semester_year,
                'category_id' => $request->category_id,
                'gender' => $request->gender,
                'min_age' => $request->min_age,
                'max_age' => $request->max_age,
            ]);

            // Create eligibility items
            $itemsCreated = 0;
            foreach ($request->items as $itemData) {
                // Only create item if at least qualification or marks is provided
                if (!empty($itemData['min_qualification_id']) || !empty($itemData['min_marks'])) {
                    EligibilityItem::create([
                        'eligibility_id' => $eligibilityCriteria->id,
                        'min_qualification_id' => $itemData['min_qualification_id'] ?? null,
                        'min_marks' => $itemData['min_marks'] ?? null,
                        'board_id' => $itemData['board_id'] ?? null,
                        'exam_id' => $itemData['exam_id'] ?? null,
                        'min_percentile' => $itemData['min_percentile'] ?? null,
                    ]);
                    $itemsCreated++;
                }
            }
            
            // Ensure at least one item was created
            if ($itemsCreated === 0) {
                throw new \Exception('At least one eligibility item with qualification or marks is required.');
            }

            DB::commit();

            return redirect()->route('university.admin.eligibility.criteria.summary', $eligibilityCriteria->id)
                ->with('success', 'Eligibility Criteria saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to save eligibility criteria: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get courses by program (AJAX)
     */
    public function getCoursesByProgram($programId): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $courses = Course::where('program_id', $programId)
            ->where('university_id', $universityId)
            ->orderBy('course_name')
            ->get(['id', 'course_name', 'course_type', 'course_duration']);
        
        return response()->json($courses);
    }

    /**
     * Get semesters/years by course (AJAX)
     */
    public function getSemesterByCourse($courseId): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $course = Course::where('id', $courseId)
            ->where('university_id', $universityId)
            ->first(['id', 'course_type', 'course_duration']);
        
        if (!$course) {
            return response()->json([]);
        }
        
        $semesters = [];
        $type = $course->course_type; // 'Semester' or 'Year'
        $duration = $course->course_duration;
        
        for ($i = 1; $i <= $duration; $i++) {
            $semesters[] = [
                'value' => $type . ' ' . $i,
                'label' => $type . ' ' . $i,
            ];
        }
        
        return response()->json($semesters);
    }

    /**
     * Get categories by program (AJAX)
     */
    public function getCategoryByProgram($programId): JsonResponse
    {
        // Categories are fixed: GENERAL, OBC, SC, ST
        $categories = [
            ['value' => 'GENERAL', 'label' => 'General'],
            ['value' => 'OBC', 'label' => 'OBC'],
            ['value' => 'SC', 'label' => 'SC'],
            ['value' => 'ST', 'label' => 'ST'],
        ];
        
        return response()->json($categories);
    }

    /**
     * Display summary of saved eligibility criteria
     */
    public function summary($id): View
    {
        $universityId = $this->getUniversityId();
        
        $eligibilityCriteria = EligibilityCriteria::with([
            'university',
            'program',
            'course',
            'items.minimumQualification',
            'items.board',
            'items.competitiveExam'
        ])
        ->where('university_id', $universityId)
        ->findOrFail($id);
        
        return view('university_admin.eligibility-criteria-summary', compact('eligibilityCriteria'));
    }

    /**
     * View all eligibility criteria
     */
    public function viewAll(): View
    {
        $universityId = $this->getUniversityId();
        
        $eligibilityCriteria = EligibilityCriteria::with([
            'program',
            'course',
            'items'
        ])
        ->where('university_id', $universityId)
        ->orderBy('id', 'desc')
        ->get();
        
        return view('university_admin.eligibility-criteria-view-all', compact('eligibilityCriteria'));
    }
}
