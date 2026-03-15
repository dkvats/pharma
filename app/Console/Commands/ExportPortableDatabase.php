<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportPortableDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-portable 
                            {--output= : Output file path (default: storage/app/database_export.sql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database to a portable SQL file with proper foreign key handling';

    /**
     * Tables in correct creation order (dependencies first).
     *
     * @var array
     */
    protected $tableOrder = [
        // Core tables (no foreign keys)
        'users',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        
        // Configuration tables
        'roles',
        'permissions',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        
        // MR module tables
        'mr_states',
        'mr_districts',
        'mr_cities',
        'mr_areas',
        
        // Product and inventory
        'products',
        'mr_stores',
        'store_stock',
        'store_sales',
        
        // Offers
        'offers',
        'offer_products',
        
        // Orders (depends on users, offers)
        'orders',
        'order_items',
        
        // Cart and wishlist
        'carts',
        'cart_items',
        'wishlists',
        'wishlist_items',
        
        // Other feature tables
        'rewards',
        'spin_histories',
        'spin_logs',
        'spin_overrides',
        'spin_campaigns',
        'grand_draw_winners',
        'grand_spins',
        'grand_spin_rewards',
        'doctor_targets',
        'activity_logs',
        'cms_pages',
        'feature_flags',
        'homepage_contents',
        'homepage_features',
        'homepage_nav_items',
        'homepage_sections',
        'media_libraries',
        'modules',
        'notification_templates',
        'role_requests',
        'settings',
        'site_settings',
        'system_settings',
        'ui_settings',
        'user_offer_usages',
        'dashboard_widgets',
        'pincodes',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputFile = $this->option('output') ?? storage_path('app/database_export.sql');
        
        $this->info('Starting portable database export...');
        $this->info('Output file: ' . $outputFile);
        
        // Get all tables from database
        $tables = $this->getAllTables();
        
        if (empty($tables)) {
            $this->error('No tables found in database!');
            return 1;
        }
        
        $this->info('Found ' . count($tables) . ' tables');
        
        // Open file for writing
        $handle = fopen($outputFile, 'w');
        if (!$handle) {
            $this->error('Cannot create output file: ' . $outputFile);
            return 1;
        }
        
        // Write header
        $this->writeHeader($handle);
        
        // Disable foreign key checks
        fwrite($handle, "-- Disable foreign key checks\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");
        
        // Sort tables by dependency order
        $sortedTables = $this->sortTablesByDependency($tables);
        
        // Step 1: Create all tables (without foreign keys)
        $this->info('Creating table structures...');
        fwrite($handle, "-- ============================================\n");
        fwrite($handle, "-- STEP 1: CREATE ALL TABLES (without foreign keys)\n");
        fwrite($handle, "-- ============================================\n\n");
        
        foreach ($sortedTables as $table) {
            $this->info('  Creating table: ' . $table);
            $this->exportTableStructure($handle, $table);
        }
        
        // Step 2: Insert data
        $this->info('Exporting data...');
        fwrite($handle, "\n-- ============================================\n");
        fwrite($handle, "-- STEP 2: INSERT DATA\n");
        fwrite($handle, "-- ============================================\n\n");
        
        foreach ($sortedTables as $table) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                $this->info('  Exporting data for: ' . $table . ' (' . $count . ' rows)');
                $this->exportTableData($handle, $table);
            }
        }
        
        // Step 3: Add foreign keys
        $this->info('Adding foreign keys...');
        fwrite($handle, "\n-- ============================================\n");
        fwrite($handle, "-- STEP 3: ADD FOREIGN KEY CONSTRAINTS\n");
        fwrite($handle, "-- ============================================\n\n");
        
        foreach ($sortedTables as $table) {
            $this->info('  Adding foreign keys for: ' . $table);
            $this->exportForeignKeys($handle, $table);
        }
        
        // Re-enable foreign key checks
        fwrite($handle, "\n-- Re-enable foreign key checks\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        
        // Write footer
        $this->writeFooter($handle);
        
        fclose($handle);
        
        $this->info('');
        $this->info('Export completed successfully!');
        $this->info('File: ' . $outputFile);
        $this->info('');
        $this->info('To import this file on another system:');
        $this->info('  1. Create a new empty database in phpMyAdmin');
        $this->info('  2. Import this SQL file');
        $this->info('  3. No errors should occur');
        
        return 0;
    }

    /**
     * Get all tables from the database.
     */
    protected function getAllTables(): array
    {
        $tables = [];
        $results = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . env('DB_DATABASE');
        
        foreach ($results as $result) {
            $tables[] = $result->$key;
        }
        
        return $tables;
    }

    /**
     * Sort tables by dependency order.
     */
    protected function sortTablesByDependency(array $tables): array
    {
        $sorted = [];
        $remaining = array_flip($tables);
        
        // First, add tables in our defined order
        foreach ($this->tableOrder as $table) {
            if (isset($remaining[$table])) {
                $sorted[] = $table;
                unset($remaining[$table]);
            }
        }
        
        // Add any remaining tables (not in our order list)
        foreach (array_keys($remaining) as $table) {
            $sorted[] = $table;
        }
        
        return $sorted;
    }

    /**
     * Export table structure without foreign keys.
     */
    protected function exportTableStructure($handle, string $table): void
    {
        fwrite($handle, "-- Table structure for table `{$table}`\n");
        
        // Get CREATE TABLE statement
        $result = DB::select("SHOW CREATE TABLE `{$table}`");
        $createSql = $result[0]->{'Create Table'};
        
        // Remove foreign key constraints from CREATE TABLE
        $createSql = $this->removeForeignKeys($createSql);
        
        // Ensure InnoDB engine
        $createSql = preg_replace('/ENGINE=\w+/', 'ENGINE=InnoDB', $createSql);
        
        // Add IF NOT EXISTS
        $createSql = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $createSql, 1);
        
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
        fwrite($handle, $createSql . ";\n\n");
    }

    /**
     * Remove foreign key constraints from CREATE TABLE statement.
     */
    protected function removeForeignKeys(string $sql): string
    {
        // Remove CONSTRAINT ... FOREIGN KEY ... lines
        $sql = preg_replace('/,\s*CONSTRAINT `[^`]+` FOREIGN KEY \([^)]+\) REFERENCES `[^`]+` \([^)]+\)( ON DELETE \w+)?( ON UPDATE \w+)?/i', '', $sql);
        
        return $sql;
    }

    /**
     * Export table data.
     */
    protected function exportTableData($handle, string $table): void
    {
        fwrite($handle, "-- Dumping data for table `{$table}`\n");
        
        $rows = DB::table($table)->get();
        
        if ($rows->isEmpty()) {
            return;
        }
        
        // Get column names from first row
        $firstRow = (array) $rows->first();
        $columns = array_keys($firstRow);
        $columnList = '`' . implode('`, `', $columns) . '`';
        
        // Build INSERT statements in batches
        $batchSize = 100;
        $batch = [];
        $counter = 0;
        
        foreach ($rows as $row) {
            $values = [];
            foreach ((array) $row as $value) {
                if ($value === null) {
                    $values[] = 'NULL';
                } elseif (is_numeric($value)) {
                    $values[] = $value;
                } else {
                    $values[] = "'" . addslashes($value) . "'";
                }
            }
            $batch[] = '(' . implode(', ', $values) . ')';
            $counter++;
            
            if ($counter >= $batchSize) {
                fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n");
                fwrite($handle, implode(",\n", $batch) . ";\n");
                $batch = [];
                $counter = 0;
            }
        }
        
        // Write remaining rows
        if (!empty($batch)) {
            fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n");
            fwrite($handle, implode(",\n", $batch) . ";\n");
        }
        
        fwrite($handle, "\n");
    }

    /**
     * Export foreign key constraints for a table.
     */
    protected function exportForeignKeys($handle, string $table): void
    {
        // Get CREATE TABLE statement to extract foreign keys
        $result = DB::select("SHOW CREATE TABLE `{$table}`");
        $createSql = $result[0]->{'Create Table'};
        
        // Extract foreign key constraints
        preg_match_all('/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^)]+)`\) REFERENCES `([^`]+)` \(`([^)]+)`\)( ON DELETE (\w+))?( ON UPDATE (\w+))?/i', $createSql, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            return;
        }
        
        fwrite($handle, "-- Foreign keys for table `{$table}`\n");
        
        foreach ($matches as $match) {
            $constraintName = $match[1];
            $column = $match[2];
            $refTable = $match[3];
            $refColumn = $match[4];
            $onDelete = isset($match[6]) ? ' ON DELETE ' . $match[6] : '';
            $onUpdate = isset($match[8]) ? ' ON UPDATE ' . $match[8] : '';
            
            $sql = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraintName}` ";
            $sql .= "FOREIGN KEY (`{$column}`) REFERENCES `{$refTable}` (`{$refColumn}`)";
            $sql .= $onDelete . $onUpdate . ";\n";
            
            fwrite($handle, $sql);
        }
        
        if (!empty($matches)) {
            fwrite($handle, "\n");
        }
    }

    /**
     * Write SQL file header.
     */
    protected function writeHeader($handle): void
    {
        $database = env('DB_DATABASE');
        $timestamp = now()->format('Y-m-d H:i:s');
        
        fwrite($handle, "-- ============================================\n");
        fwrite($handle, "-- Portable Database Export\n");
        fwrite($handle, "-- Database: {$database}\n");
        fwrite($handle, "-- Generated: {$timestamp}\n");
        fwrite($handle, "-- ============================================\n");
        fwrite($handle, "-- This file can be imported on any system\n");
        fwrite($handle, "-- without foreign key constraint errors.\n");
        fwrite($handle, "-- ============================================\n\n");
        
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET CHARACTER SET utf8mb4;\n");
        fwrite($handle, "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n\n");
    }

    /**
     * Write SQL file footer.
     */
    protected function writeFooter($handle): void
    {
        fwrite($handle, "\n-- ============================================\n");
        fwrite($handle, "-- End of portable database export\n");
        fwrite($handle, "-- ============================================\n");
    }
}
