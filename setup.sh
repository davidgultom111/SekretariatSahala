#!/bin/bash

# Sistem Informasi Admin Sekretariat Gereja - Quick Start

echo "================================"
echo "Setup Sistem Sekretariat Gereja"
echo "================================"
echo ""

# 1. Install dependencies
echo "📦 Installing composer dependencies..."
composer install
echo "✓ Composer dependencies installed"

echo ""
echo "📦 Installing npm dependencies..."
npm install
echo "✓ NPM dependencies installed"

# 2. Environment setup
echo ""
echo "⚙️  Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✓ .env file created"
fi

# 3. Generate key
php artisan key:generate
echo "✓ Application key generated"

# 4. Database setup
echo ""
echo "🗄️  Setting up database..."
read -p "Enter database password (press Enter if no password): " db_password

# Update .env with the database password
if [ ! -z "$db_password" ]; then
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=$db_password/g" .env
fi

echo "Running migrations..."
php artisan migrate --force
echo "✓ Migrations completed"

# 5. Seeding
echo ""
echo "🌱 Seeding database with demo data..."
php artisan db:seed
echo "✓ Database seeded"

# 6. Build frontend
echo ""
echo "🎨 Building Tailwind CSS..."
npm run build
echo "✓ Tailwind CSS built"

echo ""
echo "================================"
echo "✨ Setup Complete!"
echo "================================"
echo ""
echo "🌐 To start the application:"
echo ""
echo "   1. Terminal 1 - Start Laravel server:"
echo "      php artisan serve"
echo ""
echo "   2. Terminal 2 (optional) - Watch Tailwind:"
echo "      npm run dev"
echo ""
echo "📱 Access the application at:"
echo "   http://localhost:8000"
echo ""
echo "🔐 Login Credentials:"
echo "   Email: admin@gereja.com"
echo "   Password: password"
echo ""
echo "   OR"
echo ""
echo "   Email: staff@gereja.com"
echo "   Password: password"
echo ""
