<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Utils\WalletManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletAPIController extends Controller
{
    /**
     * Get wallet summary
     */
    public function getSummary(Request $request): JsonResponse
    {
        $customer = Helpers::getCustomerInformation($request);
        if ($customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }
        $walletSummary = WalletManager::getWalletSummary($customer->id);

        return response()->json([
            'success' => true,
            'data' => $walletSummary
        ]);
    }

    /**
     * Get wallet transactions
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $customer = Helpers::getCustomerInformation($request);
        if ($customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }
        
        $transactions = WalletTransaction::where('wallet_id', function($query) use ($customer) {
            $query->select('id')->from('wallets')->where('user_id', $customer->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $transactions->items()->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'wallet_id' => $transaction->wallet_id,
                    'transaction_type' => $transaction->transaction_type,
                    'amount' => $transaction->credit > 0 ? $transaction->credit : $transaction->debit,
                    'description' => $transaction->description,
                    'order_id' => $transaction->order_id,
                    'order_detail_id' => $transaction->order_detail_id,
                    'withdraw_request_id' => $transaction->withdraw_request_id,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ]
        ]);
    }

    /**
     * Request withdrawal
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $customer = Helpers::getCustomerInformation($request);
        if ($customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'withdrawal_method' => 'required|in:bank_transfer,jazzcash,easypaisa',
            'bank_name' => 'required_if:withdrawal_method,bank_transfer',
            'account_number' => 'required',
            'account_holder_name' => 'required_if:withdrawal_method,bank_transfer',
            'account_name' => 'required_if:withdrawal_method,jazzcash|required_if:withdrawal_method,easypaisa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        
        // Process withdrawal using WalletManager
        $result = WalletManager::processWithdrawal($customer->id, $request->amount, $request->withdrawal_method, [
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
            'account_name' => $request->account_name,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'data' => $result['data']
        ]);
    }

    /**
     * Get withdrawal requests
     */
    public function getWithdrawalRequests(Request $request): JsonResponse
    {
        $customer = Helpers::getCustomerInformation($request);
        if ($customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }
        
        $requests = WithdrawRequest::where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $requests->items()->map(function ($withdrawRequest) {
                return [
                    'id' => $withdrawRequest->id,
                    'user_id' => $withdrawRequest->user_id,
                    'amount' => $withdrawRequest->amount,
                    'withdrawal_method' => $withdrawRequest->withdrawal_method,
                    'bank_name' => $withdrawRequest->bank_name,
                    'account_number' => $withdrawRequest->account_number,
                    'account_holder_name' => $withdrawRequest->account_holder_name,
                    'account_name' => $withdrawRequest->account_name,
                    'approved' => $withdrawRequest->approved,
                    'approved_at' => $withdrawRequest->approved_at,
                    'rejected_at' => $withdrawRequest->rejected_at,
                    'rejection_reason' => $withdrawRequest->rejection_reason,
                    'created_at' => $withdrawRequest->created_at,
                    'updated_at' => $withdrawRequest->updated_at,
                ];
            }),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
                'last_page' => $requests->lastPage(),
            ]
        ]);
    }

    /**
     * Get wallet balance
     */
    public function getBalance(Request $request): JsonResponse
    {
        $customer = Helpers::getCustomerInformation($request);
        if ($customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }
        $wallet = Wallet::where('user_id', $customer->id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $wallet ? $wallet->balance : 0,
                'currency' => 'USD',
                'last_updated' => $wallet ? $wallet->updated_at : null,
            ]
        ]);
    }
}
