@extends('layouts.main')
@section('title', 'Allocate Requisition')
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
                            <h5>{{ __('Allocate Requisition')}}</h5>

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
                        <h3>{{ __('Allocate Requisition')}} From {{ $requisition->store->name  }}</h3>
                    </div>
                    <div class="card-body">
                        <form class="form-prevent-multiple-submits" method="POST" action="{{ route('central.requisitations.allocate.store') }}">
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
                                        <label for="date">{{ __('label.REQUISITION_DATE')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ $requisition->date->toDateString() }}" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="allocation_date">
                                            {{ __('label.ALLOCATION_DATE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="allocation_date" name="allocation_date" value="{{ currentDate() }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="from_store_id">{{ __('Request From (Store)')}}</label>
                                        <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                                        <input type="hidden" name="to_store_id" value="{{ $requisition->senderStore->id }}">
                                        <input type="hidden" name="store_id" value="{{ $requisition->store_id }}">
                                        <input type="text" class="form-control" value="{{ $requisition->senderStore->name }}" readonly>
                                    </div>
                                </div>

                                <div class="col-sm-12" id="parts_data">
                                    <table id="datatable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Parts Info</th>
                                                <th>Model No</th>
                                                <th>TSL No</th>
                                                <th>Purpose</th>
                                                <th>Stock In Hand</th>
                                                <th>Rack</th>
                                                <th>Bin</th>
                                                <th>Required Quantity</th>
                                                <th>Issue Quantity</th>
                                            </tr>
                                        </thead>
                                        {{-- onclick="getRack({{$key}})" --}}
                                        <tbody>
                                            @foreach ($details as $key=>$detail)
                                            {{-- @php
                                                dd($detail);
                                            @endphp --}}
                                            <tr>
                                                <td><input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly></td>
                                                <td><input type="text" class="form-control" value="{{ $detail->model_no }}" readonly></td>
                                                <td><input type="text" class="form-control" value="{{ $detail->tsl_no ?  "TSL-".$detail->tsl_no : ''}}" readonly></td>
                                                <td><input type="text" class="form-control" value="@purpose($detail->purpose)" readonly></td>

                                                <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                    
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                                                    <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                                                    <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $detail->required_quantity }}" min="0" readonly>
                                                    <input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}">
                                                    <input type="hidden" name="requisition_detail_id[]" value="{{ $detail->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" id="issue_quantity-{{$key}}" onInput="warning({{$key}})" name="issue_quantity[]" min="0" required>
                                                    @error('issue_quantity')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12">

                                    <div class="form-group">
                                        <button class="mt-2 btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>Submit</button>
                                    </div>
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

        function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var required_quantity=$('#required_quantity-'+key).val();
            var issue_quantity=$('#issue_quantity-'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);
            var issue_qnty=parseInt(issue_quantity);
            console.log(issue_qnty);

            if(issue_qnty > stock_qnty ) {
                alert('Whoops! Issuing Quantity is more than current stock');
                $('#issue_quantity-'+key).val(null);
            }else if(issue_qnty>required_qnty){
                alert('Whoops! Issuing Quantity is more than Required Quantity');
                $('#issue_quantity-'+key).val(null);
            }
        }
        
    </script>
@endsection
