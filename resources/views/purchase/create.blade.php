@extends('layouts.main')
@section('title', 'Add Purchase')
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
                            <h5>{{ __('label.ADD_PURCHASE')}}</h5>

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
                        <h3>{{ __('label.ADD_PURCHASE')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('url' => 'product/purchase', 'class' => 'forms-sample form-prevent-multiple-submits', 'id'=>'','method'=>'POST')) }}
                    @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customer_id">{{ __('label.CUSTOMER MOBILE')}}<span class="text-red">*</span></label>
                                        {{-- {!! Form::select('customer_id', $customers, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_CUSTOMER'),'id'=> 'customer_id', 'required']) !!} --}}
                                        {{-- <select name="customer_id" id="customer_id" class="form-control select2">
                                            <option value="">Select Customer</option>
                                            @forelse($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                @if( old('customer_id') == $customer->id )
                                                selected
                                            @endif
                                                >
                                                {{ $customer->mobile }}-{{ $customer->name }}
                                            </option>
                                            @empty
                                                <option value="">No Client Found</option>
                                            @endforelse
                                        </select> --}}
                                        <select name="customer_id" id="customer_id" class="form-control js-data-example-ajax" required>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('customer_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="">
                                        <label for="customer_name">{{__('label.CUSTOMER NAME')}}</label>
                                        <div>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" readonly>
                                        </div>
                                        @error('customer_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">    
                                    <div class="form-group" id="">
                                        <label for="customer_address">{{__('label.CUSTOMER ADDRESS')}}</label>
                                        <div>
                                            <textarea name="customer_address" id="customer_address" class="form-control" cols="12" rows="1" readonly></textarea>
                                        </div>
                                        @error('customer_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('label.SELECT_CATEGORY')}}<span class="text-red">*</span></label>
                                        {!! Form::select('category_id', $categories, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_CATEGORY'),'id'=> 'category_id', 'required']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                        <select name="brand_id" id="brand_id" class="form-control select2" required>

                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('brand_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="model_id">{{ __('label.BRAND_MODEL')}}<span class="text-red">*</span></label>
                                        <select name="model_id" id="model_id" class="form-control select2" required>

                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('model_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">    
                                    <div class="form-group">
                                    <h5>{{ __('Do You want to sell from your own stock ?')}}<span class="text-red">*</span></h5>
                                        <input type="radio" id="yes" name="own_stock" value="1" class="own_stock" checked>
                                        <label for="html">Yes</label>
                                        <input type="radio" id="no" name="own_stock" value="0" class="own_stock">
                                        <label for="no">No</label>
                                        <div class="help-block with-errors"></div>
                                        @error('own_stock')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="outlet_id">
                                        <label for="outlet_id">{{ __('label.POINT_OF_PURCHASE')}}<span class="text-red">*</span></label>
                                        {!! Form::select('outlet_id', $outlets, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_OUTLET'),'id'=> 'outlet_id']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('outlet_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="purchase_date">{{ __('label.PURCHASE_DATE')}}<span class="text-red">*</span></label>
                                        {{ Form::date('purchase_date', currentDate(), array('id'=> 'purchase_date', 'class' => 'form-control', 'placeholder' => '')) }}<div class="help-block with-errors"></div>

                                        @error('purchase_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="special_warranty_date">{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS')}}<span class="text-red">*</span></label>
                                        {{ Form::date('special_warranty_date', currentDate(), array('id'=> 'special_warranty_date', 'class' => 'form-control', 'placeholder' => '')) }}<div class="help-block with-errors"></div>

                                        @error('special_warranty_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="service_warranty_date">{{ __('label.WARRANTY_END_FOR_SERVICE')}}<span class="text-red">*</span></label>
                                        {{ Form::date('service_warranty_date', currentDate(), array('id'=> 'service_warranty_date', 'class' => 'form-control', 'placeholder' => '')) }}<div class="help-block with-errors"></div>

                                        @error('service_warranty_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_serial">{{ __('label.PRODUCT_SERIAL')}}</label>
                                        {{ Form::text('product_serial', Request::old('product_serial'), array('id'=> 'product_serial', 'class' => 'form-control', 'placeholder' => 'Enter Serial Number ...')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('product_serial')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="invoice_number">Invoice No</label>
                                        {{ Form::text('invoice_number', Request::old('invoice_number'), array('id'=> 'invoice_number', 'class' => 'form-control', 'placeholder' => 'Enter Invoice Number ...')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('invoice_number')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="general_warranty_date">{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS')}}<span class="text-red">*</span></label>
                                        {{ Form::date('general_warranty_date', currentDate(), array('id'=> 'general_warranty_date', 'class' => 'form-control', 'placeholder' => '')) }}<div class="help-block with-errors"></div>

                                        @error('general_warranty_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
                                    </div>
                                </div>
                            </div>

                            {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    <!-- push external js -->
    @push('script')
        {{-- <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script> --}}
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    <script type="text/javascript">
            $(document).ready(function() {
            // Initialize select2
            $(".js-data-example-ajax").select2({
                placeholder: "Search for a customer...",
                ajax: {
                    url: "{{route('call-center.customer_data')}}",
                    type: "post",
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            query: params.term, // search term
                            "_token": "{{ csrf_token() }}",
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });
        $(document).ready(function(){
            $('#customer_id').on('change', function(e){
                e.preventDefault();
                    var customer_id= $('#customer_id').val();
                    var url = "{{ route('sell.get-customer-info') }}";

                    $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        customer_id: customer_id
                    },
                    success: function(data) {
                        console.log(data.customer.name);
                        $('#customer_name').val(data.customer.name);
                        $('#customer_address').val(data.customer.address);
                    }
                });
            });
        });
        var ses = "{{ Session::get('brand_id') }}"
        let cat_id = $('#category_id').val();

        if(cat_id){
            var sess_id = "{{ Session::get('brand_id') }}";
            var category_id = $("#category_id").val();
            var url = "{{ url('product/get-brand') }}";
            $.ajax({
                type: "get",
                url: url,
                data: {
                    id: category_id,
                },
                success: function(data) {
                $("#brand_id").empty();
                    var html = "<option value="+null+">Select Brand</option>";
                    $.each(data.brand, function(key) {
                    if(data.brand.id == sess_id){
                        $("#brand_id").append("<option selected value="+data.brand.id+">"+data.brand.name+"</option>");
                    };
                    $("#brand_id").append("<option value="+data.brand[key].id+">"+data.brand[key].name+"</option>");
                    })
                }
            })
        }

        $(document).ready(function(){

            $("#outlet_id").hide();
            $(".own_stock").change(function() {
                var selected = $("input[type='radio'][name='own_stock']:checked");
                var selected_val=selected.val();
                if(selected_val == 0) {
                    $("#outlet_id").show();
                } else {
                    $("#outlet_id").hide();
                }
            });


            $('#category_id').on('change', function(e){
                e.preventDefault();
                var sess_id = "{{ Session::get('brand_id') }}";
                var category_id = $("#category_id").val();
                var url = "{{ url('product/get-brand') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: category_id,
                    },
                    success: function(data) {
                        console.log(data);
                    var html = "<option value="+null+">Select Brand</option>";
                    $("#brand_id").empty();
                    $.each(data.brand, function(key) {
                        if(data.brand.id == sess_id){
                            console.log(data.brand.id);
                            $('#brand_id').append("<option selected value="+data.brand.id+">"+data.brand.name+"</option>");
                        };
                        html += "<option value="+data.brand[key].id+">"+data.brand[key].name+"</option>";
                    })
                    $("#brand_id").append(html);
                    html = "";
                    }
                })
            });

            $('#brand_id').on('change', function(e){
                e.preventDefault();
                var brand_id = $("#brand_id").val();
                var url = "{{ url('product/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: brand_id,
                    },
                    success: function(data) {
                        console.log(data);
                    var html = "<option value="+null+">Select Brand Model</option>";
                    $("#model_id").empty();
                    $.each(data.brand_model, function(key) {
                        html += "<option value="+data.brand_model[key].id+">"+data.brand_model[key].model_name+"</option>";
                    })
                    $("#model_id").append(html);
                    html = "";
                    }
                })
            });

        });
    </script>
@endpush
