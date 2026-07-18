<nav class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button @click="$dispatch('toggle-sidebar')" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-800 lg:hidden" aria-label="Buka menu"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            <div class="hidden sm:block"><div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sistem Informasi</div><div class="text-sm font-bold text-slate-700">Manajemen Koperasi</div></div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden rounded-xl bg-slate-50 px-3 py-2 text-right sm:block"><div class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</div><div class="text-[11px] text-slate-400">{{ Auth::user()->email }}</div></div>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger"><button class="grid h-10 w-10 place-items-center rounded-xl bg-blue-800 font-bold text-white shadow-md shadow-blue-900/15 transition hover:bg-blue-700">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</button></x-slot>
                <x-slot name="content"><div class="px-4 py-3 sm:hidden"><div class="truncate text-sm font-bold">{{ Auth::user()->name }}</div><div class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</div></div><x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link><form method="POST" action="{{ route('logout') }}">@csrf<x-dropdown-link :href="route('logout')" onclick="event.preventDefault();this.closest('form').submit();">Keluar</x-dropdown-link></form></x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>
