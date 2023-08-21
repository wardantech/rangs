@extends('layouts.main')
@section('title', 'Receive Parts')
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
                            <h5>{{ __('Branch Receive Parts')}}</h5>
                            @if ($allocation->is_reallocated == 1)
                            <h5>{{ __('Re-Allocated')}}</h5>
                            @endif

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
                        <h3>{{ __('Branch Receive Parts')}}</h3>
                    </div>
                    <div class="card-body">

                    <form class="form-prevent-multiple-submits" method="POST" action="{{ route('branch.requisition-receive-store') }}">
                    @csrf
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="B-RSL-{{ $allocation->requisition_id }}" readonly>
                                    <input type="hidden" name="requisition_id" value="{{ $allocation->requisition->id }}">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">
                                        {{ __('label.ALLOCATION_DATE') }}
                                        <span class="text-red"></span>
                                    </label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $allocation->date->toDateString() }}" required readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">
                                        {{ __('label.RECEIVE DATE')}}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="date" name="receiveing_date" value="{{ currentDate() }}">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="outlate_id">
                                        {{ __('Request From (Store)')}}
                                    </label>
                                    <input type="hidden" name="allocation_id" value="{{ $allocation->id }}">
                                    <input type="hidden" name="store_id" value="{{ $allocation->requisition->senderStore->id }}">
                                    <input type="text" class="form-control" id="date"  value="{{ $allocation->requisition->senderStore->name }}" readonly>
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
                                            <th>Issued Quantity</th>
                                            <th>Receiving Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $key=>$detail)
                                        <tr>
                                            <td> <input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly><input type="hidden" name="part_category_id[]" class="form-control" value="{{ $detail->part->part_category_id }}"></td>
                                                 <input type="hidden" name="allocation_details[]" value="{{$detail->id}}">
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                                            </td>
                                            <td><input type="number" class="form-control" id="issued_quantity-{{$key}}" name="issued_quantity[]" value="{{ $detail->issued_quantity }}" readonly><input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}"></td>
                                            <td>
                                                <input type="number" class="form-control" id="receiving_quantity-{{$key}}" onInput="warning({{$key}})" name="receiving_quantity[]" min="0" required>
                                                <input type="hidden" name="allocation_details_id[]" class="form-control" value="{{ $detail->id }}">
                                                @error('receiving_quantity')
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
            var issue_quantity=$('#issued_quantity-'+key).val();
            var receiving_quantity=$('#receiving_quantity-'+key).val();


            var issue_qnty=parseInt(issue_quantity);
            var rec_quantity=parseInt(receiving_quantity);
            console.log(issue_qnty);

            if(rec_quantity>issue_qnty ) {
                alert('Whoops! Receiving Quantity is more than Issuing Quantity');
                $('#receiving_quantity-'+key).val(null);
            }
        }
    </script>
@endsection
