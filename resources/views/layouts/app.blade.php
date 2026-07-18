<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
        <div class="min-h-screen lg:flex">
            <aside class="bg-gradient-to-b from-blue-950 to-blue-800 text-white lg:w-64 p-5">
                <div class="text-xl font-black tracking-tight mb-8">KOPERASI<span class="text-emerald-400">MODERN</span></div>
                <nav class="space-y-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 hover:bg-white/10">Dashboard</a>
                    @can('members.view')<a href="{{ route('members.index') }}" class="block rounded-xl px-4 py-3 hover:bg-white/10">Manajemen Anggota</a>@endcan
                    <a href="#" class="block rounded-xl px-4 py-3 text-blue-200">Simpanan</a><a href="#" class="block rounded-xl px-4 py-3 text-blue-200">Pinjaman & Angsuran</a><a href="#" class="block rounded-xl px-4 py-3 text-blue-200">Laporan</a>
                </nav>
            </aside>
            <main class="min-w-0 flex-1">
                @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
                <div class="p-4 lg:p-8">{{ $slot }}</div>
            </main>
        </div>
        @if(session('success'))<script>document.addEventListener('DOMContentLoaded',()=>Swal.fire({icon:'success',title:@json(session('success')),timer:1800,showConfirmButton:false}))</script>@endif
    </body>
</html>
