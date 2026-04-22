<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Utils\WalletManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $userId = Auth::guard('customer')->id();
        $walletSummary = WalletManager::getWalletSummary($userId);
        $transactions = WalletManager::getTransactions($userId);

        return view('web-views.wallet.index', compact('walletSummary', 'transactions'));
    }

    public function withdraw(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'withdrawal_method' => 'required|string',
        ]);

        $userId = Auth::guard('customer')->id();
        
        // Ensure wallet exists for the user
        $wallet = Wallet::getOrCreate($userId);
        
        $result = WalletManager::processWithdrawal(
            $userId, 
            $request->amount, 
            $request->withdrawal_method
        );

        return response()->json($result);
    }

    public function transactions()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $userId = Auth::guard('customer')->id();
        $walletSummary = WalletManager::getWalletSummary($userId);
        $transactions = WalletManager::getTransactions($userId, 100); // More transactions for dedicated page

        return view('web-views.wallet.transactions', compact('walletSummary', 'transactions'));
    }
}
