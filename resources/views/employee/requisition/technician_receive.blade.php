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
                            <h5>{{ __('Receive Parts')}}</h5>

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
                        <h3>{{ __('Receive Parts')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'outlet.requisitionReceiveStore', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="{{ $allocation->requisition->requisition_no }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $allocation->date }}">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="outlate_id">{{ __('Request From (Store)')}}</label>
                                    <input type="hidden" name="allocation_id" value="{{ $allocation->id }}">
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
                                            <th style="width: 160px">Bin</th>
                                            <th>Issued Quantity</th>
                                            <th>Receiving Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $key=>$detail)
                                        <tr>
                                            <td> <input type="text" class="form-control" value="{{ $detail->part->name }}-[{{ $detail->part_model->name }}]" readonly></td>
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly><input type="hidden" name="model_id[]" value="{{ $detail->model_id }}"></td>
                                            {{-- <td><input type="text" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $rack->rack_name }}" min="0" readonly><input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}"></td> --}}
                                            <td>
                                                <select name="rack" id="rack" class="form-control">
                                                    <option value="">Select Rack</option>
                                                    @foreach ($racks as $rack)
                                                    <option value="{{$rack->id}}">{{$rack->name}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </td>
                                            <td style="width: 160px">
                                                <select name="bin[]" id="bin" class="form-control select2" multiple>
                                                    <option value="">Select Bin</option>
                                                </select>
                                            </td>
                                            <td><input type="number" class="form-control" id="issued_quantity-{{$key}}" name="issued_quantity[]" value="{{ $detail->issued_quantity }}" readonly><input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}"></td>
                                            <td><input type="number" class="form-control" id="receiving_quantity-{{$key}}" onInput="warning({{$key}})" name="receiving_quantity[]"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                    <button class="btn btn-primary button-prevent-multiple-submits">
                                        <i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
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
        {{-- <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script> --}}
    @endpush

    <script type="text/javascript">
        $(function(){
            $('.spinner').hide();
            $('.form-prevent-multiple-submits').on('submit', function(){
                alert('kkk');
                $('.button-prevent-multiple-submits').attr('disabled', true);
                $('.spinner').show();
            });
        });
        function warning(key){
            var stock_in_hand=$('#stock_in_hand-'+key).val();
            var required_quantity=$('#required_quantity-'+key).val();
            var issue_quantity=$('#issue_quantity-'+key).val();
                
            if(issue_quantity>stock_in_hand ) {
                alert('Whoops! Issuing Quantity is more than current stock');
                $('#issue_quantity-'+key).attr("disabled", "disabled");
                $(':input[type="submit"]').prop('disabled', true);
            }else if(issue_quantity>required_quantity){
                alert('Whoops! Issuing Quantity is more than Required Quantity');
                $('#issue_quantity-'+key).attr("disabled", "disabled");
                $(':input[type="submit"]').prop('disabled', true);
            } 
        }
    </script>
@endsection
