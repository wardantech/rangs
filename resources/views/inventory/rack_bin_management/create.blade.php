@extends('layouts.main')
@section('title', __('label.ADD_RACK_BIN_MANAGEMENT'))
@section('content')

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{__('label.ADD_RACK_BIN_MANAGEMENT')}}</h5>
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
                        <form method="POST" action="{{ route('inventory.rack-bin-management.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parts_id">
                                            {{ __('Select Part') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="parts_id" id="parts_id" class="form-control js-data-example-ajax" required>

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
                                                <option value="{{$store->id}}">{{$store->name}}</option>
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
    @push('script')

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize select2
            $(".js-data-example-ajax").select2({
                placeholder: "Search for an Item",
                ajax: {
                    url: "{{route('inventory.get_parts')}}",
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
    @endpush
@endsection
