<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Submission\SubmissionDetail;
use App\Livewire\SubmissionForm;
use App\Livewire\SubmissionList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome')->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
    Route::get('/admin/submissions', SubmissionList::class)->name('admin.submissions');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/submissions/create', SubmissionForm::class)->name('submissions.create');
    Route::get('/submissions/mahasiswa/history', SubmissionList::class)->name('submissions.history');
});

Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/submissions/signature', SubmissionList::class)->name('submissions.signature');
});

// Route detail submission untuk semua role
Route::middleware(['auth'])->group(function () {
    Route::get('/submissions/detail/{id}', SubmissionDetail::class)->name('submissions.show');
});

// Route Verifikasi Publik (Tanpa Middleware Auth)
Volt::route('/verify/submission/{id}', 'submission.verification')
    ->name('submissions.verification');

require __DIR__ . '/auth.php';
