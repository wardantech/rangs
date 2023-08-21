@extends('layouts.main')
@section('title', 'Price Management')
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
                            <h5>{{ __('label.PRICE MANAGEMENT')}}</h5>

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
                    <div class="card-header my-2">
                        <h3>{{ __('label.PRICE MANAGEMENT')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'inventory.price-management.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                        @csrf
                            <div class="row">
                                <div class="row col-sm-12">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="part">
                                                {{ __('label.SELECT_PART')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            <select name="part_id" id="part" class="form-control select2" required>
                                                <option value="">Select Part</option>
                                                @foreach($parts as $part)
                                                <option value="{{$part->id}}"
                                                    @if( old('part_id') == $part->id )
                                                        selected
                                                    @endif
                                                    >
                                                    {{$part->code}}-{{$part->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                            @error('part_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="usd">
                                                {{ __('label.BUYING PRICE (USD)')}}
                                            </label>
                                            {{ Form::text('cost_price_usd', Request::old('usd'), array('id'=> 'usd', 'class' => 'form-control', 'placeholder' => 'Enter USD Value ...')) }}
                                            <div class="help-block with-errors" ></div>

                                            @error('cost_price_usd')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-sm-12">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="cost_price_bdt">
                                                {{ __('label.BUYING PRICE (BDT)')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            {{ Form::text('cost_price_bdt', Request::old('bdt'), array('id'=> 'bdt', 'class' => 'form-control', 'placeholder' => 'Enter BDT Value ...', 'required' => 'required')) }}
                                            <div class="help-block with-errors" ></div>
                                            @error('cost_price_bdt')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="selling_price">{{ __('Selling Price (BDT)')}}<span class="text-red">*</span></label>
                                            {{ Form::text('selling_price_bdt', Request::old('selling_price'), array('id'=> 'selling_price', 'class' => 'form-control', 'placeholder' => 'Enter Selling Price...','required' => 'required')) }}
                                            <div class="help-block with-errors" ></div>

                                            @error('selling_price_bdt')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
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


            $('#part_category').on('change', function(e){
                e.preventDefault();
                var part_category_id = $("#part_category").val();
                var url = "{{ url('inventory/get/part-model') }}/"+part_category_id;
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_category_id,
                    },
                    success: function(data) {
                        // console.log(data);
                        var html = "<option value="+null+">Select Part Model</option>";
                        $("#model").empty();
                        // console.log(id);
                        $.each(data.partModels, function(key) {
                            html += "<option value="+data.partModels[key].id+">"+data.partModels[key].name+"</option>";
                        })
                        $("#model").append(html);
                        html = "";
                    }
                })
            });

            $('#model').on('change', function(e){
                e.preventDefault();
                var model_id = $("#model").val();
                var url = "{{ url('inventory/get-part') }}/"+model_id;
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: model_id,
                    },
                    success: function(data) {
                        console.log(data);
                        var html = "<option value="+null+">Select Part</option>";
                        $("#part").empty();
                        $.each(data.parts, function(key) {
                            html += "<option value="+data.parts[key].id+">"+data.parts[key].code+""+ "-" +""+data.parts[key].name+"</option>";
                        })
                        $("#part").append(html);
                        html = "";
                    }
                })
            });

        });
    </script>
@endsection
