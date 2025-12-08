<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the MySQL database with timestamp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        $timestamp = date('Y-m-d_H-i-s');
        $filePath = storage_path("backups/db_backup_{$timestamp}.sql");

        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $command = "\"{$mysqldumpPath}\" -u {$dbUser} -p{$dbPass} {$dbName} > {$filePath}";
        exec($command, $output, $returnVar);


        if ($returnVar === 0) {
            $this->info("Database backup created successfully: db_backup_{$timestamp}.sql");

            $this->deleteOldBackups(7);
        } else {
            $this->error("Failed to create database backup.");
        }
    }

    private function deleteOldBackups(int $days)
    {
        $files = glob(storage_path('backups/*.sql'));
        $now = time();

        foreach ($files as $file) {
            if ($now - filemtime($file) > ($days * 86400)) { // 86400 = ثانية في اليوم
                unlink($file);
            }
        }

        $this->info("Old backups older than {$days} days deleted.");
    }
}
