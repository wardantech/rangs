@extends('layouts.main')
@section('title', 'Part Sell')
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
                            <h5>{{ __('Part Sell')}}</h5>

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
                        <h3>{{ __('Part Sell')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'outlet.requisitionStore', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Invoice Number')}}</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number">
                                    <div class="help-block with-errors"></div>

                                    @error('requisition_no')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="selling_Date">{{ __('Selling Date')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Selling Date">
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Store')}}</label>
                                    <select name="store_id" id="store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('from_store_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        
                        </div>
                        <div class="row">
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Parts Name')}}</label>
                                    <select name="parts_id" id="parts_id" class="form-control select2" multiple required>
                                        <option value="">Select parts</option>
                                        @foreach($parts as $part)
                                        <option value="{{$part->id}}">{{$part->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="parts_model_id">{{ __('Parts Model')}}</label>
                                    <select name="parts_model_id" id="parts_model_id" class="form-control select2" multiple required>

                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_model_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">
                                
                            </div>

                            <div class="col-md-12 mt-3">

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
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){
            $('#parts_id').on('change', function(e){
                e.preventDefault();
                var part_id = $("#parts_id").val();
                //alert(part_id);
                var url = "{{ url('inventory/parts/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_id,
                    },
                    success: function(data) {
                        //console.log(data);
                        var html = "<option value="+null+">Select Parts Model</option>";
                        $("#parts_model_id").empty();
                        $.each(data.partsModel, function(key) {
                        console.log(data.partsModel[key].part.name);

                            html += "<option value="+data.partsModel[key].part.id+'-'+data.partsModel[key].id+">"+data.partsModel[key].part.name+'-['+data.partsModel[key].name+']'+"</option>";
                        })
                        $("#parts_model_id").append(html);
                        html = "";

                    }
                });
            });

        $('#parts_model_id').on('change', function(e){
        e.preventDefault();
        //var part_id = $("#parts_id").val();
        var model_id = $("#parts_model_id").val();
        // alert(model_id);
        var url = "{{ url('product/part-sell/stock') }}";
        //alert(part_id);
        $.ajax({
            type: "get",
            url: url,
            data: {
                model_id: model_id
            },
            success: function(data) {
                console.log(data);
                $("#parts_data").html(data.html);
            }
        });

    });
        });
    </script>
@endsection
