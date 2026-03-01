<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
    protected $description = 'Create a compressed database backup with verification';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup...');

        // Get database configuration
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', '3306');

        // Create backup directory
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0750, true);
        }

        // Generate filename with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $sqlFile = "backup_{$timestamp}.sql";
        $gzFile = "backup_{$timestamp}.sql.gz";
        $sqlPath = "{$backupDir}/{$sqlFile}";
        $gzPath = "{$backupDir}/{$gzFile}";

        try {
            // Build mysqldump command using Symfony Process
            $command = [
                'mysqldump',
                '--host=' . $dbHost,
                '--port=' . $dbPort,
                '--user=' . $dbUser,
                '--password=' . $dbPass,
                '--single-transaction',
                '--routines',
                '--triggers',
                '--lock-tables=false',
                $dbName,
            ];

            $this->info('Running mysqldump...');

            // Execute mysqldump with output to file
            $process = new Process($command);
            $process->setTimeout(300); // 5 minute timeout
            $process->run(function ($type, $buffer) use ($sqlPath) {
                if (Process::ERR === $type) {
                    Log::error('mysqldump error: ' . $buffer);
                } else {
                    file_put_contents($sqlPath, $buffer, FILE_APPEND | LOCK_EX);
                }
            });

            // Check if process succeeded
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Verify SQL file was created and has content
            if (!file_exists($sqlPath) || filesize($sqlPath) < 1000) {
                throw new \Exception('Backup file is empty or too small');
            }

            $this->info("SQL backup created: {$sqlFile} (" . $this->formatBytes(filesize($sqlPath)) . ")");

            // Compress the backup
            $this->info('Compressing backup...');
            $gzipProcess = new Process(['gzip', '-f', $sqlPath]);
            $gzipProcess->setTimeout(120);
            $gzipProcess->run();

            if (!$gzipProcess->isSuccessful()) {
                throw new ProcessFailedException($gzipProcess);
            }

            // Verify compressed file
            if (!file_exists($gzPath) || filesize($gzPath) < 100) {
                throw new \Exception('Compressed backup file is invalid');
            }

            $compressedSize = $this->formatBytes(filesize($gzPath));
            $this->info("Backup compressed: {$gzFile} ({$compressedSize})");

            // Cleanup old backups (keep last 30)
            $this->cleanupOldBackups($backupDir);

            // Log success
            Log::info('Database backup completed successfully', [
                'file' => $gzFile,
                'size' => $compressedSize,
            ]);

            $this->info('Backup completed successfully!');
            return self::SUCCESS;

        } catch (ProcessFailedException $e) {
            Log::error('Database backup failed - Process error', [
                'error' => $e->getMessage(),
                'command' => $e->getProcess()->getCommandLine(),
            ]);
            $this->error('Backup failed: ' . $e->getMessage());
            
            // Cleanup failed files
            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }
            if (file_exists($gzPath)) {
                unlink($gzPath);
            }
            
            return self::FAILURE;

        } catch (\Exception $e) {
            Log::error('Database backup failed - General error', [
                'error' => $e->getMessage(),
            ]);
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Cleanup old backups, keeping only the last 30
     */
    private function cleanupOldBackups(string $backupDir): void
    {
        $backups = glob("{$backupDir}/backup_*.sql.gz");
        
        if (count($backups) <= 30) {
            return;
        }

        // Sort by modification time (newest first)
        usort($backups, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Delete older backups
        $toDelete = array_slice($backups, 30);
        foreach ($toDelete as $backup) {
            if (unlink($backup)) {
                Log::info('Deleted old backup: ' . basename($backup));
            }
        }

        $this->info('Cleaned up ' . count($toDelete) . ' old backup(s)');
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
