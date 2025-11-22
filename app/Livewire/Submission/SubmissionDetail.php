<?php

namespace App\Livewire\Submission;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;
use Livewire\Attributes\On; 
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubmissionDetail extends Component
{
    public $submission;
    public $submissionId;

    public function mount($id)
    {
        $this->submissionId = $id;
        $this->loadSubmission();
    }

    #[On('submission-updated')] 
    public function loadSubmission()
    {
        $this->submission = Submission::with([
            'representative.user', 
            'memberStudents.user', 
            'admin', 
            'leader.user'
        ])->findOrFail($this->submissionId);
    }

    /**
     * GENERATE PDF (Dipanggil oleh Child Components)
     */
    #[On('generateAndSavePdf')] 
    public function generateAndSavePdf($withQr = false)
    {
        Carbon::setLocale('id');
        $dateIndo = Carbon::now()->translatedFormat('d F Y');
        
        $representative = $this->submission->representative;
        $members = $this->submission->memberStudents;
        
        // Gabungkan ketua dan anggota
        $fixedStudents = collect([$representative])->merge($members)->unique('nim')->values();

        $data = [
            'submission' => $this->submission,
            'students'   => $fixedStudents,
            'withQr'     => $withQr,
            'qrPath'     => ($withQr && $this->submission->qr_url) ? public_path('qr-code/' . $this->submission->qr_url) : null,
            'date'       => $dateIndo,
            'nomor_surat'=> 'B/'.$this->submission->submission_id.'/FSI-Unjani/'. \Carbon\Carbon::now()->format('m/Y'),
            'logo_ykep'  => public_path('assets/img/ykep.png'),
            'logo_unjani' => public_path('assets/img/unjani.png'), 
        ];

        if(!Storage::exists('documents')) {
            Storage::makeDirectory('documents');
        }

        $fileName = 'surat_tugas_' . $this->submission->submission_id . '.pdf';
        $storagePath = 'documents/' . $fileName;
        
        $pdf = Pdf::loadView('pdf.surat_tugas', $data)->setPaper('a4', 'portrait');
        Storage::put($storagePath, $pdf->output());
        
        $this->submission->update(['document_path' => $storagePath]);
        $this->submission->refresh();
    }

    /**
     * DOWNLOAD DOCUMENT
     */
    #[On('downloadDocument')]
    public function downloadDocument()
    {
        if (Auth::user()->hasRole('mahasiswa') && $this->submission->status !== 'verified') {
             session()->flash('error', 'Dokumen belum terverifikasi sepenuhnya.');
             return;
        }
        
        if (!$this->submission->document_path || !Storage::exists($this->submission->document_path)) {
            session()->flash('error', 'File dokumen tidak ditemukan. Mohon hubungi Admin.');
            return;
        }
        
        return Storage::download($this->submission->document_path, 'Surat_Tugas_' . $this->submission->id . '.pdf');
    }

    /**
     * DOWNLOAD QR CODE
     */
    public function downloadQRCode()
    {
        if (!$this->submission->qr_url) {
            session()->flash('error', 'QR Code belum tersedia.');
            return;
        }

        $qrPath = public_path('qr-code/' . $this->submission->qr_url);

        if (!file_exists($qrPath)) {
            session()->flash('error', 'File QR Code tidak ditemukan di server.');
            return;
        }

        return response()->download($qrPath, 'QR_Verifikasi_' . $this->submission->id . '.png');
    }

    public function render()
    {
        return view('livewire.submission.submission-detail')
            ->layout('layouts.detail');
    }
}