@extends('layouts.main')
@section('title', 'Job Edit')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Job Edit')}}</h5>

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
                <div class="card ">
                    <div class="card-header">
                        @if ($ticket->is_reopened == 1)
                        <h3>{{ __('label.JOB_ASSIGN_FOR_REOPENED_TICKET')}}</h3>
                        @else
                        <h3>{{ __('Job Update')}}</h3>
                        @endif
                    </div>
                    <div class="card-body">
                        <form class="form-prevent-multiple-submits" method="POST" action="{{ route('job.job.update', $job->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            {{-- <input name="purchase_id" type="hidden" value="{{$ticket->purchase->id}}" class="form-control"> --}}
                            {{-- <input name="ticket_id" type="hidden" value="{{$ticket->id}}" class="form-control"> --}}
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="date" class="">Date</label>
                                        <input name="date" type="date" value="{{ $job->date->toDateString() }}" class="form-control">
                                        @if ($errors->has('date'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="sl">{{ __('label.TICKET_SL')}}<span class="text-red">*</span></label>
                                        <input name="sl_number" id="sl_number" placeholder="Ticket Serial Number" type="text" class="form-control" value="TSL-{{ $ticket->id }}" readonly>
                                        @if ($errors->has('sl_number'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('sl_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="job_number">{{ __('label.JOB_NUMBER')}}<span class="text-red">*</span></label>
                                        <input name="job_number" id="job_number" placeholder="Ticket Serial Number" type="text" class="form-control" value="JSL-{{ $job->id }}" readonly>
                                        @if ($errors->has('job_number'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('job_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="customer_id">{{ __('label.CUSTOMER')}}<span class="text-red">*</span></label>
                                        <input name="customer_id"  type="hidden" class="form-control" value="{{ old('customer_id', optional($ticket)->purchase->customer->id) }}" readonly>
                                        <input name="customer"  placeholder={{ __('label.CUSTOMER')}} type="text" class="form-control" value="{{ old('customer', optional($ticket)->purchase->customer->name) }}" readonly>
                                        @if ($errors->has('customer_id'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('customer_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="phone">{{ __('label.PHONE')}}<span class="text-red">*</span></label>
                                        <input name="phone" id="" type="text" class="form-control" value="{{ old('phone', optional($ticket)->purchase->customer->mobile) }}" readonly>
                                        @if ($errors->has('phone'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="phone">{{ __('label.PRODUCT_SERIAL')}}<span class="text-red">*</span></label>
                                        <input name="phone" id="" type="text" class="form-control" value="{{ old('phone', optional($ticket)->purchase->product_serial) }}" readonly>
                                        @if ($errors->has('phone'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                             <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="product_catagory">{{ __('label.PRODUCT')}}<span class="text-red">*</span></label>
                                        <input name="product_category_id"  placeholder="" type="text" class="form-control" value="{{ old('product_category_id', optional($ticket)->purchase->category->name) }}" readonly>
                                        @if ($errors->has('product_category_id'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('product_category_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                        <input name="brand_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_id', optional($ticket)->purchase->brand->name) }}" readonly>
                                        @if ($errors->has('brand_id'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('brand_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="brand_model_id">{{ __('label.MODEL_NUMBER')}}<span class="text-red">*</span></label>
                                        <input name="brand_model_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_model_id', optional($ticket)->purchase->modelname->model_name) }}" readonly>
                                        @if ($errors->has('brand_model_id'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('brand_model_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="employee_id">{{ __('label.ASSIGN_TO_TECHNICIAN')}}<span class="text-red">*</span></label>
                                        <select name="employee_id" id="employee_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}"
                                                    @if ($employee->id == $job->employee_id)
                                                        selected
                                                    @endif
                                                >{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employee_id'))
                                            <span class="text-danger"
                                                <strong>{{ $errors->first('employee_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="note">{{ __('label.NOTE')}}<span class="text-red">*</span></label>
                                        <input name="note"  placeholder="" type="text" class="form-control" value="{{ $job->note }}">
                                        @if ($errors->has('note'))
                                            <span class="itext-danger">
                                                <strong>{{ $errors->first('note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button class="mt-2 btn btn-primary button-prevent-multiple-submits">
                                <i class="spinner fa fa-spinner fa-spin"></i>
                                Assign to Technician</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush
@endsection
