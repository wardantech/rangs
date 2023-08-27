@extends('layouts.main')
@section('title', 'Edit Customer')
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
                            <h5>{{__('label.EDIT CUSTOMER')}}</h5>
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
                        {{-- <form class="forms-sample">
                            {{ csrf_field() }} --}}
                            {{Form::open(['route'=>array('call-center.update.customer', 'id'=>$customer->id), 'method'=>'POST', "class"=>"form-horizontal"])}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name" class="col-form-label">
                                            {{ __('label.CUSTOMER NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{$customer->name}}" placeholder="{{ __('label.CUSTOMER NAME')}}" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label">{{ __('label.CUSTOMER EMAIL')}}</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{$customer->email}}" placeholder="Customer email">
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile" class="col-form-label">
                                            {{ __('label.CUSTOMER MOBILE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{$customer->mobile}}" placeholder="Customer Mobile Number" required>
                                        @error('mobile')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_grade_id" class="col-form-label">
                                            {{ __('Customer Grade') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="customer_grade_id" id="customer_grade_id" class="form-control select2" required>
                                            <option value="">Select Grade</option>
                                            @forelse ($customerGrades as $grade)
                                                <option value="{{ $grade->id }}" {{$grade->id==$customer->customer_grade_id ? 'selected' : ''}}>{{ $grade->name }}</option>
                                            @empty
                                                <option value="">Not found customer grade</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="address" class="col-form-label">
                                            {{ __('label.ADDRESS')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <textarea name="address" class="form-control" id="address" cols="12" rows="1" required>{{$customer->address}}</textarea>
                                        @error('address')
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
	<!-- push external js -->

@endsection
