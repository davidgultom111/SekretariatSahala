<?php

namespace App\Services;

use App\Models\Member;
use App\Models\LetterNumberCounter;
use Carbon\Carbon;

class LetterTemplateService
{
    const ORGANIZATION = 'GPdI';
    const CHURCH = 'SA';

    public static function getLetterTypes()
    {
        return [
            'surat_tugas_pelayanan' => 'Surat Tugas Pelayanan',
            'surat_pengantar' => 'Surat Pengantar',
            'surat_keterangan_jemaat_aktif' => 'Surat Keterangan Jemaat Aktif',
            'surat_nilai_sekolah' => 'Surat Nilai Sekolah',
            'surat_pengajuan_baptisan' => 'Surat Pengajuan Baptisan',
            'surat_pengajuan_penyerahan_anak' => 'Surat Pengajuan Penyerahan Anak',
            'surat_pengajuan_pernikahan' => 'Surat Pengajuan Pernikahan',
        ];
    }

    /**
     * Helper Tabel Identitas untuk hasil PDF yang presisi
     */
    private static function renderIdentityTable($rows)
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin-bottom: 2mm;">';
        foreach ($rows as $label => $value) {
            $html .= '<tr>';
            $html .= '<td style="width: 45mm; vertical-align: top; padding: 0.5mm 0;">' . $label . '</td>';
            $html .= '<td style="width: 5mm; vertical-align: top; padding: 0.5mm 0; text-align: center;">:</td>';
            $html .= '<td style="vertical-align: top; padding: 0.5mm 0;">' . $value . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public static function generateLetterNumber($letterType)
    {
        $currentYear = now()->year;
        $counter = LetterNumberCounter::firstOrCreate(
            ['letter_type' => $letterType, 'year' => $currentYear],
            ['next_number' => 1, 'abbreviation' => self::getAbbreviationForLetterType($letterType)]
        );

        $number = str_pad($counter->next_number, 3, '0', STR_PAD_LEFT);
        $letterNumber = "{$number}/" . self::ORGANIZATION . "/" . self::CHURCH . "/{$counter->abbreviation}/{$currentYear}";
        
        $counter->increment('next_number');
        return $letterNumber;
    }

    public static function getLetterNumberPreview($letterType)
    {
        $currentYear = now()->year;
        $counter = LetterNumberCounter::firstOrCreate(
            ['letter_type' => $letterType, 'year' => $currentYear],
            ['next_number' => 1, 'abbreviation' => self::getAbbreviationForLetterType($letterType)]
        );

        $number = str_pad($counter->next_number, 3, '0', STR_PAD_LEFT);
        return "{$number}/" . self::ORGANIZATION . "/" . self::CHURCH . "/{$counter->abbreviation}/{$currentYear}";
    }

    private static function getAbbreviationForLetterType($letterType)
    {
        return match ($letterType) {
            'surat_tugas_pelayanan' => 'TP',
            'surat_pengantar' => 'SP',
            'surat_keterangan_jemaat_aktif' => 'KJA',
            'surat_nilai_sekolah' => 'NS',
            'surat_pengajuan_baptisan' => 'PB',
            'surat_pengajuan_penyerahan_anak' => 'PA',
            'surat_pengajuan_pernikahan' => 'PP',
            default => 'XX',
        };
    }

    public static function getLetterOpeningText($type)
    {
        return match ($type) {
            'surat_pengantar' => 'Dengan hormat, kami memberitahukan bahwa:',
            'surat_nilai_sekolah' => 'Yang bertanda tangan di bawah ini adalah Gembala Sidang Jemaat yang menerangkan bahwa:',
            'surat_pengajuan_baptisan', 'surat_pengajuan_penyerahan_anak', 'surat_pengajuan_pernikahan' => 'Kepada Yth. Pemimpin Ibadah terkait di tempat,',
            default => 'Yang bertanda tangan di bawah ini menerangkan bahwa:',
        };
    }

    public static function generateLetterBody($type, $letter)
    {
        // Pastikan $letter adalah instance model Letter
        if (!$letter || class_basename($letter) !== 'Letter') return "";

        switch ($type) {
            case 'surat_tugas_pelayanan':
                $tglMulai = $letter->tgl_mulai_tugas ? Carbon::parse($letter->tgl_mulai_tugas)->translatedFormat('d F Y') : '-';
                $tglAkhir = $letter->tgl_akhir_tugas ? Carbon::parse($letter->tgl_akhir_tugas)->translatedFormat('d F Y') : '-';
                
                $data = [
                    'Tanggal Mulai' => $tglMulai,
                    'Tanggal Akhir' => $tglAkhir,
                    'Tujuan Tugas' => $letter->tujuan_tugas
                ];
                
                return "<p style='margin-bottom: 2mm;'>Telah ditugaskan untuk melakukan pelayanan dengan perincian sebagai berikut:</p>" . 
                       self::renderIdentityTable($data) .
                       "<p style='margin-top: 2mm;'>Semoga dapat melayani dengan sepenuh hati kepada Tuhan dan sesama.</p>";

            case 'surat_pengantar':
                return "<p>Adalah anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah dan terdaftar dalam daftar anggota kami.</p>" .
                       "<p style='margin-top: 3mm;'>Adapun surat pengantar ini diberikan untuk keperluan: <strong>{$letter->keterangan}</strong></p>" .
                       "<p style='margin-top: 3mm;'>Demikian surat pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>";

            case 'surat_keterangan_jemaat_aktif':
                return "<p>Adalah benar anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah Palembang yang aktif dan terdaftar dalam keikutsertaan agenda ibadah jemaat sejak tahun <strong>{$letter->tahun_bergabung}</strong>.</p>" .
                       "<p style='margin-top: 4mm;'>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>";

            case 'surat_nilai_sekolah':
                $data = [
                    'Asal Sekolah' => $letter->asal_sekolah,
                    'Kelas/Tingkat' => $letter->kelas ?? '-',
                    'Semester' => $letter->semester ?? '-',
                ];
                $nilai = $letter->nilai ?? 90;

                return self::renderIdentityTable($data) .
                       "<p style='margin-top: 3mm;'>Siswa/siswi tersebut telah mengikuti kegiatan ibadah dan pembelajaran agama di GPdI Jemaat Sahabat Allah Palembang dengan nilai:</p>" .
                       "<div style='text-align: center; font-size: 14pt; font-weight: bold; margin-top: 4mm;'>[ {$nilai} ]</div>";

            case 'surat_pengajuan_penyerahan_anak':
                $data = [
                    'Nama Ayah' => $letter->nama_ayah ?? '-',
                    'Nama Ibu' => $letter->nama_ibu ?? '-',
                    'Nama Anak' => $letter->nama_anak,
                    'Tempat/Tgl Lahir' => ($letter->tempat_lahir_anak ?? '-') . " / " . ($letter->tanggal_lahir_anak ? Carbon::parse($letter->tanggal_lahir_anak)->translatedFormat('d F Y') : '-')
                ];
                return self::renderIdentityTable($data) .
                       "<p style='margin-top: 3mm;'>Orang tua dari anak tersebut ingin menyerahkan anak mereka kepada Tuhan dan memohon doa restu dalam ibadah penyerahan anak.</p>";

            case 'surat_pengajuan_pernikahan':
                $tglNikah = $letter->tanggal_pernikahan ? Carbon::parse($letter->tanggal_pernikahan)->translatedFormat('d F Y') : '-';
                $data = [
                    'Mempelai Pria' => $letter->memberPria->nama_lengkap ?? '-',
                    'Mempelai Wanita' => $letter->memberWanita->nama_lengkap ?? '-',
                    'Rencana Nikah' => $tglNikah,
                    'Tempat' => 'GPdI Sahabat Allah Palembang'
                ];
                return "<p style='margin-bottom: 2mm;'>Calon mempelai telah siap untuk memasuki ikatan pernikahan kudus. Kami memohon agar proses bimbingan dan upacara pernikahan dapat dilaksanakan pada:</p>" .
                       self::renderIdentityTable($data);

            case 'surat_pengajuan_baptisan':
                return "<p>Jemaat tersebut telah menyatakan komitmennya menerima Yesus Kristus sebagai Tuhan dan Juru Selamat pribadi, dan rindu untuk dibaptis air sebagai tanda pernyataan imannya.</p>" .
                       "<p style='margin-top: 3mm;'>Kami memohon agar calon baptis ini dapat disertakan pada upacara pembaptisan berikutnya.</p>";

            default:
                return "";
        }
    }
}