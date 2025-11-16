<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Submission;

class TestMailDevCommand extends Command
{
    protected $signature = 'maildev:test {--submission-id=} {--email=} {--debug}';
    protected $description = 'Test MailDev email configuration';

    public function handle()
    {
        $notificationService = new NotificationService();

        // Test basic email
        if ($this->option('email')) {
            $this->info("🧪 Testing basic email to: " . $this->option('email'));
            $result = $notificationService->testEmail($this->option('email'));
            
            if ($result) {
                $this->info("✅ Test email sent successfully!");
                $this->info("📨 Check MailDev at: http://localhost:1080");
            } else {
                $this->error("❌ Test email failed! Check laravel.log for details");
            }
            return;
        }

        // Test submission email
        if ($this->option('submission-id')) {
            $submission = Submission::with(['representative.user', 'memberStudents.user'])
                ->find($this->option('submission-id'));
            
            if (!$submission) {
                $this->error("❌ Submission not found!");
                return;
            }

            $this->info("🧪 Testing submission email for: " . $submission->company_name);
            
            // Debug mode
            if ($this->option('debug')) {
                $this->info("🐛 Debug mode enabled");
                $notificationService->debugSubmission($submission);
            }

            $result = $notificationService->sendSubmissionStatusUpdate(
                $submission, 
                'approved', 
                'Test Admin', 
                'This is a test feedback'
            );

            if ($result) {
                $this->info("✅ Submission email sent successfully!");
                $this->info("📨 Check MailDev at: http://localhost:1080");
            } else {
                $this->error("❌ Submission email failed! Check laravel.log for details");
            }
            return;
        }

        $this->error("❌ Please provide either --submission-id or --email option");
        $this->info("💡 Usage:");
        $this->info("   php artisan maildev:test --email=test@example.com");
        $this->info("   php artisan maildev:test --submission-id=1");
        $this->info("   php artisan maildev:test --submission-id=1 --debug");
    }
}