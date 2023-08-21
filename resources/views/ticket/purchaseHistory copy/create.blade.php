@extends('layouts.main') 
@section('title', 'Dashboard')
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
                            <h5>{{ __('label.CREATE_NEW_TICKET')}}</h5>
                        
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
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
                        <h3>{{ __('label.CREATE_NEW_TICKET')}}</h3>
                    </div>
                    <div class="card-body">
                
                    {{ Form::open(array('route' => 'create-inventory', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                        @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="sl">{{ __('label.SL')}}<span class="text-red">*</span></label>
                                        {{ Form::text('sl', Request::old('sl'), array('id'=> 'sl', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>
    
                                        @error('sl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  
                                    <div class="form-group">
                                        <label for="product_catagory">{{ __('label.SELECT_PRODUCT')}}<span class="text-red">*</span></label>
                                        {!! Form::select('product_catagory', $productCategorys, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_PRODUCT_OPT'),'id'=> 'product_category']) !!}
                                        @error('product_catagory')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="model_number">{{ __('label.MODEL_NUMBER')}}<span class="text-red">*</span></label>
                                        {{ Form::text('model_number', null, array('id'=> 'model_number', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>
    
                                        @error('sl')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  
                                    <div class="form-group">
                                        <label for="customer_name">{{ __('label.CUSTOMER_NAME')}}<span class="text-red">*</span></label>
                                        {{ Form::text('customer_name', null, array('id'=> 'customer_name', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>
    
                                        @error('customer_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="address">{{ __('label.ADDRESS')}}<span class="text-red">*</span></label>
                                        {{ Form::text('address', null, array('id'=> 'address', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>
    
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  
                                    <div class="form-group">
                                        <label for="thana">{{ __('label.SELECT_THANA')}}<span class="text-red">*</span></label>
                                        {!! Form::select('thana_id', $thanas, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_THANA_OPT'),'id'=> 'thana']) !!}
                                        @error('thana_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="thana">{{ __('label.FAULT_DESCRIPTION')}}</label>
                                        <div class="border-checkbox-section">
                                            @if(!empty($faults))
                                            @foreach($faults as $fId=>$fault)
                                            <div class="border-checkbox-group border-checkbox-group-success">
                                                <input class="border-checkbox" type="checkbox" id="checkbox{{$fId}}" value="{{$fId}}">
                                                <label class="border-checkbox-label" for="checkbox{{$fId}}">{{$fault}}</label>
                                            </div>
                                            @endforeach
                                            @endif

                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="product_code">{{ __('label.PRODUCT_CODE')}}<span class="text-red">*</span></label>
                                        {{ Form::text('product_code', null, array('id'=> 'product_code', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>

                                        @error('product_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  
                                    <div class="form-group">
                                        <label for="brand">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                        {{ Form::text('brand_id', null, array('id'=> 'brand', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>

                                        @error('product_code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  
                                    <div class="form-group">
                                        <label for="warranty_type">{{ __('label.WARRANTY_TYPE')}}<span class="text-red">*</span></label>
                                        {!! Form::select('warranty_type', $warrantyTypes, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.WARRANTY_TYPE_OPT'),'id'=> 'warranty_type']) !!}
                                        @error('warranty_type')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="mobail_number">{{ __('label.MODILE_NUMBER')}}<span class="text-red">*</span></label>
                                        {{ Form::text('mobail_number', null, array('id'=> 'mobail_number', 'class' => 'form-control', 'placeholder' => '')) }}
                                        <div class="help-block with-errors" ></div>
                                        @error('mobail_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  

                                    <div class="form-group">
                                        <label for="district_id">{{ __('label.DISTRICT')}}<span class="text-red">*</span></label>
                                        {!! Form::select('district_id', $districts, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_DISTRICT_OPT'),'id'=> 'district_id']) !!}
                                        @error('district_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="">&nbsp;<span class="text-red"></span></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="">&nbsp;<span class="text-red"></span></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="service_type">{{ __('label.SERVICE_TYPE')}}</label>
                                        <div class="form-radio">
                                            @if(!empty($faults))
                                                @foreach($faults as $fId=>$fault)
                                                <div class="radio radiofill radio-success radio-inline">
                                                    <label>
                                                        <input type="radio" name="radio"  value="{{$fId}}">
                                                        <i class="helper"></i>{{$fault}}
                                                    </label>
                                                </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                        <a href="{!! URL::to('tickets/purchase-history') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                      
                                    </div>

                                

                                </div>
                            </div>
                        
                            {!! Form::close() !!}
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

            
            
        });
    </script>
@endsection