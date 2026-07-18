<x-guest-layout>
    <div class="mb-7"><div class="text-xs font-bold uppercase tracking-[.22em] text-gold-600">Keanggotaan digital</div><h1 class="mt-2 font-display text-3xl font-bold text-blue-950">Buat akun baru</h1><p class="mt-2 text-sm text-slate-500">Lengkapi data berikut untuk memulai.</p></div>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">@csrf
        <div><x-input-label for="name" value="Nama lengkap" /><x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" /><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
        <div><x-input-label for="email" value="Alamat email" /><x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-1" /></div>
        <div><x-input-label for="password" value="Kata sandi" /><x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-1" /></div>
        <div><x-input-label for="password_confirmation" value="Konfirmasi kata sandi" /><x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" /></div>
        <x-primary-button class="mt-2 w-full justify-center py-3.5">Daftar Akun</x-primary-button><p class="text-center text-sm text-slate-500">Sudah terdaftar? <a href="{{ route('login') }}" class="font-bold text-blue-700">Masuk di sini</a></p>
    </form>
</x-guest-layout>
