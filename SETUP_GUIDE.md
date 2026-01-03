# Panduan Setup Database & Testing

## Cara Mengaktifkan Extension PHP untuk Database

### Windows

1. Locate file `php.ini` (biasanya di `C:\xampp\php\php.ini` atau `C:\php\php.ini`)

2. Buka dengan text editor (Run as Administrator)

3. Uncomment (hapus `;` di awal) extension yang dibutuhkan:

```ini
; Untuk MySQL
extension=pdo_mysql
extension=mysqli

; Untuk SQLite (alternatif)
extension=pdo_sqlite
extension=sqlite3
```

4. Save file dan restart web server (Apache/Nginx)

5. Verify dengan:

```bash
php -m
```

Pastikan `pdo_mysql` atau `pdo_sqlite` ada di list.

## Setup Database MySQL

### Menggunakan XAMPP

1. Start Apache dan MySQL di XAMPP Control Panel

2. Buka phpMyAdmin: http://localhost/phpmyadmin

3. Klik "New" untuk create database baru

4. Database name: `employee_housing`

5. Collation: `utf8mb4_unicode_ci`

6. Klik "Create"

### Menggunakan Command Line

```bash
# Login ke MySQL
mysql -u root -p

# Create database
CREATE DATABASE employee_housing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Exit
exit;
```

## Setup Database SQLite (Alternatif)

SQLite tidak memerlukan server database terpisah, cukup file.

### Cara 1: Automatic (Jika extension enabled)

```bash
# Buat file database kosong
type nul > database\database.sqlite
```

### Cara 2: Download SQLite Tools

1. Download SQLite tools dari: https://www.sqlite.org/download.html

2. Extract dan jalankan:

```bash
sqlite3 database\database.sqlite
```

3. Type `.quit` untuk exit

## Konfigurasi .env

### MySQL Configuration

```env
APP_NAME="Employee Housing System"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_housing
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### SQLite Configuration

```env
APP_NAME="Employee Housing System"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Comment out MySQL config
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=employee_housing
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

## Running Migration & Seeder

### First Time Setup

```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed
```

Output yang diharapkan:

```
Dropped all tables successfully.
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
...
Seeding: EmployeeSeeder
Seeded:  EmployeeSeeder
Seeding: RoomSeeder
Seeded:  RoomSeeder
```

### Reset & Re-seed

```bash
# Drop all tables and re-run
php artisan migrate:fresh --seed

# Only re-seed (keep structure)
php artisan db:seed --force
```

### Run Specific Seeder

```bash
# Seed only employees
php artisan db:seed --class=EmployeeSeeder

# Seed only rooms
php artisan db:seed --class=RoomSeeder
```

## Verifikasi Data

### Menggunakan Tinker

```bash
php artisan tinker
```

```php
// Check total employees
App\Models\Employee::count();
// Output: 72

// Check active employees
App\Models\Employee::where('status', 'Aktif')->count();
// Output: 60

// Check total rooms
App\Models\Room::count();
// Output: 45

// Check occupied rooms
App\Models\Room::where('status', 'Terisi')->count();

// Exit
exit
```

### Menggunakan Query

```bash
# MySQL
mysql -u root -p employee_housing -e "SELECT COUNT(*) FROM employees;"
mysql -u root -p employee_housing -e "SELECT department, COUNT(*) FROM employees GROUP BY department;"

# SQLite
sqlite3 database\database.sqlite "SELECT COUNT(*) FROM employees;"
```

## Testing Application

### 1. Start Development Server

```bash
php artisan serve
```

Aplikasi berjalan di: http://localhost:8000

### 2. Login

-   URL: http://localhost:8000/login
-   Email: `admin@itci.com`
-   Password: `admin123`

### 3. Test CRUD Operations

#### Test Employee CRUD

1. Go to "Karyawan" menu
2. Click "Add" button
3. Fill form: ID Karyawan, Nama, Departemen, Status
4. Save
5. Try Edit dan Delete

#### Test Room CRUD

1. Go to "Kamar" menu
2. Click "Add" button
3. Fill form: Kode Kamar, Kapasitas
4. Save
5. Try Edit dan Delete

#### Test Room Assignment

1. Go to "Kamar" menu
2. Click "Assign Karyawan ke Kamar" button
3. Select Room
4. Select Employee
5. Set Check-in Date
6. Click "Assign"
7. Verify status kamar berubah ke "Terisi"

### 4. Test Validations

Try these scenarios:

-   ✅ Assign employee ke kamar yang sudah penuh → Should show error
-   ✅ Assign employee yang sudah punya kamar → Should show error
-   ✅ Delete kamar yang terisi → Should show error
-   ✅ Add duplicate employee ID → Should show error
-   ✅ Add duplicate room code → Should show error

### 5. Test Features

-   ✅ Search/Filter di DataGrid
-   ✅ Sort columns
-   ✅ Pagination
-   ✅ Export to Excel
-   ✅ Dashboard statistics
-   ✅ Responsive layout

## Common Issues & Solutions

### Issue: "SQLSTATE[HY000] [2002] No connection"

**Solution**: Start MySQL server di XAMPP/WAMP

### Issue: "Access denied for user 'root'@'localhost'"

**Solution**: Update password di .env atau reset MySQL password

### Issue: "Class 'PDO' not found"

**Solution**: Enable extension=pdo_mysql di php.ini

### Issue: "Base table or view not found"

**Solution**: Run migration:

```bash
php artisan migrate:fresh --seed
```

### Issue: Vite manifest not found

**Solution**: Build assets:

```bash
npm run build
```

### Issue: Permission denied untuk database file (SQLite)

**Solution**: Set correct permissions:

```bash
icacls database\database.sqlite /grant Everyone:F
```

## Database Schema Visualization

```
┌─────────────────┐
│    employees    │
├─────────────────┤
│ id (PK)         │
│ employee_id (U) │
│ name            │
│ department      │──┐
│ status          │  │
│ timestamps      │  │
└─────────────────┘  │
                     │
                     │ One-to-Many
                     │
┌─────────────────────┐
│  room_occupancies   │
├─────────────────────┤
│ id (PK)             │
│ employee_id (FK)    │──┘
│ room_id (FK)        │──┐
│ check_in_date       │  │
│ check_out_date      │  │
│ timestamps          │  │
└─────────────────────┘  │
                         │ One-to-Many
                         │
                      ┌──┘
                      │
               ┌──────────────┐
               │    rooms     │
               ├──────────────┤
               │ id (PK)      │
               │ room_code(U) │
               │ capacity     │
               │ status       │
               │ occupied_cnt │
               │ timestamps   │
               └──────────────┘
```

## Performance Tips

### Index Optimization

Indexes sudah dibuat otomatis pada:

-   Primary Keys (id)
-   Foreign Keys (employee_id, room_id)
-   Unique constraints (employee_id, room_code)

### Query Optimization

Models sudah menggunakan eager loading:

```php
// Good (Eager Loading)
Room::with('currentOccupants.employee')->get();

// Bad (N+1 Problem)
Room::all()->currentOccupants;
```

### Caching (Optional)

Untuk production, enable caching:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Backup & Restore

### MySQL Backup

```bash
# Backup
mysqldump -u root -p employee_housing > backup.sql

# Restore
mysql -u root -p employee_housing < backup.sql
```

### SQLite Backup

```bash
# Backup (just copy file)
copy database\database.sqlite database\database.backup.sqlite

# Restore
copy database\database.backup.sqlite database\database.sqlite
```

---

For more information, check README.md
