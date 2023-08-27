@extends('layouts.main')
@section('title', 'Update Ticket')
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
                            <h5>{{ __('label.UPDATE_TICKET')}}</h5>

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
                        <h3>{{ __('label.UPDATE_TICKET')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('update-ticket', $ticket->id) }}" method="POST">
                            @csrf
                            <input name="purchase_id" type="hidden" value="{{$purchase->id}}">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="date" class="">Date</label>
                                        <input name="date"  placeholder="Date" type="date" class="form-control" value="{{ $ticket->date->toDateString() }}">
                                        @if ($errors->has('date'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="sl">{{ __('label.SL')}}<span class="text-red">*</span></label>
                                        <input name="sl_number" id="sl_number" placeholder="Ticket Serial Number" type="text" class="form-control" value="TSL{{ $ticket->id }}" readonly>
                                        @if ($errors->has('sl_number'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('sl_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="product_catagory">{{ __('label.PRODUCT')}}<span class="text-red">*</span></label>
                                        <input name="product_category_id"  placeholder="" type="hidden" class="form-control" value="{{ old('product_category_id', optional($purchase)->category->id) }}" readonly>
                                        <input name="product_category"  placeholder="" type="text" class="form-control" value="{{ old('product_category_id', optional($purchase)->category->name) }}" readonly>
                                        @if ($errors->has('product_category_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('product_category_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                        <input name="brand_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_id', optional($purchase)->brand->name) }}" readonly>
                                        @if ($errors->has('brand_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('brand_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="brand_model_id">{{ __('label.MODEL_NUMBER')}}<span class="text-red">*</span></label>
                                        <input name="brand_model_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_model_id', optional($purchase)->modelname->model_name) }}" readonly>
                                        @if ($errors->has('brand_model_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('brand_model_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="customer_id">{{ __('label.CUSTOMER')}}<span class="text-red">*</span></label>
                                        <input name="customer_id"  type="hidden" class="form-control" value="{{ old('customer_id', optional($purchase)->customer->id) }}" readonly>
                                        <input name="customer"  placeholder={{ __('label.CUSTOMER')}} type="text" class="form-control" value="{{ old('customer', optional($purchase)->customer->name) }}" readonly>
                                        @if ($errors->has('customer_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('customer_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="phone">{{ __('label.PHONE')}}<span class="text-red">*</span></label>
                                        <input name="phone" id="" type="text" class="form-control" value="{{ old('phone', optional($purchase)->customer->mobile) }}" readonly>
                                        @if ($errors->has('phone'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="address">{{ __('label.ADDRESS')}}<span class="text-red">*</span></label>
                                        <input name="address"  placeholder={{ __('label.ADDRESS')}} type="text" class="form-control" value="{{ old('address', optional($purchase)->customer->address) }}">
                                        @if ($errors->has('address'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="district_id">{{ __('label.DISTRICT')}}<span class="text-red">*</span></label>
                                        <select name="district_id" id="district" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($districts as $district)
                                                <option value="{{$district->id}}"
                                                        @if ($district->id == $ticket->district_id)
                                                            selected
                                                        @endif
                                                    >
                                                    {{$district->name}}
                                                </option>
                                            @endforeach

                                        </select>
                                        @if ($errors->has('district_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('district_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="thana">{{ __('label.SELECT_THANA')}}<span class="text-red">*</span></label>
                                        <select name="thana_id" id="thana" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($thanas as $thana)
                                                <option value="{{ $thana->id }}"
                                                    @if ($thana->id == $ticket->thana_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $thana->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('thana_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('thana_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label for="fault_description_id">{{ __('label.FAULT_DESCRIPTION')}}</label>
                                        <div class="border-checkbox-section">
                                            @if(!empty($faults))
                                            @foreach($faults as $fId=>$fault)
                                            <div class="border-checkbox-group border-checkbox-group-success">
                                                <input class="border-checkbox" type="checkbox" name="fault_description_id[]" id="checkbox_fault_description{{$fId}}" value="{{$fId}}"
                                                    @foreach (json_decode($ticket->fault_description_id) as $fault_id)
                                                        @if ($fId == $fault_id)
                                                            checked
                                                        @endif
                                                    @endforeach
                                                >
                                                <label class="border-checkbox-label" for="checkbox_fault_description{{$fId}}">{{$fault}}</label>
                                            </div>
                                            @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="customer_id">{{ __('label.CARRIER_INFO')}}</label>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="customer_id">{{ __('label.CARRIER_NAME')}}</label>
                                        <input name="customer_id"  type="hidden" class="form-control" value="{{ old('customer_id', optional($purchase)->customer->id) }}">
                                        <input name="customer"  placeholder={{ __('label.CARRIER_NAME')}} type="text" class="form-control" value="{{ old('customer') }}">
                                        @if ($errors->has('customer_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('customer_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="phone">{{ __('label.CARRIER_PHONE')}}</label>
                                        <input name="phone" id="" type="text" class="form-control" value="{{ old('phone') }}" placeholder={{ __('label.CARRIER_PHONE')}}>
                                        @if ($errors->has('phone'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <div class="border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-success">
                                                <input class="border-checkbox" type="checkbox" name="carrier_own" id="carrier_own" value="1">
                                                <label class="border-checkbox-label" for="carrier_own">
                                                    Is Carrier Own The Product
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="warranty_type_id">{{ __('label.WARRANTY_TYPE')}}<span class="text-red">*</span></label>
                                        <select name="warranty_type_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            @forelse ($warrantyTypes as $item)
                                                <option value="{{$item->id}}"
                                                    @if ($item->id == $ticket->warranty_type_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$item->warranty_type}}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse

                                        </select>
                                        @if ($errors->has('warranty_type_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('warranty_type_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="job_priority_id">{{ __('label.JOB_PRIORITY')}}<span class="text-red">*</span></label>
                                        <select name="job_priority_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            @forelse ($job_priorities as $item)
                                                <option value="{{$item->id}}"
                                                    @if ($item->id == $ticket->job_priority_id )
                                                        selected
                                                    @endif
                                                >
                                                    {{$item->job_priority}}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse

                                        </select>
                                        @if ($errors->has('job_priority_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('job_priority_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="service_type_id">{{ __('label.SERVICE_TYPE')}}<span class="text-red">*</span></label>
                                    <select name="service_type_id[]" id="service_type_id" class="form-control select2" multiple>
                                        <option value="">Select</option>
                                        <?php $selectedServiceTypeIds= json_decode($ticket->service_type_id)?>
                                        @forelse ($serviceTypes as $serviceType)
                                        <option value="{{$serviceType->id}}"
                                            @if(in_array($serviceType->id, $selectedServiceTypeIds))
                                                selected
                                            @endif
                                        >{{$serviceType->service_type}}</option>
                                        @empty
                                        <option value="">No Data Found</option>
                                        @endforelse
                                    </select>
                                    @error('service_type_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="address">{{ __('label.EXPECTED_SCHEDULE')}}</label>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="start_date">{{ __('label.START_DATE')}}<span class="text-red">*</span></label>
                                        <input name="start_date" type="date" class="form-control" value="{{ $ticket->start_date->toDateString() }}">
                                        @if ($errors->has('start_date'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('start_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="end_date">{{ __('label.END_DATE')}}<span class="text-red">*</span></label>
                                        <input name="end_date" type="date" class="form-control" value="{{ $ticket->end_date->toDateString() }}">
                                        @if ($errors->has('end_date'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('end_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="customer_note">{{ __('label.CUSTOMER_NOTE')}}</label>
                                        <input name="customer_note" type="text" class="form-control" value="{{ $ticket->customer_note }}">
                                        @if ($errors->has('customer_note'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('customer_note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="product_receive_mode_id">{{ __('label.PRODUCT_RECEIVE_MODE')}}<span class="text-red">*</span></label>
                                        <select name="product_receive_mode_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1"
                                                @if ($ticket->product_receive_mode_id == 1)
                                                    selected
                                                @endif
                                            >
                                                Outet
                                            </option>
                                            <option value="2"
                                                @if ($ticket->product_receive_mode_id == 2)
                                                    selected
                                                @endif
                                            >
                                                Service Center
                                            </option>
                                            <option value="3"
                                                @if ($ticket->product_receive_mode_id == 3)
                                                    selected
                                                @endif
                                            >
                                                Customer Home
                                            </option>

                                        </select>
                                        @if ($errors->has('product_receive_mode_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('product_receive_mode_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="expected_delivery_mode_id">{{ __('label.EXPECTED_DELIVERY_MODE')}}<span class="text-red">*</span></label>
                                        <select name="expected_delivery_mode_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            <option value="1"
                                                @if ($ticket->expected_delivery_mode_id == 1)
                                                    selected
                                                @endif
                                            >
                                                Outet
                                            </option>
                                            <option value="2"
                                                @if ($ticket->expected_delivery_mode_id == 2)
                                                    selected
                                                @endif
                                            >
                                                Service Center
                                            </option>
                                            <option value="3"
                                                @if ($ticket->expected_delivery_mode_id == 3)
                                                    selected
                                                @endif
                                            >
                                                Home Delivery
                                            </option>
                                        </select>
                                        @if ($errors->has('expected_delivery_mode_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('expected_delivery_mode_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="service_charge">{{ __('label.SERVICE_CHARGE')}}<span class="text-red">*</span></label>
                                    <input name="service_charge" type="text" class="form-control" id="service_charge" value="{{$ticket->service_charge}}" readonly>
                                    @error('service_charge')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <label for="product_condition_id">{{ __('label.PRODUCT_CONDITION')}}</label>
                                    <div class="border-checkbox-section">
                                        @if(!empty($product_conditions))
                                            @foreach($product_conditions as $product_condition)
                                            <div class="border-checkbox-group border-checkbox-group-success">
                                                <input class="border-checkbox" type="checkbox" name="product_condition_id[]" id="checkbox_product_condition{{$product_condition->id}}" value="{{$product_condition->id}}"
                                                    @foreach (json_decode($ticket->product_condition_id) as $condition)
                                                        @if ($condition == $product_condition->id)
                                                             checked
                                                        @endif
                                                    @endforeach
                                                >
                                                <label class="border-checkbox-label" for="checkbox_product_condition{{$product_condition->id}}">{{$product_condition->product_condition}}</label>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="accessories_list_id">{{ __('label.ACCESSORIES_LIST')}}</label>
                                    <div class="border-checkbox-section">
                                        @if(!empty($accessories_list))
                                            @foreach($accessories_list as $accessories_item)
                                            <div class="border-checkbox-group border-checkbox-group-success">
                                                <input class="border-checkbox" type="checkbox" name="accessories_list_id[]" id="checkbox_accessories_item{{$accessories_item->id}}" value="{{$accessories_item->id}}"
                                                    @foreach (json_decode($ticket->accessories_list_id) as $accessories)
                                                        @if ($accessories == $accessories_item->id)
                                                            checked
                                                        @endif
                                                    @endforeach
                                                >
                                                <label class="border-checkbox-label" for="checkbox_accessories_item{{$accessories_item->id}}">{{$accessories_item->accessories_name}}</label>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button class="mt-2 btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){

            $(".integer-decimal-only").each(function () {
                $(this).keypress(function (e) {
                    var code = e.charCode;

                    if (((code >= 48) && (code <= 57)) || code == 0 || code == 46) {
                        return true;
                    } else {
                        return false;
                    }
                });
            });

            $('#district').on('change', function(){
                var district_id=$(this).val();
                if(district_id){
                    $.ajax({
                        url: "{{url('general/get/thana/')}}/"+district_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            $('#thana').empty();
                            $.each(data, function(key, value){
                                $('#thana').append("<option value="+value.id+">"+value.name+"</option>");
                            });
                        }
                    });
                }
            });


            $('#add-store').on('submit', function(e){
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url : "/newstore",
                    data: $('#add-store').serialize(),
                    //processData: false,
                    dataType: 'json',
                    //contentType: false,
                    //beforeSend: function(){},
                    success: function(response){
                        console.log(response);
                        alert("Data saved successfully.");
                    }
                    //error: alert("Data can not be saved.")
                });
            });

            $('#service_type_id').on('change', function(){
                var service_type_id=$(this).val();
                // console.log(service_type_id);
                if(service_type_id){
                    $.ajax({
                        url: "{{url('tickets/get/service/amount/')}}/",
                        type: 'GET',
                        data: {
                            id: service_type_id,
                        },
                        dataType: "json",
                        success: function(data){
                            $('#service_charge').empty();
                            $('#service_charge').val(data);
                        }
                    });
                }
            });

        });
    </script>
@endsection
