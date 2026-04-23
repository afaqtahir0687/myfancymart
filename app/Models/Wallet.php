<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_withdrawn',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get or create wallet for user
     */
    public static function getOrCreate($userId): self
    {
        $wallet = self::where('user_id', $userId)->first();
        
        if (!$wallet) {
            $wallet = self::create([
                'user_id' => $userId,
                'balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'status' => 1,
            ]);
        }
        
        return $wallet;
    }

    /**
     * Add amount to wallet balance
     */
    public function credit($amount, $source = null, $description = null, $orderId = null, $orderDetailId = null): WalletTransaction
    {
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
        $this->save();

        return $this->transactions()->create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $amount,
            'transaction_type' => 'credit',
            'source' => $source,
            'description' => $description,
            'order_id' => $orderId,
            'order_detail_id' => $orderDetailId,
            'status' => 'completed',
        ]);
    }

    /**
     * Deduct amount from wallet balance
     */
    public function debit($amount, $source = null, $description = null, $orderId = null, $orderDetailId = null): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $this->decrement('balance', $amount);
        $this->save();

        return $this->transactions()->create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $amount,
            'transaction_type' => 'debit',
            'source' => $source,
            'description' => $description,
            'order_id' => $orderId,
            'order_detail_id' => $orderDetailId,
            'status' => 'completed',
        ]);
    }
}
