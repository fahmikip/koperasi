<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">Transaksi Simpanan</h1>
            @can('savings.manage')
                <a href="{{ route('savings.create') }}" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Tambah Transaksi</a>
            @endcan
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="mb-5 grid gap-4 sm:grid-cols-3">
        @foreach(['deposits' => 'Total Setoran', 'withdrawals' => 'Total Penarikan', 'balance' => 'Saldo'] as $key => $label)
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <div class="text-sm text-slate-500">{{ $label }}</div>
                <div class="mt-2 text-2xl font-bold {{ $key === 'withdrawals' ? 'text-rose-700' : 'text-blue-900' }}">Rp {{ number_format($summary->$key, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <form class="mb-5 flex flex-wrap gap-3">
            <input name="search" value="{{ request('search') }}" placeholder="Nomor transaksi atau anggota..." class="min-w-64 rounded-xl border-slate-200">
            <select name="saving_type_id" class="rounded-xl border-slate-200">
                <option value="">Semua jenis</option>
                @foreach($types as $type)<option value="{{ $type->id }}" @selected((string) request('saving_type_id') === (string) $type->id)>{{ $type->name }}</option>@endforeach
            </select>
            <select name="direction" class="rounded-xl border-slate-200">
                <option value="">Semua transaksi</option>
                <option value="deposit" @selected(request('direction') === 'deposit')>Setoran</option>
                <option value="withdrawal" @selected(request('direction') === 'withdrawal')>Penarikan</option>
            </select>
            <button class="rounded-xl bg-slate-800 px-4 py-2 text-white">Filter</button>
            <a href="{{ route('savings.index') }}" class="rounded-xl px-4 py-2 text-slate-600">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500"><tr><th class="py-3">Tanggal</th><th>Nomor</th><th>Anggota</th><th>Jenis</th><th>Transaksi</th><th class="text-right">Nominal</th><th></th></tr></thead>
                <tbody>
                    @forelse($savings as $saving)
                        <tr class="border-t">
                            <td class="whitespace-nowrap py-4">{{ $saving->transaction_date->format('d-m-Y') }}</td>
                            <td class="font-mono text-xs">{{ $saving->transaction_number }}</td>
                            <td><div class="font-semibold">{{ $saving->member->name }}</div><div class="text-xs text-slate-400">{{ $saving->member->member_number }}</div></td>
                            <td>{{ $saving->type->name }}</td>
                            <td><span class="rounded-full px-3 py-1 text-xs {{ $saving->direction === 'deposit' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $saving->direction === 'deposit' ? 'Setoran' : 'Penarikan' }}</span></td>
                            <td class="text-right font-semibold {{ $saving->direction === 'withdrawal' ? 'text-rose-700' : 'text-emerald-700' }}">{{ $saving->direction === 'withdrawal' ? '-' : '+' }} Rp {{ number_format($saving->amount, 0, ',', '.') }}</td>
                            <td class="text-right"><a href="{{ route('savings.show', $saving) }}" class="font-semibold text-blue-700">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400">Belum ada transaksi simpanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $savings->links() }}</div>
    </div>
</x-app-layout>
