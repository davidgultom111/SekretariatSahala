<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter->tipe_surat }} - {{ $letter->nomor_surat }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        .container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 25mm;
            position: relative;
        }

        .letterhead {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .kop-image {
            width: 100%;
            max-height: 140px;
            object-fit: contain;
        }

        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .number {
            font-size: 11pt;
            margin-top: 5px;
        }

        .content {
            font-size: 12pt;
            text-align: justify;
        }
        .opening-text {
            margin-bottom: 15px;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0 20px 40px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label { width: 160px; }
        .colon { width: 20px; }

        .closing-text {
            margin-top: 15px;
            line-height: 1.8;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
        }
        .sig-space {
            height: 100px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .footer-system {
            position: absolute;
            bottom: 15mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead">
            @if(file_exists(public_path('images/kop-surat.jpg')))
                <img src="{{ asset('images/kop-surat.jpg') }}" alt="Kop Surat" class="kop-image">
            @else
                <div style="font-size: 14pt; font-weight: bold;">GEREJA PENTEKOSTA DI INDONESIA</div>
                <div style="font-size: 16pt; font-weight: bold;">Jemaat "SAHABAT ALLAH" Palembang</div>
                <div style="font-size: 9pt;">JL. Sejahtera Lr. Sahabat, Sukabangun II Kel. Sukajaya Kec. Sukarami Palembang</div>
            @endif
        </div>

        <div class="title-section">
            <h1 class="title">{{ $letter->tipe_surat }}</h1>
            <p class="number">No. {{ $letter->nomor_surat }}</p>
        </div>

        <div class="content">
            @if($letter->member)
                <p class="opening-text">{{ \App\Services\LetterTemplateService::getLetterOpeningText($letter->letter_type) }}</p>

                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td style="font-weight: bold;">{{ $letter->member->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. Telepon</td>
                        <td class="colon">:</td>
                        <td>{{ $letter->member->no_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat/Tanggal Lahir</td>
                        <td class="colon">:</td>
                        <td>
                            {{ $letter->member->tempat_lahir ?? '-' }}, 
                            {{ $letter->member->tanggal_lahir ? \Carbon\Carbon::parse($letter->member->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="colon">:</td>
                        <td>{{ $letter->member->alamat ?? '-' }}</td>
                    </tr>
                </table>
            @endif

            <div class="closing-text">
                {!! nl2br(\App\Services\LetterTemplateService::generateLetterBody($letter->letter_type, $letter)) !!}
            </div>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width: 60%;"></td>
                    <td style="text-align: center;">
                        Palembang, {{ $letter->tanggal_surat ? \Carbon\Carbon::parse($letter->tanggal_surat)->translatedFormat('d F Y') : '-' }}<br>
                        <strong>Gembala Sidang</strong>
                        <div class="sig-space"></div>
                        <div class="sig-name">( Tamrin Gultom, S.Th. )</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-system">
            Dokumen ini dicetak otomatis dari Sistem Sekretariat Gereja Pantekosta Jemaat Sahabat Allah Palembang
        </div>
    </div>
</body>
</html>