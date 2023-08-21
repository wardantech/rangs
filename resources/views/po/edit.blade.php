@extends('layouts.main')
@section('title', 'Edit Purchase Requisition')
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
                            <h5>{{ __('Update Purchase Requisitions')}}</h5>

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
                        <h3 class="my-2">{{ __('Update Purchase requisitions')}}</h3>
                    </div>
                    <div class="card-body">


                    <form action="{{ route('purchase.requisitions.update', $purchaseOrder->id) }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="po_number">{{ __('Purchase Requisition No')}}</label>
                                    <input type="text" class="form-control" id="po_number" name="po_number" value="{{ $purchaseOrder->po_number }}" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('po_number')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            {{-- store->name --}}
                            <div class="col-sm-4">
                                <div class="form-group">

                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $purchaseOrder->date->toDateString() }}">
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Sender / From Store')}}</label>

                                    <input type="hidden" class="form-control" id="from_store_id" name="from_store_id" value="{{ $purchaseOrder->store_id }}" readonly>
                                    <input type="text" class="form-control" id="tstore" name="tstore" value="{{ $purchaseOrder->store->name }}" readonly>
                                    

                                    <div class="help-block with-errors"></div>

                                    @error('from_store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="store_id">{{ __('To Procurement')}}</label>
                                    <input type="text" name="store_id" class="form-control" placeholder="To Procurement" readonly>

                                    @error('store_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Parts Name')}}</label>
                                    <select name="parts_id[]" id="parts_id" class="form-control select2" multiple disabled>
                                        <option value="">Select parts</option>
                                        @foreach($parts as $part)
                                        <option value="{{$part->id}}"
                                            @foreach($selectParts as $selectPart)
                                                @if($selectPart->id == $part->id)
                                                    selected
                                                @endif
                                            @endforeach
                                            >
                                            {{$part->code}}-{{$part->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Parts Info</th>
                                            <th>Stock In Hand</th>
                                            <th>Required Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchaseOrderdetails as $key=> $purchaseOrderDetail)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $purchaseOrderDetail->part->code }}-{{ $purchaseOrderDetail->part->name }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="stock_in_hand[]" value="{{ $purchaseOrderDetail->stock_in_hand }}" min="0" readonly>
                                                    
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="required_quantity[]" min="0" value="{{ $purchaseOrderDetail->required_qnty }}">
                                                    <input type="hidden" name="parts_id[]" value="{{ $purchaseOrderDetail->part->id }}">
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

    </script>
@endsection
