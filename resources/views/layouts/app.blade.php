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
    <div x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen" class="min-h-screen bg-stone-50">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="koperasi-pattern fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-gradient-to-b from-blue-950 via-blue-900 to-blue-800 text-white shadow-2xl transition-transform duration-300 lg:translate-x-0">
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-blue-800 shadow-lg"><x-application-logo class="h-8 w-8" /></div>
                <div><div class="font-display text-lg font-bold leading-tight">Koperasi Modern</div><div class="text-[10px] font-bold uppercase tracking-[.24em] text-gold-300">Tumbuh Bersama</div></div>
                <button @click="sidebarOpen = false" class="ml-auto rounded-lg p-2 text-blue-200 hover:bg-white/10 lg:hidden" aria-label="Tutup menu">✕</button>
            </div>

            <div class="px-5 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[.22em] text-blue-300">Menu Utama</div>
            <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 pb-6">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13h8V3H3v10Zm10 8h8V11h-8v10ZM3 21h8v-6H3v6Zm10-12h8V3h-8v6Z"/></svg>Dashboard</a>
                @can('members.view')<a href="{{ route('members.index') }}" class="sidebar-link {{ request()->routeIs('members.*') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m19 0v-2a4 4 0 0 0-3-3.87M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-7.87a4 4 0 0 1 0 7.75"/></svg>Anggota</a>@endcan
                @can('savings.view')<a href="{{ route('savings.index') }}" class="sidebar-link {{ request()->routeIs('savings.*') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v13H4V7Zm0 4h16M16 15h1m-9-8V5a2 2 0 0 1 2-2h8"/></svg>Simpanan</a>@endcan
                @can('loans.view')<a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2v20m5-16.5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>Pinjaman</a>@endcan
                @canany(['installments.view', 'installments.manage'])<a href="{{ route('installments.index') }}" class="sidebar-link {{ request()->routeIs('installments.*') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Zm3 6h6m-6 4h6m-6 4h3"/></svg>Angsuran</a>@endcanany
                @can('reports.view')<a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : '' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V9m6 10V5m6 14v-7m4 7H2"/></svg>Laporan</a>@endcan
            </nav>

            <div class="m-4 rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur"><div class="flex items-center gap-3"><div class="grid h-10 w-10 place-items-center rounded-xl bg-gold-400 font-bold text-blue-950">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div><div class="min-w-0"><div class="truncate text-sm font-bold">{{ Auth::user()->name }}</div><div class="truncate text-xs text-blue-200">{{ Auth::user()->getRoleNames()->first() ?: 'Pengguna' }}</div></div></div></div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            @include('layouts.navigation')
            @isset($header)<header class="border-b border-slate-200/80 bg-white"><div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">{{ $header }}</div></header>@endisset
            <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">{{ $slot }}</main>
            <footer class="mx-auto max-w-7xl px-6 pb-8 text-center text-xs text-slate-400">Koperasi Modern · Transparan, aman, dan bertumbuh bersama</footer>
        </div>
    </div>
    @if(session('success'))<script>document.addEventListener('DOMContentLoaded',()=>Swal.fire({icon:'success',title:@json(session('success')),timer:1800,showConfirmButton:false,confirmButtonColor:'#7f2532'}))</script>@endif
</body>
</html>
