<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class DocumentGeneratorService
{
    public function generateSubmissionDocument(Submission $submission, $withQR = false)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 10,
            'default_font' => 'times'
        ]);

        $html = $this->generateDocumentHTML($submission, $withQR);
        
        $mpdf->WriteHTML($html);
        
        $fileName = 'submission_' . $submission->submission_id . ($withQR ? '_signed' : '') . '.pdf';
        $filePath = 'submissions/' . $fileName;
        
        $pdfContent = $mpdf->Output('', 'S');
        Storage::put($filePath, $pdfContent);
        
        return $filePath;
    }

    private function generateDocumentHTML(Submission $submission, $withQR = false)
    {
        $members = $submission->memberStudents;
        $currentDate = now()->locale('id')->translatedFormat('j F Y');

        $qrSection = '';
        if ($withQR && $submission->qr_url) {
            $qrPath = public_path('qr-code/' . $submission->qr_url);
            if (file_exists($qrPath)) {
                $qrData = base64_encode(file_get_contents($qrPath));
                $qrSection = '
                    <div style="text-align: center; margin-top: 30px;">
                        <p style="margin-bottom: 10px;"><strong>QR Code Verifikasi</strong></p>
                        <img src="data:image/png;base64,' . $qrData . '" style="width: 100px; height: 100px;">
                        <p style="font-size: 10px; margin-top: 5px;">Scan untuk verifikasi keaslian dokumen</p>
                    </div>
                ';
            }
        }

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Surat Pengajuan Tugas Akhir</title>
            <style>
                body { 
                    font-family: "Times New Roman", Times, serif; 
                    font-size: 12pt;
                    line-height: 1.5;
                    margin: 2cm;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px;
                }
                .header h2 {
                    font-size: 14pt;
                    font-weight: bold;
                    margin: 5px 0;
                }
                .header p {
                    font-size: 11pt;
                    margin: 3px 0;
                }
                .content {
                    margin: 20px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
                .signature {
                    margin-top: 50px;
                    text-align: right;
                }
                .footer {
                    margin-top: 30px;
                    font-size: 10pt;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>YAYASAN KARTIKA EKA PAKSI</h2>
                <h2>UNIVERSITAS JENDERAL ACHMAD YANI (UNJANI)</h2>
                <h2>FAKULTAS SAINS DAN INFORMATIKA (FSI)</h2>
                <p>Kampus Cimahi : Jl. Terusan Jenderal Sudirman PO.BOX 148 Telp. (022) 6650646</p>
            </div>

            <div class="content">
                <p style="text-align: right;">Cimahi, ' . $currentDate . '</p>

                <p>Nomor&nbsp;&nbsp;&nbsp;&nbsp;: B/289/FSI-Unjani/VIII/2025<br>
                   Sifat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Biasa<br>
                   Lampiran : - <br>
                   Perihal&nbsp;&nbsp;&nbsp;: Permohonan Izin Tempat Penelitian</p>

                <p>Dengan hormat,</p>

                <p>Kepada :<br>
                   Yth. Kepala Pusat<br>
                   Riset Teknologi Lingkungan dan<br>
                   Teknologi Bersih Badan Riset dan<br>
                   Inovasi Nasional (BRIN)<br>
                   Kawasan Puspitek Gd.720 Serpong<br>
                   15314 Tanggerang Selatan</p>

                <p>1. Dasar: Nota Dinas Ketua Program Studi Kimia Nomor: ND/238/KI-FSI/VIII/2025 tanggal 4 Agustus 2025 perihal Permohonan Surat Pengantar Penelitian Tugas Akhir.</p>

                <p>2. Atas dasar tersebut di atas, kami sampalkan mahasiswa Program Studi Kimia:</p>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">No.</th>
                            <th style="width: 45%;">Nama</th>
                            <th style="width: 45%;">NIM</th>
                        </tr>
                    </thead>
                    <tbody>' . 
                    $this->generateMemberRows($members) . 
                    '</tbody>
                </table>

                <p>Saat ini yang bersangkutan sedang melaksanakan Penelitian Tugas Akhir, untuk terlaksananya kegiatan tersebut, dengan ini kami mengajukan Permohonan Izin Tempat Penelitian di ' . $submission->company_name . ' yang beralamat di ' . $submission->address_company . '. Kami mohon Bapak/Ibu berkenan memberikan izin untuk maksud tersebut di atas.</p>

                ' . ($submission->note ? '<p>Catatan: ' . $submission->note . '</p>' : '') . '

                <p>3. Demikian surat permohonan ini kami sampalkan, atas perhatian dan kerjasamanya diucapkan terima kasih.</p>

                <div class="signature">
                    <p>a.n. Dekan<br>Wakil Dekan I</p>
                    <br><br><br>
                    <p><strong>TT ELEKTRONIK</strong></p>
                    <p>Dr. Arie Hardian, S.Si., M.Si.<br>NID. 412185787</p>
                </div>

                <div class="footer">
                    <p><strong>Tembusan Yth :</strong><br>
                       1. Dekan FSI Unjani (sebagai laporan)<br>
                       2. Ketua Program Studi Kimia FSI Unjani</p>
                </div>

                ' . $qrSection . '
            </div>
        </body>
        </html>
        ';
    }

    private function generateMemberRows($members)
    {
        $rows = '';
        foreach ($members as $index => $member) {
            $rows .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . $member->user->name . '</td>
                    <td>' . $member->nim . '</td>
                </tr>
            ';
        }
        return $rows;
    }
}