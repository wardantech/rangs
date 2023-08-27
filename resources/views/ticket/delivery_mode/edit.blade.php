@extends('layouts.main')
@section('title', 'Product Delivery Mode')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.EDIT_PRODUCT_DELIVERY_MODE')}}</h5>

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
                    <div class="card-header">
                        <h3>{{ __('label.EDIT_PRODUCT_DELIVERY_MODE')}}</h3>
                    </div>
                    <div class="card-body">
                        {{-- <form class="" action="{{ route('delivery-mode.update', $deliveryMode->id) }}" method="PUT">
                            @csrf --}}
                            <form class="" method="POST" action="{{ route('delivery-mode.update',$deliveryMode->id) }}">
                                @csrf
                                @method('PUT')
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="product_receive_mode_id">
                                            {{ __('label.PRODUCT_RECEIVE_MODE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control" value="{{$deliveryMode->name}}" placeholder="Product Receive Mode name" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="status" class="">
                                            {{ __('Status') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="status" class="form-control" id="status" required>
                                            <option value="">Select</option>
                                            <option value="1" @if($deliveryMode->status == 1) selected @endif>Active</option>
                                            <option value="0" @if($deliveryMode->status == 0) selected @endif>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button class="mt-2 btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush
@endsection
