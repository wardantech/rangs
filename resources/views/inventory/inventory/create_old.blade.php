@extends('layouts.main') 
@section('title', 'Parts Receive')
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
                            <h5>{{ __('label.RECEIVE_PARTS')}}</h5>
                        
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
                        <h3>{{ __('label.RECEIVE_PARTS')}}</h3>
                    </div>
                    <div class="card-body">
                
                    {{ Form::open(array('route' => 'create-inventory', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                        @csrf
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label for="part">{{ __('label.SELECT_PART')}}<span class="text-red">*</span></label>
                                        {!! Form::select('part_id', $parts, null,[ 'class'=>'form-control', 'placeholder' => __('label.SELECT_PART_OPT'),'id'=> 'part']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('part_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="model">{{ __('label.MODEL')}}<span class="text-red">*</span></label>
                                        {{-- {!! Form::select('model_id', $models, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_PART_OPT'),'id'=> 'model']) !!} --}}
                                        <select name="model_id" id="model_id" class="form-control" required>

                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('model_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="invoice">{{ __('label.INVOICE_NUMBER')}}<span class="text-red">*</span></label>
                                        {{ Form::text('invoice_number', Request::old('invoice_number'), array('id'=> 'invoice', 'class' => 'form-control', 'placeholder' => 'Enter Invoice Number ...')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('invoice_number')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>                                 

                                    <div class="form-group">
                                        <label for="sending_date">{{ __('label.SENDING_DATE')}}<span class="text-red">*</span></label>
                                        <input id="sending_date" type="date" class="form-control @error('sending_date') is-invalid @enderror" name="sending_date" value="{{old('sending_date')}}" placeholder="">
                                        <div class="help-block with-errors"></div>

                                        @error('sending_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.ORDER_DATE')}}<span class="text-red">*</span></label>
                                        <input id="order_date" type="date" class="form-control @error('order_date') is-invalid @enderror" name="order_date" value="{{old('order_date')}}" placeholder="">
                                        <div class="help-block with-errors"></div>

                                        @error('order_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="receiveDate">{{ __('label.RECEIVE_DATE')}}<span class="text-red">*</span></label>
                                        <input id="receiveDate" type="date" class="form-control @error('receive_date') is-invalid @enderror" name="receive_date" value="{{old('receive_date')}}" placeholder="" >
                                        <div class="help-block with-errors"></div>

                                        @error('receive_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="vendor_id">{{ __('label.SELECT_VENDOR')}}<span class="text-red">*</span></label>
                                        {!! Form::select('vendor_id', $vendors, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_VENDOR_OPT'),'id'=> 'vendor_id']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('vendor_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    
                                    <div class="form-group">
                                        <label for="store">{{ __('label.SELECT_STORE')}}<span class="text-red">*</span></label>
                                        {!! Form::select('store_id', $stores, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_STORE_OPT'),'id'=> 'store']) !!}
                                        <div class="help-block with-errors"></div>

                                        @error('store_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>    
                                    <div class="form-group">
                                        <label for="rack">{{ __('label.SELECT_RACK')}}<span class="text-red">*</span></label>
                                        {{-- {!! Form::select('rack_id', $racks, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_RACK_OPT'),'id'=> 'rack']) !!} --}}
                                        <select name="rack_id" id="rack" class="form-control" required>

                                        </select>
                                        @error('rack_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="bin">{{ __('label.SELECT_BIN')}}<span class="text-red">*</span></label>
                                        {!! Form::select('bin_id[]', $bins, null,[ 'class'=>'form-control select2', 'multiple' => 'multiple', 'placeholder' => __('label.SELECT_BIN_OPT'),'id'=> 'bin']) !!}
                                        {{-- <select name="bin_id[]" id="bin" class="form-control" multiple="multiple" required>

                                        </select> --}}
                                        @error('bin_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">{{ __('label.QUANTITY')}}<span class="text-red">*</span></label>
                                        {{ Form::text('quantity', Request::old('quantity'), array('id'=> 'quantity', 'class' => 'form-control', 'placeholder' => 'Enter quantity ...')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('quantity')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div id="price-details">
                                        <div class="form-group">
                                            <label for="usd">{{ __('label.USD')}}<span class="text-red">*</span></label>
                                            <input type="hidden" name="price_management_id" id="price_management_id" value="">
                                            {{ Form::text('usd', Request::old('usd'), array('id'=> 'cost_price_usd', 'class' => 'form-control', 'placeholder' => 'Enter USD Value ...')) }}
                                            <div class="help-block with-errors" ></div>
    
                                            @error('usd')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                   
                                        <div class="form-group">
                                            <label for="bdt">{{ __('label.BDT')}}<span class="text-red">*</span></label>
                                            {{ Form::text('bdt', Request::old('bdt'), array('id'=> 'cost_price_bdt', 'class' => 'form-control', 'placeholder' => 'Enter BDT Value ...')) }}
                                            <div class="help-block with-errors" ></div>
                                            @error('bdt')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div> 
                                        <div class="form-group">
                                            <label for="selling_price">{{ __('label.SELLING_PRICE')}}<span class="text-red">*</span></label>
                                            {{ Form::text('selling_price', Request::old('selling_price'), array('id'=> 'selling_price_bdt', 'class' => 'form-control', 'placeholder' => 'Enter Selling Price...')) }}
                                            <div class="help-block with-errors" ></div>
                                            @error('selling_price')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                      
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


            $('#part').on('change', function(e){
                e.preventDefault();
                var part_id = $("#part").val();
                var url = "{{ url('inventory/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_id,
                    },
                    success: function(data) {
                        console.log(data);
                    var html = "<option value="+null+">Select Parts Model</option>";
                    $("#model_id").empty();
                    $.each(data.partsModel, function(key) {

                        html += "<option value="+data.partsModel[key].id+">"+data.partsModel[key].name+"</option>";
                    })
                    $("#model_id").append(html);
                    html = "";
                    }
                })
            });    
            //Rack  
            $('#store').on('change', function(){
                var store_id=$(this).val();
                if(store_id){
                    $.ajax({
                        url: "{{url('inventory/get/rack/')}}/"+store_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Rack</option>";
                            $('#rack').empty();
                            $.each(data, function(key, value){
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#rack").append(html);
                            html = "";
                        }
                    });
                }
            });
            //Bin
            $('#rack').on('change', function(){
                var rack_id=$(this).val();
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Bin</option>";
                            $('#bin').empty();
                            $.each(data, function(key, value){
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#bin").append(html);
                            html = "";
                        }
                    });
                }
            });

            $('#price-details').hide();

            $('#model_id').on('change', function(){
                var part_id=$('#part').val();
                var model_id=$(this).val();
                if(model_id){
                    $.ajax({
                        url: "{{url('get/price/')}}/"+part_id+"/"+model_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            
                                $('#price-details').show(500);
                                $('#price_management_id').val(data.id);
                                $('#cost_price_usd').val(data.cost_price_usd);
                                $('#cost_price_bdt').val(data.cost_price_bdt);
                                $('#selling_price_bdt').val(data.selling_price_bdt);
                            
                        }
                    });
                }
            });
        });
    </script>
@endsection