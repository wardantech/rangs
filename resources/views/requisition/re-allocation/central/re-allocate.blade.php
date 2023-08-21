@extends('layouts.main')
@section('title', 'Re-Allocate Requisition')
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
                            <h5>{{ __('Re-Allocate Requisition')}}</h5>

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
                        <h3>{{ __('Re-Allocate Requisition')}} From {{ $requisition->store->name  }}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'central.re-allocate-store', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="B-RSL-{{ $requisition->id }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('Requisition Date')}}<span class="text-red"></span></label>
                                    <input type="date" class="form-control" value="{{ $requisition->date->format('Y-m-d') }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="re_allocate_date">{{ __('Re-Allocate Date')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ currentDate() }}" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="outlate_id">{{ __('Request From (Store)')}}</label>
                                    <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                                    <input type="text" class="form-control" id="date"  value="{{ $requisition->senderStore->name }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Parts Info</th>
                                            <th>Stock In Hand</th>
                                            <th>Rack</th>
                                            <th>Bin</th>
                                            <th>Required Quantity</th>
                                            <th>Issueed Quantity</th>
                                            <th>Balance Quantity</th>
                                            <th>Issue Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $key=>$detail)
                                        <tr>
                                            <td> <input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly></td>
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                                            </td>
                                            <td><input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $detail->required_quantity }}" min="0" readonly><input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}"></td>
                                            <td><input type="number" class="form-control" id="issued_quantity-{{$key}}" onInput="warning({{$key}})" name="issued_quantity[]" value="{{ $detail->issued_quantity }}" min="0" readonly></td>
                                            <td><input type="number" class="form-control" id="balance_quantity-{{$key}}" onInput="warning({{$key}})" name="balance_quantity[]" value="{{ $detail->required_quantity - $detail->issued_quantity}}" min="0" readonly></td>
                                            <td><input type="number" class="form-control" id="issue_quantity-{{$key}}" onInput="warning({{$key}})" name="issue_quantity[]" min="0" required></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
        function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var required_quantity=$('#required_quantity-'+key).val();
            var balance_quantity=$('#balance_quantity-'+key).val();
            var issue_quantity=$('#issue_quantity-'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);
            var balance_qnty=parseInt(balance_quantity);
            var issue_qnty=parseInt(issue_quantity);
            console.log(balance_quantity);

            if(issue_qnty > stock_qnty ) {
                alert('Whoops! Issuing Quantity is more than current stock');
                $('#issue_quantity-'+key).val(null);
                // $(':input[type="submit"]').prop('disabled', true);
            }else if(balance_qnty < issue_qnty){
                alert('Whoops! Issuing Quantity is more than Balance Quantity');
                $('#issue_quantity-'+key).val(null);
                // $(':input[type="submit"]').prop('disabled', true);
            }
        }
    </script>
@endsection
