@extends('layouts.admin')
@section('title', 'QR Code - ' . $election->title)

@section('breadcrumb')
<a href="{{ route('admin.election', $election->slug) }}" class="hover:text-white">{{ $election->title }}</a>
<span>/</span>
<span class="text-sky-100">QR Code</span>
@endsection

@section('admin-content')
<h1 class="text-xl sm:text-2xl font-bold text-white mb-6">QR Code</h1>

<div class="max-w-md mx-auto">
    <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 text-center">
        <h2 class="text-white font-semibold mb-2">{{ $election->title }}</h2>
        <p class="text-sky-200/60 text-xs mb-4 break-all">{{ $url }}</p>

        <div class="bg-white rounded-2xl p-6 inline-block mb-4">
            {!! $qrSvg !!}
        </div>

        <p class="text-sky-200/40 text-xs mb-4">Imbas QR code di atas untuk mengakses borang</p>

        <a href="{{ route('admin.qr.pdf', $election->slug) }}"
           class="inline-block px-6 py-3 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-sm font-semibold transition-colors shadow-lg">
            Muat Turun PDF (A4)
        </a>
    </div>
</div>
@endsection
