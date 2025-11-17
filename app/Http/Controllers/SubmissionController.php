<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function showVerification($id)
    {
        $submission = Submission::with([
            'representative.user',
            'memberStudents.user',
            'admin',
            'leader.user',
            'leader.position'
        ])->findOrFail($id);

        return view('livewire.submission.verification', compact('submission'));
    }
}
