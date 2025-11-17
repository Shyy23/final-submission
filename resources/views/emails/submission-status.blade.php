<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Pengajuan Surat Tugas Akhir</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-verified {
            background: #d1ecf1;
            color: #0c5460;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }

        .info-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 5px 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Notifikasi Status Pengajuan</h1>
        <p>Sistem Surat Tugas Akhir</p>
    </div>

    <div class="content">
        <h2>Halo {{ $submission->representative->user->name }},</h2>

        <p>Pengajuan surat tugas akhir Anda untuk perusahaan <strong>{{ $submission->company_name }}</strong> telah
            <span class="status-badge status-{{ $status }}">
                {{ $statusText }}
            </span>
        </p>

        <div class="info-box">
            <h3>Detail Pengajuan:</h3>
            <p><strong>Perusahaan:</strong> {{ $submission->company_name }}</p>
            <p><strong>Posisi:</strong> {{ $submission->position }}</p>
            <p><strong>Perwakilan:</strong> {{ $submission->representative->user->name }}</p>
            <p><strong>Ditindaklanjuti oleh:</strong> {{ $actionBy }}</p>
            <p><strong>Waktu:</strong> {{ now()->translatedFormat('l, d F Y H:i') }}</p>

            @if($feedback)
            <p><strong>Catatan/Komentar:</strong><br>{{ $feedback }}</p>
            @endif
        </div>

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $detailUrl }}" class="button">Lihat Detail Pengajuan</a>

            @if($status === 'verified')
            <a href="{{ $verificationUrl }}" class="button" style="background: #28a745;">
                Verifikasi QR Code
            </a>
            @endif
        </div>

        <p>Silakan login ke sistem untuk informasi lebih lanjut atau menghubungi administrator jika ada pertanyaan.</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} Sistem Surat Tugas Akhir. All rights reserved.</p>
    </div>
</body>

</html>