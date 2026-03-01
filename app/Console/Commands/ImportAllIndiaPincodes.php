<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportAllIndiaPincodes extends Command
{
    protected $signature = 'pincodes:import-all {--file=.qoder/All-India-Pincode-Directory-master/all-india-pincode-json-array.json : Path to the JSON file}';
    protected $description = 'Import all India PIN codes from JSON file';

    public function handle(): int
    {
        $filePath = base_path($this->option('file'));
        
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }
        
        $this->info('Reading PIN code data...');
        
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON parsing error: ' . json_last_error_msg());
            return 1;
        }
        
        $this->info('Total records in file: ' . count($data));
        
        $batch = [];
        $batchSize = 1000;
        $totalInserted = 0;
        $totalSkipped = 0;
        $uniquePincodes = [];
        
        // Process each record
        foreach ($data as $index => $record) {
            $pincode = str_pad($record['pincode'], 6, '0', STR_PAD_LEFT);
            
            // Skip duplicates within the same file
            if (isset($uniquePincodes[$pincode])) {
                $totalSkipped++;
                continue;
            }
            $uniquePincodes[$pincode] = true;
            
            $batch[] = [
                'pincode' => $pincode,
                'post_office' => $record['officename'] ?? 'Unknown',
                'office_type' => $record['officeType'] ?? null,
                'related_suboffice' => $record['relatedSuboffice'] !== 'NA' ? $record['relatedSuboffice'] : null,
                'related_headoffice' => $record['relatedHeadoffice'] !== 'NA' ? $record['relatedHeadoffice'] : null,
                'district' => $record['Districtname'] ?? 'Unknown',
                'state' => $this->normalizeStateName($record['statename'] ?? 'Unknown'),
                'country' => 'India',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($batch) >= $batchSize) {
                $inserted = $this->insertBatch($batch);
                $totalInserted += $inserted;
                $this->info("Progress: {$totalInserted} inserted... (skipped duplicates: {$totalSkipped})");
                $batch = [];
            }
        }
        
        // Insert remaining
        if (!empty($batch)) {
            $inserted = $this->insertBatch($batch);
            $totalInserted += $inserted;
        }
        
        $this->info("Import completed!");
        $this->info("Total PIN codes in database: " . DB::table('pincodes')->count());
        $this->info("New PIN codes inserted: {$totalInserted}");
        $this->info("Duplicates skipped: {$totalSkipped}");
        
        return 0;
    }
    
    private function insertBatch(array $batch): int
    {
        $values = [];
        foreach ($batch as $row) {
            $values[] = "('" . addslashes($row['pincode']) . "', '" 
                . addslashes($row['post_office']) . "', '" 
                . addslashes($row['office_type'] ?? '') . "', '" 
                . addslashes($row['related_suboffice'] ?? '') . "', '" 
                . addslashes($row['related_headoffice'] ?? '') . "', '" 
                . addslashes($row['district']) . "', '" 
                . addslashes($row['state']) . "', '" 
                . addslashes($row['country']) . "', '" 
                . $row['created_at'] . "', '" 
                . $row['updated_at'] . "')";
        }
        
        $sql = "INSERT IGNORE INTO pincodes (pincode, post_office, office_type, related_suboffice, related_headoffice, district, state, country, created_at, updated_at) VALUES " 
            . implode(', ', $values);
        
        DB::statement($sql);
        
        return count($batch);
    }
    
    private function normalizeStateName(string $state): string
    {
        $stateMap = [
            'ANDAMAN AND NICOBAR ISLANDS' => 'Andaman and Nicobar Islands',
            'ANDHRA PRADESH' => 'Andhra Pradesh',
            'ARUNACHAL PRADESH' => 'Arunachal Pradesh',
            'ASSAM' => 'Assam',
            'BIHAR' => 'Bihar',
            'CHANDIGARH' => 'Chandigarh',
            'CHHATTISGARH' => 'Chhattisgarh',
            'DADRA AND NAGAR HAVELI' => 'Dadra and Nagar Haveli and Daman and Diu',
            'DAMAN AND DIU' => 'Dadra and Nagar Haveli and Daman and Diu',
            'DELHI' => 'Delhi',
            'GOA' => 'Goa',
            'GUJARAT' => 'Gujarat',
            'HARYANA' => 'Haryana',
            'HIMACHAL PRADESH' => 'Himachal Pradesh',
            'JAMMU AND KASHMIR' => 'Jammu and Kashmir',
            'JHARKHAND' => 'Jharkhand',
            'KARNATAKA' => 'Karnataka',
            'KERALA' => 'Kerala',
            'LADAKH' => 'Ladakh',
            'LAKSHADWEEP' => 'Lakshadweep',
            'MADHYA PRADESH' => 'Madhya Pradesh',
            'MAHARASHTRA' => 'Maharashtra',
            'MANIPUR' => 'Manipur',
            'MEGHALAYA' => 'Meghalaya',
            'MIZORAM' => 'Mizoram',
            'NAGALAND' => 'Nagaland',
            'ODISHA' => 'Odisha',
            'PUDUCHERRY' => 'Puducherry',
            'PUNJAB' => 'Punjab',
            'RAJASTHAN' => 'Rajasthan',
            'SIKKIM' => 'Sikkim',
            'TAMIL NADU' => 'Tamil Nadu',
            'TELANGANA' => 'Telangana',
            'TRIPURA' => 'Tripura',
            'UTTAR PRADESH' => 'Uttar Pradesh',
            'UTTARAKHAND' => 'Uttarakhand',
            'WEST BENGAL' => 'West Bengal',
        ];
        
        $upperState = strtoupper($state);
        return $stateMap[$upperState] ?? $state;
    }
}
