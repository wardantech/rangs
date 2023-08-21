@extends('layouts.main')
@section('title', 'Add New Parts')
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
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.ADD PARTS')}}</h5>
                            <span>{{ __('label.CREATE A NEW PARTS')}}</span>
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
            @include('include.message')
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('label.ADD PARTS')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('inventory.parts.store')}}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="product_category">Product Category<span class="text-red">*</span></label>
                                    <select name="product_category_id" id="product_category" class="form-control select2" required>
                                        <option value="">----Select a product category----</option>
                                        @foreach($productCategories as $productCategory)
                                        <option value="{{$productCategory->id}}"
                                            @if( old('product_category_id') == $productCategory->id )
											selected
										    @endif
                                            >{{$productCategory->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('product_category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="address">
                                        {{ __('Part Category') }}<span class="text-red">*</span>
                                    </label>
                                    <select name="part_category_id" id="part_category" class="form-control select2" required>
                                        <option value="">----Select a parts category----</option>
                                        @foreach($partCategories as $partCategory)
                                        <option value="{{$partCategory->id}}"
                                            @if( old('part_category_id') == $partCategory->id )
											selected
										    @endif
                                            >
                                            {{$partCategory->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('part_category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="address">Part Model<span class="text-red">*</span></label>
                                    <select name="part_model_id" id="model" class="form-control select2" required>

                                    </select>
                                    @error('part_model_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="parts_code">
                                        {{ __('Parts Code') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="parts-code" name="code" placeholder="Parts Code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="store_code">
                                        {{ __('Description') }}
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Parts Description" value="{{ old('name') }}" required>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="store_code">
                                        {{ __("Parts Unit") }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="unit" name="unit" placeholder="Parts Unit" value="{{ old('unit') }}" required>
                                    @error('unit')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="store_code">Type<span class="text-red">*</span></label>
                                    <select name="type" id="" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="1">General</option>
                                        <option value="2">Special</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
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
    });
</script>
@endsection
