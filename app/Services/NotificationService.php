<?php

namespace App\Services;

use App\Models\Submission;
use App\Mail\SubmissionStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send email notification for submission status update
     */
    public function sendSubmissionStatusUpdate(Submission $submission, $status, $actionBy, $feedback = null)
    {
        try {
            Log::info("🔔 Preparing to send email notification for submission: {$submission->id}");
            Log::info("📋 Submission details - ID: {$submission->submission_id}, Company: {$submission->company_name}, Status: {$status}");

            // Get all recipients dengan eager loading
            $recipients = $this->getRecipients($submission);
            
            if (empty($recipients)) {
                Log::warning("❌ No recipients found for submission: {$submission->submission_id}");
                return false;
            }

            Log::info("📧 Sending email to: " . implode(', ', $recipients));

            // Send email to all recipients
            Mail::to($recipients)
                ->send(new SubmissionStatusUpdated($submission, $status, $actionBy, $feedback));

            Log::info("✅ Email notification sent successfully for submission: {$submission->submission_id}");
            
            return true;

        } catch (\Exception $e) {
            Log::error("❌ Failed to send email notification for submission {$submission->submission_id}: " . $e->getMessage());
            Log::error("📝 Stack trace: " . $e->getTraceAsString());
            
            return false;
        }
    }

    /**
     * Get all email recipients for a submission
     */
    private function getRecipients(Submission $submission)
    {
        $recipients = [];

        try {
            // Load relationships dengan eager loading untuk menghindari N+1
            $submission->load([
                'representative.user',
                'memberStudents.user'
            ]);

            Log::info("👤 Checking representative...");
            
            // Add representative email - PERBAIKAN DI SINI
            if ($submission->representative && $submission->representative->user) {
                $representativeEmail = $submission->representative->user->email;
                if (filter_var($representativeEmail, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $representativeEmail;
                    Log::info("✅ Added representative: {$representativeEmail}");
                } else {
                    Log::warning("❌ Invalid representative email: {$representativeEmail}");
                }
            } else {
                Log::warning("❌ Representative or user not found for submission: {$submission->submission_id}");
                Log::warning("📊 Representative data: " . json_encode($submission->representative));
            }

            // Add member emails - PERBAIKAN DI SINI
            Log::info("👥 Checking members...");
            if ($submission->memberStudents && $submission->memberStudents->count() > 0) {
                foreach ($submission->memberStudents as $index => $member) {
                    if ($member->user) {
                        $memberEmail = $member->user->email;
                        if (filter_var($memberEmail, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = $memberEmail;
                            Log::info("✅ Added member {$index}: {$memberEmail}");
                        } else {
                            Log::warning("❌ Invalid member email: {$memberEmail}");
                        }
                    } else {
                        Log::warning("❌ User not found for member: " . $member->student_nim);
                    }
                }
            } else {
                Log::info("ℹ️ No members found for submission: {$submission->submission_id}");
            }

            // Remove duplicates
            $recipients = array_unique($recipients);

            Log::info("📨 Final recipient list: " . (empty($recipients) ? 'EMPTY' : implode(', ', $recipients)));

        } catch (\Exception $e) {
            Log::error("❌ Error getting recipients: " . $e->getMessage());
            Log::error("📝 Stack trace: " . $e->getTraceAsString());
        }

        return $recipients;
    }

    /**
     * Test email configuration
     */
    public function testEmail($toEmail = 'test@example.com')
    {
        try {
            Log::info("🧪 Testing email configuration to: {$toEmail}");

            Mail::raw('This is a test email from Laravel MailDev setup.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('Test Email from Laravel MailDev');
            });

            Log::info("✅ Test email sent successfully to: {$toEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("❌ Test email failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug submission data
     */
    public function debugSubmission(Submission $submission)
    {
        try {
            Log::info("🐛 DEBUG Submission Data:");
            Log::info("📊 Submission ID: {$submission->submission_id}");
            Log::info("🏢 Company: {$submission->company_name}");
            Log::info("📝 Status: {$submission->status}");
            
            // Load relationships
            $submission->load([
                'representative.user',
                'memberStudents.user'
            ]);

            // Debug representative
            if ($submission->representative) {
                Log::info("👤 Representative NIM: {$submission->representative->nim}");
                if ($submission->representative->user) {
                    Log::info("👤 Representative User: {$submission->representative->user->name} ({$submission->representative->user->email})");
                } else {
                    Log::warning("❌ Representative user not found");
                }
            } else {
                Log::warning("❌ Representative not found");
            }

            // Debug members
            Log::info("👥 Member count: " . $submission->memberStudents->count());
            foreach ($submission->memberStudents as $index => $member) {
                Log::info("👥 Member {$index}: NIM {$member->nim}");
                if ($member->user) {
                    Log::info("👥 Member User: {$member->user->name} ({$member->user->email})");
                } else {
                    Log::warning("❌ Member user not found for NIM: {$member->nim}");
                }
            }

        } catch (\Exception $e) {
            Log::error("❌ Debug failed: " . $e->getMessage());
        }
    }
}