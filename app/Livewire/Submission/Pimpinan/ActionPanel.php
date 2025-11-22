<?php

namespace App\Livewire\Submission\Pimpinan;

use Livewire\Component;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            // 1. SETUP PATH & NAMA FILE
            $qrFileName = 'qr_' . $this->submission->id . '_' . time() . '.png';
            $path = public_path('qr-code');
            
            // Pastikan folder public/qr-code ada
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            $fullQrPath = $path . '/' . $qrFileName;

            // URL Validasi (misal ke halaman detail)
            $verificationUrl = route('submissions.show', $this->submission->submission_id);

            // Path Logo untuk di tengah QR
            $logoPath = public_path('assets/img/unjani.png');

            // 2. GENERATE QR CODE DENGAN LOGO
            $qrBuilder = QrCode::format('png')
                ->size(300)
                ->color(0, 0, 0) // Warna Hitam
                ->backgroundColor(255, 255, 255) // Background Putih
                ->errorCorrection('H'); // High Error Correction (Penting jika ada logo)

            // Cek apakah file logo ada sebelum di-merge
            if (file_exists($logoPath)) {
                // merge(path_image, persentase_ukuran, merge_transparent_background)
                $qrBuilder->merge($logoPath, 0.3, true);
            }

            // Simpan File
            $qrBuilder->generate($verificationUrl, $fullQrPath);

            // 3. UPDATE DATA SUBMISSION
            $this->submission->update([
                'status' => 'verified',
                'leader_id' => $leader->nid,
                'qr_url' => $qrFileName, 
                'feedback' => 'Dokumen ini telah ditandatangani dan diverifikasi secara digital oleh FSI UNJANI',
                'updated_at' => now(),
            ]);

            // 4. TRIGGER REGENERATE PDF (Final dengan QR)
            $this->dispatch('generateAndSavePdf', withQr: true);

            // 5. Notifikasi & Refresh
            $this->showConfirmModal = false;
            session()->flash('message', 'Dokumen berhasil ditandatangani secara digital dan diverifikasi.');
            
            // Update UI
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