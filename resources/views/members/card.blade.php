<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kartu Anggota · {{ $member->member_number }}</title>
    <style>
        @page { margin: 0; size: 85.6mm 53.98mm; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: DejaVu Sans, Arial, sans-serif; }
        body { width: 85.6mm; height: 53.98mm; }
        .member-card { position: relative; width: 85.6mm; height: 53.98mm; overflow: hidden; color: #fff; background: #681f2b; background-image: linear-gradient(135deg, #310b12 0%, #681f2b 52%, #9e3543 100%); }
        .glow { position: absolute; width: 48mm; height: 48mm; right: -19mm; top: -22mm; border: 7mm solid rgba(255,255,255,.045); border-radius: 50%; }
        .glow-two { position: absolute; width: 34mm; height: 34mm; left: -16mm; bottom: -22mm; border: 5mm solid rgba(245,184,46,.08); border-radius: 50%; }
        .pattern { position: absolute; inset: 0; opacity: .07; background-image: radial-gradient(#fff 0.35mm, transparent 0.4mm); background-size: 4mm 4mm; }
        .gold-line { position: absolute; left: 0; top: 0; width: 2.2mm; height: 100%; background: #f5b82e; }
        .header { position: absolute; left: 5mm; right: 4.5mm; top: 3.5mm; height: 10mm; border-bottom: .25mm solid rgba(255,255,255,.2); }
        .emblem { position: absolute; left: 0; top: 0; width: 8.5mm; height: 8.5mm; border-radius: 2.4mm; background: #fff; color: #7f2532; text-align: center; }
        .emblem svg { width: 6.8mm; height: 6.8mm; margin-top: .8mm; }
        .brand { position: absolute; left: 10.5mm; top: .5mm; font-size: 3.3mm; line-height: 1.05; font-weight: 800; letter-spacing: -.1mm; }
        .tagline { margin-top: 1.1mm; color: #fcd34d; font-size: 1.55mm; font-weight: 700; letter-spacing: .38mm; text-transform: uppercase; }
        .member-chip { position: absolute; right: 0; top: .6mm; padding: 1.2mm 2.3mm; border: .2mm solid rgba(255,255,255,.22); border-radius: 4mm; background: rgba(255,255,255,.1); font-size: 1.55mm; font-weight: 700; letter-spacing: .28mm; }
        .photo-wrap { position: absolute; left: 5mm; top: 16.2mm; width: 18.5mm; height: 23.5mm; padding: .7mm; border-radius: 3.2mm; background: #fff; box-shadow: 0 1.5mm 4mm rgba(25,4,8,.3); }
        .photo { width: 100%; height: 100%; border-radius: 2.5mm; object-fit: cover; }
        .photo-fallback { width: 100%; height: 100%; border-radius: 2.5mm; background: #f8e7e8; color: #7f2532; text-align: center; font-size: 8mm; font-weight: bold; line-height: 22mm; }
        .member-data { position: absolute; left: 27mm; top: 17mm; width: 38mm; }
        .label { color: #fcd34d; font-size: 1.55mm; font-weight: 700; letter-spacing: .25mm; text-transform: uppercase; }
        .name { width: 37mm; margin-top: 1mm; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: 3.35mm; line-height: 1.15; font-weight: 800; text-transform: uppercase; }
        .number { margin-top: 1.2mm; font-family: DejaVu Sans Mono, monospace; font-size: 2.35mm; font-weight: 700; letter-spacing: .18mm; }
        .details { margin-top: 3.1mm; border-left: .55mm solid #f5b82e; padding-left: 2mm; color: #f8e7e8; font-size: 1.75mm; line-height: 1.65; }
        .details strong { color: #fff; font-weight: 700; }
        .qr-box { position: absolute; right: 4.5mm; bottom: 8.5mm; width: 16.8mm; height: 16.8mm; padding: 1.2mm; border-radius: 2.3mm; background: #fff; text-align: center; }
        .qr-box svg { width: 14.4mm !important; height: 14.4mm !important; }
        .qr-caption { position: absolute; right: 4.5mm; bottom: 5.3mm; width: 16.8mm; color: #f8e7e8; font-size: 1.3mm; text-align: center; }
        .footer { position: absolute; left: 5mm; right: 4.5mm; bottom: 2.5mm; padding-top: 1.2mm; border-top: .2mm solid rgba(255,255,255,.18); color: rgba(255,255,255,.72); font-size: 1.35mm; letter-spacing: .1mm; }
        .status { float: right; color: {{ $member->status === 'active' ? '#fcd34d' : '#fecaca' }}; font-weight: bold; text-transform: uppercase; }
        .screen-actions { display: none; }
        @media screen {
            html, body { width: 100%; height: 100%; min-height: 100vh; }
            body { display: grid; place-items: center; background: #f5f5f4; }
            .member-card { box-shadow: 0 24px 60px -20px rgba(49,11,18,.5); transform: scale(1.5); border-radius: 3mm; }
            .screen-actions { position: fixed; z-index: 10; left: 50%; bottom: 28px; display: flex; gap: 10px; transform: translateX(-50%); }
            .screen-actions a, .screen-actions button { border: 0; border-radius: 12px; padding: 11px 18px; cursor: pointer; background: #7f2532; color: white; font: 700 13px Arial; text-decoration: none; box-shadow: 0 8px 22px rgba(49,11,18,.2); }
            .screen-actions a { background: #fff; color: #681f2b; }
        }
        @media screen and (max-width: 600px) { .member-card { transform: scale(1); } }
        @media print { .screen-actions { display: none !important; } }
    </style>
</head>
<body>
    <div class="member-card">
        <div class="pattern"></div><div class="glow"></div><div class="glow-two"></div><div class="gold-line"></div>
        <div class="header">
            <div class="emblem"><svg viewBox="0 0 64 64" fill="none"><path d="M32 5 53 17v26L32 56 11 43V17L32 5Z" stroke="currentColor" stroke-width="4"/><circle cx="32" cy="23" r="6" fill="currentColor"/><circle cx="20" cy="30" r="4" fill="currentColor" opacity=".75"/><circle cx="44" cy="30" r="4" fill="currentColor" opacity=".75"/><path d="M18 45c1-7 6-11 14-11s13 4 14 11" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg></div>
            <div class="brand">KOPERASI MODERN<div class="tagline">Dari anggota · untuk anggota</div></div>
            <div class="member-chip">KARTU ANGGOTA</div>
        </div>

        <div class="photo-wrap">
            @if($member->hasStoredPhoto())
                <img class="photo" src="{{ $isPdf ? Storage::disk('public')->path($member->photo_path) : $member->photoUrl() }}" alt="Foto {{ $member->name }}">
            @else
                <div class="photo-fallback">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
            @endif
        </div>

        <div class="member-data"><div class="label">Nama Anggota</div><div class="name">{{ $member->name }}</div><div class="number">{{ $member->member_number }}</div><div class="details"><strong>Bergabung</strong> {{ $member->joined_at->format('d/m/Y') }}<br><strong>Berlaku</strong> {{ $member->valid_until->format('d/m/Y') }}</div></div>
        <div class="qr-box">{!! QrCode::size(60)->margin(0)->generate(route('members.verify', $member->qr_token)) !!}</div><div class="qr-caption">PINDAI UNTUK VERIFIKASI</div>
        <div class="footer">VERIFIKASI DIGITAL RESMI <span class="status">{{ $member->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</span></div>
    </div>

    @unless($isPdf)<div class="screen-actions"><a href="{{ route('members.show', $member) }}">Kembali</a><button onclick="window.print()">Cetak Kartu</button></div>@endunless
</body>
</html>
