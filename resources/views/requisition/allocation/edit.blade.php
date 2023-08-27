@extends('layouts.main')
@section('title', 'Edit Allocated Requisition')
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
                            <h5>{{ __('Edit Allocated Requisition')}}</h5>
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
                    <div class="card-header my-3">
                        <h3>{{ __('Edit Allocated Requisition')}}</h3>
                    </div>
                    <div class="card-body">

                    <form action="{{ route('central.allocation.update', $allocation->id) }}"  method="POST">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="B-RSL-{{ $allocation->requisition_id }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $allocation->date->toDateString() }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_id">{{ __('Request From (Store)')}}</label>
                                    <input type="hidden" name="requisition_id" value="{{ $allocation->requisition_id }}">
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
                                            <th>Required Quantity</th>
                                            <th>Balance Quantity</th>
                                            <th>Issue Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allocationDetails as $key=>$detail)
                                        <tr>
                                            <td><input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly></td>
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $detail->rack ? $detail->rack->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="rack_id[]" value="{{ $detail ? $detail->rack_id : '' }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $detail->bin ? $detail->bin->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="bin_id[]" value="{{ $detail ? $detail->bin_id : '' }}">
                                            </td>
                                            <td><input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $detail->requisition_quantity }}" min="0" readonly><input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}"></td>
                                            <td>
                                                <input type="number" class="form-control" id="balance_quantity-{{$key}}" onInput="warning({{$key}})" name="balance_quantity[]" value="{{ $detail->requisition_quantity - $detail->issued_quantity}}" min="0" readonly>
                                                <input type="hidden" class="form-control" id="issued_quantity-{{$key}}" onInput="warning({{$key}})" name="issued_quantity[]" value="{{ $detail->issued_quantity }}" min="0" readonly>
                                            </td>
                                            <td><input type="number" class="form-control" id="issue_quantity-{{$key}}" onInput="warning({{$key}})" name="issue_quantity[]" min="0" value="{{ $detail->issued_quantity }}" required></td>
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

    <script type="text/javascript">
        function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var required_quantity=$('#required_quantity-'+key).val();
            var balance_quantity=$('#balance_quantity-'+key).val();
            var issued_quantity=$('#issued_quantity-'+key).val();
            var issue_quantity=$('#issue_quantity-'+key).val();

            // var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);
            var balance_qnty=parseInt(balance_quantity);
            var issued_qnty=parseInt(issued_quantity);
            var issue_qnty=parseInt(issue_quantity);
            var stock_qnty=parseInt(stock_in_hand) + issued_qnty;
            console.log(balance_quantity);

            if(issue_qnty > stock_qnty ) {
                alert('Whoops! Issuing Quantity is more than current stock');
                $('#issue_quantity-'+key).val(null);
                // $(':input[type="submit"]').prop('disabled', true);
            }else if(required_qnty < issue_qnty){
                alert('Whoops! Issuing Quantity is more than required Quantity');
                $('#issue_quantity-'+key).val(null);
                // $(':input[type="submit"]').prop('disabled', true);
            }
        }
    </script>
@endsection
