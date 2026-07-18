<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-bold">{{ $saving->exists ? 'Edit' : 'Tambah' }} Transaksi Simpanan</h1></x-slot>

    <form method="POST" action="{{ $saving->exists ? route('savings.update', $saving) : route('savings.store') }}" class="rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @if($saving->exists) @method('PUT') @endif

        @if($errors->any())
            <div class="mb-5 rounded-xl bg-rose-100 px-4 py-3 text-rose-800">
                <div class="font-semibold">Transaksi belum dapat disimpan.</div>
                <ul class="mt-1 list-inside list-disc text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <label><span class="text-sm font-medium">Anggota aktif</span><select name="member_id" class="mt-1 w-full rounded-xl border-slate-200" required><option value="">Pilih anggota</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected((string) old('member_id', $saving->member_id) === (string) $member->id)>{{ $member->member_number }} — {{ $member->name }}</option>@endforeach</select></label>
            <label><span class="text-sm font-medium">Jenis simpanan</span><select name="saving_type_id" class="mt-1 w-full rounded-xl border-slate-200" required><option value="">Pilih jenis</option>@foreach($types as $type)<option value="{{ $type->id }}" data-default="{{ $type->default_amount }}" @selected((string) old('saving_type_id', $saving->saving_type_id) === (string) $type->id)>{{ $type->name }}</option>@endforeach</select></label>
            <label><span class="text-sm font-medium">Tanggal transaksi</span><input type="date" name="transaction_date" value="{{ old('transaction_date', $saving->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" class="mt-1 w-full rounded-xl border-slate-200" required></label>
            <label><span class="text-sm font-medium">Jenis transaksi</span><select name="direction" class="mt-1 w-full rounded-xl border-slate-200" required><option value="deposit" @selected(old('direction', $saving->direction ?? 'deposit') === 'deposit')>Setoran</option><option value="withdrawal" @selected(old('direction', $saving->direction) === 'withdrawal')>Penarikan</option></select></label>
            <label><span class="text-sm font-medium">Nominal (Rp)</span><input type="number" name="amount" value="{{ old('amount', $saving->amount) }}" min="0.01" step="0.01" class="mt-1 w-full rounded-xl border-slate-200" required></label>
            <label><span class="text-sm font-medium">Catatan</span><textarea name="notes" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border-slate-200">{{ old('notes', $saving->notes) }}</textarea></label>
        </div>
        <p class="mt-4 text-sm text-slate-500">Penarikan hanya dapat disimpan jika saldo jenis simpanan anggota tetap tidak negatif.</p>
        <div class="mt-6 flex justify-end gap-3"><a href="{{ $saving->exists ? route('savings.show', $saving) : route('savings.index') }}" class="rounded-xl px-5 py-3">Batal</a><button class="rounded-xl bg-blue-700 px-6 py-3 font-semibold text-white">Simpan Transaksi</button></div>
    </form>
</x-app-layout>
