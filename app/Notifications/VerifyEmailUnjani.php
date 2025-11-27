<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail; // Pastikan ini ada

// PERBAIKAN: Ubah 'extends Notification' menjadi 'extends VerifyEmail'
class VerifyEmailUnjani extends VerifyEmail
{
    use Queueable;

    // Hapus constructor jika tidak dipakai
    // public function __construct() { }

    // Hapus method via() karena sudah ada di parent (VerifyEmail), 
    // kecuali Anda mau mengubah channelnya. Defaultnya sudah ['mail'].
    
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Sekarang method ini bisa dipanggil karena kita extends VerifyEmail
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Email - Submission Tugas Akhir UNJANI')
            ->greeting('Yth. Mahasiswa,')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('Jika Anda tidak merasa mendaftar akun di sistem Submission UNJANI, abaikan pesan ini.')
            ->salutation('Salam Hormat, Admin Akademik');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [];
    }
}