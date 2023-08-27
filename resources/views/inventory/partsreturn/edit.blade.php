@extends('layouts.main')
@section('title', 'Edit Parts Return')
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
                            <h5>{{ __('label.EDIT_PARTS_RETURN')}}</h5>

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
                        <h3>{{ __('label.EDIT_PARTS_RETURN')}}</h3>
                    </div>
                    <div class="card-body">
                        {{-- <form class="" method="POST" action="">
                            @csrf

                        </form> --}}
                        <form action="{{route('technician.parts-return.update', $partsreturn->id)}}" method="POST" class="forms-sample form-prevent-multiple-submits">
                            @method('PUT')
                        @csrf
                            <input type="hidden" name="parts_return_id" id="parts_return_id" value="{{$partsreturn->id}}">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="sl_no">{{ __('Serial No')}}</label>
                                        <input type="text" class="form-control" id="sl_no" name="sl_no" value="T-RSL-{{$partsreturn->id}}" readonly>
                                        <div class="help-block with-errors"></div>

                                        @error('sl_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{$partsreturn->date->toDateSTring()}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="from_store_id">{{ __('Sender / From Store')}}</label>
                                        <input type="text" class="form-control" id="" name="" value="{{ $partsreturn->senderStore->name }}" readonly>
                                        <input type="hidden" class="form-control" id="from_store_id" name="from_store_id" value="{{ $partsreturn->from_store_id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="to_store_id">{{ __('To Store')}}</label>
                                        <input type="text" class="form-control" id="" name="" value="{{ $partsreturn->toStore->name }}" readonly>  
                                        <input type="hidden" class="form-control" id="to_store_id" name="to_store_id" value="{{ $partsreturn->to_store_id }}" readonly>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="requisition_no">{{ __('Parts Name')}}</label>
                                        <select name="" id="parts_id" class="form-control select2" multiple disabled>
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
                                                <th>Stock In Hand</th>
                                                <th>Required Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partsReturnDetails as $key=>$detal)
                                                <tr>
                                                    <td> <input type="text" class="form-control" value="{{ $detal->part->code }} -[{{ $detal->part->name }}]" readonly></td>
                                                    <td><input type="number" class="form-control" name="stock_in_hand[]" id="stock_in_hand-{{$key}}" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                    
                                                    <td><input type="number" class="form-control" name="required_quantity[]" id="required_quantity-{{$key}}" onInput="warning({{$key}})" min="0" value="{{$detal->required_quantity}}">
                                                        <input type="hidden" name="part_id[]" value="{{ $detal->parts_id }}"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12">

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
                                    </div>
                                </div>

                            </div>

                        {{-- {!! Form::close() !!} --}}
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

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);

            if(required_qnty > stock_qnty ) {
                alert('Whoops! Return Quantity is more than current stock');
                $('#required_quantity-'+key).val(null);
            }
        }
    </script>
@endsection
