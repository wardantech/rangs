@extends('layouts.main')
@section('title', 'Edit Price')
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
                            <h5>{{ __('label.EDIT PRICE MANAGEMENT')}}</h5>

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
                        <h3>{{ __('label.EDIT PRICE MANAGEMENT')}}</h3>
                    </div>
                    <div class="card-body">

                    {{Form::open(['route'=>array('inventory.price-management.update', $priceManagementRow->id), 'method'=>'PUT', "class"=>"form-horizontal"])}}
                        @csrf
                        <div class="row">
                            <div class="row col-sm-12">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="part">
                                            {{ __('label.PART')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="hidden" name="part_id" class="form-control" value="{{$priceManagementRow->part_id}}">
                                        <input type="text" name="part" class="form-control" value="{{$priceManagementRow->part->code}}-{{$priceManagementRow->part->name}}" readonly>
                                        <div class="help-block with-errors"></div>
                                        @error('part_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="usd">
                                            {{ __('label.BUYING PRICE (USD)')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="number" name="cost_price_usd" class="form-control" value="{{$priceManagementRow->cost_price_usd}}" min="0" step="any">
                                        <div class="help-block with-errors" ></div>

                                        @error('cost_price_usd')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row col-sm-12">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="cost_price_bdt">
                                            {{ __('label.BUYING PRICE (BDT)')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="number" name="cost_price_bdt" class="form-control" value="{{$priceManagementRow->cost_price_bdt}}" min="0" step="any" required>
                                        <div class="help-block with-errors" ></div>
                                        @error('cost_price_bdt')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="selling_price">
                                            {{ __('Selling Price (BDT)')}}
                                        </label>
                                        <input type="number" name="selling_price_bdt" class="form-control" value="{{$priceManagementRow->selling_price_bdt}}" min="0" step="any">
                                        <div class="help-block with-errors" ></div>

                                        @error('selling_price_bdt')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush

    <script type="text/javascript">
    </script>
@endsection
