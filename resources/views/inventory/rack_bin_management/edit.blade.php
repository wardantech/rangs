@extends('layouts.main')
@section('title', __('label.UPDATE_RACK_BIN_MANAGEMENT'))
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
                            <h5>{{__('label.UPDATE_RACK_BIN_MANAGEMENT')}}</h5>
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
                        <form method="POST" action="{{ route('inventory.rack-bin-management.update', $rackBinManagement->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parts_id">
                                            {{ __('Select Part') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="parts_id" id="parts_id" class="form-control select2">
                                            <option value="">Select Part</option>
                                            @foreach($parts as $part)
                                                <option value="{{ $part->id }}"
                                                    @if($part->id === $rackBinManagement->parts_id)
                                                        selected
                                                    @endif
                                                >
                                                {{ $part->code}}-{{ $part->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parts_id')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="store">
                                            {{ __('Select Store') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="store_id" id="store" class="form-control select2">
                                            <option value="">Select A Store</option>
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}"
                                                    @if($store->id === $rackBinManagement->store_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$store->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rack">
                                            {{ __('Select Rack') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="rack_id" id="rack" class="form-control select2">
                                            <option value="">Select A Rack</option>
                                            @foreach($racks as $rack)
                                                <option value="{{$rack->id}}"
                                                        @if($rack->id === $rackBinManagement->rack_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$rack->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('rack_id')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="bin">
                                            {{ __('Select Bin') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="bin_id" id="bin" class="form-control select2">
                                            <option value="">Select A Bin</option>
                                            @foreach($bins as $bin)
                                                <option value="{{$bin->id}}"
                                                        @if($bin->id === $rackBinManagement->bin_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$bin->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bin_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
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

            $('#rack').on('change', function(){
                var rack_id = $(this).val();
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
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
        });
    </script>
@endsection
