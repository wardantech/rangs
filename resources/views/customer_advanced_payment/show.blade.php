@extends('layouts.main')
@section('title', 'Advanced Payment Details ')
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
                        <i class="fas fa-user-md bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Advanced Payment Details')}}</h5>
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
                        <div class="card-header-left">
                        </div>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Advanced Payment --}}
                        @isset($customerAdvancedPayment)
                            <table id="datatable" class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th>{{ __('label.ADV_MR_NO')}}</th>
                                        <td>{{$customerAdvancedPayment->adv_mr_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.ADVANCE RECEIPT DATE')}}</th>
                                        <td>
                                            {{\Carbon\Carbon::parse($customerAdvancedPayment->advance_receipt_date)->format('m/d/Y')}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.JOB NO')}}</th>
                                        <td>JSL-{{$customerAdvancedPayment->job->id}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.BRANCH')}}</th>
                                        <td>{{$customerAdvancedPayment->branch->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.CUSTOMER NAME')}}</th>
                                        <td>{{$customerAdvancedPayment->customer_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.CUSTOMER MOBILE')}}</th>
                                        <td>{{$customerAdvancedPayment->customer_phone}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.CUSTOMER ADDRESS')}}</th>
                                        <td>{{$customerAdvancedPayment->customer_address}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.RECEIVE DATE')}}</th>
                                        <td>{{$customerAdvancedPayment->receive_date}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.PRODUCT NAME')}}</th>
                                        <td>{{$customerAdvancedPayment->product_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.PRODUCT SL')}}</th>
                                        <td>{{$customerAdvancedPayment->product_sl}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.ADVANCE AMOUNT')}}</th>
                                        <td>{{$customerAdvancedPayment->advance_amount}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('label.PAY TYPE')}}</th>
                                        <td>
                                            @if($customerAdvancedPayment->pay_type==1)
                                            Cash
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($customerAdvancedPayment->createdBy)
                                    <tr>
                                        <td>{{ __('Created By') }}</td>
                                        <td>{{ $customerAdvancedPayment->createdBy ? $customerAdvancedPayment->createdBy->name : '' }}</td>
                                    </tr>
                                    @endif 
                                    @if ($customerAdvancedPayment->updatedBy)
                                    <tr>
                                        <td>{{ __('Updated By') }}</td>
                                        <td>{{ $customerAdvancedPayment->updatedBy ? $customerAdvancedPayment->updatedBy->name : '' }}</td>
                                    </tr>
                                    @endif 
                                    @if ($customerAdvancedPayment->remark)
                                    <tr>
                                        <td>{{ __('Remark') }}</td>
                                        <td>{{ $customerAdvancedPayment->remark ? $customerAdvancedPayment->remark : '' }}</td>
                                    </tr>
                                    @endif 
                                    <tr>
                                        <td>{{ __('Created At') }}</td>
                                        <td>{{ $customerAdvancedPayment->created_at->format('m/d/yy H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
