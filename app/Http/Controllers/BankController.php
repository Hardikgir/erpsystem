<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    /**
     * Get the university ID for the logged-in admin
     */
    private function getUniversityId()
    {
        return Auth::user()->university_id;
    }

    /**
     * Display the bank master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $banks = Bank::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.bank', compact('banks'));
    }

    /**
     * Store a newly created bank
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
            'bank_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
        ], [
            'bank_name.required' => 'Bank Name is required.',
            'account_no.required' => 'Account Number is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Bank::create([
            'university_id' => $universityId,
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
        ]);

        return redirect()->route('university.admin.bank.master')
            ->with('success', 'Bank created successfully.');
    }

    /**
     * Show the form for editing the specified bank
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $bank = Bank::where('university_id', $universityId)
            ->findOrFail($id);
        $banks = Bank::where('university_id', $universityId)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('university_admin.bank', compact('banks', 'bank'));
    }

    /**
     * Update the specified bank
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $bank = Bank::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255',
        ], [
            'bank_name.required' => 'Bank Name is required.',
            'account_no.required' => 'Account Number is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bank->update([
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
        ]);

        return redirect()->route('university.admin.bank.master')
            ->with('success', 'Bank updated successfully.');
    }
}
