<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="{{ asset('assets/icons/uns.png') }}" sizes="any">
    <title>Surat Kelayakan</title>
    <style>
        @page {
            margin-top: 30px;
            margin-right: 50px;
            margin-left: 50px;
        }
        body {
            font-size: 14px;
        	line-height: 1.5;
            font-family: "Times New Roman", Times, serif;
        }
    </style>
</head>
<body>
    <header name="page-header">
        @include('layouts.kop_surat')
    </header>
    <div style="font-family:'Times New Roman', Times, serif; font-size: 14px;">
        <div style="padding-top: 15px; text-align: center; margin-bottom: 15px;">
            <strong style="font-size: 15px">
                SURAT KELAYAKAN MENGIKUTI SIDANG PROPOSAL TESIS
            </strong>
        </div>
        <div style="text-align: justify;">
            {{-- Pembuka --}}
            <table style="margin-bottom: 5px; border: none;">
                <tr>
                    <td style="border: none;">Laporan Proposal Tesis yang telah disusun oleh:</td>
                </tr>
                <tr>
                    <td style="width: 20%; padding-bottom: 5px;">Nama</td>
                    <td style="width: 3%; padding-bottom: 5px;" >:</td>
                    <td style="width: 67%; padding-bottom: 5px;"><b>{{ $draft->mahasiswa->nama_mhs }}</b></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">NIM</td>
                    <td style="padding-bottom: 5px;">:</td>
                    <td style="padding-bottom: 5px;"><b>{{ $draft->mahasiswa->nim }}</b></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">Prodi/Fakultas</td>
                    <td style="padding-bottom: 5px;">:</td>
                    <td style="padding-bottom: 5px;"><b>PWK/Tenkik</b></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">Tema/Topik</td>
                    <td style="padding-bottom: 5px;">:</td>
                    <td style="padding-bottom: 5px;"><b>Lorem Ipsum</b></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">Judul Penelitian</td>
                    <td style="padding-bottom: 5px;">:</td>
                    <td style="padding-bottom: 5px;"><b>{{ $draft->us_judul }}</b></td>
                </tr>
            </table>
            </div>
            {{-- Pembimbing --}}
            <p style="margin-top: 15px">Telah memenuhi ketentuan dan persyaratan secara substansi maupun teknis untuk bisa diujikan pada Sidang Proposal Tesis/Tesis1/Tesis 2 Tahun Ajaran 202x/202x. Adapun draft yang mendapatkan kelayakan dapat dilihat pada link</p>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 5%; padding-bottom: 5px; text-align: center;">link draft</td>
                </tr>
            </table>
            {{-- Signature --}}
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; padding-bottom: 5px;">Menyetujui,</td>
                    <td style="width: 40%; padding-bottom: 5px;">Surakarta, {{ \Carbon\Carbon::parse($draft->tgl_pengajuan)->locale('id')->translatedFormat('j F Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 60%; padding-bottom: 5px;">Pembimbing Utama</td>
                    <br><br><br><br><br>
                    <td style="width: 60%; padding-bottom: 5px;">Pembimbing Pendamping</td>
                    <br><br><br><br><br>
                </tr>
                <tr>
                    <td style="width: 60%; padding-bottom: 5px; line-height: 20px;">
                        {{ $draft->dosenPembimbingSatu->nama_dosen }}<br>
                        <b>NIP.{{ $draft->dosenPembimbingSatu->nip }}</b>
                    </td>
                    <td style="width: 60%; padding-bottom: 5px; line-height: 20px;">
                        {{ $draft->dosenPembimbingDua->nama_dosen }}<br>
                        <b>NIP.{{ $draft->dosenPembimbingDua->nip }}</b>
                    </td>
                </tr>
            </table>
        </div>


    </div>
</body>
</html>
