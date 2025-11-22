<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        .header img {
            height: 60px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .bg-green {
            background-color: #10b981;
        }

        .bg-red {
            background-color: #ef4444;
        }

        .bg-blue {
            background-color: #3b82f6;
        }

        .content {
            margin-bottom: 25px;
        }

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
        }

        .details p {
            margin: 5px 0;
        }

        .btn {
            display: inline-block;
            background-color: #059669;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Submission System FSI UNJANI</h2>
        </div>

        <div style="text-align: center;">
            @if($statusType === 'approved')
            <span class="status-badge bg-blue">Disetujui Admin</span>
            @elseif($statusType === 'rejected')
            <span class="status-badge bg-red">Ditolak / Perlu Revisi</span>
            @elseif($statusType === 'verified')
            <span class="status-badge bg-green">Selesai & Terverifikasi</span>
            @endif
        </div>

        <div class="content">
            <p>Halo, <strong>{{ $submission->representative->user->name }}</strong></p>

            <p>
                @if($statusType === 'approved')
                Pengajuan surat tugas Anda telah <strong>DISETUJUI</strong> oleh Admin. Saat ini dokumen sedang menunggu
                tanda tangan digital dari Pimpinan.
                @elseif($statusType === 'rejected')
                Mohon maaf, pengajuan surat tugas Anda <strong>DITOLAK</strong> atau memerlukan revisi. Silakan perbaiki
                data sesuai catatan di bawah ini.
                @elseif($statusType === 'verified')
                Selamat! Surat tugas Anda telah <strong>DIVERIFIKASI</strong> dan ditandatangani secara digital oleh
                Pimpinan. Anda sekarang dapat mengunduh surat tugas tersebut.
                @endif
            </p>

            @if($feedback)
            <div class="details" style="border-left: 4px solid #ccc; margin: 20px 0;">
                <strong>Catatan / Feedback:</strong><br>
                <em>"{{ $feedback }}"</em>
            </div>
            @endif

            <div class="details">
                <strong>Detail Pengajuan:</strong>
                <p>Perusahaan: {{ $submission->company_name }}</p>
                <p>Tanggal Pengajuan: {{ $submission->created_at->format('d M Y') }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('submissions.show', $submission->submission_id) }}" class="btn">
                    Lihat Detail Pengajuan
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Pengajuan Tugas Akhir FSI UNJANI.</p>
            <p>&copy; {{ date('Y') }} FSI UNJANI</p>
        </div>
    </div>
</body>

</html>