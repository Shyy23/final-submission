<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Submission;

class SubmissionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $status;
    public $actionBy;
    public $feedback;

    /**
     * Create a new message instance.
     */
    public function __construct(Submission $submission, $status, $actionBy, $feedback = null)
    {
        $this->submission = $submission;
        $this->status = $status;
        $this->actionBy = $actionBy;
        $this->feedback = $feedback;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->getStatusText();
        
        return new Envelope(
            subject: "Status Pengajuan Surat Tugas Akhir - {$statusText}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-status',
            with: [
                'submission' => $this->submission,
                'status' => $this->status,
                'statusText' => $this->getStatusText(),
                'actionBy' => $this->actionBy,
                'feedback' => $this->feedback,
                'verificationUrl' => route('submissions.verification', $this->submission->submission_id),
                'detailUrl' => route('submissions.show', $this->submission->submission_id),
            ],
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

    private function getStatusText()
    {
        $statuses = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'verified' => 'Terverifikasi'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}