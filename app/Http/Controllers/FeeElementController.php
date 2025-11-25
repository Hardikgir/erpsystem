<?php

namespace App\Http\Controllers;

use App\Models\FeeElement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeeElementController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the fee element master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $feeElements = FeeElement::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.fee-element', compact('feeElements'));
    }

    /**
     * Store a newly created fee element
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
            'fee_element_name' => 'required|string|max:255|unique:fee_elements,element_name,NULL,id,university_id,' . $universityId,
            'pattern' => 'required|in:Annual,Semester,Quarter,Monthly,One Time',
        ], [
            'fee_element_name.required' => 'Fee Element Name is required.',
            'fee_element_name.unique' => 'Fee Element Name already exists for this university.',
            'pattern.required' => 'Pattern is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        FeeElement::create([
            'university_id' => $universityId,
            'element_name' => $request->fee_element_name,
            'pattern' => $request->pattern,
        ]);

        return redirect()->route('university.admin.fee.element')
            ->with('success', 'Fee Element created successfully.');
    }

    /**
     * Show the form for editing the specified fee element
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $feeElement = FeeElement::where('university_id', $universityId)
            ->findOrFail($id);
        $feeElements = FeeElement::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.fee-element', compact('feeElements', 'feeElement'));
    }

    /**
     * Update the specified fee element
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $feeElement = FeeElement::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fee_element_name' => 'required|string|max:255|unique:fee_elements,element_name,' . $id . ',id,university_id,' . $universityId,
            'pattern' => 'required|in:Annual,Semester,Quarter,Monthly,One Time',
        ], [
            'fee_element_name.required' => 'Fee Element Name is required.',
            'fee_element_name.unique' => 'Fee Element Name already exists for this university.',
            'pattern.required' => 'Pattern is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $feeElement->update([
            'element_name' => $request->fee_element_name,
            'pattern' => $request->pattern,
        ]);

        return redirect()->route('university.admin.fee.element')
            ->with('success', 'Fee Element updated successfully.');
    }
}
