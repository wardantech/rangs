@extends('layouts.main')
@section('title', 'Details Parts Transfer ')
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
                            <h5>{{ __('Parts Transfer Details')}}</h5>
                            <span>{{ __('Parts Transfer Details')}}</span>
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
                        <h3>@lang('Details')</h3>
                        <div class="card-header-right">
                           {{-- <a href="{{URL::to('inventory/parts-return')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a> --}}
                           {{-- <a href="{{route('inventory.central.returnReceive',$partsreturn->id)}}">  <i class="fa fa-check-square f-16 mr-15 text-green" aria-hidden="true">Receive</i></a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th><strong>{{trans('label.DATE')}}</strong></th>
                                <td>{{$partsreturn->date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.TRANSFER_SL')}}</strong></th>
                                <td>B-RSL-{{$partsreturn->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.SENT_FROM')}}</strong></th>
                                <td>{{$partsreturn->senderStore->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.TO_STORE')}}</strong></th>
                                <td>{{$partsreturn->toStore->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.TOTAL_RETURN_REQUEST_QUANTITY')}}</strong></th>
                                <td>{{$partsreturn->total_quantity}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.TOTAL_RECEIVED_QUANTITY')}}</strong></th>
                                @if($partsreturn->total_receiving_quantity)
                                <td>{{$partsreturn->total_receiving_quantity}}</td>
                                @else
                                <td>-</td>
                                @endif
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.CREATED_BY')}}</strong></th>
                                <td>{{$partsreturn->createdBy->name}}</td>
                            </tr>
                            @if ($partsreturn->description)
                            <tr>
                                <th><strong>{{trans('label.DESCRIPTION')}}</strong></th>
                                <td>{{$partsreturn->description ?? null}}</td>
                            </tr>
                            @endif
                        </table>
                        <table id="table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Parts Name')}}</th>
                                    <th>{{ __('Requested Returned Quantity')}}</th>
                                    <th>{{ __('Received Quantity')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($partsReturnDetails as $partsreturnDetail)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$partsreturnDetail->part->code}}-{{$partsreturnDetail->part->name}}</td>
                                <td>{{$partsreturnDetail->required_quantity}}</td>
                                <td>{{$partsreturnDetail->received_quantity}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
