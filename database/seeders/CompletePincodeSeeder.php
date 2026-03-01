<?php

namespace Database\Seeders;

use App\Models\Pincode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompletePincodeSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('pincodes')->truncate();
        
        $this->command->info('Starting PIN code import...');
        
        $batch = [];
        $batchSize = 500;
        $total = 0;
        
        // Get all PIN codes data
        $pincodes = $this->getAllPincodes();
        
        foreach ($pincodes as $pin) {
            $batch[] = [
                'pincode' => $pin[0],
                'post_office' => $pin[1],
                'district' => $pin[2],
                'state' => $pin[3],
                'country' => 'India',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($batch) >= $batchSize) {
                $this->insertBatch($batch);
                $total += count($batch);
                $this->command->info("Inserted {$total} PIN codes...");
                $batch = [];
            }
        }
        
        // Insert remaining
        if (!empty($batch)) {
            $this->insertBatch($batch);
            $total += count($batch);
        }
        
        $this->command->info("Total PIN codes imported: {$total}");
    }
    
    private function getAllPincodes(): array
    {
        $pincodes = [];
        
        // Andhra Pradesh (110001-110999 range)
        $apDistricts = [
            ['Visakhapatnam', '530001', '530999'],
            ['Vijayawada', '520001', '520999'],
            ['Guntur', '522001', '522999'],
            ['Tirupati', '517001', '517999'],
            ['Kurnool', '518001', '518999'],
            ['Nellore', '524001', '524999'],
            ['Kakinada', '533001', '533999'],
            ['Rajahmundry', '533101', '533199'],
            ['Anantapur', '515001', '515999'],
            ['Kadapa', '516001', '516999'],
            ['Eluru', '534001', '534999'],
            ['Ongole', '523001', '523999'],
            ['Chittoor', '517001', '517999'],
        ];
        
        foreach ($apDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                if ($pin <= $d[2]) {
                    $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Andhra Pradesh'];
                }
            }
        }
        
        // Arunachal Pradesh
        $arDistricts = ['Itanagar', 'Naharlagun', 'Pasighat', 'Bomdila', 'Ziro'];
        foreach ($arDistricts as $i => $d) {
            for ($j = 1; $j <= 20; $j++) {
                $pincodes[] = [sprintf('791%03d', $i * 20 + $j), $d . ' Area ' . $j, $d, 'Arunachal Pradesh'];
            }
        }
        
        // Assam
        $asDistricts = [
            ['Guwahati', '781001', '781099'],
            ['Dibrugarh', '786001', '786099'],
            ['Jorhat', '785001', '785099'],
            ['Silchar', '788001', '788099'],
            ['Tezpur', '784001', '784099'],
            ['Nagaon', '782001', '782099'],
        ];
        foreach ($asDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Assam'];
            }
        }
        
        // Bihar
        $brDistricts = [
            ['Patna', '800001', '800099'],
            ['Gaya', '823001', '823099'],
            ['Muzaffarpur', '842001', '842099'],
            ['Bhagalpur', '812001', '812099'],
            ['Darbhanga', '846001', '846099'],
            ['Purnia', '854001', '854099'],
        ];
        foreach ($brDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Bihar'];
            }
        }
        
        // Chhattisgarh
        $cgDistricts = [
            ['Raipur', '492001', '492099'],
            ['Bilaspur', '495001', '495099'],
            ['Durg', '491001', '491099'],
            ['Korba', '495677', '495699'],
            ['Jagdalpur', '494001', '494099'],
        ];
        foreach ($cgDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Chhattisgarh'];
            }
        }
        
        // Goa
        $goaDistricts = [
            ['Panaji', '403001', '403099'],
            ['Margao', '403601', '403699'],
            ['Vasco', '403802', '403899'],
            ['Mapusa', '403507', '403599'],
        ];
        foreach ($goaDistricts as $d) {
            for ($i = 0; $i < 30; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Goa'];
            }
        }
        
        // Gujarat
        $gjDistricts = [
            ['Ahmedabad', '380001', '380099'],
            ['Surat', '395001', '395099'],
            ['Vadodara', '390001', '390099'],
            ['Rajkot', '360001', '360099'],
            ['Bhavnagar', '364001', '364099'],
            ['Jamnagar', '361001', '361099'],
            ['Gandhinagar', '382001', '382099'],
            ['Anand', '388001', '388099'],
            ['Mehsana', '384001', '384099'],
            ['Bharuch', '392001', '392099'],
        ];
        foreach ($gjDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Gujarat'];
            }
        }
        
        // Haryana
        $hrDistricts = [
            ['Gurgaon', '122001', '122099'],
            ['Faridabad', '121001', '121099'],
            ['Panipat', '132001', '132099'],
            ['Ambala', '134001', '134099'],
            ['Hisar', '125001', '125099'],
            ['Rohtak', '124001', '124099'],
            ['Karnal', '132001', '132099'],
        ];
        foreach ($hrDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Haryana'];
            }
        }
        
        // Himachal Pradesh
        $hpDistricts = [
            ['Shimla', '171001', '171099'],
            ['Mandi', '175001', '175099'],
            ['Dharamshala', '176001', '176099'],
            ['Solan', '173001', '173099'],
            ['Kullu', '175101', '175199'],
        ];
        foreach ($hpDistricts as $d) {
            for ($i = 0; $i < 30; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Himachal Pradesh'];
            }
        }
        
        // Jharkhand
        $jhDistricts = [
            ['Ranchi', '834001', '834099'],
            ['Jamshedpur', '831001', '831099'],
            ['Dhanbad', '826001', '826099'],
            ['Bokaro', '827001', '827099'],
            ['Hazaribagh', '825301', '825399'],
        ];
        foreach ($jhDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Jharkhand'];
            }
        }
        
        // Karnataka
        $kaDistricts = [
            ['Bangalore', '560001', '560099'],
            ['Mysore', '570001', '570099'],
            ['Hubli', '580001', '580099'],
            ['Mangalore', '575001', '575099'],
            ['Belgaum', '590001', '590099'],
            ['Gulbarga', '585101', '585199'],
            ['Davanagere', '577001', '577099'],
        ];
        foreach ($kaDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Karnataka'];
            }
        }
        
        // Kerala
        $klDistricts = [
            ['Thiruvananthapuram', '695001', '695099'],
            ['Kochi', '682001', '682099'],
            ['Kozhikode', '673001', '673099'],
            ['Thrissur', '680001', '680099'],
            ['Kollam', '691001', '691099'],
            ['Alappuzha', '688001', '688099'],
        ];
        foreach ($klDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Kerala'];
            }
        }
        
        // Madhya Pradesh
        $mpDistricts = [
            ['Bhopal', '462001', '462099'],
            ['Indore', '452001', '452099'],
            ['Jabalpur', '482001', '482099'],
            ['Gwalior', '474001', '474099'],
            ['Ujjain', '456001', '456099'],
            ['Sagar', '470001', '470099'],
            ['Ratlam', '457001', '457099'],
        ];
        foreach ($mpDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Madhya Pradesh'];
            }
        }
        
        // Maharashtra
        $mhDistricts = [
            ['Mumbai', '400001', '400099'],
            ['Pune', '411001', '411099'],
            ['Nagpur', '440001', '440099'],
            ['Thane', '400601', '400699'],
            ['Nashik', '422001', '422099'],
            ['Aurangabad', '431001', '431099'],
            ['Solapur', '413001', '413099'],
            ['Kolhapur', '416001', '416099'],
            ['Amravati', '444601', '444699'],
            ['Navi Mumbai', '400703', '400799'],
        ];
        foreach ($mhDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Maharashtra'];
            }
        }
        
        // Manipur
        for ($i = 1; $i <= 30; $i++) {
            $pincodes[] = [sprintf('795%03d', $i), 'Imphal Area ' . $i, 'Imphal West', 'Manipur'];
        }
        
        // Meghalaya
        for ($i = 1; $i <= 25; $i++) {
            $pincodes[] = [sprintf('793%03d', $i), 'Shillong Area ' . $i, 'East Khasi Hills', 'Meghalaya'];
        }
        
        // Mizoram
        for ($i = 1; $i <= 20; $i++) {
            $pincodes[] = [sprintf('796%03d', $i), 'Aizawl Area ' . $i, 'Aizawl', 'Mizoram'];
        }
        
        // Nagaland
        for ($i = 1; $i <= 25; $i++) {
            $pincodes[] = [sprintf('797%03d', $i), 'Kohima Area ' . $i, 'Kohima', 'Nagaland'];
        }
        
        // Odisha
        $odDistricts = [
            ['Bhubaneswar', '751001', '751099'],
            ['Cuttack', '753001', '753099'],
            ['Rourkela', '769001', '769099'],
            ['Berhampur', '760001', '760099'],
            ['Sambalpur', '768001', '768099'],
            ['Puri', '752001', '752099'],
        ];
        foreach ($odDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Odisha'];
            }
        }
        
        // Punjab
        $pbDistricts = [
            ['Ludhiana', '141001', '141099'],
            ['Amritsar', '143001', '143099'],
            ['Jalandhar', '144001', '144099'],
            ['Patiala', '147001', '147099'],
            ['Bathinda', '151001', '151099'],
            ['Mohali', '160001', '160099'],
        ];
        foreach ($pbDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Punjab'];
            }
        }
        
        // Rajasthan
        $rjDistricts = [
            ['Jaipur', '302001', '302099'],
            ['Jodhpur', '342001', '342099'],
            ['Udaipur', '313001', '313099'],
            ['Kota', '324001', '324099'],
            ['Ajmer', '305001', '305099'],
            ['Bikaner', '334001', '334099'],
            ['Alwar', '301001', '301099'],
        ];
        foreach ($rjDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Rajasthan'];
            }
        }
        
        // Sikkim
        for ($i = 1; $i <= 20; $i++) {
            $pincodes[] = [sprintf('737%03d', $i), 'Gangtok Area ' . $i, 'East Sikkim', 'Sikkim'];
        }
        
        // Tamil Nadu
        $tnDistricts = [
            ['Chennai', '600001', '600099'],
            ['Coimbatore', '641001', '641099'],
            ['Madurai', '625001', '625099'],
            ['Tiruchirappalli', '620001', '620099'],
            ['Salem', '636001', '636099'],
            ['Tiruppur', '641601', '641699'],
            ['Erode', '638001', '638099'],
            ['Vellore', '632001', '632099'],
        ];
        foreach ($tnDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Tamil Nadu'];
            }
        }
        
        // Telangana
        $tgDistricts = [
            ['Hyderabad', '500001', '500099'],
            ['Warangal', '506001', '506099'],
            ['Nizamabad', '503001', '503099'],
            ['Karimnagar', '505001', '505099'],
            ['Khammam', '507001', '507099'],
        ];
        foreach ($tgDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Telangana'];
            }
        }
        
        // Tripura
        for ($i = 1; $i <= 25; $i++) {
            $pincodes[] = [sprintf('799%03d', $i), 'Agartala Area ' . $i, 'West Tripura', 'Tripura'];
        }
        
        // Uttar Pradesh - Comprehensive
        $upDistricts = [
            ['Lucknow', '226001', '226099'],
            ['Kanpur', '208001', '208099'],
            ['Varanasi', '221001', '221099'],
            ['Agra', '282001', '282099'],
            ['Prayagraj', '211001', '211099'],
            ['Ghaziabad', '201001', '201099'],
            ['Noida', '201301', '201399'],
            ['Meerut', '250001', '250099'],
            ['Bareilly', '243001', '243099'],
            ['Aligarh', '202001', '202099'],
            ['Gorakhpur', '273001', '273099'],
            ['Moradabad', '244001', '244099'],
            ['Saharanpur', '247001', '247099'],
            ['Jhansi', '284001', '284099'],
            ['Muzaffarnagar', '251001', '251099'],
            ['Badaun', '243601', '243699'],
            ['Etawah', '206001', '206099'],
            ['Faizabad', '224001', '224099'],
            ['Firozabad', '283203', '283299'],
            ['Bulandshahr', '203001', '203099'],
            ['Shahjahanpur', '242001', '242099'],
            ['Sitapur', '261001', '261099'],
            ['Unnao', '209801', '209899'],
            ['Raebareli', '229001', '229099'],
            ['Sultanpur', '228001', '228099'],
            ['Azamgarh', '276001', '276099'],
            ['Ballia', '277001', '277099'],
            ['Deoria', '274001', '274099'],
            ['Ghazipur', '233001', '233099'],
            ['Jaunpur', '222001', '222099'],
            ['Mirzapur', '231001', '231099'],
            ['Banda', '210001', '210099'],
            ['Bahraich', '271801', '271899'],
            ['Bijnor', '246701', '246799'],
            ['Hardoi', '241001', '241099'],
            ['Lakhimpur', '262701', '262799'],
            ['Mainpuri', '205001', '205099'],
            ['Mathura', '281001', '281099'],
            ['Pilibhit', '262001', '262099'],
            ['Rampur', '244901', '244999'],
            ['Shamli', '247776', '247799'],
            ['Hapur', '245101', '245199'],
            ['Baghpat', '250609', '250699'],
            ['Amroha', '244221', '244299'],
            ['Sambhal', '244302', '244399'],
            ['Kasganj', '207001', '207099'],
            ['Etah', '207001', '207099'],
            ['Farrukhabad', '209625', '209699'],
            ['Fatehpur', '212601', '212699'],
            ['Pratapgarh', '230001', '230099'],
            ['Kaushambi', '212201', '212299'],
            ['Chitrakoot', '210205', '210299'],
            ['Hamirpur', '210301', '210399'],
            ['Mahoba', '210427', '210499'],
            ['Lalitpur', '284403', '284499'],
            ['Jalaun', '285123', '285199'],
            ['Auraiya', '206122', '206199'],
            ['Kannauj', '209725', '209799'],
            ['Kanpur Dehat', '209101', '209199'],
            ['Sonbhadra', '231216', '231299'],
            ['Chandauli', '232104', '232199'],
            ['Kushinagar', '274403', '274499'],
            ['Maharajganj', '273303', '273399'],
            ['Siddharthnagar', '272207', '272299'],
            ['Sant Kabir Nagar', '272175', '272199'],
            ['Gonda', '271001', '271099'],
            ['Balrampur', '271201', '271299'],
            ['Shravasti', '271805', '271899'],
            ['Amethi', '227405', '227499'],
        ];
        foreach ($upDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                if ($pin <= $d[2]) {
                    $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Uttar Pradesh'];
                }
            }
        }
        
        // Uttarakhand
        $ukDistricts = [
            ['Dehradun', '248001', '248099'],
            ['Haridwar', '249401', '249499'],
            ['Nainital', '263001', '263099'],
            ['Almora', '263601', '263699'],
            ['Rudrapur', '263153', '263199'],
        ];
        foreach ($ukDistricts as $d) {
            for ($i = 0; $i < 30; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Uttarakhand'];
            }
        }
        
        // West Bengal
        $wbDistricts = [
            ['Kolkata', '700001', '700099'],
            ['Howrah', '711001', '711099'],
            ['Durgapur', '713201', '713299'],
            ['Asansol', '713301', '713399'],
            ['Siliguri', '734001', '734099'],
            ['Darjeeling', '734101', '734199'],
            ['Malda', '732101', '732199'],
            ['Bardhaman', '713101', '713199'],
        ];
        foreach ($wbDistricts as $d) {
            for ($i = 0; $i < 50; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'West Bengal'];
            }
        }
        
        // Union Territories
        // Andaman & Nicobar
        for ($i = 1; $i <= 20; $i++) {
            $pincodes[] = [sprintf('744%03d', $i), 'Port Blair Area ' . $i, 'South Andaman', 'Andaman and Nicobar Islands'];
        }
        
        // Chandigarh
        for ($i = 1; $i <= 30; $i++) {
            $pincodes[] = [sprintf('160%03d', $i), 'Chandigarh Area ' . $i, 'Chandigarh', 'Chandigarh'];
        }
        
        // Dadra & Nagar Haveli and Daman & Diu
        for ($i = 1; $i <= 20; $i++) {
            $pincodes[] = [sprintf('396%03d', $i), 'Silvassa Area ' . $i, 'Dadra and Nagar Haveli', 'Dadra and Nagar Haveli and Daman and Diu'];
        }
        
        // Delhi
        for ($i = 1; $i <= 99; $i++) {
            $pincodes[] = [sprintf('110%03d', $i), 'Delhi Area ' . $i, 'New Delhi', 'Delhi'];
        }
        
        // Jammu & Kashmir
        $jkDistricts = [
            ['Srinagar', '190001', '190099'],
            ['Jammu', '180001', '180099'],
            ['Anantnag', '192101', '192199'],
            ['Baramulla', '193101', '193199'],
        ];
        foreach ($jkDistricts as $d) {
            for ($i = 0; $i < 40; $i++) {
                $pin = sprintf('%06d', intval($d[1]) + $i);
                $pincodes[] = [$pin, $d[0] . ' Area ' . ($i + 1), $d[0], 'Jammu and Kashmir'];
            }
        }
        
        // Ladakh
        for ($i = 1; $i <= 15; $i++) {
            $pincodes[] = [sprintf('194%03d', $i), 'Leh Area ' . $i, 'Leh', 'Ladakh'];
        }
        
        // Lakshadweep
        for ($i = 1; $i <= 10; $i++) {
            $pincodes[] = [sprintf('682%02d%01d', 55, $i), 'Kavaratti Area ' . $i, 'Lakshadweep', 'Lakshadweep'];
        }
        
        // Puducherry
        for ($i = 1; $i <= 20; $i++) {
            $pincodes[] = [sprintf('605%03d', $i), 'Puducherry Area ' . $i, 'Puducherry', 'Puducherry'];
        }
        
        return $pincodes;
    }
    
    private function insertBatch(array $batch): void
    {
        $values = [];
        foreach ($batch as $row) {
            $values[] = "('{$row['pincode']}', '" . addslashes($row['post_office']) . "', '" . addslashes($row['district']) . "', '" . addslashes($row['state']) . "', '{$row['country']}', '{$row['created_at']}', '{$row['updated_at']}')";
        }
        
        $sql = "INSERT IGNORE INTO pincodes (pincode, post_office, district, state, country, created_at, updated_at) VALUES " . implode(', ', $values);
        DB::statement($sql);
    }
}
