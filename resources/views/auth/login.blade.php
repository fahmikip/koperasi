<x-guest-layout>
    <div class="mb-8"><div class="text-xs font-bold uppercase tracking-[.22em] text-gold-600">Selamat datang kembali</div><h1 class="mt-2 font-display text-3xl font-bold text-blue-950">Masuk ke akun Anda</h1><p class="mt-2 text-sm leading-relaxed text-slate-500">Gunakan akun yang telah terdaftar untuk mengakses layanan koperasi.</p></div>
    <x-auth-session-status class="mb-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="space-y-5">@csrf
        <div><x-input-label for="email" value="Alamat email" /><x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" placeholder="nama@koperasi.id" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
        <div><div class="flex items-center justify-between"><x-input-label for="password" value="Kata sandi" />@if(Route::has('password.request'))<a class="text-xs font-bold text-blue-700 hover:text-blue-900" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif</div><x-text-input id="password" class="mt-2 block w-full" type="password" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
        <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-600"><input id="remember_me" type="checkbox" class="rounded" name="remember"> Ingat saya di perangkat ini</label>
        <x-primary-button class="w-full justify-center py-3.5">Masuk ke Sistem</x-primary-button>
        @if(Route::has('register'))<p class="text-center text-sm text-slate-500">Belum memiliki akun? <a href="{{ route('register') }}" class="font-bold text-blue-700">Daftar sekarang</a></p>@endif
    </form>
</x-guest-layout>
