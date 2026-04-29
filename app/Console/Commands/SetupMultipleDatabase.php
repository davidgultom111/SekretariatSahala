<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupMultipleDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup-multi {--force : Force the operation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup multiple databases for sekretariat system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (!$force && !$this->confirm('This will create multiple databases. Continue?')) {
            return Command::FAILURE;
        }

        $this->info('🔄 Setting up multiple databases...');

        // Database connections to create
        $databases = [
            'sekretariat_core' => 'Core System (users, tokens, cache, jobs)',
            'sekretariat_biodata' => 'Biodata (members)',
            'sekretariat_surat_baptisan' => 'Surat Baptisan',
            'sekretariat_surat_pernikahan' => 'Surat Pernikahan',
            'sekretariat_surat_serah_nikah' => 'Surat Serah Nikah',
            'sekretariat_surat_kematian' => 'Surat Kematian',
            'sekretariat_surat_keluar_jemaat' => 'Surat Keluar Jemaat',
            'sekretariat_surat_masuk_jemaat' => 'Surat Masuk Jemaat',
            'sekretariat_surat_keterangan_jemaat' => 'Surat Keterangan Jemaat',
            'sekretariat_surat_rekomendasi' => 'Surat Rekomendasi',
        ];

        // Create each database
        foreach ($databases as $dbName => $description) {
            try {
                $this->createDatabase($dbName);
                $this->info("✅ Database created: {$description} ({$dbName})");
            } catch (\Exception $e) {
                $this->error("❌ Failed to create {$dbName}: " . $e->getMessage());
            }
        }

        $this->info('✅ All databases created successfully!');
        return Command::SUCCESS;
    }

    /**
     * Create a database
     */
    private function createDatabase($dbName)
    {
        $connection = DB::connection('mysql');
        $connection->statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}
