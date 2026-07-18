<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-xs font-bold uppercase tracking-[.2em] text-gold-600">Pusat Kendali</div><h1 class="mt-1 font-display text-3xl font-bold text-blue-950">Ringkasan Koperasi</h1><p class="mt-1 text-sm text-slate-500">Pantau aktivitas dan kesehatan koperasi dalam satu halaman.</p></div><div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600"><span class="font-bold text-blue-800">{{ now()->translatedFormat('d F Y') }}</span></div></div></x-slot>

    <section class="koperasi-pattern relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-950 via-blue-900 to-blue-700 p-6 text-white shadow-soft sm:p-8"><div class="absolute -right-10 -top-16 h-52 w-52 rounded-full border-[35px] border-white/5"></div><div class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center"><div><div class="text-sm font-semibold text-gold-300">Selamat datang, {{ Auth::user()->name }}</div><h2 class="mt-2 max-w-2xl font-display text-2xl font-bold sm:text-3xl">Bersama anggota, membangun ekonomi yang lebih kuat.</h2><p class="mt-2 max-w-xl text-sm leading-relaxed text-blue-100">Kelola transaksi dengan transparan dan berikan pelayanan terbaik untuk seluruh anggota koperasi.</p></div><div class="flex flex-wrap gap-2">@can('members.create')<a href="{{ route('members.create') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-blue-900 shadow-lg transition hover:-translate-y-0.5">+ Anggota</a>@endcan @can('savings.manage')<a href="{{ route('savings.create') }}" class="rounded-xl bg-gold-400 px-4 py-2.5 text-sm font-bold text-blue-950 shadow-lg transition hover:-translate-y-0.5">+ Simpanan</a>@endcan @can('create', App\Models\Loan::class)<a href="{{ route('loans.create') }}" class="rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/20">+ Pinjaman</a>@endcan</div></div></section>

    @php
        $cards = [
            'members' => ['Total Anggota', 'Orang', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0'],
            'active' => ['Anggota Aktif', 'Orang', 'm5 12 4 4L19 6'],
            'inactive' => ['Anggota Nonaktif', 'Orang', 'M6 6l12 12M18 6 6 18'],
            'savings' => ['Total Simpanan', 'Rupiah', 'M4 7h16v13H4V7Zm0 4h16m-4 4h1'],
            'loans' => ['Sisa Pinjaman', 'Rupiah', 'M12 2v20m5-16H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
            'installments' => ['Total Angsuran', 'Rupiah', 'M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 6h6m-6 4h6'],
            'monthly_income' => ['Pendapatan Bulan Ini', 'Rupiah', 'm4 15 5-5 4 4 7-8m0 0h-5m5 0v5'],
        ];
    @endphp
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $key => [$label, $unit, $icon])
            <article class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-soft transition hover:-translate-y-1 hover:border-blue-100"><div class="flex items-start justify-between"><div class="grid h-11 w-11 place-items-center rounded-xl bg-blue-50 text-blue-800 transition group-hover:bg-blue-800 group-hover:text-white"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon }}"/></svg></div><span class="rounded-full bg-gold-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gold-700">{{ $unit }}</span></div><div class="mt-5 text-sm font-semibold text-slate-500">{{ $label }}</div><div class="mt-1 text-2xl font-extrabold tracking-tight text-blue-950">{{ in_array($key, ['members','active','inactive']) ? number_format($stats[$key]) : 'Rp '.number_format($stats[$key], 0, ',', '.') }}</div></article>
        @endforeach
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-soft"><div class="flex items-center justify-between border-b border-slate-100 px-6 py-5"><div><h2 class="font-display text-lg font-bold text-blue-950">Transaksi Simpanan Terbaru</h2><p class="mt-0.5 text-xs text-slate-400">Aktivitas keuangan terbaru anggota</p></div>@can('savings.view')<a href="{{ route('savings.index') }}" class="text-sm font-bold text-blue-700 hover:text-blue-900">Lihat semua →</a>@endcan</div><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="text-left"><tr><th class="py-3">Nomor</th><th>Anggota</th><th>Jenis</th><th class="text-right">Nilai</th></tr></thead><tbody>@forelse($recent as $row)<tr class="border-t border-slate-100 transition hover:bg-blue-50/40"><td class="py-4 font-mono text-xs text-slate-500">{{ $row->transaction_number }}</td><td class="font-bold text-slate-700">{{ $row->member->name }}</td><td><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $row->type->name }}</span></td><td class="text-right font-bold {{ $row->direction === 'withdrawal' ? 'text-rose-700' : 'text-emerald-700' }}">{{ $row->direction === 'withdrawal' ? '-' : '+' }} Rp {{ number_format($row->amount, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="4" class="py-12 text-center text-slate-400">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div></section>
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-soft">
        <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-end lg:justify-between">
            <div><div class="text-xs font-bold uppercase tracking-[.18em] text-gold-600">Analitik Periode</div><h2 class="mt-1 font-display text-xl font-bold text-blue-950">Pergerakan Keuangan</h2><p class="mt-1 text-xs text-slate-400">{{ $chart['caption'] }}</p></div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2" x-data="{ period: @js($chart['period']) }">
                <label><span class="mb-1 block text-[10px] font-bold uppercase text-slate-400">Periode</span><select name="period" x-model="period" class="min-w-40 text-sm"><option value="7_days">7 Hari</option><option value="30_days">30 Hari</option><option value="90_days">90 Hari</option><option value="year">Tahun Berjalan</option><option value="custom">Tanggal Khusus</option></select></label>
                <label x-show="period === 'custom'" x-cloak><span class="mb-1 block text-[10px] font-bold uppercase text-slate-400">Dari</span><input type="date" name="date_from" value="{{ $chart['from'] }}" class="text-sm"></label>
                <label x-show="period === 'custom'" x-cloak><span class="mb-1 block text-[10px] font-bold uppercase text-slate-400">Sampai</span><input type="date" name="date_to" value="{{ $chart['to'] }}" class="text-sm"></label>
                <button class="rounded-xl bg-blue-800 px-4 py-2.5 text-sm font-bold text-white">Tampilkan</button>
            </form>
        </div>
        <div class="grid gap-6 p-5 lg:grid-cols-[minmax(0,1fr)_220px] lg:p-6">
            <div class="relative h-80"><canvas id="financial-period-chart" aria-label="Grafik keuangan berdasarkan periode"></canvas></div>
            <div class="grid grid-cols-2 gap-3 self-start lg:grid-cols-1">
                @foreach([['Setoran', 'deposits', 'bg-emerald-500'], ['Penarikan', 'withdrawals', 'bg-rose-500'], ['Pencairan Pinjaman', 'loans', 'bg-gold-500'], ['Pembayaran Angsuran', 'installments', 'bg-blue-700']] as [$label, $key, $color])
                    <div class="rounded-xl bg-slate-50 p-3"><div class="flex items-center gap-2 text-xs font-semibold text-slate-500"><span class="h-2.5 w-2.5 rounded-full {{ $color }}"></span>{{ $label }}</div><div class="mt-1 text-sm font-extrabold text-blue-950">Rp {{ number_format(array_sum($chart['datasets'][$key]), 0, ',', '.') }}</div></div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('financial-period-chart');
            if (!canvas || typeof Chart === 'undefined') return;
            new Chart(canvas, { type: 'line', data: { labels: @json($chart['labels']), datasets: [
                { label: 'Setoran', data: @json($chart['datasets']['deposits']), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.1)', fill: true, tension: .35 },
                { label: 'Penarikan', data: @json($chart['datasets']['withdrawals']), borderColor: '#e11d48', backgroundColor: 'transparent', tension: .35 },
                { label: 'Pencairan Pinjaman', data: @json($chart['datasets']['loans']), borderColor: '#d59b19', backgroundColor: 'transparent', tension: .35 },
                { label: 'Pembayaran Angsuran', data: @json($chart['datasets']['installments']), borderColor: '#1e3a8a', backgroundColor: 'transparent', tension: .35 }
            ] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => `${context.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(context.raw)}` } } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { callback: value => `Rp ${new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value)}` }, grid: { color: 'rgba(148,163,184,.15)' } } } } });
        });
    </script>
</x-app-layout>
