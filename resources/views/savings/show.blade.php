<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">Detail Transaksi Simpanan</h1>
            <div class="flex gap-2">
                @can('update', $saving)<a href="{{ route('savings.edit', $saving) }}" class="rounded-xl bg-slate-800 px-4 py-2 text-white">Edit</a>@endcan
                @can('delete', $saving)<form method="POST" action="{{ route('savings.destroy', $saving) }}" onsubmit="return confirm('Hapus transaksi ini? Saldo akan dihitung ulang.')">@csrf @method('DELETE')<button class="rounded-xl bg-rose-700 px-4 py-2 text-white">Hapus</button></form>@endcan
            </div>
        </div>
    </x-slot>

    @if(session('success'))<div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-emerald-800">{{ session('success') }}</div>@endif
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 p-6 text-white shadow-lg">
            <div class="text-sm text-blue-200">Saldo {{ $saving->type->name }}</div>
            <div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($balance, 0, ',', '.') }}</div>
            <div class="mt-6 font-mono text-sm text-blue-100">{{ $saving->transaction_number }}</div>
        </div>
        <dl class="grid gap-5 rounded-2xl bg-white p-6 shadow-sm sm:grid-cols-2 lg:col-span-2">
            @foreach([
                'Anggota' => $saving->member->member_number.' — '.$saving->member->name,
                'Jenis simpanan' => $saving->type->name,
                'Tanggal' => $saving->transaction_date->format('d-m-Y'),
                'Transaksi' => $saving->direction === 'deposit' ? 'Setoran' : 'Penarikan',
                'Nominal' => 'Rp '.number_format($saving->amount, 0, ',', '.'),
                'Dicatat oleh' => $saving->creator->name,
                'Catatan' => $saving->notes ?: '-',
            ] as $label => $value)<div><dt class="text-xs uppercase text-slate-400">{{ $label }}</dt><dd class="mt-1 font-medium">{{ $value }}</dd></div>@endforeach
        </dl>
    </div>
    <div class="mt-5"><a href="{{ route('savings.index') }}" class="font-semibold text-blue-700">← Kembali ke daftar transaksi</a></div>
</x-app-layout>
