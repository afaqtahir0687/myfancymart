@extends('layouts.front-end.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">{{ translate('my_wallet') }}</h2>
        </div>
    </div>

    <!-- Wallet Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">{{ translate('current_balance') }}</h5>
                    <h3 class="text-primary">{{ webCurrencyConverter(amount: $walletSummary['balance']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">{{ translate('total_earned') }}</h5>
                    <h3 class="text-success">{{ webCurrencyConverter(amount: $walletSummary['total_earned']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">{{ translate('total_withdrawn') }}</h5>
                    <h3 class="text-info">{{ webCurrencyConverter(amount: $walletSummary['total_withdrawn']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('request_withdrawal') }}</h5>
                </div>
                <div class="card-body">
                    <form id="withdrawal-form">
                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ translate('withdrawal_amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ getCurrencySymbol(type: 'web') }}</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="1" max="{{ $walletSummary['balance'] }}" step="0.01" required>
                            </div>
                            <small class="text-muted">{{ translate('maximum_withdrawable_amount') }}: {{ webCurrencyConverter(amount: $walletSummary['balance']) }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="withdrawal_method" class="form-label">{{ translate('withdrawal_method') }}</label>
                            <select class="form-control" id="withdrawal_method" name="withdrawal_method" required>
                                <option value="">{{ translate('select_method') }}</option>
                                <option value="bank_transfer">{{ translate('bank_transfer') }}</option>
                                <option value="paypal">{{ translate('paypal') }}</option>
                                <option value="stripe">{{ translate('stripe') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            {{ translate('request_withdrawal') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('wallet_status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $walletSummary['status'] == 'active' ? 'bg-success' : 'bg-danger' }} me-2">
                            {{ $walletSummary['status'] == 'active' ? translate('active') : translate('inactive') }}
                        </span>
                        <small class="text-muted">
                            {{ $walletSummary['status'] == 'active' ? translate('wallet_is_active') : translate('wallet_is_inactive') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ translate('recent_transactions') }}</h5>
                    <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-primary">
                        {{ translate('view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ translate('date') }}</th>
                                        <th>{{ translate('description') }}</th>
                                        <th>{{ translate('type') }}</th>
                                        <th>{{ translate('amount') }}</th>
                                        <th>{{ translate('status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="badge {{ $transaction->transaction_type == 'credit' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $transaction->transaction_type == 'credit' ? translate('credit') : translate('debit') }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->transaction_type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->transaction_type == 'credit' ? '+' : '-' }}{{ webCurrencyConverter(amount: $transaction->amount) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ translate($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">{{ translate('no_transactions_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    $('#withdrawal-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("wallet.withdraw") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('{{ translate('something_went_wrong') }}');
            }
        });
    });
});
</script>
@endpush
