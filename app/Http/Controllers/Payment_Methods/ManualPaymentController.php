<?php

namespace App\Http\Controllers\Payment_Methods;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ManualPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payment_id = $request->payment_id;
        $payment_data = PaymentRequest::find($payment_id);

        if (!$payment_data) {
            Toastr::error(translate('Invalid payment request'));
            return redirect()->route('home');
        }

        return view(VIEW_FILE_NAMES['manual_payment'], compact('payment_data'));
    }

    public function payment_submit(Request $request)
    {
        $request->validate([
            'payment_id' => 'required',
            'transaction_id' => 'required',
        ]);

        $payment_data = PaymentRequest::find($request->payment_id);
        
        // In a real scenario, we would save the transaction ID and wait for admin approval
        // For this "Offline" mockup that looks premium, we will just redirect to success
        // or a "Payment Pending Approval" page.
        
        return redirect()->route('web-payment-success', ['flag' => 'success']);
    }
}
