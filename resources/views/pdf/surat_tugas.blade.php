@php
// Helper Romawi
if (!function_exists('getRomanMonth')) {
function getRomanMonth($month) {
$map = [
1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
];
return $map[intval($month)] ?? 'I';
}
}

// 1. Ambil Nama Prodi
$mainProdiName = $submission->department_name
?? ($submission->representative->studyProgram->study_name ?? 'Kimia');

// 2. Ambil Kode Prodi
$prodiCode = $submission->department_code ?? 'XX';

// 3. Logika Tembusan
$additionalProdis = collect();
if ($submission->memberStudents) {
foreach($submission->memberStudents as $member) {
$memProdi = $member->studyProgram->study_name ?? '';
if ($memProdi && $memProdi !== $mainProdiName) {
$additionalProdis->push($memProdi);
}
}
}
$uniqueAdditionalProdis = $additionalProdis->unique()->values();

$currentMonth = date('n');
$currentYear = date('Y');
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Surat Permohonan Izin Tempat Penelitian</title>
    <style>
        /* MARGIN HALAMAN UTAMA */
        @page {
            margin: 0.36cm 0.5cm 0.48cm 0.76cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.15;
            color: #000;
        }

        /* HEADER / KOP SURAT (FULL WIDTH - Tidak kena padding body) */
        .header-table {
            width: 100%;
            border-bottom: 3px double black;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .header-text {
            text-align: center;
        }

        .font-yayasan {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .font-univ {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .font-fakultas {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .font-alamat {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 2px;
        }

        /* CONTAINER BODY (Seluruh isi surat selain Kop) */
        /* Padding Kiri 2cm, Kanan 2cm */
        .body-container {
            padding-left: 2cm;
            padding-right: 2cm;
        }

        /* LAYOUT META & RECIPIENT */
        .top-section-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .top-section-table td {
            vertical-align: top;
        }

        /* META DATA (KIRI) */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-label {
            width: 80px;
            vertical-align: top;
        }

        .meta-sep {
            width: 10px;
            vertical-align: top;
            text-align: center;
        }

        .meta-val {
            vertical-align: top;
        }

        /* CONTENT UTAMA */
        .content-block {
            text-align: justify;
            margin-bottom: 5px;
        }

        /* WRAPPER POIN (INDENTASI LEBIH DALAM) */
        .poin-wrapper {
            margin-left: 1.0cm;
            /* Indentasi tambahan agar lebih kanan dari "Dengan hormat" */
        }

        /* TABEL POIN 1, 2, 3 */
        .poin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .poin-num {
            width: 25px;
            vertical-align: top;
        }

        .poin-content {
            text-align: justify;
            vertical-align: top;
        }

        /* TABEL MAHASISWA */
        .student-table {
            width: 95%;
            margin-left: 25px;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .student-table th,
        .student-table td {
            border: 1px solid black;
            padding: 3px 6px;
            text-align: left;
            font-size: 11pt;
        }

        .student-table th {
            text-align: center;
            font-weight: bold;
        }

        /* FOOTER LAYOUT */
        .footer-container {
            width: 100%;
            margin-top: 30px;
            display: table;
            /* Pengganti clearfix modern */
        }

        /* KOLOM KIRI: TEMBUSAN */
        .footer-left {
            float: left;
            width: 45%;
            font-size: 10pt;
            vertical-align: bottom;
            /* UPDATE: Margin top diperbesar lagi agar turun lebih jauh */
            margin-top: 130px;
        }

        /* KOLOM KANAN: TTD & QR */
        .footer-right {
            float: right;
            width: 45%;
            text-align: left;
            padding-left: 20px;
        }

        /* SECTION TANDA TANGAN */
        .ttd-section {
            margin-bottom: 20px;
        }

        /* SECTION QR (Di bawah TTD, Align Right) */
        .qr-section {
            text-align: right;
            /* Geser konten ke kanan */
            /* UPDATE: Tambah jarak dari TTD */
            margin-top: 60px;
        }

        /* Tabel QR Inner */
        .qr-table-inner {
            margin-left: auto;
            /* Push table to the right */
            margin-right: 0;
            width: auto;
        }

        .tt-electronic-text {
            font-weight: bold;
            font-size: 9pt;
            vertical-align: middle;
        }

        .qr-text {
            font-size: 6pt;
            line-height: 1.1;
            text-align: left;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT (FULL WIDTH) --}}
    <table class="header-table">
        <tr>
            <td width="15%" align="center" style="vertical-align: middle;">
                <img src="{{ $logo_ykep }}" width="100">
            </td>
            <td width="70%" class="header-text">
                <div class="font-yayasan">YAYASAN KARTIKA EKA PAKSI</div>
                <div class="font-univ">UNIVERSITAS JENDERAL ACHMAD YANI (UNJANI)</div>
                <div class="font-fakultas">FAKULTAS SAINS DAN INFORMATIKA (FSI)</div>
                <div class="font-alamat">
                    Kampus Cimahi: Jl. Terusan Jenderal Sudirman PO.BOX 148 Telp. (022) 6650646
                </div>
            </td>
            <td width="15%" align="center" style="vertical-align: middle;">
                <img src="{{ $logo_unjani }}" width="100">
            </td>
        </tr>
    </table>

    {{-- WRAPPER UNTUK SELURUH ISI SURAT --}}
    <div class="body-container">

        {{-- TANGGAL (Di Kanan Atas Body) --}}
        <div style="text-align: right; margin-bottom: 5px;">
            Cimahi, {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y') }}
        </div>

        {{-- SECTION ATAS: META DATA & KEPADA YTH --}}
        <table class="top-section-table">
            <tr>
                {{-- KOLOM KIRI: Nomor, Sifat, dll --}}
                <td width="55%">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Nomor</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">
                                B/{{ $submission->submission_id }}/FSI-Unjani/{{ getRomanMonth($currentMonth) }}/{{
                                $currentYear }}
                            </td>
                        </tr>
                        <tr>
                            <td class="meta-label">Sifat</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">Biasa</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Lampiran</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val">-</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Perihal</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-val"><strong>Permohonan Izin Tempat Penelitian</strong></td>
                        </tr>
                    </table>
                </td>

                {{-- KOLOM KANAN: Kepada Yth --}}
                <td width="45%" style="padding-left: 10px;">
                    Kepada Yth:<br>
                    <strong>Kepala/Pimpinan {{ $submission->company_name }}</strong><br>
                    @if($submission->address_company)
                    {!! nl2br(e($submission->address_company)) !!}
                    @else
                    di Tempat
                    @endif
                </td>
            </tr>
        </table>

        <div class="content-block" style="margin-top: 10px;">Dengan hormat,</div>

        {{-- WRAPPER ISI SURAT (INDENTASI LEBIH DALAM) --}}
        <div class="poin-wrapper">
            {{-- POIN 1 --}}
            <table class="poin-table">
                <tr>
                    <td class="poin-num">1.</td>
                    <td class="poin-content">
                        Dasar: Nota Dinas Ketua Program Studi {{ $mainProdiName }} Nomor:
                        ND/{{ $submission->submission_id }}/{{ $prodiCode }}-FSI/{{ getRomanMonth($currentMonth) }}/{{
                        $currentYear }}
                        tanggal {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y') }}
                        perihal Permohonan Surat Pengantar Penelitian Tugas Akhir.
                    </td>
                </tr>
            </table>

            {{-- POIN 2 --}}
            <table class="poin-table">
                <tr>
                    <td class="poin-num">2.</td>
                    <td class="poin-content">
                        Atas dasar tersebut di atas, kami sampaikan mahasiswa Program Studi {{ $mainProdiName }}:
                    </td>
                </tr>
            </table>

            {{-- TABEL SISWA --}}
            <table class="student-table">
                <thead>
                    <tr>
                        <th width="10%">No.</th>
                        <th width="50%">Nama</th>
                        <th width="40%">NIM</th>
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

            {{-- PARAGRAF PENUTUP POIN 2 --}}
            <table class="poin-table">
                <tr>
                    <td class="poin-num"></td>
                    <td class="poin-content">
                        Saat ini yang bersangkutan sedang melaksanakan Penelitian Tugas Akhir, untuk terlaksananya
                        kegiatan tersebut,
                        dengan ini kami mengajukan Permohonan Izin Tempat Penelitian di <strong>{{
                            $submission->company_name }}</strong>.
                        Kami mohon Bapak/Ibu berkenan memberikan izin untuk maksud tersebut di atas.
                    </td>
                </tr>
            </table>

            {{-- POIN 3 --}}
            <table class="poin-table">
                <tr>
                    <td class="poin-num">3.</td>
                    <td class="poin-content">
                        Demikian surat permohonan ini kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima
                        kasih.
                    </td>
                </tr>
            </table>
        </div>

        {{-- FOOTER AREA --}}
        <div class="footer-container clearfix">

            {{-- BAGIAN KIRI: TEMBUSAN --}}
            <div class="footer-left">
                Tembusan Yth:<br>
                1. Dekan FSI Unjani (sebagai laporan)<br>
                2. Ketua Program Studi {{ $mainProdiName }} FSI Unjani<br>
                @foreach($uniqueAdditionalProdis as $idx => $otherProdi)
                {{ $idx + 3 }}. Ketua Program Studi {{ $otherProdi }} FSI Unjani<br>
                @endforeach
            </div>

            {{-- BAGIAN KANAN: TANDA TANGAN & QR --}}
            <div class="footer-right">

                {{-- 1. TANDA TANGAN --}}
                <div class="ttd-section">
                    a.n. Dekan<br>
                    Wakil Dekan I,<br>

                    @if($withQr && $qrPath)
                    <div style="margin: 10px 0;">
                        <table style="border:none;">
                            <tr>
                                <td style="border:none; padding-right: 5px;"><img src="{{ $logo_unjani }}" width="20">
                                </td>
                                <td style="border:none;" class="tt-electronic-text">TT ELEKTRONIK</td>
                            </tr>
                        </table>
                    </div>
                    @else
                    <div style="height: 60px; color: #ccc; font-style: italic; display: flex; align-items: center;">
                        <br><br>(Draft Dokumen)
                    </div>
                    @endif

                    <div>
                        <span style="text-decoration: underline;">Dr. Arie Hardian, S.Si., M.Si.</span><br>
                        NID. 412185787
                    </div>
                </div>

                {{-- 2. QR CODE (DI BAWAH TTD, GESER KANAN) --}}
                @if($withQr && $qrPath)
                <div class="qr-section">
                    <table class="qr-table-inner" style="border:none;">
                        <tr>
                            <td width="60" style="border:none; vertical-align:top; padding:0;">
                                <img src="{{ $qrPath }}" width="60" height="60">
                            </td>
                            <td style="border:none; vertical-align:middle; padding-left:5px;" class="qr-text">
                                Dokumen ini telah<br>
                                ditandatangani dan<br>
                                diverifikasi secara digital oleh<br>
                                <strong>FSI UNJANI</strong>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif

            </div>

        </div>

    </div>

</body>

</html>