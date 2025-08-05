<!DOCTYPE html>
<html>
<head>
    <title>Surat Permohonan Bimbingan Tesis</title>
    <style type="text/css">
        body {
            font-size: 14px;
            line-height: 1.5;
            font-family: "Times New Roman", Times, serif;
        }
        table * {
            border: none;
        }
        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>
</head>
<body>
<htmlpageheader name="page-header">
    @include('layouts.kop_surat_ft') {{-- Kop surat fakultas teknik --}}
</htmlpageheader>

<htmlpagefooter name="page-footer">
</htmlpagefooter>

<div class="container">
    <hr style="border: solid; color: black; margin-top: 18px; height: 3px">

    <table style="width: 100%;">
        <tr>
            <th style="width: 15%;">Nomor</th>
            <td style="width: 3%;">:</td>
            <td style="width: 60%;">{{ $data['draft']->no_surat_permohonan ?? '......' }}</td>
            <td style="width: 22%; text-align: right;">
                {{ \Carbon\Carbon::parse($data['tanggal'])->format('d') }}
                {{ $monthList[\Carbon\Carbon::parse($data['tanggal'])->format('M')] }}
                {{ \Carbon\Carbon::parse($data['tanggal'])->format('Y') }}
            </td>
        </tr>
        <tr>
            <th>Lampiran</th>
            <td>:</td>
            <td>1 (satu) berkas</td>
            <td></td>
        </tr>
        <tr>
            <th>Hal</th>
            <td>:</td>
            <td>Permohonan Bimbingan Tesis</td>
            <td></td>
        </tr>
    </table>

    <br>

    <p>Yth. Bapak/Ibu {{ $data['dosen_pembimbing_1']->nama_dosen ?? '-' }}<br>
    Dosen Pembimbing Tesis Mahasiswa<br>
    di tempat</p>

    <p>Dengan hormat,</p>

    <p style="text-align: justify;">
        Bersama ini kami sampaikan permohonan untuk dapat membimbing mahasiswa Program Studi Teknik Elektro pada program tesis. Adapun data mahasiswa yang dimaksud adalah sebagai berikut:
    </p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 5%;"></td>
            <td style="width: 25%;">Nama</td>
            <td style="width: 3%;">:</td>
            <td style="width: 67%;"><strong>{{ $data['mahasiswa']->user->name }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $data['mahasiswa']->nim }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Judul Tesis</td>
            <td>:</td>
            <td>{{ $data['draft']->judul_tesis }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Dosen Pembimbing</td>
            <td>:</td>
            <td>
                1. {{ $data['dosen_pembimbing_1']->nama_dosen ?? '-' }} <br>
                2. {{ $data['dosen_pembimbing_2']->nama_dosen ?? '-' }}
            </td>
        </tr>
    </table>

    <p style="text-align: justify;">
        Besar harapan kami Bapak/Ibu bersedia memberikan bimbingan kepada mahasiswa tersebut demi kelancaran penyusunan tesisnya. Atas perhatian dan kerjasama Bapak/Ibu kami ucapkan terima kasih.
    </p>

    <br><br>

    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="text-align: center;">
                Surakarta, {{ \Carbon\Carbon::parse($data['tanggal'])->format('d') }}
                {{ $monthList[\Carbon\Carbon::parse($data['tanggal'])->format('M')] }}
                {{ \Carbon\Carbon::parse($data['tanggal'])->format('Y') }}<br>
                Ketua Program Studi Teknik Elektro,<br><br><br><br><br>
                <strong>Nama Ketua Prodi</strong><br>
                NIP. 196xxxxxxxxxxxxx
            </td>
        </tr>
    </table>
</div>
</body>
</html>
