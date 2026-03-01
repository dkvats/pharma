<?php

namespace App\Console\Commands;

use App\Models\Pincode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPincodes extends Command
{
    protected $signature = 'pincodes:import {file? : Path to JSON file}';
    protected $description = 'Import PIN codes from JSON file to database';

    public function handle(): int
    {
        $filePath = $this->argument('file') ?? storage_path('app/pincodes/processed_zip_codes.json');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }
        
        $this->info("Loading PIN code data from: {$filePath}");
        
        $jsonContent = file_get_contents($filePath);
        
        // Try to clean up the JSON content if needed
        $jsonContent = trim($jsonContent);
        
        // Remove BOM if present
        $bom = pack('H*','EFBBBF');
        $jsonContent = preg_replace("/^$bom/", '', $jsonContent);
        
        $data = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            $this->error('Error at position: ' . json_last_error());
            return 1;
        }
        
        $this->info("Parsing PIN code data...");
        
        $records = [];
        $totalStates = 0;
        $totalPins = 0;
        
        foreach ($data as $stateName => $pins) {
            $totalStates++;
            if (!is_array($pins)) {
                continue;
            }
            
            foreach ($pins as $pincode => $postOffice) {
                $totalPins++;
                $records[] = [
                    'pincode' => $pincode,
                    'post_office' => $postOffice,
                    'state' => $stateName,
                    'district' => null,
                    'country' => 'India',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in batches of 1000 to avoid memory issues
                if (count($records) >= 1000) {
                    $this->insertBatch($records);
                    $records = [];
                }
            }
        }
        
        // Insert remaining records
        if (!empty($records)) {
            $this->insertBatch($records);
        }
        
        $this->info("Import completed!");
        $this->info("States processed: {$totalStates}");
        $this->info("Total PIN codes: {$totalPins}");
        $this->info("Records in database: " . Pincode::count());
        
        return 0;
    }
    
    private function insertBatch(array $records): void
    {
        DB::table('pincodes')->insertOrIgnore($records);
        $this->info("Inserted batch of " . count($records) . " records...");
    }
}
