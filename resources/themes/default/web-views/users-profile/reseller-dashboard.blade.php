@extends('layouts.front-end.app')

@section('title', translate('reseller_dashboard'))

@section('content')
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row {{Session::get('direction') === "rtl" ? '__dir-rtl' : ''}}">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">{{ translate('reseller_dashboard') }}</h3>
                        </div>
                        <div class="card-body">
                            @if(session()->has('success_message'))
                                <div class="alert alert-success">
                                    {{ session()->get('success_message') }}
                                </div>
                                @php
                                    session()->forget('success_message');
                                @endphp
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ translate('total_sales') }}</h4>
                                            <h2 class="text-primary">{{ getCurrencySymbol(type: 'web') }}0</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>{{ translate('total_profit') }}</h4>
                                            <h2 class="text-success">{{ getCurrencySymbol(type: 'web') }}0</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h4>{{ translate('your_resell_products') }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('product_name') }}</th>
                                                <th>{{ translate('resell_price') }}</th>
                                                <th>{{ translate('commission') }}</th>
                                                <th>{{ translate('profit') }}</th>
                                                <th>{{ translate('status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($customer->resellProducts) && count($customer->resellProducts) > 0)
                                                @foreach($customer->resellProducts as $resellProduct)
                                                    <tr>
                                                        <td>{{ $resellProduct->product_name ?? 'N/A' }}</td>
                                                        <td>{{ getCurrencySymbol(type: 'web') }}{{ number_format($resellProduct->resell_price, 2) }}</td>
                                                        <td>{{ $resellProduct->commission_rate }}%</td>
                                                        <td>{{ getCurrencySymbol(type: 'web') }}{{ number_format($resellProduct->profit, 2) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $resellProduct->status == 'active' ? 'success' : 'secondary' }}">
                                                                {{ $resellProduct->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        {{ translate('no_resell_products_found') }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
