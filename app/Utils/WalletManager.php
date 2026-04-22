<?php

namespace App\Utils;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletManager
{
    /**
     * Add resell profit to reseller's wallet when order is completed
     */
    public static function addResellProfit($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return false;
        }

        foreach ($order->orderDetails as $orderDetail) {
            // Check if this is a resell item
            if ($orderDetail->is_resell && $orderDetail->resell_profit > 0) {
                // Get or create wallet for the reseller (user who placed the order)
                $wallet = Wallet::getOrCreate($order->customer_id);
                
                // Add profit to wallet
                $profitAmount = $orderDetail->resell_profit * $orderDetail->qty;
                
                $transaction = $wallet->credit(
                    $profitAmount,
                    'resell_profit',
                    "Resell profit from order #{$order->id} - {$orderDetail->product_name}",
                    $order->id,
                    $orderDetail->id
                );

                // Log the transaction
                \Log::info("Resell profit added to wallet", [
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'profit_amount' => $profitAmount,
                    'transaction_id' => $transaction->id
                ]);
            }
        }

        return true;
    }

    /**
     * Get wallet balance for user
     */
    public static function getBalance($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        return $wallet ? $wallet->balance : 0;
    }

    /**
     * Get wallet transactions for user
     */
    public static function getTransactions($userId, $limit = 50)
    {
        return WalletTransaction::where('user_id', $userId)
            ->with(['order', 'orderDetail'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get wallet summary for user
     */
    public static function getWalletSummary($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return [
                'balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'status' => 'inactive'
            ];
        }

        return [
            'balance' => $wallet->balance,
            'total_earned' => $wallet->total_earned,
            'total_withdrawn' => $wallet->total_withdrawn,
            'status' => $wallet->status ? 'active' : 'inactive'
        ];
    }

    /**
     * Process withdrawal request
     */
    public static function processWithdrawal($userId, $amount, $withdrawalMethod = null)
    {
        try {
            $wallet = Wallet::where('user_id', $userId)->first();
            
            if (!$wallet) {
                return [
                    'success' => false,
                    'message' => 'No wallet found for this user'
                ];
            }
            
            if ($wallet->balance < $amount) {
                return [
                    'success' => false,
                    'message' => 'Insufficient balance. Available balance: ' . $wallet->balance
                ];
            }

            // Create debit transaction
            $transaction = $wallet->debit(
                $amount,
                'withdrawal',
                "Withdrawal request - {$withdrawalMethod}",
                null,
                null
            );

            return [
                'success' => true,
                'message' => 'Withdrawal processed successfully',
                'transaction_id' => $transaction->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Withdrawal failed: ' . $e->getMessage()
            ];
        }
    }
}
