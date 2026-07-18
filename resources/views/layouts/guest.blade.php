<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Koperasi Modern') }}</title>
    <meta name="theme-color" content="#7f2532">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lora:600,700|manrope:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans">
    <main class="grid min-h-screen bg-stone-50 lg:grid-cols-[1.08fr_.92fr]">
        <section class="koperasi-pattern relative hidden overflow-hidden bg-gradient-to-br from-blue-950 via-blue-900 to-blue-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full border-[50px] border-white/5"></div><div class="absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-gold-400/10 blur-3xl"></div>
            <a href="/" class="relative flex items-center gap-4"><div class="grid h-14 w-14 place-items-center rounded-2xl bg-white text-blue-800 shadow-xl"><x-application-logo class="h-10 w-10" /></div><div><div class="font-display text-2xl font-bold">Koperasi Modern</div><div class="text-xs font-bold uppercase tracking-[.28em] text-gold-300">Tumbuh Bersama</div></div></a>
            <div class="relative max-w-xl"><div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-widest text-gold-200"><span class="h-2 w-2 rounded-full bg-gold-300"></span> Digitalisasi Koperasi Indonesia</div><h1 class="font-display text-5xl font-bold leading-tight">Keuangan yang transparan, kesejahteraan yang berkelanjutan.</h1><p class="mt-6 max-w-lg text-lg leading-relaxed text-blue-100">Kelola anggota, simpanan, pinjaman, dan angsuran dalam satu sistem yang aman serta mudah digunakan.</p><div class="mt-10 grid grid-cols-3 gap-5 border-t border-white/10 pt-7"><div><div class="text-2xl font-extrabold text-gold-300">Aman</div><div class="mt-1 text-xs text-blue-200">Akses berbasis peran</div></div><div><div class="text-2xl font-extrabold text-gold-300">Rapi</div><div class="mt-1 text-xs text-blue-200">Audit setiap transaksi</div></div><div><div class="text-2xl font-extrabold text-gold-300">Cepat</div><div class="mt-1 text-xs text-blue-200">Layanan terintegrasi</div></div></div></div>
            <p class="relative text-xs text-blue-300">© {{ date('Y') }} Koperasi Modern · Dari anggota, oleh anggota, untuk anggota.</p>
        </section>
        <section class="flex items-center justify-center p-5 sm:p-10 lg:p-14"><div class="w-full max-w-md"><div class="mb-8 flex items-center gap-3 lg:hidden"><div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-800 text-white"><x-application-logo class="h-8 w-8" /></div><div><div class="font-display text-xl font-bold text-blue-950">Koperasi Modern</div><div class="text-[10px] font-bold uppercase tracking-[.2em] text-gold-600">Tumbuh Bersama</div></div></div><div class="rounded-3xl border border-white bg-white p-6 shadow-soft sm:p-9">{{ $slot }}</div><p class="mt-6 text-center text-xs text-slate-400">Butuh bantuan? Hubungi administrator koperasi Anda.</p></div></section>
    </main>
</body>
</html>
