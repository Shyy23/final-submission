<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $submission;
    public $statusType; // 'approved', 'rejected', 'verified'
    public $feedback;

    /**
     * Create a new message instance.
     */
    public function __construct(Submission $submission, string $statusType, ?string $feedback = null)
    {
        $this->submission = $submission;
        $this->statusType = $statusType;
        $this->feedback = $feedback;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->statusType) {
            'approved' => '✅ Pengajuan Disetujui - Surat Tugas Akhir',
            'rejected' => '❌ Pengajuan Perlu Revisi - Surat Tugas Akhir',
            'verified' => '🎉 Surat Tugas Selesai & Terverifikasi',
            default => 'Update Status Pengajuan'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}