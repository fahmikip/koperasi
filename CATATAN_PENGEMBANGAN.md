# Catatan Alur dan Pengembangan Sistem Koperasi

Dokumen ini menjadi panduan utama untuk memahami alur aplikasi, struktur teknis, status implementasi, dan urutan pengembangan Sistem Informasi Koperasi Modern.

## 1. Ringkasan Aplikasi

Aplikasi dibangun menggunakan:

- Laravel 13 dan PHP 8.3+
- MySQL untuk produksi dan SQLite untuk pengujian lokal
- Blade, Livewire, Alpine.js, dan Tailwind CSS
- Laravel Breeze untuk autentikasi
- Spatie Permission untuk role dan permission
- Spatie Activitylog untuk audit log
- DomPDF untuk dokumen PDF
- Simple QR Code untuk verifikasi anggota
- Laravel Excel untuk ekspor spreadsheet
- Chart.js, SweetAlert2, dan Simple DataTables

Arsitektur bisnis menggunakan alur berikut:

```text
Browser
   ↓
Route
   ↓
Middleware autentikasi dan authorization
   ↓
Controller
   ↓
Form Request → validasi dan permission
   ↓
Service → aturan bisnis dan database transaction
   ↓
Repository → akses data
   ↓
Eloquent Model
   ↓
MySQL
```

## 2. Alur Pengguna Web

### Pengguna belum login

1. Pengguna membuka `/`.
2. Sistem mengarahkan pengguna ke `/login`.
3. Pengguna memasukkan email dan password.
4. Laravel Breeze memvalidasi kredensial.
5. Login yang berhasil dicatat ke audit log.
6. Pengguna diarahkan ke dashboard.

Halaman `/verify-member/{token}` dapat dibuka tanpa login. Halaman ini hanya menampilkan foto, nama, nomor anggota, status, dan masa berlaku. NIK, alamat, nomor WhatsApp, dan informasi sensitif lain tidak ditampilkan.

### Pengguna setelah login

1. Sistem membaca role dan permission pengguna.
2. Menu hanya ditampilkan apabila pengguna memiliki permission terkait.
3. Dashboard menampilkan statistik sesuai data koperasi.
4. Setiap perubahan data melewati validasi dan authorization.
5. Aktivitas penting disimpan dalam audit log.
6. Logout pengguna juga dicatat.

## 3. Role dan Hak Akses

### Super Admin

Memiliki seluruh permission, termasuk pengguna, anggota, transaksi, laporan, dan audit log.

### Admin

Mengelola sebagian besar kegiatan operasional koperasi, tetapi hak khusus sistem dapat dibatasi.

### Bendahara

Mengelola simpanan, pinjaman, angsuran, dan laporan keuangan.

### Petugas

Mengelola pendaftaran anggota dan transaksi harian sesuai permission yang diberikan.

### Anggota

Direncanakan hanya dapat melihat profil dan transaksi miliknya sendiri.

Permission utama:

```text
members.view
members.create
members.update
members.delete
savings.view
savings.manage
loans.view
loans.manage
loans.create
loans.approve
loans.disburse
installments.manage
reports.view
users.manage
audit.view
```

## 4. Alur Modul Anggota

### Menambah anggota

1. Petugas membuka menu `Manajemen Anggota`.
2. Petugas memilih `Tambah Anggota`.
3. Form dikirim ke `MemberController@store`.
4. `MemberRequest` memvalidasi data dan permission.
5. `MemberService` membuka database transaction.
6. Sistem mencari nomor anggota terakhir dengan row lock.
7. Sistem membuat nomor baru dalam format `KOP-TAHUN-000001`.
8. Sistem menghasilkan UUID sebagai QR token.
9. Foto disimpan pada disk `public` di folder `members`.
10. Repository menyimpan anggota.
11. Activity log merekam pembuatan data.
12. Pengguna diarahkan ke halaman detail dengan notifikasi berhasil.

Nomor anggota memiliki unique index di database. Row lock dan unique index digunakan bersama untuk mencegah nomor ganda.

### Memperbarui anggota

1. Policy memeriksa permission `members.update`.
2. Form Request memvalidasi data.
3. Service menjalankan perubahan dalam transaction.
4. Jika foto diganti, foto lama dihapus dan foto baru disimpan.
5. Perubahan atribut dicatat dalam activity log.

### Menghapus anggota

Data anggota menggunakan soft delete. Data historis tidak langsung hilang dari database sehingga masih dapat ditelusuri untuk kebutuhan audit.

## 5. Alur QR dan Kartu Anggota

1. Setiap anggota memiliki `qr_token` unik.
2. QR mengarah ke `/verify-member/{token}`.
3. Token dicari pada tabel `members`.
4. Jika token tidak ditemukan, server mengembalikan halaman 404.
5. Jika ditemukan, halaman verifikasi aman ditampilkan.

Kartu menggunakan ukuran ISO ID-1, yaitu 85,60 mm × 53,98 mm. Pengguna berwenang dapat membuka preview dan mengunduh PDF. Setiap download dicatat pada `card_print_histories` dan activity log.

## 6. Alur Transaksi yang Direncanakan

### Simpanan

```text
Pilih anggota
   ↓
Pilih jenis simpanan
   ↓
Pilih setoran atau penarikan
   ↓
Validasi nominal dan saldo
   ↓
Generate nomor transaksi
   ↓
Simpan transaksi atomik
   ↓
Cetak bukti / ekspor laporan
```

Jenis simpanan awal:

- Simpanan Pokok
- Simpanan Wajib
- Simpanan Sukarela

Saldo tidak disimpan sebagai angka terpisah. Saldo dihitung dari total setoran dikurangi total penarikan agar sumber data tetap konsisten.

### Pinjaman

```text
Pengajuan
   ↓
Pemeriksaan kelayakan
   ↓
Persetujuan atau penolakan
   ↓
Pencairan
   ↓
Pembayaran angsuran
   ↓
Perhitungan denda
   ↓
Pelunasan
```

Status pinjaman:

```text
submitted → approved → disbursed → paid
          ↘ rejected
```

Perubahan status harus dilakukan melalui Service, divalidasi berdasarkan status sebelumnya, dan dijalankan dalam database transaction.

### Angsuran

1. Petugas memilih pinjaman aktif.
2. Sistem menghitung nomor angsuran berikutnya.
3. Sistem membagi nilai pembayaran menjadi pokok, bunga, dan denda.
4. Angsuran disimpan.
5. Sisa pinjaman dikurangi sebesar pokok yang dibayar.
6. Jika sisa pinjaman nol, status pinjaman menjadi `paid`.
7. Sistem membuat bukti pembayaran.

## 7. Struktur Folder Utama

```text
app/
├── Http/
│   ├── Controllers/       Controller HTTP
│   └── Requests/          Validasi dan authorization request
├── Models/                Model dan relasi Eloquent
├── Policies/              Authorization per model
├── Providers/             Binding dependency dan event listener
├── Repositories/
│   ├── Contracts/         Interface repository
│   └── ...                Implementasi Eloquent
└── Services/              Aturan bisnis dan transaction

database/
├── factories/             Generator data pengujian
├── migrations/            Struktur database
└── seeders/               Role, permission, master data, admin

resources/
├── css/                   Tailwind CSS
├── js/                    Alpine, Chart.js, SweetAlert2
└── views/                 Blade templates

routes/
├── auth.php               Route autentikasi Breeze
└── web.php                Route aplikasi koperasi

tests/
├── Feature/               Pengujian alur HTTP dan bisnis
└── Unit/                  Pengujian unit
```

## 8. Database dan Relasi

Tabel domain utama:

- `users`
- `members`
- `saving_types`
- `savings`
- `loans`
- `installments`
- `card_print_histories`
- `roles`, `permissions`, dan tabel pivot Spatie
- `activity_log`

Relasi utama:

```text
User 1 ─── 0..1 Member
Member 1 ─── * Saving
SavingType 1 ─── * Saving
Member 1 ─── * Loan
Loan 1 ─── * Installment
Member 1 ─── * CardPrintHistory
User 1 ─── * CardPrintHistory
User * ─── * Role
Role * ─── * Permission
```

## 9. Status Pengembangan

### Sudah tersedia

- Inisialisasi Laravel 13
- Autentikasi Breeze
- Role dan permission dasar
- Audit login dan logout
- Migration seluruh domain inti
- Model dan relasi inti
- Seeder role, permission, jenis simpanan, dan Super Admin
- Factory anggota
- Dashboard statistik dasar
- CRUD anggota
- Upload foto anggota
- Nomor anggota otomatis
- QR token dan halaman verifikasi
- Preview kartu anggota
- Download kartu PDF
- Riwayat download kartu
- Responsive dashboard shell
- SweetAlert2
- Build frontend produksi
- Test autentikasi dan anggota
- CRUD transaksi simpanan dan validasi saldo berjalan
- Workflow pengajuan, persetujuan, penolakan, dan pencairan pinjaman

### Belum selesai dan menjadi roadmap

1. Export simpanan ke PDF dan Excel.
2. Kalkulator bunga lanjutan dan simulasi jadwal angsuran.
3. Pembayaran angsuran, denda, dan pelunasan otomatis.
4. Laporan anggota, simpanan, pinjaman, angsuran, dan transaksi.
5. Halaman manajemen pengguna, role, dan permission.
6. Halaman penelusuran audit log.
7. Grafik dashboard berdasarkan periode.
8. Filter, sorting, pagination, print, dan export seluruh tabel.
9. Resize dan optimasi foto otomatis; server harus mengaktifkan ekstensi GD.
10. Penambahan sisi belakang kartu, tanda tangan, alamat, dan ketentuan.
11. Pengujian workflow keuangan dan concurrency.
12. Deployment, backup, queue worker, scheduler, dan monitoring.

## 10. Persiapan Lingkungan Development

Kebutuhan minimum:

```text
PHP 8.3+
Composer 2
Node.js 20+
MySQL 8+
Ekstensi PHP: bcmath, curl, dom, fileinfo, gd, mbstring,
mysqli, openssl, pdo_mysql, xml, xmlreader, xmlwriter, zip
```

Instalasi awal:

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
npm install
php artisan storage:link
```

Buat database MySQL bernama `koperasi_modern`, kemudian sesuaikan `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi_modern
DB_USERNAME=root
DB_PASSWORD=
```

Siapkan database dan aset:

```powershell
php artisan migrate --seed
npm run build
```

Menjalankan development server:

```powershell
composer run dev
```

Atau jalankan server secara terpisah:

```powershell
php artisan serve
npm run dev
```

Akun awal development:

```text
Email    : admin@koperasi.test
Password : password
```

Password tersebut wajib diganti sebelum deployment.

## 11. Pemeriksaan Kualitas

Jalankan sebelum setiap commit atau deployment:

```powershell
php artisan test
vendor\bin\pint --test
composer audit --locked
npm run build
```

Jika format PHP belum sesuai:

```powershell
vendor\bin\pint
```

Untuk membangun ulang database development:

```powershell
php artisan migrate:fresh --seed
```

Perintah tersebut menghapus seluruh data. Jangan digunakan pada produksi.

## 12. Aturan Pengembangan Modul Baru

Setiap modul baru sebaiknya mengikuti urutan berikut:

1. Tetapkan kebutuhan bisnis dan permission.
2. Buat atau evaluasi migration dan foreign key.
3. Buat model, cast, dan relasi.
4. Buat Form Request.
5. Buat Policy atau permission check.
6. Buat Repository interface dan implementasinya jika akses data kompleks.
7. Tempatkan aturan bisnis pada Service.
8. Gunakan database transaction untuk operasi keuangan.
9. Buat Controller yang tipis.
10. Buat Blade atau Livewire UI yang responsive.
11. Tambahkan audit log dan notifikasi.
12. Tambahkan Feature Test dan Unit Test.
13. Jalankan Pint, test, audit, dan build.

Controller tidak boleh memuat kalkulasi keuangan yang kompleks. Kalkulasi ditempatkan pada Service agar dapat diuji dan digunakan kembali.

## 13. Pedoman Keamanan Produksi

- Gunakan `APP_ENV=production` dan `APP_DEBUG=false`.
- Gunakan password database khusus aplikasi.
- Paksa HTTPS dan secure cookie.
- Jangan menaruh `.env` dalam version control.
- Batasi ukuran dan tipe upload.
- Jangan mengandalkan menu tersembunyi sebagai authorization; selalu gunakan Policy atau permission pada server.
- Gunakan queue untuk pembuatan laporan besar.
- Jadwalkan backup database dan storage.
- Simpan audit log sesuai kebijakan retensi organisasi.
- Ubah password Super Admin awal.
- Nonaktifkan registrasi publik jika akun hanya dibuat administrator.

## 14. Target Deployment

Urutan deployment yang disarankan:

```text
Provision server
   ↓
Konfigurasi PHP, web server, dan MySQL
   ↓
Salin source code dan .env produksi
   ↓
composer install --no-dev --optimize-autoloader
   ↓
npm ci && npm run build
   ↓
php artisan migrate --force
   ↓
php artisan storage:link
   ↓
php artisan optimize
   ↓
Jalankan queue worker dan scheduler
   ↓
Smoke test dan monitoring
```

Seeder development tidak boleh dijalankan tanpa evaluasi pada produksi karena mengandung akun awal dengan password yang diketahui.

---

Dokumen ini perlu diperbarui setiap kali sebuah modul, permission, perubahan database, atau prosedur deployment ditambahkan.
