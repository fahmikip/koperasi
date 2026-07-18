<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between gap-3"><h1 class="text-2xl font-bold">Pinjaman</h1>@can('create', App\Models\Loan::class)<a href="{{ route('loans.create') }}" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Buat Pengajuan</a>@endcan</div></x-slot>

    @if(session('success'))<div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-emerald-800">{{ session('success') }}</div>@endif

    <div class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach(['total' => 'Total Pengajuan', 'submitted' => 'Menunggu Review', 'approved' => 'Siap Dicairkan'] as $key => $label)<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-2 text-2xl font-bold text-blue-900">{{ number_format($summary->$key) }}</div></div>@endforeach
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100"><div class="text-sm text-slate-500">Sisa Pinjaman Aktif</div><div class="mt-2 text-2xl font-bold text-blue-900">Rp {{ number_format($summary->outstanding, 0, ',', '.') }}</div></div>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <form class="mb-5 flex flex-wrap gap-3"><input name="search" value="{{ request('search') }}" placeholder="Nomor pinjaman atau anggota..." class="min-w-64 rounded-xl border-slate-200"><select name="status" class="rounded-xl border-slate-200"><option value="">Semua status</option>@foreach(['submitted' => 'Diajukan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'disbursed' => 'Dicairkan', 'paid' => 'Lunas'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select><button class="rounded-xl bg-slate-800 px-4 py-2 text-white">Filter</button><a href="{{ route('loans.index') }}" class="rounded-xl px-4 py-2 text-slate-600">Reset</a></form>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="text-left text-slate-500"><tr><th class="py-3">Nomor</th><th>Anggota</th><th>Tanggal</th><th class="text-right">Pokok</th><th>Tenor</th><th>Status</th><th></th></tr></thead><tbody>
            @forelse($loans as $loan)<tr class="border-t"><td class="py-4 font-mono text-xs">{{ $loan->loan_number }}</td><td><div class="font-semibold">{{ $loan->member->name }}</div><div class="text-xs text-slate-400">{{ $loan->member->member_number }}</div></td><td>{{ $loan->applied_at->format('d-m-Y') }}</td><td class="text-right font-semibold">Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}</td><td>{{ $loan->term_months }} bulan</td><td><span class="rounded-full px-3 py-1 text-xs {{ match($loan->status) {'submitted' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-blue-100 text-blue-700', 'rejected' => 'bg-rose-100 text-rose-700', 'disbursed' => 'bg-emerald-100 text-emerald-700', default => 'bg-slate-100 text-slate-700'} }}">{{ ['submitted'=>'Diajukan','approved'=>'Disetujui','rejected'=>'Ditolak','disbursed'=>'Dicairkan','paid'=>'Lunas'][$loan->status] }}</span></td><td class="text-right"><a href="{{ route('loans.show', $loan) }}" class="font-semibold text-blue-700">Detail</a></td></tr>
            @empty<tr><td colspan="7" class="py-12 text-center text-slate-400">Belum ada pengajuan pinjaman.</td></tr>@endforelse
        </tbody></table></div><div class="mt-5">{{ $loans->links() }}</div>
    </div>
</x-app-layout>
