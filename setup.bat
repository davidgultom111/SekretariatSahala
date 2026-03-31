@echo off
REM Sistem Informasi Admin Sekretariat Gereja - Quick Start
REM Windows Batch Version

echo ================================
echo Setup Sistem Sekretariat Gereja
echo ================================
echo.

REM 1. Install dependencies
echo 📦 Installing composer dependencies...
call composer install
echo ✓ Composer dependencies installed

echo.
echo 📦 Installing npm dependencies...
call npm install
echo ✓ NPM dependencies installed

REM 2. Environment setup
echo.
echo ⚙️  Setting up environment...
if not exist .env (
    copy .env.example .env
    echo ✓ .env file created
)

REM 3. Generate key
call php artisan key:generate
echo ✓ Application key generated

REM 4. Database setup
echo.
echo 🗄️  Setting up database...
echo Make sure MySQL is running and database 'sekretariat' exists
pause

echo Running migrations...
call php artisan migrate --force
echo ✓ Migrations completed

REM 5. Seeding
echo.
echo 🌱 Seeding database with demo data...
call php artisan db:seed
echo ✓ Database seeded

REM 6. Build frontend
echo.
echo 🎨 Building Tailwind CSS...
call npm run build
echo ✓ Tailwind CSS built

echo.
echo ================================
echo ✨ Setup Complete!
echo ================================
echo.
echo 🌐 To start the application:
echo.
echo   1. Command Prompt 1 - Start Laravel server:
echo      php artisan serve
echo.
echo   2. Command Prompt 2 (optional) - Watch Tailwind:
echo      npm run dev
echo.
echo 📱 Access the application at:
echo    http://localhost:8000
echo.
echo 🔐 Login Credentials:
echo    Email: admin@gereja.com
echo    Password: password
echo.
echo    OR
echo.
echo    Email: staff@gereja.com
echo    Password: password
echo.
pause
