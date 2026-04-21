<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireDiscounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discounts:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire discounts that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting discount expiration check...');
        
        $today = date('Y-m-d');
        $expiredCount = 0;
        
        // Find products with expired discounts
        $expiredProducts = Product::where('discount_end_date', '<', $today)
            ->where('discount_is_active', true)
            ->get();
        
        foreach ($expiredProducts as $product) {
            // Deactivate the discount
            $product->update([
                'discount_is_active' => false,
                'discount' => 0, // Optional: reset discount amount
            ]);
            
            $expiredCount++;
            
            $this->line("Expired discount for product: {$product->name} (ID: {$product->id})");
        }
        
        // Find products with discounts expiring today
        $expiringToday = Product::where('discount_end_date', $today)
            ->where('discount_is_active', true)
            ->get();
        
        foreach ($expiringToday as $product) {
            $this->warn("Discount expires today for product: {$product->name} (ID: {$product->id})");
        }
        
        // Find products with discounts expiring in next 3 days
        $threeDaysFromNow = date('Y-m-d', strtotime('+3 days'));
        $expiringSoon = Product::where('discount_end_date', '<=', $threeDaysFromNow)
            ->where('discount_end_date', '>', $today)
            ->where('discount_is_active', true)
            ->get();
        
        foreach ($expiringSoon as $product) {
            $this->warn("Discount expiring soon for product: {$product->name} (ID: {$product->id}) - Expires: {$product->discount_end_date}");
        }
        
        $message = "Discount expiration check completed. ";
        $message .= "Expired: {$expiredCount}, ";
        $message .= "Expiring today: " . count($expiringToday) . ", ";
        $message .= "Expiring soon: " . count($expiringSoon);
        
        $this->info($message);
        
        Log::info('Discount expiration check completed', [
            'expired_count' => $expiredCount,
            'expiring_today' => count($expiringToday),
            'expiring_soon' => count($expiringSoon),
            'date' => $today
        ]);
        
        return 0;
    }
}
