<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Tugas Akhir - {{ $submission->company_name }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-circle-check text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Verifikasi Keaslian Dokumen</h1>
            <p class="text-gray-600">Surat Tugas Akhir - {{ $submission->company_name }}</p>
        </div>

        <!-- Verification Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Status Badge -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-circle-check text-white text-xl mr-3"></i>
                        <span class="text-white font-semibold text-lg">DOKUMEN TERVERIFIKASI</span>
                    </div>
                    <div class="text-white text-sm">
                        <i class="far fa-clock mr-1"></i>
                        {{ $submission->updated_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Informasi Dokumen -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">ID Pengajuan</label>
                            <p class="text-lg font-bold text-gray-800">#{{ str_pad($submission->submission_id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Perusahaan</label>
                            <p class="text-gray-800">{{ $submission->company_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Alamat Perusahaan</label>
                            <p class="text-gray-800">{{ $submission->address_company }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Pengajuan</label>
                            <p class="text-gray-800">{{ $submission->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Status</label>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full">
                                {{ strtoupper($submission->status) }}
                            </span>
                        </div>
                        @if($submission->leader)
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Ditandatangani Oleh</label>
                            <p class="text-gray-800">{{ $submission->leader->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $submission->leader->position->position_name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Daftar Anggota -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        Anggota Kelompok
                    </h3>
                    <div class="space-y-2">
                        @foreach($submission->memberStudents as $member)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-bold">
                                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-800">{{ $member->user->name }}</div>
                                    <div class="text-sm text-gray-500">NIM: {{ $member->nim }}</div>
                                </div>
                            </div>
                            @if($member->nim === $submission->representative_nim)
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                Perwakilan
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($submission->qr_url)
                <div class="border-t border-gray-200 pt-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-center">
                        <i class="fas fa-qrcode text-purple-500 mr-2"></i>
                        Kode Verifikasi
                    </h3>
                    <div class="bg-white p-4 rounded-lg border-2 border-dashed border-gray-300 inline-block mb-3">

                        <img src="{{ asset('qr-code/' . $submission->qr_url) }}"
                            alt="QR Code Verifikasi"
                            class="w-48 h-48 mx-auto">
                    </div>
                    <p class="text-sm text-gray-600 max-w-md mx-auto">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Scan QR code di atas untuk memverifikasi keaslian dokumen ini.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>
                <i class="fas fa-lock text-green-500 mr-1"></i>
                Dokumen ini telah diverifikasi dan dilindungi sistem keamanan
            </p>
            <p class="mt-1">
                Sistem Pengajuan Surat Tugas Akhir &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</body>

</html>