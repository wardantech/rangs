@extends('layouts.main')
@section('title', 'Add New Bin')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{__('label.ADD BIN')}}</h5>
                            <span>{{__('label.CREATE A NEW BIN')}}</span>
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
        @can('create')
            <div class="row">
                @include('include.message')
                <div class="col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            <form action="{{ route('inventory.bins.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <label for="store_name">
                                            {{ __('Select Store') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="store_id" id="store" class="form-control" required>
                                            <option value="">Select a store</option>
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-red" id="validStoreId"></div>
                                        @error('store_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                        <label for="rack_name">
                                            {{ __('Select Rack') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="rack_id" id="rack" class="form-control" required>
                                            <option value="">Select a rack</option>
                                        </select>
                                        <div class="text-red" id="validRackId"></div>
                                        @error('rack_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col col-sm-6">
                                        <label for="bin_name">
                                            {{ __('Bin Name') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="bin-name" name="name" placeholder="Bin Name" value="{{ old('name') }}" required>
                                        <div class="text-red" id="validName"></div>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mt-30">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn form-bg-danger mr-2">{{ __('label.SUBMIT') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
	<!-- push external js -->


    <script type="text/javascript">
        $(document).ready(function(){
            $('#store').on('change', function(){
                var store_id=$(this).val();
                var selection = "<option value="+null+">Select Rack</option>";
                if(store_id){
                    $.ajax({
                        url: "{{url('inventory/get/rack/')}}/"+store_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            $('#rack').empty();
                            $.each(data, function(key, value){
                                selection += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $('#rack').append(selection);
                            selection = "";
                        }
                    });
                }
            });
        });
    </script>
@endsection
