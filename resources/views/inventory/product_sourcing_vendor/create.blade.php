@extends('layouts.main')
@section('title', 'Add New Vendor')
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
                            <h5>{{__('label.ADD VENDOR')}}</h5>
                            <span>{{__('label.CREATE A NEW VENDOR')}}</span>
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
                        <form id="add-vendor" action="{{ route('general.product-sourcing-vendor.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label for="name">
                                        {{ __('Vendor Name') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="vendor-name" name="name" placeholder="Vendor Name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="store_code">
                                        {{ __('Vendor Code') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" name="code" id="store_code" class="form-control" placeholder="Vendor Code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="grade">
                                        {{ __('Vendor Grade') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" name="grade" id="grade" class="form-control" placeholder="Vendor Grade" value="{{ old('grade') }}" required>
                                    @error('grade')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="store_code">
                                        {{ __('label.PHONE') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label for="store_code">
                                        {{ __('label.EMAIL') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="address">
                                        {{ __('label.ADDRESS') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <textarea name="address" id="address" class="form-control" cols="30" rows="1" value="{{ old('address') }}" required></textarea>
                                    @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
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
                                <div class="col">
                                    <label for="store_code">Thana</label>
                                    <select name="thana_id" id="thana" class="form-control">
                                        <option value="">Select a thana</option>
                                    </select>
                                    @error('thana_id')
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

    @push('script')
    <script src="{{ asset('js/thana-depedency.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){
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
