<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixProductMRP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-mrp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix products with MRP = 0 by setting MRP = price';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = Product::where('mrp', 0)->orWhereNull('mrp')->count();
        
        if ($count === 0) {
            $this->info('No products need MRP fixing.');
            return;
        }

        $this->info("Found {$count} products with MRP = 0 or NULL.");

        Product::where('mrp', 0)->orWhereNull('mrp')->update([
            'mrp' => DB::raw('price')
        ]);

        $this->info('MRP values updated successfully!');
        $this->info('Products now have MRP = Price, so discount display will work.');
    }
}
