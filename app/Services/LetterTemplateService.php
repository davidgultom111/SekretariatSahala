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
     * Helper untuk memformat identitas jemaat agar seragam
     */
    private static function getMemberIdentity(Member $member)
    {
        $tglLahir = $member->tanggal_lahir ? Carbon::parse($member->tanggal_lahir)->translatedFormat('d F Y') : '-';
        
        return "Nama                    : {$member->nama_lengkap}\n" .
               "No. Identitas           : " . ($member->no_identitas ?? '-') . "\n" .
               "Tempat/Tanggal Lahir    : {$member->tempat_lahir} / {$tglLahir}\n" .
               "Alamat                  : {$member->alamat}";
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

    /**
     * Get preview nomor surat TANPA increment counter
     * Digunakan untuk form preview di halaman create
     */
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

    /**
     * Digunakan untuk tampilan preview di Web/Admin
     */
    public static function getLetterOpeningText($type)
    {
        return match ($type) {
            'surat_pengantar' => 'Dengan hormat, kami memberitahukan bahwa:',
            'surat_nilai_sekolah' => 'Yang bertanda tangan di bawah ini adalah Gembala Sidang Jemaat yang menerangkan bahwa:',
            'surat_pengajuan_baptisan', 'surat_pengajuan_penyerahan_anak', 'surat_pengajuan_pernikahan' => 'Kepada Yth. Pemimpin Ibadah terkait di tempat,',
            default => 'Yang bertanda tangan di bawah ini menerangkan bahwa:',
        };
    }

    /**
     * Menghasilkan Body Surat (Hanya bagian isi setelah data diri)
     * Parameter bisa Member atau Letter object (untuk data khusus seperti tugas pelayanan)
     */
    public static function generateLetterBody($type, $memberOrLetter = null)
    {
        // Handle Letter object untuk surat dengan data tambahan
        if ($memberOrLetter && class_basename($memberOrLetter) === 'Letter') {
            $letter = $memberOrLetter;
            $member = $letter->member;
            
            if ($type === 'surat_tugas_pelayanan' && $letter->tujuan_tugas) {
                $tglMulai = $letter->tgl_mulai_tugas ? Carbon::parse($letter->tgl_mulai_tugas)->translatedFormat('d F Y') : '............................';
                $tglAkhir = $letter->tgl_akhir_tugas ? Carbon::parse($letter->tgl_akhir_tugas)->translatedFormat('d F Y') : '............................';
                
                return "Telah ditugaskan untuk melakukan pelayanan sesuai dengan tujuan tugas yang tertera dibawah ini, dengan perincian tugas sebagai berikut:\n\n" .
                       "Tanggal Mulai       : {$tglMulai}\n" .
                       "Tanggal Akhir       : {$tglAkhir}\n" .
                       "Tujuan Tugas        :\n{$letter->tujuan_tugas}\n\n" .
                       "Semoga dengan tugas ini dapat melayani dengan sepenuh hati kepada Tuhan dan sesama.";
            }

            if ($type === 'surat_pengantar' && $letter->keterangan) {
                return "Adalah anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah dan terdaftar dalam daftar anggota kami.\n\n" .
                       "Adapun surat pengantar ini diberikan untuk keperluan:\n{$letter->keterangan}\n\n" .
                       "Demikian surat pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.";
            }

            if ($type === 'surat_keterangan_jemaat_aktif' && $letter->tahun_bergabung) {
                return "Adalah benar anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah Palembang yang aktif dan terdaftar dalam keikutsertaan agenda ibadah jemaat sejak tahun {$letter->tahun_bergabung}.\n\n" .
                       "Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.";
            }
        }

        // Handle Member object (backward compatibility)
        $member = is_object($memberOrLetter) && class_basename($memberOrLetter) === 'Letter' ? $memberOrLetter->member : $memberOrLetter;

        return match ($type) {
            'surat_tugas_pelayanan' => 
                "Telah ditugaskan untuk melakukan pelayanan di Gereja Pantekosta Jemaat Sahabat Allah Palembang mulai tanggal sebagaimana tercantum dalam surat keputusan yang terpisah.\n\nSemoga dengan tugas ini dapat melayani dengan sepenuh hati kepada Tuhan dan sesama.",
            
            'surat_pengantar' => 
                "Adalah anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah yang aktif dan terdaftar dalam daftar anggota kami.\n\nSurat pengantar ini diberikan untuk keperluan sebagaimana dimaksud agar dapat dipergunakan sebagaimana mestinya.",
            
            'surat_keterangan_jemaat_aktif' => 
                "Adalah benar anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah Palembang yang aktif dan terdaftar dalam keikutsertaan agenda ibadah jemaat sejak tahun .....\n\nDemikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.",
            
            'surat_nilai_sekolah' => 
                "Sekolah/Tempat Belajar : .............................\nKelas/Tingkat          : .............................\n\nSiswa/siswi tersebut telah mengikuti kegiatan ibadah dan pembelajaran agama di Gereja Pantekosta Jemaat Sahabat Allah dengan nilai:\n\n[ ............................. ]",
            
            'surat_pengajuan_baptisan' => 
                "Jemaat tersebut telah menyatakan komitmennya menerima Yesus Kristus sebagai Tuhan dan Juru Selamat pribadi, dan rindu untuk dibaptis air sebagai tanda pernyataan imannya.\n\nKami memohon agar calon baptis ini dapat disertakan pada upacara pembaptisan berikutnya.",
            
            'surat_pengajuan_penyerahan_anak' => 
                "Nama Anak              : .............................\nTempat/Tanggal Lahir   : .............................\n\nOrang tua dari anak tersebut ingin menyerahkan anak mereka kepada Tuhan dan memohon doa restu dalam ibadah penyerahan anak.",
            
            'surat_pengajuan_pernikahan' => 
                "Calon mempelai telah siap untuk memasuki ikatan pernikahan kudus. Kami memohon agar proses bimbingan dan upacara pernikahan dapat dilaksanakan pada:\n\nHari/Tanggal : ............................\nTempat       : ............................",
            
            default => "",
        };
    }

    /**
     * Full Template (Untuk Preview Cepat di Textarea)
     */
    public static function generateLetterContent($type, Member $member)
    {
        $title = strtoupper(str_replace('_', ' ', $type));
        $opening = self::getLetterOpeningText($type);
        $identity = self::getMemberIdentity($member);
        $body = self::generateLetterBody($type, $member);

        return "{$title}\n\n{$opening}\n\n{$identity}\n\n{$body}\n\nPalembang, " . now()->translatedFormat('d F Y') . "\n\nGembala Sidang";
    }
}