@extends('layouts.main')
@section('title', 'Brand Model Create')
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
                            <h5>{{ __('label.BRAND_MODEL')}}</h5>

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
                        <h3>{{ __('label.BRAND_MODEL')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('url' => 'product/brand_model', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                        @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('label.SELECT_CATEGORY')}}<span class="text-red">*</span></label>
                                        {!! Form::select('category_id', $categories, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_CATEGORY'),'id'=> 'category_id']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                            {{-- {!! Form::select('brand_id',  null,[ 'class'=>'form-control select2', 'placeholder' => 'Select Brand','id'=> 'brand_id']) !!} --}}
                                            <select name="brand_id" id="brand_id" class="form-control" required>

                                            </select>
                                            <div class="help-block with-errors"></div>
                                            @error('brand_id')
                                                <span class="text-red-error" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="model_name">{{ __('label.BRAND_MODEL')}}<span class="text-red">*</span></label>
                                        {{ Form::text('model_name', Request::old('model_name'), array('id'=> 'model_name', 'class' => 'form-control', 'placeholder' => 'Enter Model', 'required' => 'required')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('model_name')
                                            <span class="text-red" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="code">{{ __('label.BRAND_MODEL_CODE')}}<span class="text-red">*</span></label>
                                        <input type="text" name="code" class="form-control" placeholder="Enter Model Code" value="{{ old('code') }}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('code')
                                            <span class="text-red" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                        var html = "<option value="+null+">Select Brand</option>";
                        console.log(sess_id);
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

        });
    </script>
@endsection
