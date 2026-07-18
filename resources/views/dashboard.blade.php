<x-app-layout>
 <x-slot name="header"><h1 class="text-2xl font-bold">Ringkasan Koperasi</h1></x-slot>
 <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
 @foreach(['members'=>'Total Anggota','active'=>'Anggota Aktif','inactive'=>'Nonaktif','savings'=>'Total Simpanan','loans'=>'Sisa Pinjaman','installments'=>'Total Angsuran','monthly_income'=>'Pendapatan Bulan Ini'] as $key=>$label)
  <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-2 text-2xl font-extrabold text-blue-900">{{ in_array($key,['members','active','inactive']) ? number_format($stats[$key]) : 'Rp '.number_format($stats[$key],0,',','.') }}</div></div>
 @endforeach
 </div>
 <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm"><h2 class="font-bold">Transaksi Terbaru</h2><div class="mt-4 overflow-x-auto"><table class="w-full text-sm"><thead class="text-left text-slate-500"><tr><th class="py-3">Nomor</th><th>Anggota</th><th>Jenis</th><th class="text-right">Nilai</th></tr></thead><tbody>@forelse($recent as $row)<tr class="border-t"><td class="py-3">{{ $row->transaction_number }}</td><td>{{ $row->member->name }}</td><td>{{ $row->type->name }}</td><td class="text-right">Rp {{ number_format($row->amount,0,',','.') }}</td></tr>@empty<tr><td colspan="4" class="py-8 text-center text-slate-400">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div></div>
</x-app-layout>
