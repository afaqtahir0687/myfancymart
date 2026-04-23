<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Devrabiul\ToastMagic\Facades\ToastMagic;

class ResellerWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawRequest::whereNotNull('user_id')->with('user');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('approved', $request->status);
        }

        $withdrawRequests = $query->latest()->paginate(25);

        return view('admin-views.customer.reseller-withdraw-list', compact('withdrawRequests'));
    }

    public function statusUpdate(Request $request, $id)
    {
        $withdraw = WithdrawRequest::whereNotNull('user_id')->find($id);
        if (!$withdraw) {
            ToastMagic::error(translate('request_not_found'));
            return back();
        }

        if ($withdraw->approved != 0) {
            ToastMagic::error(translate('request_already_processed'));
            return back();
        }

        if ($request->approved == 1) {
            // Approve
            $withdraw->approved = 1;
            $withdraw->transaction_note = $request->transaction_note;
            $withdraw->save();

            // Increment total withdrawn in wallet
            $wallet = Wallet::where('user_id', $withdraw->user_id)->first();
            if ($wallet) {
                $wallet->increment('total_withdrawn', $withdraw->amount);
            }

            // Update wallet transaction status
            $transaction = WalletTransaction::where('user_id', $withdraw->user_id)
                ->where('description', 'LIKE', "%Withdrawal request #{$withdraw->id}%")
                ->first();
            
            if ($transaction) {
                $transaction->update(['status' => 'completed']);
            }

            ToastMagic::success(translate('withdrawal_approved_successfully'));
        } elseif ($request->approved == 2) {
            // Deny/Reject
            $withdraw->approved = 2;
            $withdraw->transaction_note = $request->transaction_note;
            $withdraw->save();

            // Refund the wallet
            $wallet = Wallet::where('user_id', $withdraw->user_id)->first();
            if ($wallet) {
                $wallet->increment('balance', $withdraw->amount);
                
                // Create refund transaction
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'amount' => $withdraw->amount,
                    'transaction_type' => 'credit',
                    'source' => 'withdrawal_refund',
                    'description' => "Refund for rejected withdrawal request #{$withdraw->id}",
                    'status' => 'completed',
                ]);
            }

            // Mark original transaction as cancelled
            $transaction = WalletTransaction::where('user_id', $withdraw->user_id)
                ->where('description', 'LIKE', "%Withdrawal request #{$withdraw->id}%")
                ->first();
            
            if ($transaction) {
                $transaction->update(['status' => 'cancelled']);
            }

            ToastMagic::success(translate('withdrawal_rejected_successfully'));
        }

        return back();
    }
}
