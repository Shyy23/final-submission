<?php

namespace App\Livewire\Submission\Pimpinan;

use App\Mail\SubmissionStatusNotification;
use Livewire\Component;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ActionPanel extends Component
{
    public Submission $submission;
    public $showConfirmModal = false;

    public function mount(Submission $submission)
    {
        $this->submission = $submission;
    }
    
    #[On('submission-updated')] 
    public function refreshData()
    {
        $this->submission->refresh();
    }

    public function openVerifyModal()
    {
        $this->showConfirmModal = true;
    }

    public function verifyAndSign()
    {
        $leader = Auth::user()->leader; 

        if (!$leader) {
            session()->flash('error', 'Akun pimpinan tidak terhubung ke data Leader.');
            $this->showConfirmModal = false;
            return;
        }

        try {
            // 1. SETUP IDENTIFIER & PATH
            $identifier = $this->submission->submission_id ?? $this->submission->id;
            
            if (!$identifier) {
                throw new \Exception("ID Submission tidak ditemukan.");
            }

            $qrFileName = 'qr_' . $identifier . '_' . time() . '.png';
            $path = public_path('qr-code');
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $fullQrPath = $path . '/' . $qrFileName;

            // 2. URL Validasi & Logo
            $verificationUrl = route('submissions.show', $identifier);
            $logoPath = public_path('assets/img/unjani.png');

            // 3. GENERATE QR CODE
            $qrBuilder = QrCode::format('png')
                ->size(300)
                ->color(0, 0, 0) 
                ->backgroundColor(255, 255, 255) 
                ->errorCorrection('H'); 

            if (file_exists($logoPath)) {
                try {
                     $qrBuilder->merge($logoPath, 0.3, true);
                } catch (\Exception $e) {
                    Log::warning('Gagal merge logo QR: ' . $e->getMessage());
                }
            }

            $qrBuilder->generate($verificationUrl, $fullQrPath);

            // 4. UPDATE DATA SUBMISSION
            $feedbackMsg = 'Dokumen ini telah ditandatangani dan diverifikasi secara digital oleh FSI UNJANI';
            $this->submission->update([
                'status' => 'verified',
                'leader_id' => $leader->nid,
                'qr_url' => $qrFileName, 
                'feedback' => $feedbackMsg,
                'updated_at' => now(),
            ]);

            // 5. TRIGGER REGENERATE PDF
            $this->dispatch('generateAndSavePdf', withQr: true);

            // --- LOGIKA KIRIM EMAIL NOTIFIKASI ---
            try {
                $representative = $this->submission->representative;
                if ($representative && $representative->user && $representative->user->email) {
                    // Kirim Email Verified
                    Mail::to($representative->user->email)->send(
                        new SubmissionStatusNotification($this->submission, 'verified', $feedbackMsg)
                    );
                    Log::info("Email notifikasi (verified) dikirim ke: " . $representative->user->email);
                }
            } catch (\Exception $mailEx) {
                Log::error('Gagal mengirim email notifikasi pimpinan: ' . $mailEx->getMessage());
            }
            // -------------------------------------

            $this->showConfirmModal = false;
            session()->flash('message', 'Dokumen berhasil ditandatangani secara digital dan diverifikasi.');
            
            $this->dispatch('submission-updated');

        } catch (\Exception $e) {
            Log::error('Gagal Verifikasi Pimpinan: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            session()->flash('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }
    
    public function downloadDocument()
    {
        $this->dispatch('downloadDocument');
    }

    public function render()
    {
        return view('livewire.submission.pimpinan.action-panel');
    }
}