<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="{{ asset('assets/icons/uns.png') }}" sizes="any">
    <title>Surat Seminar Hasil Tugas Akhir</title>
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
                PERMOHONAN TIM PEMBIMBING THESIS
            </strong>
        </div>
        <div style="text-align: justify;">
            {{-- Pembuka --}}
            <table style="margin-bottom: 5px; border: none;">
                <tr>
                    <td style="border: none;">Yth. Kepala Prodi Magister Perencanaan Wilayah dan Kota</td>
                </tr>
                <tr>
                    <td style="border: none; padding-left: 31px;">Fakultas Teknik</td>
                </tr>
                <tr>
                    <td style="border: none; padding-left: 31px;">Universitas Sebelas Maret</td>
                </tr>
            </table>
            </div>
            {{-- Penyataan --}}
            <p>Yang bertanda tangan di bawah ini, saya:</p>
            <table style="width: 100%;">
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
            </table>
            <p style="margin-bottom: 15px">Mengajukan proposal tesis berjudul:</p>
            <p style="text-align: center"><b>{{ $draft->us_judul }}</b></p>
            {{-- Pembimbing --}}
            <p style="margin-top: 15px">Sehubungan dengan pengajuan proposal tesis tersebut, maka saya mengajukan usulan calon tim pembimbing sebagai berikut:</p>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 5%; padding-bottom: 5px;">1.</td>
                    <td style="width: 60%; padding-bottom: 5px;"><b>{{ $draft->dosenPembimbingSatu->nama_dosen }}</b></td>
                    <td style="width: 20%; padding-bottom: 5px;">sebagai Pembimbing Utama</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 5px;">2.</td>
                    <td style="padding-bottom: 5px;"><b>{{ $draft->dosenPembimbingDua->nama_dosen }}</b></td>
                    <td style="padding-bottom: 5px;">sebagai Pembimbing Pendamping</td>
                </tr>
            </table>
            {{-- Signature --}}
            <p style="margin-top: 15px">Demikian atas perkenan dan kerjasamanya kami sampaikan terima kasih.</p><br>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; padding-bottom: 5px;">Mengetahui dan menyetujui,</td>
                    <td style="width: 40%; padding-bottom: 5px;">Surakarta, {{ \Carbon\Carbon::parse($draft->tgl_pengajuan)->locale('id')->translatedFormat('j F Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 60%; padding-bottom: 5px;">Pembimbing Utama</td>
                    <br><br><br><br><br>
                    <td style="width: 40%; padding-bottom: 5px;">Mahasiswa</td>
                    <br><br><br><br><br>
                </tr>
                <tr>
                    <td style="width: 60%; padding-bottom: 5px; line-height: 20px;">
                        {{ $draft->dosenPembimbingSatu->nama_dosen }}<br>
                        <b>NIP.{{ $draft->dosenPembimbingSatu->nip }}</b>
                    </td>
                    <td style="width: 40%; padding-bottom: 5px; line-height: 20px">
                        {{ $draft->mahasiswa->nama_mhs }}<br>
                        <b>NIM.{{ $draft->mahasiswa->nim }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width: 60%; padding-top: 50px;">Mengetahui dan menyetujui,</td>
                </tr>
                <tr>
                    <td style="width: 60%; padding-bottom: 5px;">Pembimbing Pendamping</td>
                    <br><br><br><br><br>
                </tr>
                <tr>
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
