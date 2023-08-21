@extends('layouts.main')
@section('title', 'Region')
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
                            <h5>{{__('label.CREATE REGION')}}</h5>
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
                        <form action="{{ route('general.region.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name" class="col-form-label">
                                                {{ __('label.REGION NAME')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Region Name ..." value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="parent" class="col-form-label">
                                                {{ __('label.REGION CODE')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            <input type="number" class="form-control" id="code" name="code" value="{{ old('code') }}" placeholder="Region Code" required>
                                            @error('code')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="division" class="col-form-label">
                                                {{ __('label.DIVISION')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            <select id="division" class="form-control" name="division_id" required>
                                                <option value="">Select a division</option>
                                                @foreach($divisions as $division)
                                                    <option value="{{$division->id}}"
                                                        @if (old('division_id') == $division->id)
                                                            selected
                                                        @endif
                                                    >
                                                        {{$division->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('division_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="code" class="col-form-label">
                                                {{ __('label.DISTRICT')}}
                                                <span class="text-red">*</span>
                                            </label>
                                            @php
                                                $array = Session::get('districtIds');
                                            @endphp
                                            <select id="district_id" class="form-control select2" multiple="multiple" name="district_id[]" multiple="multiple" required>
                                                <option value="">Select a district</option>
                                                {{--@if(Session::has('districtIds'))--}}
                                                    {{--@foreach($districts as $district)--}}
                                                        {{--@if(in_array($district->id, $array))--}}
                                                            {{--<option value="{{ $district->id }}" selected>--}}
                                                                {{--{{ $district->name }}--}}
                                                            {{--</option>--}}
                                                        {{--@endif--}}
                                                    {{--@endforeach--}}
                                                {{--@endif--}}
                                            </select>
                                            @error('district_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label id="label"></label>
                                        <div id="thana">

                                        </div>
                                        @error('thana_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <input type="submit" class="btn form-bg-danger mr-2">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function(){
        $('#division').on('change', function(){
            var division_id=$(this).val();
            if(division_id){
                $.ajax({
                    url: "{{url('general/get/district')}}/"+division_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data){
                        $('#district_id').empty();
                        $.each(data, function(key, value){
                            $('#district_id').append("<option value="+value.id+">"+value.name+"</option>");
                        });
                    }
                });
            }
        });

        $('#district_id').on('change', function(){
            var district_id = $(this).val();
            var url = "{{ url('general/get/multi/thana/') }}";
            var label = "Select Thana <span class='text-red'>*</span>";
            if(district_id){
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        district_id: district_id,
                    },
                    dataType: "json",
                    success: function(data){
                        $('#thana').empty();
                        $('#label').empty();
                        $('#label').append(label);
                        $.each(data, function(key, value){
                            $('#thana').append("<div class='form-check form-check-inline'><input class='form-check-input ml-1' name='thana_id[]' type='checkbox' value="+value.id+" id="+value.id+"><label class='form-check-label mt-1' for="+value.id+">"+ value.name +"</label></div>");
                        });
                    }
                });
            }
        });
    });
</script>

@endsection
