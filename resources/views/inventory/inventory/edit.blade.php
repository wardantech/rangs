@extends('layouts.main')
@section('title', 'Update Received Parts')

 <!-- push external head elements to head -->
 @push('head')
 <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
 {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
@endpush

@section('content')

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Update Receive Parts')}}</h5>

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
            <div class="col-md-12" id="ajax">
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Update Receive Parts')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('update-inventory', $inventory->id)}}" class="form-prevent-multiple-submits" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="hidden" name="inventory_id" id="inventory_id" value="{{$inventory->id}}">
                                    <div class="form-group">
                                        <label for="invoice_number">
                                            {{ __('label.INVOICE_NUMBER')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', optional($inventory)->invoice_number) }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="po_number">{{ __('label.PO_NUMBER')}}</label>
                                        <input type="text" class="form-control" id="po_number" name="po_number" value="{{ old('po_number', optional($inventory)->po_number) }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="lc_number">{{ __('label.LC_NUMBER')}}</label>
                                        <input type="text" class="form-control" id="lc_number" name="lc_number" value="{{ old('lc_number', optional($inventory)->lc_number) }}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="receiveDate">
                                            {{ __('label.RECEIVE_DATE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="receiveDate" type="date" class="form-control @error('receive_date') is-invalid @enderror" name="receive_date" value="{{ $inventory->receive_date->toDateString() }}" placeholder="" >
                                        <div class="help-block with-errors"></div>

                                        @error('receive_date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="vendor_id">{{ __('label.SELECT_VENDOR')}}<span class="text-red">*</span></label>
                                        <select name="vendor_id" id="vendor_id" class="form-control">
                                            <option value="">Select a vendor</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{$vendor->id}}" {{$vendor->id == $inventory->vendor_id ? 'selected' : ''}}>{{$vendor->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('vendor_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="source">Select Source</label>
                                        <select name="source_id" id="" class="form-control">
                                            <option value="">Select Source</option>
                                            @foreach($sources as $source)
                                                <option value="{{$source->id}}" {{$source->id == $inventory->source_id ? 'selected' : ''}}>{{$source->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="store_id">
                                            {{ __('To Store')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="store_id" id="store" class="form-control select2" disabled>
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{$store->id}}" {{$store->id == $inventory->store_id ? 'selected' : ''}}>{{$store->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('store_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label for="requisition_no">
                                            {{ __('Parts Name')}}
                                            <span class="text-red">*</span>
                                        </label>
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
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12" id="parts_data">
                                    <table id="datatable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Parts Info</th>
                                                <th>Rack</th>
                                                <th>Bin</th>
                                                <th>Cost Price (BDT)</th>
                                                <th>Cost Price (USD)</th>
                                                <th>Selling Price (BDT)</th>
                                                <th>Receiving Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inventoryStocks as $key=>$item)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $item->part->code}} -[{{ $item->part->name }}]" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $item->rack->name ?? '' }}" readonly>
                                                        <input type="hidden" class="form-control" name="rack_id[]" value="{{ $item->rack_id ? $item->rack_id : '' }}" readonly>
                                                    </td>
                                                    <td width="100">
                                                        <input type="text" class="form-control" value="{{ $item->bin->name ?? '' }}" readonly>
                                                        <input type="hidden" class="form-control" name="bin_id[]" value="{{ $item->bin_id ? $item->bin_id : '' }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="price_management_id[]" id="price_management_id" value="{{$item->price_management_id}}">
                                                        <input type="number" name="cost_price_bdt[]" id="cost_price_bdt" value="{{$item->cost_price_bdt}}" class="form-control" min="0" step="any">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="cost_price_usd[]" id="cost_price_usd" value="{{$item->cost_price_usd}}" class="form-control" min="0" step="any">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="selling_price_bdt[]" id="selling_price_bdt" value="{{$item->selling_price_bdt}}" class="form-control" min="0" step="any">
                                                    </td>
                                                    <td><input type="number" class="form-control" name="quantity[]" @if(isset($item->stock_in)) value="{{$item->stock_in}}" @endif min="0" step="any">
                                                        <input type="hidden" name="part_id[]" value="{{ $item->part_id }}"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="note">{{ __('Note')}}</label>
                                        <textarea name="note" id="note" class="form-control" cols="30" rows="2">{{$inventory->description}}</textarea>
                                        <div class="help-block with-errors"></div>
                                        @error('note')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->

@endsection

@push('script')
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>


<script type="text/javascript">

    $(document).ready( function() {

    selectRefresh();

    })

    function selectRefresh() {
        $('#parts_data .select2').select2({
            //-^^^^^^^^--- update here
            tags: true,
            placeholder: "Select an Option",
            allowClear: true,
            width: '100%'
        });
        }
    </script>
@endpush

