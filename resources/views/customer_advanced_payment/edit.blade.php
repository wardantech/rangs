@extends('layouts.main')
@section('title', 'Customer Advance Payment Info')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{__('label.CUSTOMER ADVANCE PAYMENT INFO')}}</h5>
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
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-body">
                            {{Form::open(['route'=>array('customer-advanced-payment.update', $customerAdvancePayment->id), 'method'=>'PUT', "class"=>"form-horizontal form-prevent-multiple-submits"])}}
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.ADV_MR_NO')}}</label>
                                    <div>
                                        <input type="text" class="form-control" id="adv_mr_no" name="adv_mr_no" value="{{$customerAdvancePayment->adv_mr_no}}" placeholder="Adv. MR No">
                                        @error('adv_mr_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.ADVANCE RECEIPT DATE')}}</label>
                                    <div>
                                        <input type="date" class="form-control" id="advance_receipt_date" name="advance_receipt_date" value="{{$customerAdvancePayment->advance_receipt_date}}" placeholder="Date Name">
                                        @error('advance_receipt_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-2 mb-3"/>
                            <div><h6><strong>Job Info:</strong></h6></div>
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.JOB NO')}}</label>
                                    <div>
                                        <input type="hidden" class="form-control" id="job_id" name="job_id" value="{{$customerAdvancePayment->job_id}}" placeholder="Job No">
                                        <input type="text" class="form-control" id="job_no" name="" value="JSL-{{$customerAdvancePayment->job->id}}" placeholder="Job No">
                                        @error('job_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.BRANCH')}}</label>
                                    <div>
                                        <select name="branch_id" id="branch" class="form-control">
                                            <option value="">Select a branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" {{$branch->id == $customerAdvancePayment->branch_id ? 'selected' : ''}}>{{$branch->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="customer_name">{{__('label.CUSTOMER NAME')}}</label>
                                    <div>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{$customerAdvancePayment->customer_name}}" placeholder="Customer Name">
                                        @error('customer_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="customer_phone">{{__('label.CUSTOMER MOBILE')}}</label>
                                    <div>
                                        <input type="number" class="form-control" id="customer_phone" name="customer_phone" value="{{$customerAdvancePayment->customer_phone}}" placeholder="Customer Phone">
                                        @error('customer_phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="customer_address">{{__('label.CUSTOMER ADDRESS')}}</label>
                                    <div>
                                        <textarea name="customer_address" id="customer_address" class="form-control" cols="12" rows="1">{{$customerAdvancePayment->customer_address}}</textarea>
                                        @error('customer_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="receive_date">{{__('label.RECEIVE DATE')}}</label>
                                    <div>
                                        <input type="date" class="form-control" id="receive_date" name="receive_date" value="{{$customerAdvancePayment->receive_date}}">
                                        @error('receive_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="product_name">{{__('label.PRODUCT NAME')}}</label>
                                    <div>
                                        <input type="text" class="form-control" id="product_name" name="product_name" value="{{$customerAdvancePayment->product_name}}" placeholder="product name">
                                        @error('product_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="product_sl">{{__('label.PRODUCT SL')}}</label>
                                    <div>
                                        <input type="text" name="product_sl" id="product_sl" class="form-control" value="{{$customerAdvancePayment->product_sl}}">
                                        @error('product_sl')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-2 mb-3"/>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="advance_amount">{{__('label.ADVANCE AMOUNT')}}</label>
                                    <div>
                                        <input type="number" class="form-control" id="advance_amount" name="advance_amount" value="{{$customerAdvancePayment->advance_amount}}" placeholder="Advance Amount">
                                        @error('advance_amount')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="pay_type">{{__('label.PAY TYPE')}}</label>
                                    <div>
                                        <select name="pay_type" id="pay_type" class="form-control">
                                            <option value="">Select pay type</option>
                                            <option value="1" {{$customerAdvancePayment->pay_type == 1 ? 'selected' : ''}}>Cash</option>
                                        </select>
                                        @error('pay_type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-2 mb-3"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="parts">{{ __('label.REMARK')}}</label>
                                    <textarea name="remark" id="remark" class="form-control" cols="30" rows="2">{{$customerAdvancePayment->remark}}</textarea>
                                    @error('remark')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush


    <script type="text/javascript">
    $(document).ready(function(){

        });
    </script>
@endsection
