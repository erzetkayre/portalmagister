@php
    $kopType = \Illuminate\Support\Facades\View::yieldContent('kop-type', 'new');
    $kopMargin = $kopType === 'old' ? '44mm' : '32mm';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Document')</title>
    <style>
        body {
            font-size: 14px;
            line-height: 1.5;
            font-family: 'Times New Roman', Times, serif;
        }
        @page {
            header: page-header;
            margin-top: {{ $kopMargin }};
            margin-left: 20mm;
            margin-right: 20mm;
            margin-bottom: 30mm;
        }
        .page-break { page-break-after: always; }
        @yield('styles')
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
        @if($kopType === 'old')
            @include('pwk.template.kop-surat-old')
        @else
            @include('pwk.template.kop-surat-new')
        @endif
    </htmlpageheader>
    <br>
    @yield('content')
</body>
</html>
