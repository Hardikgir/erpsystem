<?php

namespace App\Http\Controllers;

use App\Models\FeePackage;
use App\Models\FeePackageItem;
use App\Models\FeeElement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FeePackageController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the fee package master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $feePackages = FeePackage::where('university_id', $universityId)
            ->with('items.element')
            ->orderBy('id', 'desc')
            ->get();
        
        $feeElements = FeeElement::where('university_id', $universityId)
            ->orderBy('element_name')
            ->get();
        
        return view('university_admin.fee-package', compact('feePackages', 'feeElements'));
    }

    /**
     * Store a newly created fee package
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
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selected_elements' => 'required|array|min:1',
            'selected_elements.*' => 'required|exists:fee_elements,id',
            'patterns' => 'required|array',
            'patterns.*' => 'required|in:Annual,Semester,Quarter,Monthly,One Time',
        ], [
            'package_name.required' => 'Package Name is required.',
            'selected_elements.required' => 'Please select at least one fee element.',
            'selected_elements.min' => 'Please select at least one fee element.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $universityId) {
            $package = FeePackage::create([
                'university_id' => $universityId,
                'package_name' => $request->package_name,
                'description' => $request->description,
                'status' => true,
            ]);

            foreach ($request->selected_elements as $index => $elementId) {
                FeePackageItem::create([
                    'package_id' => $package->id,
                    'element_id' => $elementId,
                    'pattern' => $request->patterns[$elementId] ?? 'One Time',
                ]);
            }
        });

        $redirectRoute = $request->input('action') === 'proceed_to_fee_plan' 
            ? route('university.admin.fee.plan')
            : route('university.admin.fee.package');

        return redirect($redirectRoute)
            ->with('success', 'Fee Package created successfully.');
    }

    /**
     * Show the form for editing the specified fee package
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $feePackage = FeePackage::where('university_id', $universityId)
            ->with('items.element')
            ->findOrFail($id);
        $feePackages = FeePackage::where('university_id', $universityId)
            ->with('items.element')
            ->orderBy('id', 'desc')
            ->get();
        
        $feeElements = FeeElement::where('university_id', $universityId)
            ->orderBy('element_name')
            ->get();
        
        return view('university_admin.fee-package', compact('feePackages', 'feeElements', 'feePackage'));
    }

    /**
     * Update the specified fee package
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $feePackage = FeePackage::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selected_elements' => 'required|array|min:1',
            'selected_elements.*' => 'required|exists:fee_elements,id',
            'patterns' => 'required|array',
            'patterns.*' => 'required|in:Annual,Semester,Quarter,Monthly,One Time',
        ], [
            'package_name.required' => 'Package Name is required.',
            'selected_elements.required' => 'Please select at least one fee element.',
            'selected_elements.min' => 'Please select at least one fee element.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($request, $feePackage) {
            $feePackage->update([
                'package_name' => $request->package_name,
                'description' => $request->description,
            ]);

            // Delete existing items
            FeePackageItem::where('package_id', $feePackage->id)->delete();

            // Create new items
            foreach ($request->selected_elements as $elementId) {
                FeePackageItem::create([
                    'package_id' => $feePackage->id,
                    'element_id' => $elementId,
                    'pattern' => $request->patterns[$elementId] ?? 'One Time',
                ]);
            }
        });

        return redirect()->route('university.admin.fee.package')
            ->with('success', 'Fee Package updated successfully.');
    }
}
