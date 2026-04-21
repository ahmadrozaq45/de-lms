<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicController extends Controller
{
    public function enroll(Request $request) {
        return response()->json(CourseEnrollment::firstOrCreate(['user_id' => Auth::id(), 'course_id' => $request->course_id]));
    }

    public function storeAssignment(Request $request) {
        $validated = $request->validate(['course_id' => 'required', 'title' => 'required', 'instructions' => 'required', 'due_date' => 'required|date']);
        return response()->json(Assignment::create($validated), 201);
    }

    public function submitAssignment(Request $request, $assignmentId) {
        return response()->json(Submission::create(['assignment_id' => $assignmentId, 'student_id' => Auth::id(), 'file_path' => $request->file_path]));
    }

    public function giveGrade(Request $request) {
        // Polymorphic: gradeable_type bisa 'App\Models\Assignment' atau 'App\Models\QuizAttempt'
        return response()->json(Grade::create($request->all()), 201);
    }
}
