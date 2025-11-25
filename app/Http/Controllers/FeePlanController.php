<?php

namespace App\Http\Controllers;

use App\Models\FeePlan;
use App\Models\FeePlanItem;
use App\Models\FeePackage;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FeePlanController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the fee plan master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $feePlans = FeePlan::where('university_id', $universityId)
            ->with(['course', 'package', 'items.element'])
            ->orderBy('id', 'desc')
            ->get();
        
        $courses = Course::where('university_id', $universityId)
            ->orderBy('course_name')
            ->get();
        
        $feePackages = FeePackage::where('university_id', $universityId)
            ->where('status', true)
            ->with('items.element')
            ->orderBy('package_name')
            ->get();
        
        $categories = ['General', 'SC/ST', 'Differently Abled'];
        
        return view('university_admin.fee-plan', compact('feePlans', 'courses', 'feePackages', 'categories'));
    }

    /**
     * Store a newly created fee plan
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
            'course_type' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'required|exists:courses,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|in:General,SC/ST,Differently Abled',
            'package_id' => 'required|exists:fee_packages,id',
            'fee_items' => 'required|array',
            'fee_items.*.element_id' => 'required|exists:fee_elements,id',
            'fee_items.*.amount' => 'required|numeric|min:0',
            'fee_items.*.semester_no' => 'nullable|integer',
            'fee_items.*.installment_no' => 'required|integer|min:0',
        ], [
            'course_ids.required' => 'Please select at least one course.',
            'categories.required' => 'Please select at least one category.',
            'package_id.required' => 'Fee Package is required.',
            'fee_items.required' => 'Please add fee items.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $universityId) {
            $totalAmount = 0;
            foreach ($request->fee_items as $item) {
                $totalAmount += $item['amount'];
            }

            foreach ($request->course_ids as $courseId) {
                foreach ($request->categories as $category) {
                    $plan = FeePlan::create([
                        'university_id' => $universityId,
                        'course_id' => $courseId,
                        'category' => $category,
                        'package_id' => $request->package_id,
                        'total_amount' => $totalAmount,
                    ]);

                    foreach ($request->fee_items as $item) {
                        FeePlanItem::create([
                            'plan_id' => $plan->id,
                            'element_id' => $item['element_id'],
                            'amount' => $item['amount'],
                            'semester_no' => $item['semester_no'] ?? null,
                            'installment_no' => $item['installment_no'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('university.admin.fee.plan')
            ->with('success', 'Fee Plan created successfully.');
    }

    /**
     * Show the form for editing the specified fee plan
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $feePlan = FeePlan::where('university_id', $universityId)
            ->with(['course', 'package', 'items.element'])
            ->findOrFail($id);
        $feePlans = FeePlan::where('university_id', $universityId)
            ->with(['course', 'package', 'items.element'])
            ->orderBy('id', 'desc')
            ->get();
        
        $courses = Course::where('university_id', $universityId)
            ->orderBy('course_name')
            ->get();
        
        $feePackages = FeePackage::where('university_id', $universityId)
            ->where('status', true)
            ->with('items.element')
            ->orderBy('package_name')
            ->get();
        
        $categories = ['General', 'SC/ST', 'Differently Abled'];
        
        return view('university_admin.fee-plan', compact('feePlans', 'courses', 'feePackages', 'categories', 'feePlan'));
    }

    /**
     * Update the specified fee plan
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $feePlan = FeePlan::where('university_id', $universityId)
            ->findOrFail($id);

        // For update, we accept either single course_id or course_ids array
        $courseId = $request->course_id ?? (is_array($request->course_ids) && count($request->course_ids) > 0 ? $request->course_ids[0] : null);
        $category = $request->category ?? (is_array($request->categories) && count($request->categories) > 0 ? $request->categories[0] : null);

        $validator = Validator::make($request->all(), [
            'course_id' => 'nullable|exists:courses,id',
            'course_ids' => 'nullable|array',
            'category' => 'nullable|in:General,SC/ST,Differently Abled',
            'categories' => 'nullable|array',
            'package_id' => 'required|exists:fee_packages,id',
            'fee_items' => 'required|array',
            'fee_items.*.element_id' => 'required|exists:fee_elements,id',
            'fee_items.*.amount' => 'required|numeric|min:0',
            'fee_items.*.semester_no' => 'nullable|integer',
        ], [
            'package_id.required' => 'Fee Package is required.',
            'fee_items.required' => 'Please add fee items.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $feePlan, $courseId, $category) {
            $totalAmount = 0;
            foreach ($request->fee_items as $item) {
                $totalAmount += $item['amount'];
            }

            $feePlan->update([
                'course_id' => $courseId ?? $feePlan->course_id,
                'category' => $category ?? $feePlan->category,
                'package_id' => $request->package_id,
                'total_amount' => $totalAmount,
            ]);

            // Delete existing items
            FeePlanItem::where('plan_id', $feePlan->id)->delete();

            // Create new items
            $installmentNo = 0;
            foreach ($request->fee_items as $item) {
                FeePlanItem::create([
                    'plan_id' => $feePlan->id,
                    'element_id' => $item['element_id'],
                    'amount' => $item['amount'],
                    'semester_no' => $item['semester_no'] ?? null,
                    'installment_no' => $installmentNo++,
                ]);
            }
        });

        return redirect()->route('university.admin.fee.plan')
            ->with('success', 'Fee Plan updated successfully.');
    }
}
