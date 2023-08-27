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
                            <h5>{{__('label.EDIT REGION')}}</h5>
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
                    <div class="card-body">
                        @php
                            $tahanaIds = Session::get('tahanaIds');
                        @endphp

                        <form action="{{ route('general.region.update', $region->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">
                                            {{__('label.REGION NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{old('name') ?? $region->name}}" placeholder="Region Name" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parent">
                                            {{__('label.REGION CODE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="code" name="code" value="{{old('code') ?? $region->code}}" placeholder="Region Code" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parent">
                                            {{__('label.DIVISION')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select id="division" class="form-control" name="division_id" required>
                                            <option value="">Select a division</option>
                                            @foreach($divisions as $division)
                                            <option value="{{$division->id}}" {{$division->id==$region->division_id ? 'selected' : ''}}>{{$division->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('division_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @php
                                    $districtId = json_decode($region->district_id);
                                @endphp
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="code">
                                            {{__('label.DISTRICT')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select id="district_id" class="form-control select2" name="district_id[]" multiple="multiple" required>
                                            <option value="">Select a district</option>
                                            @foreach($districts as $district)
                                                 <option value="{{$district->id}}"
                                                    <?php echo (isset($districtId) && in_array($district->id, $districtId) ) ? 'selected="selected"' : "" ?>
                                                    >{{$district->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('district_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span id="lable">Select Thana</span>
                                    <div id="thana">
                                        <label id="label"></label>
                                        @foreach ($thanas as $thana)
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input ml-1" name='thana_id[]' value="{{ $thana->id }}" id="{{ $thana->id }}" <?php echo(isset($thanaId) && in_array($thana->id, $thanaId)) ? 'checked' : '' ?>><label for="{{ $thana->id }}" class="form-check-label mt-1">{{ $thana->name }}</label>
                                            </div>
                                        @endforeach
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

        var tahanaIds = <?php echo json_encode($tahanaIds); ?>;

        $('#district_id').on('change', function(){
            let district_id = $(this).val();
            let url = "{{ url('general/get/multi/thana/') }}";
            let label = "Select Thana <span class='text-red'>*</span>";
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
                            if (tahanaIds!=null) {
                                for(var i = 0; i < tahanaIds.length; i++) {
                                    if(tahanaIds[i] == value.id) {
                                        $('#thana').append("<div class='form-check form-check-inline'><input class='form-check-input ml-1' name='thana_id[]' type='checkbox' value="+value.id+" id="+value.id+" checked><label class='form-check-label mt-1' for="+value.id+">"+ value.name +"</label></div>");
                                    }
                                }
                            }

                            $('#thana').append("<div class='form-check form-check-inline'><input class='form-check-input ml-1' name='thana_id[]' type='checkbox' value="+value.id+" id="+value.id+"><label class='form-check-label mt-1' for="+value.id+">"+ value.name +"</label></div>");
                        });
                    }
                });
            }
        });

        // let getThanaId = $("form input:checkbox").val();
        // console.log(getThanaId);
        /*
        let values = [];
        $("form input:checkbox").each(function() {
            // values.push(this.value);
            if (this.checked) {
                let d = $("form input:checkbox");
                console.log(d);
            }
        });

        let checkedValues = [];
        $("form input:checked").each(function() {
            checkedValues.push(this.value);
        });

        console.log(values);
        */

    });
</script>

@endsection
