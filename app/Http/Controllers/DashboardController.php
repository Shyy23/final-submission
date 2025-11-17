<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match (true) {
            $user->hasRole('mahasiswa') => $this->studentDashboard($user),
            $user->hasRole('admin') => $this->adminDashboard($user),
            $user->hasRole('pimpinan') => $this->pimpinanDashboard($user),
            default => redirect()->route('home')->with('error', 'Akses ditolak')
        };
    }

    private function studentDashboard($user)
    {
        $student = $user->student;

        if (!$student) {
            return redirect()->route('profile')->with('error', 'Profil mahasiswa tidak ditemukan. Silakan lengkapi data profil Anda.');
        }

        // Query untuk mendapatkan semua submission dimana mahasiswa terlibat
        $submissionQuery = Submission::where(function ($query) use ($student) {
            $query->where('representative_nim', $student->nim)
                ->orWhereHas('members', function ($q) use ($student) {
                    $q->where('student_nim', $student->nim);
                });
        });

        // Hitung statistik dengan query terpisah TANPA orderBy dan limit
        $stats = Submission::where(function ($query) use ($student) {
            $query->where('representative_nim', $student->nim)
                ->orWhereHas('members', function ($q) use ($student) {
                    $q->where('student_nim', $student->nim);
                });
        })
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count')
            ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count')
            ->selectRaw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count')
            ->selectRaw('SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_count')
            ->first();

        // Ambil 5 pengajuan terbaru dengan eager loading (query terpisah)
        $recentSubmissions = $submissionQuery
            ->with(['representative.user', 'memberStudents.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'totalSubmissions' => $stats->total ?? 0,
            'pendingCount' => $stats->pending_count ?? 0,
            'approvedCount' => $stats->approved_count ?? 0,
            'rejectedCount' => $stats->rejected_count ?? 0,
            'verifiedCount' => $stats->verified_count ?? 0,
            'recentSubmissions' => $recentSubmissions
        ]);
    }

    private function adminDashboard($user)
    {
        // Menggunakan query builder untuk efisiensi
        $submissionStats = Submission::selectRaw(
            'COUNT(*) as total_submissions,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_submissions,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_submissions,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_submissions,
            SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_submissions'
        )->first();

        // Ambil 5 submission terbaru dengan relasi yang diperlukan
        $recentSubmissions = Submission::with(['representative.user'])
            ->latest()
            ->take(5)
            ->get();

        // Data untuk ringkasan status
        $statusCounts = [
            [
                'label' => 'Pending',
                'count' => $submissionStats->pending_submissions,
                'icon' => 'fa-clock',
                'color' => 'amber'
            ],
            [
                'label' => 'Approved',
                'count' => $submissionStats->approved_submissions,
                'icon' => 'fa-circle-check',
                'color' => 'emerald'
            ],
            [
                'label' => 'Rejected',
                'count' => $submissionStats->rejected_submissions,
                'icon' => 'fa-times-circle',
                'color' => 'red'
            ],
            [
                'label' => 'Verified',
                'count' => $submissionStats->verified_submissions,
                'icon' => 'fa-shield-alt',
                'color' => 'teal'
            ],
        ];

        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalSubmissions' => $submissionStats->total_submissions ?? 0,
            'pendingSubmissions' => $submissionStats->pending_submissions ?? 0,
            'approvedSubmissions' => $submissionStats->approved_submissions ?? 0,
            'recentSubmissions' => $recentSubmissions ?? 0,
            'statusCounts' => $statusCounts
        ]);
    }

    private function pimpinanDashboard($user)
    {
        // Optimasi query untuk statistik menggunakan conditional aggregation
        $stats = Submission::selectRaw(
            'COUNT(*) as total_submissions,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_count'
        )->first();

        // Ambil 5 submission menunggu verifikasi (status: approved) dengan eager loading
        $pendingSignature = Submission::with(['representative.user'])
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        // Ambil 5 submission sudah diverifikasi (status: verified) dengan eager loading
        $recentVerified = Submission::with(['representative.user'])
            ->where('status', 'verified')
            ->latest()
            ->take(5)
            ->get();

        // Hitung progress verifikasi
        $totalApproved = $stats->approved_count + $stats->verified_count;
        $progressPercentage = $totalApproved > 0
            ? round(($stats->verified_count / $totalApproved) * 100)
            : 0;

        return view('dashboard', [
            'approvedSubmissions' => $stats->approved_count ?? 0,
            'verifiedSubmissions' => $stats->verified_count ?? 0,
            'totalSubmissions' => $stats->total_submissions ?? 0,
            'pendingSignature' => $pendingSignature,
            'recentVerified' => $recentVerified,
            'progressPercentage' => $progressPercentage
        ]);
    }
}
