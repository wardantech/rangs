@extends('layouts.main')
@section('title', 'Parts Sell Details')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.PARTS_SELL')}}</h5>
                            <span>{{ __('label.PARTS_SELL_DETAILS')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}" class="btn btn-outline-success" title="Home"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-danger" title="Go Back"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row print">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.PARTS_SELL_DETAILS')</h3>
                        <div class="card-header-right">
                            <button id="print" class="btn btn-info">Print</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th><strong>{{trans('label.DATE')}}</strong></th>
                                <td>{{$partSell->date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.MR_NO')}}</strong></th>
                                <td>{{$partSell->mr_no}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.SALES_BY')}}</strong></th>
                                <td>{{$partSell->sales_by}}</td>
                            </tr>
                            @if (isset($partSell->store_id))
                            <tr>
                                <th><strong>{{trans('label.STORE')}}</strong></th>
                                <td>{{$partSell->store->name ?? null }}</td>
                            </tr>
                            @endif
                            @if($partSell->customer_id)
                            <tr>
                                <th><strong>{{trans('label.CUSTOMER_NAME')}}</strong></th>
                                <td>{{$partSell->customer->name ?? null}}</td>
                            </tr>
                            @endif
                            @if($partSell->customer_id)
                            <tr>
                                <th><strong>{{trans('label.CUSTOMER_MOBILE')}}</strong></th>
                                <td>{{$partSell->customer->mobile ?? null }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th><strong>{{trans('label.CUSTOMER_ADDRESS')}}</strong></th>
                                <td>@isset($partSell->customer_id)
                                    {{$partSell->customer->address ?? null}}
                                @endisset

                                </td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.SPARE_PARTS_AMOUNT')}}</strong></th>
                                <td>{{$partSell->spare_parts_amount}} BDT.</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.DISCOUNT')}}</strong></th>
                                <td>{{$partSell->discount}} BDT.</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.NET AMOUNT')}}</strong></th>
                                <td>{{$partSell->net_amount}} BDT.</td>
                            </tr>
                        </table>
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART')}}</th>
                                    <th>{{ __('label.QUANTITY')}}</th>
                                    <th>{{ __('label.SELLING_PRICE')}}</th>
                                    <th>{{ __('label.AMOUNT')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$partSellDetails->isEmpty())
                                    <?php
                                        $sl = 0;
                                        $total=0;
                                    ?>
                                    @foreach($partSellDetails as $partSellDetail)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{ $partSellDetail->part->code }}-{{$partSellDetail->part->name}}</td>
                                        <td>{{$partSellDetail->quantity ? $partSellDetail->quantity:''}}</td>
                                        <td>
                                            @isset($partSellDetail->selling_price)
                                                {{ $partSellDetail->selling_price }}
                                            @endisset
                                        </td>

                                        <td>
                                            {{$partSellDetail->amount}}
                                            @php
                                                $total+=$partSellDetail->amount;
                                            @endphp
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"><strong>Total Amount</strong></td>
                                    <td><strong>{{ number_format($total,2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/print.js') }}"></script>
    @endpush
@endsection
