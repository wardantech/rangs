@extends('layouts.main')
@section('title', 'Requisitions')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Requisitions')}}</h5>
                            <span>{{ __('label.BRANCH_REQUISITION_LIST')}}</span>
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
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('Requisitions')</h3>
                        <div class="card-header-right">
                           <a class="btn btn-info" data-toggle="modal" data-target="#demoModal">  @lang('label._CREATE')</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('Parts Name')}}</th>
                                    <th>{{ __('Parts Model')}}</th>
                                    <th>{{ __('Outlet')}}</th>
                                    <th>{{ __('Required Quantity')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                @foreach($requisitions as $key=>$requisition)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $requisition->date }}</td>
                                        <td>{{ $requisition->requisition_no }}</td>
                                        <td>{{ $requisition->part->name }}</td>
                                        <td>{{ $requisition->part_model->name }}</td>
                                        <td>{{ $requisition->outlate->name }}</td>
                                        <td>{{ $requisition->required_quantity }}</td>
                                        <td>
                                            @if($requisition->status == 0)
                                               <span class="badge badge-danger">Pending</span>
                                            @elseif($requisition->status == 1 && $requisition->total_quantity > $requisition->issue_quantity)
                                               <span class="badge badge-warning">Partially Allocated</span>
                                            @elseif($requisition->status == 2)
                                               <span class="badge badge-info">Received</span>
                                            @elseif($requisition->status == 3)
                                               <span class="badge badge-warning">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">
                                                {{ Form::open(['route' => ['destroy.jobs', $requisition->id], 'method' => 'DELETE'] ) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                <a  data-id="{{$requisition->id}}"  href="#" class="showJobPriority" data-id="{{$requisition->id}}">
                                                    <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                </a>
                                                <a class="btn btn-info allocateModal" data-parts_id="{{ $requisition->parts_id }}" data-model_id="{{ $requisition->parts_model_id }}" data-outlet_id="{{ $requisition->outlate_id }}" data-date="{{ $requisition->date }}" data-qty={{ $requisition->required_quantity }} data-parts="{{ $requisition->part->name }}" data-model="{{ $requisition->part_model->name }}" data-outlate="{{ $requisition->outlate->name }}" data-requisition_no="{{ $requisition->requisition_no }}" data-id="{{$requisition->id}}">Allocation</a>


                                                <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                <i class="ik ik-trash-2 f-16 text-red"></i>
                                                </button>
                                                {{ Form::close() }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Add Warranty Type modal-->

    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('Add New Requisition')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'requisitions.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="requisition_no">{{ __('Requisition No')}}</label>
                                        <input type="text" class="form-control" id="requisition_no" name="requisition_no" placeholder="Requisition No">
                                        <div class="help-block with-errors"></div>

                                        @error('requisition_no')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" placeholder="Date">
                                        <div class="help-block with-errors"></div>

                                        @error('date')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="outlate_id">{{ __('Outlet')}}</label>
                                        <select name="outlate_id" id="" class="form-control">
                                            <option value="">Select Outlate</option>
                                            @foreach($outlates as $outlat)
                                            <option value="{{$outlat->id}}">{{$outlat->name}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('outlate_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="requisition_no">{{ __('Parts Name')}}</label>
                                        <select name="parts_id" id="parts_id" class="form-control">
                                            <option value="">Select parts</option>
                                            @foreach($parts as $part)
                                            <option value="{{$part->id}}">{{$part->name}}</option>
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="parts_model_id">{{ __('Parts Model')}}</label>
                                        <select name="parts_model_id" id="parts_model_id" class="form-control">

                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('parts_model_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="stock_in_hand">{{ __('Stock In Hand')}}</label>
                                        <input type="number" class="form-control" id="stock_in_hand" name="stock_in_hand">
                                        <div class="help-block with-errors"></div>

                                        @error('stock_in_hand')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="number">{{ __('Required Quantity')}}</label>
                                        <input type="number" class="form-control" id="required_quantity" name="required_quantity" placeholder="Required Quantity">
                                        <div class="help-block with-errors"></div>

                                        @error('number')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="cllocationModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('Add New Requisition')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'requisition.allocate', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="requisition_no">{{ __('Requisition No')}}</label>
                                        <input type="text" class="form-control" id="requisition_no" name="requisition_no" placeholder="Requisition No">
                                        <div class="help-block with-errors"></div>

                                        @error('requisition_no')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" placeholder="Date">
                                        <div class="help-block with-errors"></div>

                                        @error('date')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="outlate_id">{{ __('Outlet')}}</label>
                                        <input type="text" name="outlate" class="form-control">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="requisition_no">{{ __('Parts Name')}}</label>
                                        <input type="text" name="parts" class="form-control">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="parts_model_id">{{ __('Parts Model')}}</label>
                                        <input type="text" name="parts_model" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="stock_in_hand">{{ __('Stock In Hand')}}</label>
                                        <input type="number" class="form-control" id="stock_in_hand" name="stock_in_hand">
                                        <div class="help-block with-errors"></div>

                                        @error('stock_in_hand')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="number">{{ __('Required Quantity')}}</label>
                                        <input type="number" class="form-control" id="required_quantity" name="required_quantity" placeholder="Required Quantity">
                                        <div class="help-block with-errors"></div>

                                        @error('number')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="number">{{ __('Issue Quantity')}}</label>
                                        <input type="number" class="form-control" id="required_quantity" name="issue_quantity" placeholder="Issue Quantity">
                                        <div class="help-block with-errors"></div>
                                        <input type="hidden" name="requisition_id">
                                        @error('number')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush
    <script type="text/javascript">
    $('#parts_id').on('change', function(e){
        e.preventDefault();
        var part_id = $("#parts_id").val();
        var url = "{{ url('inventory/model') }}";
        $.ajax({
            type: "get",
            url: url,
            data: {
                id: part_id,
            },
            success: function(data) {
                console.log(data);
                // $("#available_qty").val(data.stock);
            var html = "<option value="+null+">Select Parts Model</option>";
            $("#parts_model_id").empty();
            $.each(data.partsModel, function(key) {
            //   console.log(data.recYarn_name[key].brand);

                html += "<option value="+data.partsModel[key].id+">"+data.partsModel[key].name+"</option>";
            })
            $("#parts_model_id").append(html);
            html = "";
            }
        });
    });

    $('#parts_model_id').on('change', function(e){
        e.preventDefault();
        var part_id = $("#parts_id").val();
        var model_id = $("#parts_model_id").val();
        var url = "{{ url('inventory/getStockData') }}";
        //alert(part_id);
        $.ajax({
            type: "get",
            url: url,
            data: {
                id: part_id,
                model_id: model_id
            },
            success: function(data) {
                console.log(data);
                $('#stock_in_hand').val(data.stockInHand);
                $('#stock_in_hand').attr('readonly',true);
            }
        });

    });

    $('.allocateModal').on('click', function(e){
        var part_id = $(this).data('parts_id');
        var model_id = $(this).data('model_id');
        var url = "{{ url('inventory/getStockInfo') }}";
        $.ajax({
            type: "get",
            url: url,
            data: {
                id: part_id,
                model_id: model_id
            },
            success: function(data) {
                console.log(data);
                //$('#stock_in_hand').val(data.stockInHand);
                //$('#stock_in_hand').attr('readonly',true);
                $("#cllocationModal input[name='stock_in_hand']").val(data.stockIn);
            }
        });

        $("#cllocationModal input[name='date']").val($(this).data('date'));
        $("#cllocationModal input[name='requisition_no']").val($(this).data('requisition_no'));
        $("#cllocationModal input[name='required_quantity']").val($(this).data('qty'));
        $("#cllocationModal input[name='parts']").val($(this).data('parts'));
        $("#cllocationModal input[name='parts_model']").val($(this).data('model'));
        $("#cllocationModal input[name='outlate']").val($(this).data('outlate'));
        $("#cllocationModal input[name='date']").val($(this).data('date'));
        $("#cllocationModal input[name='requisition_id']").val($(this).data('id'));

          $('#cllocationModal').modal('show');
    });
    </script>

@endsection
