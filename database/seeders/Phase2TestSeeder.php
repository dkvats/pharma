<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SpinHistory;
use App\Models\User;
use App\Services\DoctorTargetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Phase2TestSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== PHASE 2 TEST DATA SEEDER ===\n";
        
        // Get first product
        $product = Product::first();
        if (!$product) {
            echo "ERROR: No products found!\n";
            return;
        }
        echo "Product: {$product->name} (ID: {$product->id})\n";
        
        // Set spin target product
        Setting::set('spin_target_product_id', $product->id, 'integer', 'spin', 'Target');
        echo "Spin target product set\n";
        
        // Create Doctor A
        $doctorA = User::firstOrCreate(
            ['email' => 'doctora_phase2@test.com'],
            [
                'name' => 'Doctor A Phase2',
                'password' => Hash::make('password'),
                'unique_code' => 'DOC-A' . strtoupper(Str::random(4)),
                'status' => 'active',
            ]
        );
        $doctorA->assignRole('Doctor');
        echo "Doctor A: ID={$doctorA->id}, Code={$doctorA->unique_code}\n";
        
        // Create Doctor B
        $doctorB = User::firstOrCreate(
            ['email' => 'doctorb_phase2@test.com'],
            [
                'name' => 'Doctor B Phase2',
                'password' => Hash::make('password'),
                'unique_code' => 'DOC-B' . strtoupper(Str::random(4)),
                'status' => 'active',
            ]
        );
        $doctorB->assignRole('Doctor');
        echo "Doctor B: ID={$doctorB->id}, Code={$doctorB->unique_code}\n";
        
        // Create Store 1 (Mumbai)
        $store1 = User::firstOrCreate(
            ['email' => 'store1_phase2@test.com'],
            [
                'name' => 'Store Mumbai Phase2',
                'password' => Hash::make('password'),
                'unique_code' => 'STR-M' . strtoupper(Str::random(4)),
                'status' => 'active',
                'city' => 'Mumbai',
                'district' => 'District A',
                'tehsil' => 'Tehsil X',
                'village' => 'Village 1',
            ]
        );
        $store1->assignRole('Store');
        echo "Store 1: ID={$store1->id}, City={$store1->city}\n";
        
        // Create Store 2 (Delhi)
        $store2 = User::firstOrCreate(
            ['email' => 'store2_phase2@test.com'],
            [
                'name' => 'Store Delhi Phase2',
                'password' => Hash::make('password'),
                'unique_code' => 'STR-D' . strtoupper(Str::random(4)),
                'status' => 'active',
                'city' => 'Delhi',
                'district' => 'District B',
                'tehsil' => 'Tehsil Y',
                'village' => 'Village 2',
            ]
        );
        $store2->assignRole('Store');
        echo "Store 2: ID={$store2->id}, City={$store2->city}\n";
        
        // Create 29 delivered orders for Doctor A (NO target product)
        echo "\nCreating 29 orders for Doctor A...\n";
        $this->createOrders($doctorA, 29, $store1, $product, false);
        
        // Create 30 delivered orders for Doctor B (WITH target product)
        echo "Creating 30 orders for Doctor B...\n";
        $this->createOrders($doctorB, 30, $store2, $product, true);
        
        echo "\n=== TEST DATA CREATION COMPLETE ===\n";
        
        // Verify counts
        $countA = Order::where('doctor_id', $doctorA->id)->count();
        $countB = Order::where('doctor_id', $doctorB->id)->count();
        echo "Doctor A orders in DB: {$countA}\n";
        echo "Doctor B orders in DB: {$countB}\n";
    }
    
    private function createOrders($doctor, $count, $store, $product, $includeTargetProduct)
    {
        $targetService = new DoctorTargetService();
        
        for ($i = 1; $i <= $count; $i++) {
            $order = Order::create([
                'order_number' => 'ORD-P2-' . strtoupper(Str::random(6)),
                'user_id' => $store->id,
                'doctor_id' => $doctor->id,
                'store_id' => $store->id,
                'status' => 'delivered',
                'total_amount' => 100,
                'delivered_at' => now(),
            ]);
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 100,
                'subtotal' => 100,
            ]);
            
            $targetService->incrementTarget($doctor->id, 1);
        }
        
        echo "Created {$count} orders for doctor ID {$doctor->id}\n";
    }
}
