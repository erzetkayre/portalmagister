@extends('layouts.pwk-pdf')

@section('title', "Surat Keterangan Permohonan Pembimbing Tesis")
@section('kop-type', 'new')
@section('styles')
    p {
        margin: 0;
        padding: 0;
        line-height: 1.5;
    }
    table {
        width: 100%;
    }
    .text {
        text-align: justify;
        margin-bottom: 8px;
    }
@endsection

@section('content')
    <p style="text-align: center; text-transform: uppercase; font-weight: bold; padding-top: 5px; padding-bottom: 15px">Permohonan Tim Pembimbing Tesis</p>
    <div class="text">
        <p>Yth. Kepala Prodi Magister Perencanaan Wilayah dan Kota</p>
        <p style="text-indent: 30px;">Fakultas Teknik</p>
        <p style="text-indent: 30px">Universitas Sebelas Maret</p>
    </div>
    <p>Yang bertanda tangan di bawah ini, saya:</p>
    <table style="padding: 10px 0 15px 0; line-height: 1.5; vertical-align: top;">
        <tr>
            <td style="width: 15%; padding-left: 30px">Nama</td>
            <td style="width: 5%">:</td>
            <td style="width: 80%">{{ $mhs->nama_mhs }}</td>
        </tr>
        <tr>
            <td style="padding-left: 30px">NIM</td>
            <td>:</td>
            <td>{{ $mhs->nim }}</td>
        </tr>
    </table>
    <p>Mengajukan proposal tesis berjudul:</p>
    <p style="text-align: center; padding: 15px 0 20px 0;"><strong>{{ $tesis->judul }}</strong></p>
    <p class="text">Sehubungan dengan pengajuan proposal tesis tersebut, maka saya mengajukan usulan calon tim pembimbing sebagai berikut:</p>
    @php
        $pem1 = $tesis->pengajuan->firstWhere('slot', 1)?->dosen;
        $pem2 = $tesis->pengajuan->firstWhere('slot', 2)?->dosen;
    @endphp
    <table style="padding-bottom: 15px; line-height: 1.5; vertical-align: top;">
        <tr>
            <td style="width: 3%">1</td>
            <td style="width: 62%">{{ $pem1->nama_dsn ?? '-' }}</td>
            <td style="width: 35%;">sebagai Pembimbing Utama</td>
        </tr>
        <tr>
            <td>2</td>
            <td>{{ $pem2->nama_dsn ?? '-' }}</td>
            <td>sebagai Pembimbing Pendamping</td>
        </tr>
    </table>
    <p class="text">Demikian atas perkenan dan kerjasamanya kami sampaikan terima kasih.</p>
    <table style="padding: 15px 0 15px 0; line-height: 1.5; vertical-align: top;">
        <tr>
            <td style="width: 65%;">
                <p>Mengetahui/Menyetujui</p>
                <p>Pembimbing Utama</p>
                <br><br><br>
                <p><strong>{{ $pem1->nama_dsn ?? '-' }}</strong></p>
                <p>NIP.{{ $pem1->nip ?? '-' }}</p>
            </td>
            <td style="width: 45%;">
                <p>Surakarta, {{ $tesis->created_at->locale('id')->translatedFormat('d F Y') }}</p>
                <p>Mahasiswa</p>
                <br><br><br>
                <p><strong>{{ $mhs->nama_mhs }}</strong></p>
                <p>NIM.{{ $mhs->nim }}</p>
            </td>
        </tr>
    </table>
    <table style="padding-top: 15px; line-height: 1.5; vertical-align: top;">
        <tr>
            <td style="width: 60%; vertical-align: top; inline-height: 1.5;">
                <p>Mengetahui/Menyetujui</p>
                <p>Pembimbing Pendamping</p>
                <br><br><br>
                <p><strong>{{ $pem2->nama_dsn ?? '-' }}</strong></p>
                <p>NIP. {{ $pem2->nip ?? '-' }}</p>
            </td>
            <td style="width: 45%"></td>
        </tr>
    </table>
@endsection
