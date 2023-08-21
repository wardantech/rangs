@extends('layouts.main') 
@section('title', 'Dashboard')
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
                            <h5>{{__('label.EDIT PRODUCT CATEGORY')}}</h5>
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
                            {{Form::open(['route'=>array('inventory.update.product-category', 'id'=>$productCategory->id), 'method'=>'POST', "class"=>"form-horizontal"])}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="name" class="col-sm-4 col-form-label">1. Product Category Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="name" name="name" value="{{$productCategory->name}}" placeholder="Product Category Name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="parent" class="col-sm-4 col-form-label">3. Parent</label>
                                        <div class="col-sm-8">
                                            <select name="parent_id" id="parent" class="form-control">
                                                <option value="">Select a parent</option>
                                                <option value="1">Demo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="code" class="col-sm-4 col-form-label">2. Product Code</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="code" name="code" value="{{$productCategory->code}}" placeholder="code">
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <input type="submit" class="btn form-bg-danger mr-2">
                                    <button class="btn form-bg-inverse">Cancel</button>
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

        });
    </script>
@endsection