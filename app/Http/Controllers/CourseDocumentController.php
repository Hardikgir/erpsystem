<?php

namespace App\Http\Controllers;

use App\Models\CourseDocument;
use App\Models\CourseDocumentMapping;
use App\Models\Program;
use App\Models\Course;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseDocumentController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the course document mapping form
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        
        $programs = Program::where('university_id', $universityId)
            ->orderBy('program_name')
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
        
        return view('university_admin.course_document.index', compact('programs', 'sessions', 'courses'));
    }

    /**
     * Search for documents based on criteria (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'session_id' => 'required|exists:university_sessions,id',
            'domicile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $documents = CourseDocument::where('university_id', $universityId)
            ->where('program_id', $request->program_id)
            ->where('course_id', $request->course_id)
            ->where('session_id', $request->session_id)
            ->where('domicile', $request->domicile)
            ->orderBy('document_name')
            ->get();

        // Get existing mappings
        $existingMappings = CourseDocumentMapping::where('university_id', $universityId)
            ->where('program_id', $request->program_id)
            ->where('course_id', $request->course_id)
            ->where('session_id', $request->session_id)
            ->where('domicile', $request->domicile)
            ->pluck('document_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'mapped_document_ids' => $existingMappings
        ]);
    }

    /**
     * Store a new course document (AJAX)
     */
    public function store(Request $request): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'session_id' => 'required|exists:university_sessions,id',
            'domicile' => 'required|string',
            'document_name' => 'required|string|max:255',
        ], [
            'document_name.required' => 'Document Name is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that course belongs to selected program and university
        $course = Course::where('id', $request->course_id)
            ->where('program_id', $request->program_id)
            ->where('university_id', $universityId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Selected course does not belong to the selected program.'
            ], 422);
        }

        try {
            $document = CourseDocument::create([
                'university_id' => $universityId,
                'program_id' => $request->program_id,
                'course_id' => $request->course_id,
                'session_id' => $request->session_id,
                'domicile' => $request->domicile,
                'document_name' => $request->document_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document saved successfully.',
                'document' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a course document (AJAX)
     */
    public function destroy($id): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $document = CourseDocument::where('university_id', $universityId)
            ->findOrFail($id);
        
        try {
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses by program (AJAX)
     */
    public function getCourses($programId): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $courses = Course::where('program_id', $programId)
            ->where('university_id', $universityId)
            ->orderBy('course_name')
            ->get(['id', 'course_name']);
        
        return response()->json($courses);
    }

    /**
     * Get sessions by program (AJAX)
     */
    public function getSessions($programId): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        // Sessions are university-wide, not program-specific
        // But we'll filter by university_id
        $sessions = Session::where('university_id', $universityId)
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get(['id', 'session_label']);
        
        return response()->json($sessions);
    }

    /**
     * Submit mapped documents for registration form (AJAX)
     */
    public function submitMappedDocuments(Request $request): JsonResponse
    {
        $universityId = $this->getUniversityId();
        
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'session_id' => 'required|exists:university_sessions,id',
            'domicile' => 'required|string',
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:course_documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that course belongs to selected program and university
        $course = Course::where('id', $request->course_id)
            ->where('program_id', $request->program_id)
            ->where('university_id', $universityId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Selected course does not belong to the selected program.'
            ], 422);
        }

        try {
            // Delete existing mappings for this combination
            CourseDocumentMapping::where('university_id', $universityId)
                ->where('program_id', $request->program_id)
                ->where('course_id', $request->course_id)
                ->where('session_id', $request->session_id)
                ->where('domicile', $request->domicile)
                ->delete();

            // Insert new mappings
            $mappings = [];
            foreach ($request->document_ids as $documentId) {
                // Verify document belongs to the same combination
                $document = CourseDocument::where('id', $documentId)
                    ->where('university_id', $universityId)
                    ->where('program_id', $request->program_id)
                    ->where('course_id', $request->course_id)
                    ->where('session_id', $request->session_id)
                    ->where('domicile', $request->domicile)
                    ->first();

                if ($document) {
                    $mappings[] = [
                        'university_id' => $universityId,
                        'program_id' => $request->program_id,
                        'course_id' => $request->course_id,
                        'session_id' => $request->session_id,
                        'domicile' => $request->domicile,
                        'document_id' => $documentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($mappings)) {
                CourseDocumentMapping::insert($mappings);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documents successfully mapped!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to map documents: ' . $e->getMessage()
            ], 500);
        }
    }
}
