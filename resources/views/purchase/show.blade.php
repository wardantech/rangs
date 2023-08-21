@extends('layouts.main')
@section('title', 'Purchase Details ')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-user-md bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Purchase Details')}}</h5>
                            <span>{{ __('Purchase Details')}}</span>
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
                <div class="card p-3">
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <td><label for="">Customer Name</label></td>
                                <td>{{$purchase->customer->name}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Product Category</label></td>
                                <td>{{$purchase->category->name}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Brand</label></td>
                                <td>{{$purchase->brand->name}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Brand Model</label></td>
                                <td>
                                    @isset($purchase->modelname)
                                        {{$purchase->modelname->model_name}}
                                    @endisset
                                </td>
                            </tr>
                            <tr>
                                <td><label for="">Product Serial</label></td>
                                <td>{{$purchase->product_serial}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Invoice Number</label></td>
                                <td>{{$purchase->invoice_number}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Purchase Date</label></td>
                                <td>{{$purchase->purchase_date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Warranty End For General Parts</label></td>
                                <td>{{$purchase->general_warranty_date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Warranty End For Special Parts</label></td>
                                <td>{{$purchase->special_warranty_date->format('m/d/Y')}}</td>
                            </tr>
                            <tr>
                                <td><label for="">Warranty End For Service</label></td>
                                <td>{{$purchase->service_warranty_date->format('m/d/Y')}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
