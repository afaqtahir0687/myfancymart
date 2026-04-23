@extends('layouts.front-end.app')

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">{{ translate('my_wallet') }}</h2>
        </div>
    </div>

    <!-- Wallet Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center p-3">
                    <h5 class="card-title text-primary fs-14">{{ translate('current_balance') }}</h5>
                    <h3 class="text-primary fs-20">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $walletSummary['balance']), currencyCode: getCurrencyCode()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center p-3">
                    <h5 class="card-title text-success fs-14">{{ translate('total_earned') }}</h5>
                    <h3 class="text-success fs-20">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $walletSummary['total_earned']), currencyCode: getCurrencyCode()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center p-3">
                    <h5 class="card-title text-warning fs-14">{{ translate('pending_to_approval') }}</h5>
                    <h3 class="text-warning fs-20">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $walletSummary['pending_withdrawal']), currencyCode: getCurrencyCode()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center p-3">
                    <h5 class="card-title text-info fs-14">{{ translate('total_withdrawn') }}</h5>
                    <h3 class="text-info fs-20">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $walletSummary['total_withdrawn']), currencyCode: getCurrencyCode()) }}</h3>
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
                                <span class="input-group-text">{{ getCurrencySymbol() }}</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="1" max="{{ $walletSummary['balance'] }}" step="0.01" required>
                            </div>
                            <small class="text-muted">{{ translate('maximum_withdrawable_amount') }}: {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $walletSummary['balance']), currencyCode: getCurrencyCode()) }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="withdrawal_method" class="form-label">{{ translate('withdrawal_method') }}</label>
                            <select class="form-control" id="withdrawal_method" name="withdrawal_method" required>
                                <option value="">{{ translate('select_method') }}</option>
                                <option value="jazzcash">{{ translate('jazzCash') }}</option>
                                <option value="easypaisa">{{ translate('easyPaisa') }}</option>
                                <option value="bank_transfer">{{ translate('bank_transfer') }}</option>
                            </select>
                        </div>

                        <!-- Dynamic Fields -->
                        <div id="method-fields">
                            <div id="bank-fields" class="d-none">
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('bank_name') }}</label>
                                    <input type="text" name="bank_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('account_number') }}</label>
                                    <input type="text" name="account_number" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('account_holder_name') }}</label>
                                    <input type="text" name="account_holder_name" class="form-control">
                                </div>
                            </div>
                            <div id="mobile-wallet-fields" class="d-none">
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('account_number') }}</label>
                                    <input type="text" name="account_number" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('account_name') }}</label>
                                    <input type="text" name="account_name" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
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
                                                {{ $transaction->transaction_type == 'credit' ? '+' : '-' }}{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $transaction->amount), currencyCode: getCurrencyCode()) }}
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
    $('#withdrawal_method').on('change', function() {
        let method = $(this).val();
        $('#bank-fields').addClass('d-none');
        $('#mobile-wallet-fields').addClass('d-none');
        $('#method-fields input').removeAttr('required');

        if (method === 'bank_transfer') {
            $('#bank-fields').removeClass('d-none');
            $('#bank-fields input').attr('required', 'required');
        } else if (method === 'jazzcash' || method === 'easypaisa') {
            $('#mobile-wallet-fields').removeClass('d-none');
            $('#mobile-wallet-fields input').attr('required', 'required');
        }
    });

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
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Show validation errors
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        toastr.error(errors[field][0]);
                    }
                } else {
                    toastr.error('{{ translate('something_went_wrong') }}: ' + xhr.status);
                }
            }
        });
    });
});
</script>
@endpush
