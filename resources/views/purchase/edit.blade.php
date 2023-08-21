@extends('layouts.main')
@section('title', 'Update Purchase')
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
                            <h5>{{ __('label.UPDATE_PURCHASE')}}</h5>

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
                        <h3>{{ __('label.UPDATE_PURCHASE')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="form-prevent-multiple-submits" method="POST" action="{{ route('product.purchase.update',$purchase->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="customer_id">{{ __('label.SELECT_CUSTOMER')}}<span class="text-red">*</span></label>
                                        <select name="customer_id" id="customer_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @forelse($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                @if( old('customer_id', optional($purchase)->customer_id) == $customer->id )
                                                    selected
                                                @endif
                                                >
                                                {{ $customer->mobile }}-{{ $customer->name }}
                                            </option>
                                            @empty
                                                <option value="">No Client Found</option>
                                            @endforelse
                                        </select>

                                        <div class="help-block with-errors"></div>
                                        @error('customer_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group" id="">
                                        <label for="customer_name">{{__('label.CUSTOMER NAME')}}</label>
                                        <div>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" value="{{ $purchase->customer->name ?? null}}" readonly>
                                        </div>
                                        @error('customer_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group" id="">
                                        <label for="customer_address">{{__('label.CUSTOMER ADDRESS')}}</label>
                                        <div>
                                            <textarea name="customer_address" id="customer_address" class="form-control" cols="12" rows="1" readonly>{{ $purchase->customer->address ?? null }}</textarea>
                                        </div>
                                        @error('customer_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('label.SELECT_CATEGORY')}}<span class="text-red">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control select2">
                                            <option value="">Select</option>
                                            @forelse($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if( old('category_id', optional($purchase)->product_category_id ) == $category->id )
                                                    selected
                                                @endif
                                                >
                                                {{ $category->name }}
                                            </option>
                                            @empty
                                                <option value="">No Client Found</option>
                                            @endforelse
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                        <select name="brand_id" id="brand_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    @if( old('brand_id', optional($purchase)->brand_id ) == $brand->id )
                                                        selected
                                                    @endif
                                                    >
                                                    {{ $brand->name }}
                                                </option>
                                            @empty
                                                <option value="">No Item Found</option>
                                            @endforelse
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('brand_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="model_id">{{ __('label.BRAND_MODEL')}}<span class="text-red">*</span></label>

                                        <select name="model_id" id="model_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($brandmodels as $brandmodel)
                                            <option value="{{ $brandmodel->id }}"
                                                @if( old('model_id', optional($purchase)->brand_model_id  ) == $brandmodel->id )
                                                    selected
                                                @endif
                                                >
                                                {{ $brandmodel->model_name }}
                                            </option>
                                            @empty
                                                <option value="">No Item Found</option>
                                            @endforelse
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

                                        <input type="radio" id="yes" name="own_stock" value="1" class="own_stock" @if ($purchase->outlet_id ==null)
                                            checked
                                            @endif>
                                        <label for="html">Yes</label>

                                        <input type="radio" id="no" name="own_stock" value="0" class="own_stock" @if ($purchase->outlet_id !=null)
                                            checked
                                            @endif >
                                        <label for="no">No</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="outlet_id">
                                        <label for="outlet_id">{{ __('label.POINT_OF_PURCHASE')}}<span class="text-red">*</span></label>
                                        <select name="outlet_id" id="outlet_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($outlets as $outlet)
                                                <option value="{{ $outlet->id }}"
                                                    @if( old('outlet_id', optional($purchase)->outlet_id ) == $outlet->id )
                                                        selected
                                                    @endif
                                                    >
                                                    {{ $outlet->name }}
                                                </option>
                                            @empty
                                                <option value="">No Item Found</option>
                                            @endforelse
                                        </select>
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
                                        <input name="purchase_date" id="purchase_date" placeholder="Date" type="date" class="form-control" data-toggle="datepicker" value="{{$purchase->purchase_date->toDateString()}}">

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
                                        <input name="special_warranty_date" id="special_warranty_date" placeholder="Date" type="date" class="form-control" data-toggle="datepicker" value="{{ old('special_warranty_date', optional($purchase)->special_warranty_date->toDateString()) }}">
                                        <div class="help-block with-errors"></div>

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
                                        <input name="service_warranty_date" id="service_warranty_date" placeholder="Date" type="date" class="form-control" data-toggle="datepicker" value="{{ old('service_warranty_date', optional($purchase)->service_warranty_date->toDateString()) }}">
                                        <div class="help-block with-errors"></div>

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
                                        <input name="product_serial" id="product_serial" placeholder="Product Serial" type="text" class="form-control" data-toggle="datepicker" value="{{ old('product_serial', optional($purchase)->product_serial) }}">
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
                                        <input name="invoice_number" id="invoice_number" placeholder="Invoice Number" type="text" class="form-control" data-toggle="datepicker" value="{{ old('invoice_number', optional($purchase)->invoice_number) }}">
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
                                        <input name="general_warranty_date" id="general_warranty_date" placeholder="Date" type="date" class="form-control" data-toggle="datepicker" value="{{ old('general_warranty_date', optional($purchase)->general_warranty_date->toDateString()) }}">

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

    <script type="text/javascript">
        $(document).ready(function(){
            @if($purchase->outlet_id !=null)
                $("#outlet_id").show();
            @endif

            $(".own_stock").change(function() {
                var selected = $("input[type='radio'][name='own_stock']:checked");
                var selected_val=selected.val();
                if(selected_val == 0) {
                    $("#outlet_id").show();
                } else {
                    $("#outlet_id").hide();
                }
            });

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


            $('#category_id').on('change', function(e){
                e.preventDefault();
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
                    var html = "<option value="+null+">Select Brand</option>";
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
                        console.log(data);
                        $('#customer_phone').val(data.customer.mobile);
                        $('#customer_address').val(data.customer.address);
                    }
                });
            });
        });
    </script>
@endsection
