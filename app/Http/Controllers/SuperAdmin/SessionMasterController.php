<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class SessionMasterController extends Controller
{
    /**
     * Generate session label from type and year
     */
    private function generateSessionLabel($sessionType, $year)
    {
        $typeLabel = $sessionType === 'jul-dec' ? 'Jul-Dec' : 'Jan-Jun';
        return $typeLabel . ' ' . $year;
    }

    /**
     * Display the session master page (all sessions from all universities)
     */
    public function index(): View
    {
        $sessions = Session::with('university')
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.session-master', compact('sessions', 'universities'));
    }

    /**
     * Store a newly created session
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'session_type' => 'required|in:jul-dec,jan-jun',
            'year' => 'required|integer|min:2000|max:2100',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sessionLabel = $this->generateSessionLabel($request->session_type, $request->year);

        // Check if session already exists for this university
        $existingSession = Session::where('university_id', $request->university_id)
            ->where('session_label', $sessionLabel)
            ->first();

        if ($existingSession) {
            return redirect()->back()
                ->withErrors(['error' => 'Session already exists for this university.'])
                ->withInput();
        }

        Session::create([
            'university_id' => $request->university_id,
            'session_label' => $sessionLabel,
            'session_type' => $request->session_type,
            'year' => $request->year,
        ]);

        return redirect()->route('superadmin.session.master')
            ->with('success', 'Session created successfully.');
    }

    /**
     * Show the form for editing the specified session
     */
    public function edit($id): View
    {
        $session = Session::with('university')->findOrFail($id);
        $sessions = Session::with('university')
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get();
        
        $universities = \App\Models\University::where('status', true)->orderBy('university_name')->get();
        
        return view('superadmin.university_master.session-master', compact('sessions', 'session', 'universities'));
    }

    /**
     * Update the specified session
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $session = Session::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'session_type' => 'required|in:jul-dec,jan-jun',
            'year' => 'required|integer|min:2000|max:2100',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sessionLabel = $this->generateSessionLabel($request->session_type, $request->year);

        // Check if session already exists for this university (excluding current)
        $existingSession = Session::where('university_id', $request->university_id)
            ->where('session_label', $sessionLabel)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSession) {
            return redirect()->back()
                ->withErrors(['error' => 'Session already exists for this university.'])
                ->withInput();
        }

        $session->update([
            'university_id' => $request->university_id,
            'session_label' => $sessionLabel,
            'session_type' => $request->session_type,
            'year' => $request->year,
        ]);

        return redirect()->route('superadmin.session.master')
            ->with('success', 'Session updated successfully.');
    }
}


