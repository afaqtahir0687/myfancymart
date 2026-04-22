@extends('layouts.front-end.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('wallet.index') }}">{{ translate('my_wallet') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ translate('transactions') }}</li>
                </ol>
            </nav>
            <h2 class="mb-4">{{ translate('wallet_transactions') }}</h2>
        </div>
    </div>

    <!-- Wallet Summary -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-primary">{{ translate('current_balance') }}</h6>
                    <h5 class="text-primary">{{ webCurrencyConverter(amount: $walletSummary['balance']) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-success">{{ translate('total_earned') }}</h6>
                    <h5 class="text-success">{{ webCurrencyConverter(amount: $walletSummary['total_earned']) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-info">{{ translate('total_withdrawn') }}</h6>
                    <h5 class="text-info">{{ webCurrencyConverter(amount: $walletSummary['total_withdrawn']) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-warning">{{ translate('status') }}</h6>
                    <h5>
                        <span class="badge {{ $walletSummary['status'] == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ $walletSummary['status'] == 'active' ? translate('active') : translate('inactive') }}
                        </span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('all_transactions') }}</h5>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ translate('date') }}</th>
                                        <th>{{ translate('description') }}</th>
                                        <th>{{ translate('source') }}</th>
                                        <th>{{ translate('type') }}</th>
                                        <th>{{ translate('amount') }}</th>
                                        <th>{{ translate('status') }}</th>
                                        <th>{{ translate('order') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div>
                                                    {{ $transaction->description }}
                                                    @if($transaction->orderDetail && $transaction->orderDetail->product_name)
                                                        <small class="text-muted d-block">
                                                            {{ translate('product') }}: {{ $transaction->orderDetail->product_name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($transaction->source)
                                                    <span class="badge bg-secondary">
                                                        {{ translate($transaction->source) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $transaction->transaction_type == 'credit' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $transaction->transaction_type == 'credit' ? translate('credit') : translate('debit') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold {{ $transaction->transaction_type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->transaction_type == 'credit' ? '+' : '-' }}{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->amount), currencyCode: getCurrencyCode()) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ translate($transaction->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->order_id)
                                                    <a href="{{ route('account-order-details', ['id' => $transaction->order_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                        #{{ $transaction->order_id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination (if needed) -->
                        @if($transactions->count() >= 100)
                            <div class="text-center mt-3">
                                <small class="text-muted">{{ translate('showing_latest_100_transactions') }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-receipt fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">{{ translate('no_transactions_found') }}</h5>
                            <p class="text-muted">{{ translate('start_reselling_to_earn_profit') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                {{ translate('start_shopping') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
