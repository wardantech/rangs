@extends('layouts.main')
@section('title', 'Job Create')
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
                            <h5>{{ __('label.JOB_ASSIGN')}}</h5>

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
                        <h3>{{ __('label.JOB_ASSIGN')}}</h3>
                        @endif
                    </div>
                    <div class="card-body">
                        <form class="form-prevent-multiple-submits" method="POST" action="{{ route('job.job.store') }}">
                            @csrf
                            <input name="purchase_id" type="hidden" value="{{$ticket->purchase->id}}" class="form-control">
                            <input name="ticket_id" type="hidden" value="{{$ticket->id}}" class="form-control">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="date" class="">Date</label>
                                        <input name="date"  value="{{ currentDate() }}" type="date" class="form-control">
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
                                        <input name="sl_number" id="sl_number" placeholder="Ticket Serial Number" type="text" class="form-control" value="TSL-{{ old('sl_number', optional($ticket)->id) }}" readonly>
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
                                        <input name="job_number" id="job_number" placeholder="Ticket Serial Number" type="text" class="form-control" value="{{$job_number}}" readonly>
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
                                        <label for="technician_type">Technician Type<span class="text-red">*</span></label>
                                        <select name="technician_type" id="technician_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1">Own Technician</option>
                                            <option value="2">Vendor Technician</option>
                                        </select>
                                        @if ($errors->has('technician_type'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('technician_type') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="employee">{{ __('label.ASSIGN_TO_TECHNICIAN')}}<span class="text-red">*</span></label>
                                        <select name="employee_id" id="employee" class="form-control select2">
                                            <option value="">Select</option>
                                            {{-- @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach --}}
                                        </select>
                                        @if ($errors->has('employee_id'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('employee_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label for="note">{{ __('label.NOTE')}}<span class="text-red">*</span></label>
                                        {{-- <input name="note"  placeholder="" type="text" class="form-control" value="{{ old('note') }}"> --}}
                                        <textarea name="note" id="" cols="30" rows="" style="width:100%">{{ old('note') }}</textarea>
                                        @if ($errors->has('note'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="summery">
                                <div class="card-header">
                                    <h3>Technician's Job Summery</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table" id="status_table">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Status</th>
                                                <th>Qnty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button class="mt-2 btn btn-primary button-prevent-multiple-submits">
                                <i class="spinner fa fa-spinner fa-spin"></i>
                                Assign to Technician</button>
                        </form>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-12">
                <div class="card ">
                    <div class="card-header">
                        <h3>Summerty</h3>
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush
    <script type="text/javascript">
            $('#technician_type').on('change', function(){
                var technician_type=$(this).val();
                var employee = {{ old('employee') }}
                $('#employee').find('option').not(':first').remove();
                if(technician_type){
                    $.ajax({
                        url: "{{url('hrm/technicians/')}}/"+technician_type,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            $.each(data, function(key, value){
                                var option = "<option value='"+value.user_id+"'>"+value.name+"</option>";
                                // var option = "<option value='"+value.id+"'>"+value.name+"</option>";
                                $("#employee").append(option);
                            });
                            if (employee) {
                                $('#employee').val(employee).prop('selected', true);
                            }
                        }
                    });
                }
            });
            $('#summery').hide()
            $('#employee').on('change', function(){
                var employee=$(this).val();
               // console.log(employee);
              //  alert(employee);
                $('#summery').show()
                if(employee){
                    // alert(technician_type);
                    $('#status_table').find('tr').not(':first').remove();
                    $.ajax({
                        url: "{{url('technician/jobstatus/')}}/"+employee,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            $('#summery').show();
                            $.each(data, function(key, value){
                                var index='1';
                                var sta='';
                                if(value.status == 0){
                                    var sta='Created'
                                }else if(value.status == 1){
                                    var sta='Accepted'
                                }else if(value.status == 2){
                                    var sta='Rejected'
                                }else if(value.status == 3){
                                    var sta='Started'
                                }else if(value.status == 5){
                                    var sta='Pending'
                                }else if(value.status == 6){
                                    var sta='Paused'
                                }else{
                                    var sta='Completed'
                                }
                                var trtd='<tr><td>'+ ++key +'</td><td>'+sta+'</td><td>'+value.count+'</td></tr>';
                                $('#status_table').append(trtd);
                            });
                        }
                    });
                }
            });
    </script>
@endsection
