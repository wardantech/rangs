@extends('layouts.main')
@section('title', 'Receive Parts')

 <!-- push external head elements to head -->
 @push('head')
 <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
 {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
@endpush

@section('content')
   


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Receive Parts')}}</h5>

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
            <div class="col-md-12" id="ajax">
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Receive Parts')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'create-inventory', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="invoice_number">{{ __('label.INVOICE_NUMBER')}}</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{old('invoice_number')}}">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="po_number">{{ __('label.PO_NUMBER')}}</label>
                                    <input type="text" class="form-control" id="po_number" name="po_number" value="{{old('po_number')}}">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.ORDER_DATE')}}</label>
                                    <input id="order_date" type="date" class="form-control @error('order_date') is-invalid @enderror" name="order_date" value="{{old('order_date')}}" placeholder="">
                                    <div class="help-block with-errors"></div>

                                    @error('order_date')
                                    <span class="text-red-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
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
                            </div>
                            <div class="col-sm-4">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="vendor_id">{{ __('label.SELECT_STORE')}}<span class="text-red">*</span></label>
                                    {{-- {!! Form::select('store', $stores, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_VENDOR_OPT'),'id'=> 'store']) !!} --}}
                                    <select name="store_id" id="store" class="form-control">
                                        <option value="">Select Store</option>
                                        @foreach ($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>
                                    @error('vendor_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="note">{{ __('Note')}}<span class="text-red">*</span></label>
                                    <textarea name="note" id="note" class="form-control" cols="30" rows="2"></textarea>
                                    <div class="help-block with-errors"></div>
                                    @error('note')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>Parts</th>
                                        <th>Parts Model</th>
                                        {{-- <th>Store</th> --}}
                                        {{-- <th>Rack</th>
                                        <th>Bin</th>
                                        <th>Receiving Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="parts_data">
                                    
                                </tbody>
                            </table> --}}
                            <div class="row col-sm-12 mb-4 mt-2" id="parts_data">
                                
                            </div>

                            <button id="add-row" class="btn btn-success mb-2 ml-3">+</button>
                            <button id="delete-row" class="btn btn-danger mb-2 ml-2">-</button>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
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
   
@endsection

@push('script')
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>



<script type="text/javascript">

    $(document).ready( function() {
        var row_counter = 1;
    function warning(key){
        var stock_in_hand=$('#stock_in_hand-'+key).val();
        var required_quantity=$('#required_quantity-'+key).val();
        var issue_quantity=$('#issue_quantity-'+key).val();
            
        if(issue_quantity>stock_in_hand ) {
            alert('Whoops! Issuing Quantity is more than current stock');
            $('#issue_quantity-'+key).attr("disabled", "disabled");
            $(':input[type="submit"]').prop('disabled', true);
        }else if(issue_quantity>required_quantity){
            alert('Whoops! Issuing Quantity is more than Required Quantity');
            $('#issue_quantity-'+key).attr("disabled", "disabled");
            $(':input[type="submit"]').prop('disabled', true);
        } 
    }
    
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
                            // $('#bin').append("<option value="+value.id+">"+value.name+"</option>");
                            html += "<option value="+value.id+">"+value.name+"</option>";
                        });
                        $("#bin").append(html);
                        html = "";
                    }
                });
            }
        });
    
    $(document).on('click','#ajax #add-row', function(e){
    var id = row_counter++;
    e.preventDefault();
    
    var model_id = $("#parts_model_id").val();
    var url = "{{ url('inventory/parts-receive/rows') }}";
    var store_id=$('#store').val();
    // alert(store_id);
    $.ajax({
        type: "get",
        url: url,
        data: {
            model_id: model_id,
            id:id,
            store_id: store_id,
        },
        success: function(data) {
            $("#parts_data").append(data.html);

            selectRefresh();
        }
    });
    
    });
    
    // $('#delete-row').on('change', function(){
    //         var id=$(this).val();

    //     });

    selectRefresh();
    
    })

    function selectRefresh() {
        $('#parts_data .select2').select2({
            //-^^^^^^^^--- update here
            tags: true,
            placeholder: "Select an Option",
            allowClear: true,
            width: '100%'
        });
        }
    </script>
@endpush

