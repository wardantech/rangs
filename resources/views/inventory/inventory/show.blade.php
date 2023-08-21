@extends('layouts.main')
@section('title', 'Details Parts Receive ')
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
                            <h5>{{ __('label.PARTS')}}</h5>
                            <span>{{ __('label.PARTS_RECEIVE_DETAILS')}}</span>
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
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.RECEIVED_PARTS')</h3>
                        <div class="card-header-right">
                           {{-- <a href="{{URL::to('inventory/create')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a> --}}
                           <a class="btn btn-success" href="{{ URL::signedRoute('edit-inventory', ['id' => $inventory->id]) }}" title="Edit">
                            <i class='ik ik-edit f-16 mr-15 text-white'></i>
                            Edit
                        </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th><strong>{{trans('label.INVOICE_NUMBER')}}</strong></th>
                                <td>{{$inventory->invoice_number}}</td>
                            </tr>
                            @if (isset($inventory->po_number))
                            <tr>
                                <th><strong>{{trans('label.PO_NUMBER')}}</strong></th>
                                <td>{{$inventory->po_number ? $inventory->po_number : ''}}</td>
                            </tr>
                            @endif
                            @if($inventory->lc_number ? $inventory->lc_number : '')
                            <tr>
                                <th><strong>{{trans('label.LC_NUMBER')}}</strong></th>
                                <td>{{$inventory->lc_number ?? null}}</td>
                            </tr>
                            @endif
                            @if($inventory->source)
                            <tr>
                                <th><strong>{{trans('label.SOURCE')}}</strong></th>
                                <td>{{$inventory->source->name ?? null}}</td>
                            </tr>
                            @endif
                            <tr>
                                <th><strong>{{trans('label.RECEIVE_DATE')}}</strong></th>
                                <td>{{$inventory->receive_date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.VENDOR')}}</strong></th>
                                <td>{{$inventory->productVendor ? $inventory->productVendor->name : ''}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.CREATED_BY')}}</strong></th>
                                <td>{{$inventory->created_by ? $inventory->createdBy->name : ''}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.CREATED_AT')}}</strong></th>
                                <td>{{$inventory->created_at ? $inventory->created_at->format('m/d/yy H:i:s') : ''}}</td>
                            </tr>
                        </table>
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART')}}</th>
                                    <th>{{ __('label.MODEL')}}</th>
                                    <th>{{ __('label.RACK')}}</th>
                                    <th>{{ __('label.BIN')}}</th>
                                    <th>{{ __('label.QUANTITY')}}</th>
                                    <th>{{ __('label.COST_PRICE_BDT')}}</th>
                                    <th>{{ __('label.COST_PRICE_USD')}}</th>
                                    <th>{{ __('label.UNIT_PRICE_BDT')}}</th>
                                    <th>{{ __('label.AMOUNT')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$inventory_details->isEmpty())
                                    <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($inventory_details as $inventory)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{ $inventory->part->code }}-{{$inventory->part->name}}</td>
                                        <td>{{$inventory->part->partModel ? $inventory->part->partModel->name:''}}</td>
                                        <td>
                                            @isset($inventory->rack)
                                                {{ $inventory->rack->name }}
                                            @endisset
                                        </td>
                                        <td>
                                            @isset($inventory->bin)
                                            {{ $inventory->bin->name }}
                                        @endisset
                                        </td>
                                        <td>{{$inventory->stock_in}}</td>
                                        <td>
                                            {{$inventory->cost_price_bdt ?? null}}
                                        </td>
                                        <td>
                                            {{$inventory->cost_price_usd ?? null}}
                                        </td>
                                        <td>
                                            {{$inventory->selling_price_bdt ?? null}}
                                        </td>
                                        <td>
                                            @if (isset($inventory->price->selling_price_bdt))
                                            {{$inventory->price->selling_price_bdt * $inventory->stock_in}}   
                                            @endif
                                            
                                        </td>
                                    </tr>
                                    @endforeach

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
