@extends('layouts.front-end.app')

@section('title', translate('payment_instructions'))

@push('css_or_js')
    <style>
        .manual-payment-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            background: #fff;
        }
        .manual-payment-header {
            background: linear-gradient(135deg, #0b2239 0%, #1a3c5a 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .manual-payment-body {
            padding: 40px;
        }
        .instruction-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        .step-number {
            background: #e9ecef;
            color: #495057;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        .account-details-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .copy-btn {
            background: none;
            border: none;
            color: #007bff;
            font-size: 0.9rem;
            cursor: pointer;
            padding: 0;
            margin-left: 10px;
            text-decoration: underline;
        }
        .trx-input {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ced4da;
            width: 100%;
            margin-top: 10px;
        }
        .premium-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }
        .premium-btn:hover {
            transform: translateY(-2px);
            background: #0056b3;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="manual-payment-card">
                    <div class="manual-payment-header">
                        <img width="80" src="{{ dynamicAsset(path: 'public/assets/back-end/img/modal/payment-methods/' . $payment_data->payment_method . '.png') }}" alt="" class="mb-3 rounded">
                        <h2>{{ translate('Complete_Your_Payment') }}</h2>
                        <p class="mb-0">{{ translate('Follow_the_steps_below_to_finalize_your_order') }}</p>
                    </div>
                    
                    <div class="manual-payment-body">
                        <div class="text-center mb-4">
                            <h4 class="text-muted">{{ translate('Amount_to_Pay') }}</h4>
                            <h1 class="text-primary">{{ webCurrencyConverter($payment_data->payment_amount) }}</h1>
                        </div>

                        <div class="instruction-item">
                            <div class="step-number">1</div>
                            <div>
                                <h6>{{ translate('Transfer_money_to_the_account_below') }}</h6>
                                <div class="account-details-box">
                                    @if($payment_data->payment_method == 'easypaisa')
                                        <p class="mb-1"><strong>{{ translate('Account_Title') }}:</strong> MyFancyMart Pvt Ltd</p>
                                        <p class="mb-0"><strong>{{ translate('EasyPaisa_Number') }}:</strong> 0300 1234567</p>
                                    @elseif($payment_data->payment_method == 'jazzcash')
                                        <p class="mb-1"><strong>{{ translate('Account_Title') }}:</strong> MyFancyMart Pvt Ltd</p>
                                        <p class="mb-0"><strong>{{ translate('JazzCash_Number') }}:</strong> 0300 7654321</p>
                                    @else
                                        <p class="mb-1"><strong>{{ translate('Bank_Name') }}:</strong> Habib Bank Limited (HBL)</p>
                                        <p class="mb-1"><strong>{{ translate('Account_Title') }}:</strong> MyFancyMart Pvt Ltd</p>
                                        <p class="mb-0"><strong>{{ translate('IBAN') }}:</strong> PK00 HABB 0123 4567 8901 23</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="instruction-item">
                            <div class="step-number">2</div>
                            <form action="{{ route('payment.manual.submit') }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="payment_id" value="{{ $payment_data->id }}">
                                <h6>{{ translate('Enter_Your_Transaction_ID') }}</h6>
                                <p class="text-muted small">{{ translate('Once_you_have_transferred_the_amount_please_enter_the_TRX_ID_below_as_proof_of_payment.') }}</p>
                                <input type="text" name="transaction_id" class="trx-input" placeholder="e.g. 1234567890" required>
                                
                                <div class="mt-4">
                                    <button type="submit" class="premium-btn">{{ translate('Submit_Payment_Proof') }}</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('checkout-payment') }}" class="text-muted small">{{ translate('Cancel_and_Go_Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
