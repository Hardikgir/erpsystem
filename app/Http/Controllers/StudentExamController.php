<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentExamController extends Controller
{
    /**
     * Display the exam details page for students.
     */
    public function index(): View
    {
        // Check if user is a student
        $user = auth()->user();
        
        if (!$user->role || $user->role->role_name !== 'student') {
            abort(403, 'Access denied. This page is only for students.');
        }

        // You can add exam data fetching logic here
        // For now, returning a simple view
        
        return view('student.exam.index');
    }
}
