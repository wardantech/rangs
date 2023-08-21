@extends('layouts.main')
@section('title', 'Branch')
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
                            <h5>{{__('label.EDIT OUTLET')}}</h5>
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
                        @php
                            $tahanaIds = Session::get('tahanaIds');
                        @endphp

                        <form action="{{ route('general.outlet.update', $outlet->id) }}" class="form-horizontal" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">
                                            {{__('label.BRANCH NAME')}}
                                            <span class="text-red">*</span>
                                        </label>

                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($outlet)->name) }}" placeholder="Outlet Name" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="code">
                                            {{__('label.BRANCH CODE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', optional($outlet)->code) }}" placeholder="code" required>
                                        @error('code')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="district_id">
                                            District <span class="text-red">*</span>
                                        </label>
                                        <select name="district_id" id="district_id" class="form-control">
                                            <option value="">Select a district</option>
                                            @foreach($districts as $district)
                                                <option value="{{$district->id}}"
                                                    @if ($outlet->district_id == $district->id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$district->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('district_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        @php
                                            $thanaId = json_decode($outlet->thana_id)
                                        @endphp
                                        <span id="label">Select Thana</span>
                                        <div id="thana">
                                            @foreach ($thanas as $thana)
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input ml-1" name='thana_id[]' value="{{ $thana->id }}" id="{{ $thana->id }}" <?php echo(isset($thanaId) && in_array($thana->id, $thanaId)) ? 'checked' : '' ?>><label for="{{ $thana->id }}" class="form-check-label mt-1">{{ $thana->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('thana_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="outlet_owner_name">
                                            {{__('label.BRANCH OWNER NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="outlet_owner_name" name="outlet_owner_name" value="{{ old('outlet_owner_name', optional($outlet)->outlet_owner_name) }}" placeholder="Branch owner name" required>
                                        @error('outlet_owner_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="market">
                                            {{ __('Market') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="market" name="market" value="{{ old('market', optional($outlet)->market) }}" placeholder="Market" required>
                                        @error('market')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="mobile">
                                            {{ __('label.MOBILE_NUMBER') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', optional($outlet)->mobile) }}" placeholder="Mobile Number" required>
                                        @error('mobile')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="code">
                                            {{__('label.BRANCH ADDRESS')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <textarea name="address" id="address" class="form-control" cols="12" rows="1" required>{{ $outlet->address }}</textarea>
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="outlet_owner_address">
                                            {{__('label.BRANCH OWNER ADDRESS')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <textarea name="outlet_owner_address" id="outlet_owner_address" class="form-control" cols="12" rows="1" required>{{ $outlet->outlet_owner_address }}</textarea>
                                        @error('outlet_owner_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-info mr-2">
                                        Submit
                                    </button>
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
        var tahanaIds = <?php echo json_encode($tahanaIds); ?>;

        $('#district_id').on('change', function(){
            var district_id = $(this).val();
            var url = "{{ url('general/get/multi/thana/') }}";
            var label = "Select Thana";
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
                        $('#label').text(label);
                        console.log(tahanaIds);
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
    </script>
@endsection
