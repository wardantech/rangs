@extends('layouts.main')
@section('title', 'Add New Store')
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
                            <h5>{{__('label.ADD STORE')}}</h5>
                            <span>{{__('label.CREATE A NEW STORE')}}</span>
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
                            <form action="{{ route('inventory.store.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="outlet">
                                                {{ __('Branch') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <select name="outlet_id" id="" class="form-control" required>
                                                <option value="">Select branch</option>
                                                @foreach($outlets as $outlet)
                                                    <option value="{{$outlet->id}}"
                                                        @if( old('outlet_id') == $outlet->id )
                                                        selected
                                                        @endif
                                                        >
                                                        {{$outlet->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('outlet_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="address">
                                                {{ __('Address') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <textarea name="address" id="address" class="form-control" cols="30" rows="1" required></textarea>
                                            @error('address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">
                                                {{ __('Store Name') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="store-name" name="name" placeholder="Store Name" value="{{ old('name') }}" required>
                                            @error('name')
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
        @endcan
    </div>

    @push('script')
        <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    @endpush

@endsection
