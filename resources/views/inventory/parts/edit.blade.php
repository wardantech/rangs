@extends('layouts.main')
@section('title', 'Edit Parts')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
        @include('include.message')
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.EDIT PARTS')}}</h5>
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
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-body">
                        <form method="POST" action="{{ route('inventory.parts.update', $parts->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="product_category">Product Category</label>
                                    <select name="product_category_id" id="" class="form-control select2">
                                        <option value="">----Select a parts category----</option>
                                        @foreach($productCategories as $productCategory)
                                        <option value="{{$productCategory->id}}"@if ($parts->product_category_id == $productCategory->id)
                                            selected
                                        @endif>{{$productCategory->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('product_category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="address">Part Category</label>
                                    <select name="part_category_id" id="part_category" class="form-control select2">
                                        <option value="">----Select a parts category----</option>
                                        @foreach($partCategories as $partCategory)
                                        <option value="{{$partCategory->id}}" @if ($parts->part_category_id == $partCategory->id)
                                            selected
                                        @endif>{{$partCategory->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('part_category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="address">Part Model</label>
                                    <select name="part_model_id" id="model" class="form-control select2">
                                        <option value="">----Select a part model----</option>
                                        @foreach($partModels as $partModel)
                                        <option value="{{$partModel->id}}" @if ($parts->part_model_id == $partModel->id)
                                            selected
                                        @endif>{{$partModel->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('part_model_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="parts_code">
                                        {{ __('Parts Code') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="parts-code" name="code" value="{{ old('code',$parts->code) }}" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="store_code">
                                        {{ __('Description') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $parts->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="store_code">
                                        {{ __("Parts Unit") }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $parts->unit) }}" value="{{ old('unit') }}" required>
                                    @error('unit')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="store_code">Type</label>
                                    <select name="type" id="" class="form-control">
                                        <option value="">Select Type</option>

                                        <option value="1" @if ($parts->type == 1) selected @endif>General</option>

                                        <option value="2" @if ($parts->type == 2) selected @endif>Special</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-danger mr-2">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<!-- push external js -->
<script type="text/javascript">
    $(document).ready(function(){
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
                    var html = "<option value="+null+">Select Part</option>";
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
    });
</script>
@endsection
