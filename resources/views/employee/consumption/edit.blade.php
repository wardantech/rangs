@extends('layouts.main')
@section('title', 'Consumption')
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
                            <h5>{{ __('Part Consumption')}}</h5>

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
                        <h3>{{ __('Update Part Consumption')}}</h3>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('technician.consumption.update', $consumption->id) }}" class="forms-sample form-prevent-multiple-submits" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="job_no">{{ __('Job No')}}</label>
                                        <input type="text" class="form-control" id="job_no" name="job_no" value="JSL-{{ $consumption->job->id ?? '' }}" readonly>
                                        <div class="help-block with-errors"></div>

                                        @error('job_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ currentDate() }}" readonly>
                                        <div class="help-block with-errors"></div>

                                        @error('date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="from_store_id">{{ __('Store')}}</label>

                                            <input type="text" class="form-control"  name="" value="{{ $consumption->store->name }}" readonly>
                                            
                                        <div class="help-block with-errors"></div>

                                        @error('from_store_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Parts Info</th>
                                            <th>Stock In Hand</th>
                                            <th>Used Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control" value="{{ $consumption->part->code }}-{{ $consumption->part->name }}" readonly></td>
                                            <td><input type="number" class="form-control" id='stock_in_hand' name="stock_in_hand" value="{{ $stock_in_hand }}" readonly></td>
                                            <td>
                                                <input type="number" class="form-control" id="required_quantity" name="required_quantity" onInput="requisition_warning" value="{{ $consumption->stock_out }}" min="0" >
                                                <input type="hidden" class="form-control" id="used_quantity" name="used_quantity" onInput="requisition_warning" value="{{ $consumption->stock_out }}" min="0" >
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary button-prevent-multiple-submits">{{ __('label.SUBMIT')}}</button>
                                    <i class="spinner fa fa-spinner fa-spin"></i>
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
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush

    <script type="text/javascript">

        $(document).ready(function(){
            var stock_in_hand=$('#stock_in_hand').val();
            var used_quantity=$('#used_quantity').val();
            var total=stock_in_hand -( - used_quantity);
            $('#required_quantity').on('change', function(e){
            
            var required_quantity=$('#required_quantity').val();

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);

            if(required_qnty > total ) {
                $('#required_quantity').css({"background-color":"red","color":"white"});
                $('#required_quantity').val(null);
                alert('Whoops! Consumption Quantity is more than current stock');
                }else{
                    $('#required_quantity').css({"background-color":"green","color":"white"}); 
                }
            })
        });
    </script>
@endsection
