@extends('layouts.main')
@section('title', 'Add New Sourcing Vendor')
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
                            <h5>{{__('label.ADD SOURCING VENDOR')}}</h5>
                            <span>{{__('label.CREATE A NEW SOURCING VENDOR')}}</span>
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
                        <form action="{{ route('general.service-sourcing-vendor.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">
                                            {{ __('Vendor Name') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vendor-name" name="name" value="{{ old('name') }}" placeholder="Vendor Name" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="address">
                                            {{ __('Vendor Grade') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" name="grade" id="grade" class="form-control" value="{{ old('grade') }}" placeholder="Vendor Grade" required>
                                        @error('grade')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="store_code">
                                            {{ __('Vendor Code') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vendor-code" name="code" value="{{ old('code') }}" placeholder="Vendor Code" required>
                                        @error('code')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="store_code">
                                            {{ __('label.PHONE') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone" required>
                                        @error('phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="store_code">
                                            {{ __('label.EMAIL') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="store_code">District</label>
                                        <select name="district_id" id="district" class="form-control">
                                            <option value="">Select a district</option>
                                            @foreach($districts as $district)
                                            <option value="{{$district->id}}"
                                                @if (old('district_id') == $district->id)
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
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address" class="form-control" cols="30" rows="1"></textarea>
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="store_code">Thana</label>
                                        <select name="thana_id" id="thana" class="form-control">
                                            <option value="">Select a thana</option>
                                        </select>
                                        @error('thana_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-info mr-2">Submit</button>
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
    <script src="{{ asset('js/thana-depedency.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){
            $('.store-edit-btn').on('click', function(){
                $("#store-edit-modal input[name='name']").val($(this).data('name'));
                $("#store-edit-modal textarea[name='address']").val($(this).data('address'));
                $("#store-edit-modal input[name='code']").val($(this).data('code'));
            });

            $('#store-edit-form').on('submit', function(e){
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url : "/update/store",
                    data: $('#store-edit-form').serialize(),
                    //processData: false,
                    dataType: 'json',
                    //contentType: false,
                    //beforeSend: function(){},
                    success: function(response){
                        //console.log(response);
                        //alert("Data saved successfully.");
                    }
                    //error: alert("Data can not be saved.")
                });
            });

            $('#district').on('change', function(){
                var district_id=$(this).val();
        if(district_id){
            $.ajax({
                url: "{{url('general/get/thana/')}}/"+district_id,
                type: 'GET',
                dataType: "json",
                success: function(data){
                    $('#thana').empty();
                    $.each(data, function(key, value){
                        $('#thana').append("<option value="+value.id+">"+value.name+"</option>");
                    });
                }
            });
        }
    });

        });
    </script>
@endsection
