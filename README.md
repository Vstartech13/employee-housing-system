# Sistem Manajemen Perumahan Karyawan

Aplikasi web untuk mengelola penempatan karyawan dan tamu di kamar mess/perumahan perusahaan.

## Deskripsi Sistem

Sistem ini dibuat untuk memudahkan pengelolaan kamar karyawan, mulai dari pendataan kamar, karyawan, hingga proses check-in dan check-out. Sistem juga mendukung pengelolaan tamu dengan durasi menginap yang sudah ditentukan.

Fitur utama:

-   CRUD karyawan dengan ID otomatis berdasarkan departemen (contoh: HR001, FIN002)
-   CRUD kamar dengan kode otomatis berdasarkan kapasitas (contoh: M-1-001 untuk kamar kapasitas 1 orang)
-   Penempatan karyawan ke kamar dengan validasi kapasitas
-   Check-in tamu dengan durasi menginap (1-90 hari)
-   Otomatis checkout tamu yang melebihi durasi
-   History tracking untuk audit trail
-   Soft delete untuk semua data penting

## Modul Aplikasi

### 1. Manajemen Departemen

Mengelola data departemen perusahaan. Setiap departemen punya kode unik (HR, FIN, PROD, dll) yang digunakan untuk generate ID karyawan.

### 2. Manajemen Karyawan

Pendataan karyawan dengan auto-generate ID. Format ID: {KODE_DEPT}{3digit} - contoh HR001, FIN017.
Status karyawan bisa: Aktif, Cuti, Resign.

### 3. Manajemen Kamar

Pendataan kamar mess dengan kode otomatis. Format: M-{kapasitas}-{3digit} - contoh M-1-001, M-2-015.
Kapasitas maksimal per kamar: 4 orang.

### 4. Room Occupancy (Check-in/Check-out)

Modul utama untuk mengelola penghuni kamar:

-   Check-in karyawan/tamu
-   Check-out manual
-   Auto-checkout untuk tamu (via scheduled task)
-   Relokasi karyawan antar kamar
-   Tracking durasi tamu

### 5. History

Mencatat semua aktivitas check-in, check-out, dan relokasi untuk keperluan audit.

## Struktur Database

### Tabel Utama

**departments**

-   id, name, code
-   Relasi: hasMany ke employees

**employees**

-   id, employee_id (auto), name, department_id, status
-   Relasi: belongsTo department, hasMany room_occupancies

**rooms**

-   id, room_code (auto), capacity, status, occupied_count
-   Relasi: hasMany room_occupancies

**room_occupancies**

-   id, employee_id, room_id, check_in_date, check_out_date
-   is_guest, guest_name, guest_purpose, guest_duration_days, estimated_checkout_date
-   Relasi: belongsTo employee & room, hasMany histories

**room_occupancy_histories**

-   Menyimpan log aktivitas: check_in, check_out, relocate, auto_checkout
-   Relasi: belongsTo room_occupancy

### Relasi Data

```
departments (1) --- (N) employees
employees (1) --- (N) room_occupancies
rooms (1) --- (N) room_occupancies
room_occupancies (1) --- (N) room_occupancy_histories
```

Semua tabel utama menggunakan soft delete untuk menjaga integritas data historical.

## Validasi & Business Logic

1. **Kapasitas Kamar**: Tidak bisa check-in jika kamar penuh
2. **Status Karyawan**: Hanya karyawan Aktif yang bisa check-in
3. **Durasi Tamu**: Minimal 1 hari, maksimal 90 hari
4. **Tanggal Check-in**: Tidak boleh sebelum hari ini
5. **Reduce Capacity**: Tidak bisa mengurangi kapasitas kamar jika penghuni melebihi kapasitas baru
6. **Delete Restrictions**: Tidak bisa hapus kamar/karyawan yang sedang aktif

## Scheduled Task

**Auto-checkout Tamu**

-   Runs: Setiap hari jam 00:01
-   Command: `php artisan guests:auto-checkout`
-   Fungsi: Otomatis checkout tamu yang melewati estimated_checkout_date

Untuk setup di server production, tambahkan di crontab:

```
1 0 * * * cd /path/to/project && php artisan guests:auto-checkout
```

## Asumsi Sistem

1. Satu karyawan hanya bisa menempati satu kamar dalam waktu bersamaan
2. ID karyawan dan kode kamar di-generate otomatis, tidak bisa diubah manual
3. Tamu tidak perlu terdaftar sebagai employee
4. Durasi tamu dihitung dalam hari penuh (bukan jam)
5. Auto-checkout tamu hanya check tanggal, tidak peduli jam
6. Departemen harus ada sebelum input karyawan
7. Kapasitas kamar maksimal 4 orang (bisa disesuaikan)

## Keterbatasan Sistem

1. **Tidak ada role management** - semua user bisa akses semua fitur
2. **Tidak ada approval workflow** - check-in/check-out langsung diproses
3. **Tidak ada notifikasi** - tidak ada email/SMS reminder untuk tamu
4. **Tidak ada dashboard analytics** - belum ada visualisasi data occupancy
5. **Relokasi sederhana** - belum ada konfirmasi ketersediaan kamar tujuan secara real-time
6. **History read-only** - tidak bisa edit/delete history record
7. **Single language** - hanya bahasa Indonesia
8. **Auto-checkout fixed time** - jam checkout tamu fixed di 00:01, tidak bisa custom per tamu

## Tech Stack

-   Laravel 12
-   MySQL
-   DevExtreme 23.2.5 (DataGrid)
-   jQuery 3.7.1
-   Tailwind CSS

## Setup Project

1. Clone repository
2. `composer install`
3. Copy `.env.example` ke `.env`
4. Setup database di `.env`
5. `php artisan migrate`
6. `php artisan db:seed` (opsional)
7. `php artisan serve`

## Testing

Untuk test scheduled task:

```bash
php artisan guests:auto-checkout
```

Untuk test di tinker:

```bash
php artisan tinker
>>> App\Models\Room::create(['capacity' => 2])
>>> App\Models\Employee::create(['name' => 'Test', 'department_id' => 1])
```

---

**Catatan**: Sistem ini masih bisa dikembangkan lebih lanjut sesuai kebutuhan. Beberapa improvement yang bisa ditambahkan: notification system, dashboard, reporting, role-based access, mobile app integration, dll.
