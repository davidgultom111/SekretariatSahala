# 🛠️ DEVELOPER QUICK REFERENCE

## 📚 Dokumentasi Project

| File                   | Tujuan                                    |
| ---------------------- | ----------------------------------------- |
| **SELESAI.md**         | ⭐ START HERE - Overview project & status |
| **PANDUAN_LENGKAP.md** | User guide lengkap dalam Bahasa Indonesia |
| **SETUP.md**           | Setup instructions & troubleshooting      |
| **MANIFEST.md**        | Complete component checklist              |
| **DEV_GUIDE.md**       | This file - Developer reference           |

---

## 🚀 QUICK COMMANDS

### Setup & Installation

```bash
# Full setup
bash setup.sh                  # Linux/Mac
# or
setup.bat                      # Windows

# Manual setup
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
```

### Development

```bash
# Start server
php artisan serve              # http://localhost:8000

# Watch Tailwind CSS
npm run dev

# Build Tailwind CSS
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Database

```bash
# Run all migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migrate (WARNING: deletes data)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

### Tinker (Interactive Shell)

```bash
php artisan tinker

# Test dalam shell
>>> $users = App\Models\User::all();
>>> $members = App\Models\Member::all();
>>> $letters = App\Models\Letter::all();
```

---

## 📝 ADD NEW FEATURE

### 1. Create Migration

```bash
php artisan make:migration add_field_to_members_table
```

Edit di `database/migrations/`:

```php
Schema::table('members', function (Blueprint $table) {
    $table->string('new_field')->nullable();
});
```

### 2. Create Model

```bash
php artisan make:model MyModel
```

### 3. Create Controller

```bash
# Resource controller
php artisan make:controller MyController --resource

# API controller
php artisan make:controller MyController --api
```

### 4. Add Route

Edit `routes/web.php`:

```php
Route::resource('myroute', MyController::class);
```

### 5. Create View

```
resources/views/myview/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php
```

---

## 🎨 TAILWIND STYLING QUICK REFERENCE

### Colors Used

```css
/* Blue (Sidebar & Primary) */
#1e3a8a /* bg-blue-900 */

/* Yellow (Edit/Add buttons) */
#eab308 /* bg-yellow-500 */

/* Red (Delete/Danger) */
#dc2626 /* bg-red-600 */

/* Green (Status Aktif) */
#16a34a /* bg-green-600 */
```

### Common Classes

```html
<!-- Buttons -->
<button
    class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition"
>
    Click Me
</button>

<!-- Cards -->
<div class="bg-white rounded-lg shadow-md p-6">Content</div>

<!-- Forms -->
<input
    type="text"
    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg
focus:ring-2 focus:ring-blue-500 focus:border-transparent"
/>

<!-- Badges -->
<span
    class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800"
>
    Badge
</span>
```

---

## 🗄️ DATABASE SCHEMA REFERENCE

### Users Table

```sql
id, name, email(UNIQUE), email_verified_at, password,
remember_token, timestamps
```

### Members Table

```sql
id, nama_lengkap, jenis_kelamin, tanggal_lahir, tempat_lahir,
no_identitas(UNIQUE), alamat, kelurahan, kecamatan, kota,
provinsi, kode_pos, no_telepon, email, status_perkawinan,
pekerjaan, tanggal_bergabung, status_aktif, timestamps
```

### Letters Table

```sql
id, member_id(FK), tipe_surat(ENUM), nomor_surat(UNIQUE),
tanggal_surat, keterangan, isi_surat, file_path, timestamps
```

---

## 🔗 ROUTING OVERVIEW

### Auth Routes (Public)

```
GET    /login              → Show login form
POST   /login              → Process login
```

### Protected Routes (Authenticated)

```
GET    /dashboard          → Dashboard
POST   /logout             → Logout

GET    /member             → List members
POST   /member             → Store member
GET    /member/create      → Create form
GET    /member/{id}        → Show member detail
GET    /member/{id}/edit   → Edit form
PUT    /member/{id}        → Update member
DELETE /member/{id}        → Delete member

GET    /letter/types       → Show 8 letter types
GET    /letter             → List all letters (archive)
POST   /letter             → Store letter
GET    /letter/create/{t}  → Create form for type
GET    /letter/{id}        → Show letter detail
DELETE /letter/{id}        → Delete letter
GET    /letter/search      → Search & filter
```

---

## 💾 MODEL RELATIONSHIPS

### User Model

```php
// User tidak punya direct relationship di case ini
```

### Member Model

```php
class Member extends Model {
    public function letters() {
        return $this->hasMany(Letter::class);
    }
}
```

### Letter Model

```php
class Letter extends Model {
    public function member() {
        return $this->belongsTo(Member::class);
    }
}
```

---

## 🧪 TESTING QUERIES

### In Tinker

```bash
php artisan tinker

# Check members
$members = App\Models\Member::all();
$member = App\Models\Member::find(1);

# Check letters
$letters = App\Models\Letter::all();
$letter = App\Models\Letter::find(1);
$letter->member;  # Get member data

# Create sample data
$member = App\Models\Member::create([
    'nama_lengkap' => 'Test User',
    'jenis_kelamin' => 'Laki-laki',
    // ... other fields
]);

# Query examples
App\Models\Member::where('status_aktif', 'Aktif')->get();
App\Models\Letter::with('member')->latest()->take(5)->get();
```

---

## 🐛 DEBUGGING TIPS

### Enable Debug Mode

Edit `.env`:

```
APP_DEBUG=true
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

### Use dd() function

```php
dd($variable);  # Dump and die
dump($variable);  # Just dump
```

### Laravel Debugbar (Optional)

```bash
composer require barryvdh/laravel-debugbar --dev
```

---

## 📦 DEPENDENCY VERSIONS

### PHP

```
php ^8.2
```

### Laravel

```
laravel/framework ^12.0
```

### Node.js

```
vite ^7.0.7
tailwindcss ^4.0.0
@tailwindcss/vite ^4.0.0
laravel-vite-plugin ^2.0.0
```

---

## ⚙️ ENV VARIABLES

Important `.env` variables:

```
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sekretariat
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=log
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
```

---

## 🔒 SECURITY CHECKLIST

- [ ] `.env` tidak di-commit ke git
- [ ] `APP_DEBUG=false` di production
- [ ] CSRF token di semua form ✓
- [ ] Password hashing ✓
- [ ] SQL injection prevention ✓
- [ ] XSS prevention ✓
- [ ] Unique constraints ✓
- [ ] Foreign key constraints ✓

---

## 📱 RESPONSIVE BREAKPOINTS

Tailwind default breakpoints:

```
sm: 640px   → Mobile
md: 768px   → Tablet
lg: 1024px  → Desktop
xl: 1280px  → Large screen
```

Usage:

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
    <!-- 1 column on mobile, 2 on tablet, 3 on desktop -->
</div>
```

---

## 🎯 COMMON BLADE SYNTAX

### Variables

```blade
{{ $variable }}              {# Echo variable #}
{{ $variable ?? 'default' }} {# With default #}
```

### Conditionals

```blade
@if($condition)
    ...
@elseif($other)
    ...
@else
    ...
@endif
```

### Loops

```blade
@foreach($items as $item)
    {{ $item->name }}
@empty
    No items
@endforeach

@forelse($items as $item)
    {{ $item }}
@empty
    Empty
@endforelse
```

### Layouts

```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
    ...
@endsection
```

---

## 🚀 DEPLOYMENT CHECKLIST

Before deploy to production:

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Run `npm run build` (not `npm run dev`)
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Backup database
- [ ] Set up proper file permissions
- [ ] Configure web server (nginx/apache)

---

## 📞 QUICK LINKS

- Laravel Docs: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Blade Syntax: https://laravel.com/docs/blade
- Eloquent ORM: https://laravel.com/docs/eloquent
- Database: https://laravel.com/docs/migrations

---

## 📝 FILE LOCATIONS

```
Controllers:    app/Http/Controllers/
Models:         app/Models/
Migrations:     database/migrations/
Seeders:        database/seeders/
Views:          resources/views/
CSS:            resources/css/app.css
Routes:         routes/web.php
Config:         config/*
```

---

## 🎓 LEARNING PATH

1. **Basics**: Read PANDUAN_LENGKAP.md
2. **Setup**: Follow SETUP.md
3. **Structure**: Check MANIFEST.md
4. **Develop**: Use this guide
5. **Deploy**: Check deployment checklist

---

**Last Updated**: 31 March 2026  
**Version**: 1.0.0  
**Status**: Production Ready ✅
