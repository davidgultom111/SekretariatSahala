<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4;
            margin: 10mm 15mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: #fff;
            font-size: 11pt;
        }
        .kop-table {
            width: 100%;
            border-bottom: 2pt solid #000;
            margin-bottom: 5mm;
            padding-bottom: 2mm;
        }
        .kop-image {
            max-width: 100%;
            max-height: 30mm;
            display: block;
            margin: 0 auto;
        }
        .kop-text { text-align: center; }
        .line1 { font-size: 12pt; font-weight: bold; }
        .line2 { font-size: 14pt; font-weight: bold; }
        .line3 { font-size: 12pt; }

        .title-section { text-align: center; margin-bottom: 6mm; }
        .title { font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .number { font-size: 11pt; margin-top: 1mm; }

        .content {
            line-height: 1.4;
            text-align: justify;
        }

        .data-table {
            width: 100%;
            margin-left: 10mm;
            margin-bottom: 3mm;
            border-collapse: collapse;
        }
        .data-table td { padding: 0.5mm 0; vertical-align: top; }
        .label { width: 45mm; }
        .colon { width: 5mm; }

        .body-content { margin-top: 2mm; }

        .sig-table {
            width: 100%;
            margin-top: 8mm;
            border-collapse: collapse;
        }
        .sig-right {
            width: 50%;
            text-align: center;
        }
        .sig-name { font-weight: bold; text-decoration: underline; }

        .footer-system {
            margin-top: 8mm;
            text-align: center;
            font-size: 8pt;
            color: #666;
            font-style: italic;
            border-top: 0.5pt solid #ccc;
            padding-top: 2mm;
        }
    </style>
</head>
<body>
    <div>
        <table class="kop-table">
            <tr>
                <td class="kop-text">
                    @php $kopPath = public_path('images/kop-surat.jpg'); @endphp
                    @if(file_exists($kopPath))
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($kopPath)) }}" class="kop-image">
                    @else
                        <div class="line1">GEREJA PENTEKOSTA DI INDONESIA</div>
                        <div class="line2">Jemaat "SAHABAT ALLAH" Palembang</div>
                        <div class="line3">JL. Sejahtera Lr. Sahabat, Sukabangun II Kel. Sukajaya Kec. Sukarami, Palembang 30152</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="title-section">
            <div class="title">{{ $letter->tipe_surat }}</div>
            <div class="number">No. {{ $letter->nomor_surat }}</div>
        </div>

        <div class="content">
            @if($letter->member)
                <div style="margin-bottom: 2mm;">{{ \App\Services\LetterTemplateService::getLetterOpeningText($letter->letter_type) }}</div>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td style="font-weight: bold;">{{ $letter->member->nama_lengkap }}</td>
                    </tr>
                    <tr><td class="label">No. Telepon</td><td class="colon">:</td><td>{{ $letter->member->no_telepon }}</td></tr>
                    <tr><td class="label">Tempat/Tgl Lahir</td><td class="colon">:</td><td>{{ $letter->member->tempat_lahir }}, {{ \Carbon\Carbon::parse($letter->member->tanggal_lahir)->translatedFormat('d F Y') }}</td></tr>
                    <tr><td class="label">Alamat</td><td class="colon">:</td><td>{{ $letter->member->alamat }}</td></tr>
                </table>
            @endif

            <div class="body-content">
                {!! \App\Services\LetterTemplateService::generateLetterBody($letter->letter_type, $letter) !!}
            </div>
        </div>

        <table class="sig-table">
            <tr>
                <td style="width: 50%;"></td>
                <td class="sig-right">
                    <div style="margin-bottom: 1mm;">Palembang, {{ \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d F Y') }}</div>
                    <div style="font-weight: bold; margin-bottom: 15mm;">Gembala Sidang</div>
                    <div class="sig-name">( Tamrin Gultom, S.Th. )</div>
                </td>
            </tr>
        </table>

        <div class="footer-system">
            Dokumen ini dicetak otomatis dari Sistem Sekretariat Gereja Pantekosta Jemaat Sahabat Allah Palembang
        </div>
    </div>
</body>
</html>