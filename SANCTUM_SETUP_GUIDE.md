# ⚠️ PENTING: Setup Sanctum - Solusi Autoloader Issue

## 🔴 Issue yang Terjadi

Saat running `php artisan db:seed`, muncul error:

```
Trait "Laravel\Sanctum\HasApiTokens" not found
```

Ini adalah issue umum dengan Laravel Sanctum autoloader caching.

---

## ✅ Solusi Paling Cepat

### Langkah 1: Hapus Cache & Temporary Files

```bash
cd d:\Skripsi\sekretariat

# Hapus cache files
rm -r bootstrap/cache/*
rm -r vendor/laravel/sanctum/src/../../../

# atau di PowerShell:
Remove-Item bootstrap/cache/* -Force
```

### Langkah 2: Reinstall Sanctum

```bash
composer remove laravel/sanctum
composer require laravel/sanctum
```

### Langkah 3: Update Autoloader

```bash
composer dump-autoload -o
```

### Langkah 4: Publish Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Langkah 5: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Langkah 6: Try Seeding Again

```bash
php artisan db:seed --class=MemberSeeder
```

---

## 📝 Alternatif: Manual Setup Jika Masih Error

Jika masih error, ikuti langkah ini:

### 1. Uncomment HasApiTokens di Member Model

File: `app/Models/Member.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model
{
    use HasFactory, HasApiTokens;
    // ... rest of the code
}
```

### 2. Buat Member Manual

Buka `php artisan tinker` dan jalankan:

```php
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

// Create regular member
Member::create([
    'id_jemaat' => '15051980',
    'nama_lengkap' => 'Budi Santoso',
    'jenis_kelamin' => 'Laki-laki',
    'tanggal_lahir' => '1980-05-15',
    'tempat_lahir' => 'Jakarta',
    'alamat' => 'Jl. Merdeka No. 123',
    'no_telepon' => '081234567890',
    'status_aktif' => true,
    'password' => Hash::make('12345'),
    'role' => 'member',
]);

// Create admin
Member::create([
    'id_jemaat' => '01011980',
    'nama_lengkap' => 'Admin Gereja',
    'jenis_kelamin' => 'Laki-laki',
    'tanggal_lahir' => '1980-01-01',
    'tempat_lahir' => 'Medan',
    'alamat' => 'Jl. Gereja No. 1',
    'no_telepon' => '081111111111',
    'status_aktif' => true,
    'password' => Hash::make('12345'),
    'role' => 'admin',
]);

exit;
```

---

## 🧪 Test setelah Setup

```bash
# Start Laravel server
php artisan serve

# Di terminal lain, test login
curl -X POST http://localhost:8000/api/jemaat/login \
  -H "Content-Type: application/json" \
  -d '{"id_jemaat":"15051980","password":"12345"}'
```

Expected response:

```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "member": { ... },
    "token": "1|..."
  }
}
```

---

## 🔧 Troubleshooting

### If still getting "Trait not found"

1. Check Sanctum is in vendor:

    ```bash
    ls vendor/laravel/sanctum/src/HasApiTokens.php
    ```

2. Force PSR-4 autoload:

    ```bash
    composer dump-autoload --optimize --no-dev
    ```

3. Check composer.json has Sanctum:

    ```bash
    composer show | grep sanctum
    ```

4. If all else fails, add Sanctum to bootstrap/providers.php:
    ```php
    return [
        App\Providers\AppServiceProvider::class,
        \Laravel\Sanctum\SanctumServiceProvider::class,
    ];
    ```

---

## 📋 Checklist untuk Production

- [ ] Sanctum installed dan autoloaded
- [ ] HasApiTokens trait uncommented di Member model
- [ ] Test members created (via seeder atau tinker)
- [ ] API routes tested dengan cURL/Postman
- [ ] CORS properly configured
- [ ] SANCTUM_STATEFUL_DOMAINS di .env
- [ ] FRONTEND_URL di .env
- [ ] Database migrations ran successfully
- [ ] API responses consistent JSON format
- [ ] Token generation working

---

## 📞 Jika Masih Ada Masalah

1. Cek error log:

    ```bash
    tail -f storage/logs/laravel.log
    ```

2. Debug di tinker:

    ```php
    use App\Models\Member;
    Member::first(); // Check if members exist
    ```

3. Test raw Sanctum:
    ```php
    $member = Member::first();
    $token = $member->createToken('test');
    ```

Dokumentasi: https://laravel.com/docs/12.x/sanctum
