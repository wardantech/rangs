@extends('layouts.main')
@section('title', 'Edit Request Loan')
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
                            <h5>{{ __('Edit Request Loan')}}</h5>

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
                        <h3>{{ __('Edit Request Loan')}}</h3>
                    </div>
                    <div class="card-body">

                    <form action="{{route('loan.loan.update', $loan->id)}}" method="POST">
                    @csrf
                        <div class="row">
                            <input type="hidden" name="loan_id" id="loan_id" value="{{$loan->id}}">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="loan_no">{{ __('Loan No')}}</label>
                                    <input type="text" class="form-control" id="loan_no" name="loan_no" value="{{$loan->loan_no}}" placeholder="Loan No" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('loan_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{$loan->date->toDateString()}}" placeholder="Date">
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Sender / From Store')}}</label>
                                        <input type="text" class="form-control" id="" name="" value="{{ $loan->senderStore->name }}" readonly>
                                        <input type="hidden" class="form-control" id="from_store_id" name="from_store_id" value="{{ $loan->from_store_id }}" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="to_store_id">{{ __('To Store')}}</label>
                                    <input type="text" class="form-control" id="" name="" value="{{ $loan->store->name }}" readonly>  
                                    <input type="hidden" class="form-control" id="store_id" name="store_id" value="{{ $loan->store_id }}" readonly> 
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Parts Name')}}</label>
                                    <select name="parts_id" id="parts_id" class="form-control select2" multiple disabled>
                                        <option value="">Select parts</option>
                                        @foreach($parts as $part)
                                        <option value="{{$part->id}}"
                                        @foreach($partIds as $partId)
                                            @if($part->id == $partId)
                                                selected
                                            @endif
                                        @endforeach
                                        >{{$part->code}}-{{$part->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanDetails as $key=>$item)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $item->part->code}} -[{{ $item->part->name }}]" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" id="stock_in_hand-{{$key}}" name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $item->rack ? $item->rack->name : '' }}" readonly>
                                                    <input type="hidden" class="form-control" name="rack_id[]" value="{{ $item ? $item->rack_id : '' }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $item->bin ? $item->bin->name : '' }}" readonly>
                                                    <input type="hidden" class="form-control" name="bin_id[]" value="{{ $item ? $item->bin_id : '' }}" readonly>
                                                </td>
                                                <td><input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $item->required_quantity  }}" min="0" required>
                                                    <input type="hidden" name="part_id[]" value="{{ $item->parts_id }}">
                                                </td>
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
        // $(document).ready(function(){
        //     var parts_id = $("#parts_id").val();
        //     var loan_id = $("#loan_id").val();
        //     var from_store_id = $("#from_store_id").val();
        //         var url = "{{ url('loan/request/edit-parts-row') }}";
        //         //alert(part_id);
        //         $.ajax({
        //             type: "get",
        //             url: url,
        //             data: {
        //                 parts_id: parts_id,
        //                 loan_id: loan_id,
        //                 from_store_id: from_store_id
        //             },
        //             success: function(data) {
        //                 console.log(data);
        //                 $("#parts_data").html(data.html);
        //             }
        //         });

        //     $('#parts_id').on('change', function(e){
        //         e.preventDefault();
        //         var parts_id = $("#parts_id").val();
        //         var from_store_id = $("#from_store_id").val();
        //         var url = "{{ url('loan/request/edit-parts-row') }}";
        //         //alert(part_id);
        //         $.ajax({
        //             type: "get",
        //             url: url,
        //             data: {
        //                 parts_id: parts_id,
        //                 from_store_id: from_store_id
        //             },
        //             success: function(data) {
        //                 console.log(data);
        //                 $("#parts_data").html(data.html);
        //             }
        //         });

        //     });
        // });
    </script>
@endsection
