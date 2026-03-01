<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            [
                'name' => '$50 Cash Bonus',
                'description' => 'Cash reward of $50',
                'type' => 'cash',
                'value' => 50.00,
                'probability' => 5.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'name' => '$25 Cash Bonus',
                'description' => 'Cash reward of $25',
                'type' => 'cash',
                'value' => 25.00,
                'probability' => 10.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'name' => '$10 Cash Bonus',
                'description' => 'Cash reward of $10',
                'type' => 'cash',
                'value' => 10.00,
                'probability' => 20.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'name' => 'Premium Product Pack',
                'description' => 'Free premium product sample pack',
                'type' => 'product',
                'value' => 30.00,
                'probability' => 15.00,
                'is_active' => true,
                'stock' => 100,
            ],
            [
                'name' => '$5 Gift Card',
                'description' => 'Gift card worth $5',
                'type' => 'gift_card',
                'value' => 5.00,
                'probability' => 25.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'name' => 'Try Again',
                'description' => 'Better luck next time!',
                'type' => 'other',
                'value' => 0.00,
                'probability' => 25.00,
                'is_active' => true,
                'stock' => null,
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
}
