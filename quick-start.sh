#!/usr/bin/env bash

# Quick Start - Sistem Sekretariat Gereja
# This file provides quick commands for common development tasks

echo "================================"
echo "Quick Start Commands"
echo "================================"
echo ""
echo "Pilih perintah yang ingin dijalankan:"
echo ""
echo "1. Setup lengkap (install + migrate + seed)"
echo "2. Install dependencies (composer + npm)"
echo "3. Database migrations"
echo "4. Database seeding"
echo "5. Tailwind build"
echo "6. Tailwind watch (dev mode)"
echo "7. Start Laravel server"
echo "8. Fresh migrate + seed"
echo ""
echo "Contoh penggunaan:"
echo "  bash quick-start.sh 1"
echo ""

# Get command number
CMD=${1:-0}

case $CMD in
    1)
        echo "🚀 Running full setup..."
        composer install && npm install && php artisan key:generate && php artisan migrate && php artisan db:seed && npm run build
        echo "✅ Setup complete! Run 'php artisan serve' to start"
        ;;
    2)
        echo "📦 Installing dependencies..."
        composer install
        npm install
        echo "✅ Dependencies installed"
        ;;
    3)
        echo "🗄️  Running migrations..."
        php artisan migrate
        echo "✅ Migrations completed"
        ;;
    4)
        echo "🌱 Seeding database..."
        php artisan db:seed
        echo "✅ Database seeded"
        ;;
    5)
        echo "🎨 Building Tailwind..."
        npm run build
        echo "✅ Tailwind built"
        ;;
    6)
        echo "👀 Watching Tailwind (press Ctrl+C to stop)..."
        npm run dev
        ;;
    7)
        echo "▶️  Starting Laravel server..."
        php artisan serve
        ;;
    8)
        echo "🔄 Fresh migrate + seed..."
        php artisan migrate:fresh --seed
        echo "✅ Fresh setup completed"
        ;;
    *)
        echo "❌ Invalid command. Use: bash quick-start.sh [1-8]"
        ;;
esac
