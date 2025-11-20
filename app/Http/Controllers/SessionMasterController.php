<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SessionMasterController extends Controller
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
     * Generate session label from type and year
     */
    private function generateSessionLabel($sessionType, $year)
    {
        $typeLabel = $sessionType === 'jul-dec' ? 'Jul-Dec' : 'Jan-Jun';
        return $typeLabel . ' ' . $year;
    }

    /**
     * Display the session master page
     */
    public function index(): View
    {
        $universityId = $this->getUniversityId();
        $sessions = Session::where('university_id', $universityId)
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get();
        
        return view('university_admin.session-master', compact('sessions'));
    }

    /**
     * Store a newly created session
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
            'session_type' => 'required|in:jul-dec,jan-jun',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sessionLabel = $this->generateSessionLabel($request->session_type, $request->year);

        // Check if session already exists
        $existingSession = Session::where('university_id', $universityId)
            ->where('session_label', $sessionLabel)
            ->first();

        if ($existingSession) {
            return redirect()->back()
                ->withErrors(['error' => 'Session already exists.'])
                ->withInput();
        }

        Session::create([
            'university_id' => $universityId,
            'session_label' => $sessionLabel,
            'session_type' => $request->session_type,
            'year' => $request->year,
        ]);

        return redirect()->route('university.admin.session.master')
            ->with('success', 'Session created successfully.');
    }

    /**
     * Show the form for editing the specified session
     */
    public function edit($id): View
    {
        $universityId = $this->getUniversityId();
        $session = Session::where('university_id', $universityId)
            ->findOrFail($id);
        $sessions = Session::where('university_id', $universityId)
            ->orderBy('year', 'desc')
            ->orderBy('session_type', 'desc')
            ->get();
        
        return view('university_admin.session-master', compact('sessions', 'session'));
    }

    /**
     * Update the specified session
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $universityId = $this->getUniversityId();
        $session = Session::where('university_id', $universityId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'session_type' => 'required|in:jul-dec,jan-jun',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sessionLabel = $this->generateSessionLabel($request->session_type, $request->year);

        // Check if session already exists (excluding current)
        $existingSession = Session::where('university_id', $universityId)
            ->where('session_label', $sessionLabel)
            ->where('id', '!=', $id)
            ->first();

        if ($existingSession) {
            return redirect()->back()
                ->withErrors(['error' => 'Session already exists.'])
                ->withInput();
        }

        $session->update([
            'session_label' => $sessionLabel,
            'session_type' => $request->session_type,
            'year' => $request->year,
        ]);

        return redirect()->route('university.admin.session.master')
            ->with('success', 'Session updated successfully.');
    }
}
