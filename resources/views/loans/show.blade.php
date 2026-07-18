<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-bold">Detail Pinjaman</h1><div class="font-mono text-sm text-slate-500">{{ $loan->loan_number }}</div></div><div class="flex gap-2">@can('update', $loan)<a href="{{ route('loans.edit', $loan) }}" class="rounded-xl bg-slate-800 px-4 py-2 text-white">Edit</a>@endcan @can('delete', $loan)<form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Hapus pengajuan ini?')">@csrf @method('DELETE')<button class="rounded-xl bg-rose-700 px-4 py-2 text-white">Hapus</button></form>@endcan</div></div></x-slot>

    @if(session('success'))<div class="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-4 rounded-xl bg-rose-100 px-4 py-3 text-rose-800"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="mb-6 flex flex-wrap items-center gap-2 text-sm">
        @foreach(['submitted' => 'Pengajuan', 'approved' => 'Disetujui', 'disbursed' => 'Dicairkan', 'paid' => 'Lunas'] as $state => $label)
            @php $states = ['submitted' => 0, 'approved' => 1, 'disbursed' => 2, 'paid' => 3]; $active = $loan->status !== 'rejected' && $states[$loan->status] >= $states[$state]; @endphp
            <div class="rounded-full px-4 py-2 {{ $active ? 'bg-blue-700 text-white' : 'bg-slate-200 text-slate-500' }}">{{ $label }}</div>@if(!$loop->last)<span class="text-slate-300">→</span>@endif
        @endforeach
        @if($loan->status === 'rejected')<div class="rounded-full bg-rose-700 px-4 py-2 text-white">Ditolak</div>@endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-br from-blue-950 to-blue-700 p-6 text-white shadow-lg"><div class="text-sm text-blue-200">Total tagihan</div><div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($loan->total_payable, 0, ',', '.') }}</div><div class="mt-6 grid grid-cols-2 gap-4 text-sm"><div><div class="text-blue-300">Pokok</div><div class="font-semibold">Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}</div></div><div><div class="text-blue-300">Bunga</div><div class="font-semibold">Rp {{ number_format($loan->total_interest, 0, ',', '.') }}</div></div><div><div class="text-blue-300">Tenor</div><div class="font-semibold">{{ $loan->term_months }} bulan</div></div><div><div class="text-blue-300">Sisa</div><div class="font-semibold">Rp {{ number_format($loan->remaining_balance, 0, ',', '.') }}</div></div></div></div>
        <dl class="grid gap-5 rounded-2xl bg-white p-6 shadow-sm sm:grid-cols-2 lg:col-span-2">
            @foreach(['Anggota' => $loan->member->member_number.' — '.$loan->member->name, 'Tanggal pengajuan' => $loan->applied_at->format('d-m-Y'), 'Bunga flat' => number_format($loan->interest_rate, 4, ',', '.').' % per bulan', 'Disetujui oleh' => $loan->approver?->name ?? '-', 'Tanggal persetujuan' => $loan->approved_at?->format('d-m-Y') ?? '-', 'Tanggal pencairan' => $loan->disbursed_at?->format('d-m-Y') ?? '-', 'Tujuan' => $loan->purpose, 'Catatan' => $loan->notes ?: '-'] as $label => $value)<div><dt class="text-xs uppercase text-slate-400">{{ $label }}</dt><dd class="mt-1 whitespace-pre-line font-medium">{{ $value }}</dd></div>@endforeach
        </dl>
    </div>

    @can('review', $loan)
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            <form method="POST" action="{{ route('loans.approve', $loan) }}" class="rounded-2xl bg-white p-5 shadow-sm">@csrf<h2 class="font-bold text-emerald-800">Setujui Pinjaman</h2><p class="mt-1 text-sm text-slate-500">Pinjaman yang disetujui akan masuk antrean pencairan.</p><textarea name="review_notes" rows="3" placeholder="Catatan persetujuan (opsional)" class="mt-4 w-full rounded-xl border-slate-200"></textarea><button class="mt-3 rounded-xl bg-emerald-700 px-5 py-2 font-semibold text-white">Setujui</button></form>
            <form method="POST" action="{{ route('loans.reject', $loan) }}" class="rounded-2xl bg-white p-5 shadow-sm">@csrf<h2 class="font-bold text-rose-800">Tolak Pengajuan</h2><p class="mt-1 text-sm text-slate-500">Alasan penolakan wajib dicatat.</p><textarea name="review_notes" rows="3" placeholder="Alasan penolakan" class="mt-4 w-full rounded-xl border-slate-200" required></textarea><button class="mt-3 rounded-xl bg-rose-700 px-5 py-2 font-semibold text-white">Tolak</button></form>
        </div>
    @endcan

    @can('disburse', $loan)
        <form method="POST" action="{{ route('loans.disburse', $loan) }}" class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">@csrf<h2 class="font-bold text-blue-900">Pencairan Pinjaman</h2><p class="mt-1 text-sm text-blue-700">Pencairan mengaktifkan saldo tagihan sebesar Rp {{ number_format($loan->total_payable, 0, ',', '.') }}.</p><div class="mt-4 grid gap-4 md:grid-cols-2"><label><span class="text-sm font-medium">Tanggal pencairan</span><input type="date" name="disbursed_at" value="{{ now()->format('Y-m-d') }}" min="{{ $loan->approved_at?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="mt-1 w-full rounded-xl border-blue-200" required></label><label><span class="text-sm font-medium">Catatan pencairan</span><input name="disbursement_notes" maxlength="2000" class="mt-1 w-full rounded-xl border-blue-200"></label></div><button class="mt-4 rounded-xl bg-blue-700 px-5 py-2 font-semibold text-white">Konfirmasi Pencairan</button></form>
    @endcan

    <div class="mt-5"><a href="{{ route('loans.index') }}" class="font-semibold text-blue-700">← Kembali ke daftar pinjaman</a></div>
</x-app-layout>
