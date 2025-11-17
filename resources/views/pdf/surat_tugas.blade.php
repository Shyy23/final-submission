<!DOCTYPE html>
<html>

<head>
    <title>Surat Permohonan Izin Tempat Penelitian</title>
    <style>
        @page {
            margin: 1cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.15;
        }

        /* HEADER & KONTEN LAINNYA (TETAP) */
        .header-table {
            width: 100%;
            border-bottom: 4px double black;
            padding-bottom: 4px;
            margin-bottom: 20px;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .font-header-main {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            letter-spacing: 0.5px;
        }

        .font-alamat {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 2px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .meta-col-label {
            width: 80px;
            vertical-align: top;
        }

        .meta-col-sep {
            width: 10px;
            vertical-align: top;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .student-table th,
        .student-table td {
            border: 1px solid black;
            padding: 4px 8px;
            text-align: left;
        }

        .student-table th {
            text-align: center;
            font-weight: bold;
        }

        .content-text {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* --- PERBAIKAN UTAMA DI SINI --- */
        /* FOOTER MENGGUNAKAN TABLE UNTUK STABILITAS */
        .footer-table {
            width: 100%;
            margin-top: 20px;
            border: none;
        }

        .col-tembusan {
            width: 40%;
            vertical-align: top;
            font-size: 10pt;
        }

        .col-signature {
            width: 60%;
            /* Sisa lebar untuk tanda tangan */
            vertical-align: top;
            padding-left: 50px;
            /* Geser blok tanda tangan agak ke kanan */
        }

        /* Styling Elemen Tanda Tangan */
        .tt-elektronik-wrapper {
            margin: 10px 0;
            display: block;
        }

        .tt-elektronik-icon {
            width: 20px;
            height: auto;
            vertical-align: middle;
            margin-right: 8px;
            /* Jarak antara Logo dan Teks */
        }

        .tt-elektronik-text {
            font-weight: bold;
            color: #333;
            font-size: 11pt;
            vertical-align: middle;
        }

        /* QR Code Styling di dalam Tabel */
        .qr-table {
            margin-top: 15px;
            width: 100%;
        }

        .qr-img {
            width: 85px;
            height: 85px;
        }

        .qr-text {
            font-size: 8pt;
            color: #333;
            line-height: 1.2;
            padding-left: 10px;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    {{-- ... (HEADER, TANGGAL, META DATA, KONTEN SURAT SAMA SEPERTI SEBELUMNYA) ... --}}

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="15%" align="center"><img src="{{ $logo_ykep }}" width="100"></td>
            <td width="70%" class="header-text">
                <div class="font-header-main">YAYASAN KARTIKA EKA PAKSI</div>
                <div class="font-header-main">UNIVERSITAS JENDERAL ACHMAD YANI (UNJANI)</div>
                <div class="font-header-main">FAKULTAS SAINS DAN INFORMATIKA (FSI)</div>
                <div class="font-alamat">Kampus Cimahi: Jl. Terusan Jenderal Sudirman PO.BOX 148 Telp. (022) 6650646
                </div>
            </td>
            <td width="15%" align="center"><img src="{{ $logo_unjani }}" width="100"></td>
        </tr>
    </table>

    <div style="text-align: right; margin-bottom: 5px;">Cimahi, {{ $date }}</div>

    <table class="meta-table">
        <tr>
            <td class="meta-col-label">Nomor</td>
            <td class="meta-col-sep">:</td>
            <td>B/{{ $submission->submission_id }}/FSI-Unjani/{{ \Carbon\Carbon::now()->format('m/Y') }}</td>
        </tr>
        <tr>
            <td class="meta-col-label">Sifat</td>
            <td class="meta-col-sep">:</td>
            <td>Biasa</td>
        </tr>
        <tr>
            <td class="meta-col-label">Lampiran</td>
            <td class="meta-col-sep">:</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="meta-col-label">Perihal</td>
            <td class="meta-col-sep">:</td>
            <td><strong>Permohonan Izin Tempat Penelitian</strong></td>
        </tr>
    </table>

    <div style="margin-bottom: 20px;">
        Kepada Yth:<br><strong>Kepala/Pimpinan {{ $submission->company_name }}</strong><br>
        @if($submission->address_company) {{ $submission->address_company }}<br> @else di Tempat @endif
    </div>

    <div class="content-text">Dengan hormat,</div>
    <div class="content-text">
        1. Dasar: Nota Dinas Ketua Program Studi Kimia Nomor: ND/{{ $submission->submission_id }}/KI-FSI/{{
        \Carbon\Carbon::now()->format('m/Y') }}
        tanggal {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y') }}
        perihal Permohonan Surat Pengantar Penelitian Tugas Akhir.
    </div>
    <div class="content-text">2. Atas dasar tersebut di atas, kami sampaikan mahasiswa Program Studi Kimia:</div>

    <table class="student-table">
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="60%">Nama</th>
                <th width="30%">NIM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $student->user->name ?? $student->name }}</td>
                <td align="center">{{ $student->nim }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="content-text">
        Saat ini yang bersangkutan sedang melaksanakan Penelitian Tugas Akhir, untuk terlaksananya kegiatan tersebut,
        dengan ini kami mengajukan Permohonan Izin Tempat Penelitian di <strong>{{ $submission->company_name
            }}</strong>.
    </div>
    <div class="content-text">Kami mohon Bapak/Ibu berkenan memberikan izin untuk maksud tersebut di atas.</div>
    <div class="content-text">3. Demikian surat permohonan ini kami sampaikan, atas perhatian dan kerjasamanya diucapkan
        terima kasih.</div>
    {{-- ... (AKHIR KONTEN SURAT) ... --}}


    {{-- FOOTER MENGGUNAKAN TABEL (Fix Layout) --}}
    <table class="footer-table">
        <tr>
            {{-- KOLOM KIRI: TEMBUSAN --}}
            <td class="col-tembusan">
                Tembusan Yth:<br>
                1. Dekan FSI Unjani (sebagai laporan)<br>
                2. Ketua Program Studi Kimia FSI Unjani
            </td>

            {{-- KOLOM KANAN: TANDA TANGAN --}}
            <td class="col-signature">
                a.n. Dekan<br>
                Wakil Dekan I,<br>

                {{-- LOGIK TAMPILAN TT ELEKTRONIK --}}
                @if($withQr && $qrPath)
                <div class="tt-elektronik-wrapper">
                    <img src="{{ $logo_unjani }}" class="tt-elektronik-icon">
                    <span class="tt-elektronik-text">TT ELEKTRONIK</span>
                </div>
                @else
                <br><br>
                <div style="color: #ccc; font-style: italic;">(Draft Dokumen)</div>
                <br>
                @endif

                {{-- NAMA DAN NID (TIDAK AKAN HILANG KARENA DALAM TABEL) --}}
                <div style="margin-top: 2px;">
                    <span style="text-decoration: underline; font-weight: bold;">Dr. Arie Hardian, S.Si.,
                        M.Si.</span><br>
                    NID. 412185787
                </div>

                {{-- QR CODE DI BAWAH NAMA (MASIH DI KOLOM KANAN) --}}
                @if($withQr && $qrPath)
                <table class="qr-table">
                    <tr>
                        <td width="90" valign="top">
                            <img src="{{ $qrPath }}" class="qr-img">
                        </td>
                        <td valign="middle" class="qr-text">
                            Dokumen ini telah ditandatangani dan<br>
                            diverifikasi secara digital oleh<br>
                            <strong>FSI UNJANI</strong>
                        </td>
                    </tr>
                </table>
                @endif
            </td>
        </tr>
    </table>

</body>

</html>