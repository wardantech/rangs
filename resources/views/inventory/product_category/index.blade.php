@extends('layouts.main')
@section('title', 'Users')
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
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Product Category')}}</h5>
                            <span>{{ __('List of product category')}}</span>
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
                    <div class="card-header">
                        <h3>@lang('Product Category')</h3>
                        <div class="card-header-right">
                           <a class="btn btn-info" data-toggle="modal" data-target="#demoModal">  @lang('label._CREATE')</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL')}}</th>
                                    <th>{{ __('Product Category Name')}}</th>
                                    <th>{{ __('Product Code')}}</th>
                                    <th>{{ __('Parent')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                @foreach($productCategories as $productCategory)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$productCategory->name}}</td>
                                    <td>{{$productCategory->code}}</td>
                                    <td>{{$productCategory->parent_id}}</td>
                                    <td>
                                        <div class='text-center'>

                                            {{ Form::open(['route' => ['inventory.destroy.product-category', $productCategory->id], 'method' => 'DELETE'] ) }}
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <a href="{{route('inventory.edit.product-category', ['id'=>$productCategory->id])}}" class="show-product-category">
                                                <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                            </a>
                                            <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                               <i class="ik ik-trash-2 f-16 text-red"></i>
                                            </button>
                                            {{ Form::close() }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Add Warranty Type modal-->

    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.PRODUCT CATEGORY')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'inventory.create.product-category', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="name" class="col-sm-4 col-form-label">1. Product Category Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Product Category Name">
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
                                            <input type="text" class="form-control" id="code" name="code" placeholder="code">
                                        </div>
                                    </div>
                                </div> 
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

@endsection
