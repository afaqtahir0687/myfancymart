@extends('layouts.admin.app')

@section('title', translate('reseller_withdraw_requests'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png')}}" alt="">
                {{translate('reseller_withdraw_requests')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row gy-1 align-items-center justify-content-between mb-4">
                    <div class="col-auto">
                        <h3 class="text-capitalize">
                        {{ translate('withdraw_request_table')}}
                            <span class="badge badge-info text-bg-info">{{ $withdrawRequests->total() }}</span>
                        </h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-3">
                            <div class="select-wrapper">
                                <select name="withdraw_status_filter" data-action="{{url()->current()}}" class="form-select min-w-120 withdraw-status-filter">
                                    <option value="all" {{request('status') == 'all' ? 'selected' : ''}}>{{translate('all')}}</option>
                                    <option value="1" {{request('status') == '1' ? 'selected' : ''}}>{{translate('approved')}}</option>
                                    <option value="2" {{request('status') == '2' ? 'selected' : ''}}>{{translate('denied')}}</option>
                                    <option value="0" {{request('status') == '0' ? 'selected' : ''}}>{{translate('pending')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-borderless table-nowrap align-middle">
                        <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('amount')}}</th>
                            <th>{{ translate('reseller_name') }}</th>
                            <th>{{ translate('method_details') }}</th>
                            <th>{{translate('request_time')}}</th>
                            <th class="text-center">{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($withdrawRequests as $key => $withdrawRequest)
                            <tr>
                                <td>{{$withdrawRequests->firstItem() + $key }}</td>
                                <td>
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $withdrawRequest['amount']), currencyCode: getCurrencyCode(type: 'default')) }}
                                </td>

                                <td>
                                    @if (isset($withdrawRequest->user))
                                        <a href="{{route('admin.customer.view', [$withdrawRequest->user_id])}}" class="text-dark text-hover-primary">
                                            {{ $withdrawRequest->user->f_name . ' ' . $withdrawRequest->user->l_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{translate('not_found')}}</span>
                                    @endif
                                </td>
                                <td>
                                    @php($fields = $withdrawRequest->withdrawal_method_fields)
                                    @if($fields)
                                        <div class="small">
                                            <strong>{{ translate('method') }}:</strong> {{ translate($fields['method_name'] ?? 'N/A') }} <br>
                                            @foreach($fields as $fkey => $fvalue)
                                                @if($fkey != 'method_name')
                                                    <strong>{{ translate(str_replace('_', ' ', $fkey)) }}:</strong> {{ $fvalue }} <br>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>{{$withdrawRequest->created_at->format('d M Y, h:i A')}}</td>
                                <td class="text-center">
                                    @if($withdrawRequest->approved == 0)
                                        <label class="badge badge-info">{{translate('pending')}}</label>
                                    @elseif($withdrawRequest->approved == 1)
                                        <label class="badge badge-success">{{translate('approved')}}</label>
                                    @elseif($withdrawRequest->approved == 2)
                                        <label class="badge badge-danger">{{translate('denied')}}</label>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($withdrawRequest->approved == 0)
                                            <button class="btn btn-outline-success btn-sm" onclick="show_modal('approve-{{$withdrawRequest->id}}')">
                                                <i class="fi fi-sr-check"></i> {{translate('approve')}}
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="show_modal('deny-{{$withdrawRequest->id}}')">
                                                <i class="fi fi-sr-cross"></i> {{translate('deny')}}
                                            </button>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approve-{{$withdrawRequest->id}}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{route('admin.customer.reseller-withdraw-status', $withdrawRequest->id)}}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="approved" value="1">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{translate('approve_withdrawal')}}</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>{{translate('note')}}</label>
                                                                    <textarea name="transaction_note" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Deny Modal -->
                                            <div class="modal fade" id="deny-{{$withdrawRequest->id}}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{route('admin.customer.reseller-withdraw-status', $withdrawRequest->id)}}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="approved" value="2">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{translate('deny_withdrawal')}}</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>{{translate('reason')}}</label>
                                                                    <textarea name="transaction_note" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{translate('processed')}}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-center justify-content-end">
                        {{ $withdrawRequests->links() }}
                    </div>
                </div>
                @if(count($withdrawRequests) == 0)
                    @include('layouts.admin.partials._empty-state',['text'=>'no_withdraw_request_found'],['image'=>'default'])
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    function show_modal(id) {
        $('#' + id).modal('show');
    }

    $('.withdraw-status-filter').on('change', function () {
        location.href = $(this).data('action') + '?status=' + $(this).val();
    });
</script>
@endpush
