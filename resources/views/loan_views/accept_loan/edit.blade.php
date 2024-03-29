@extends('layouts.main')
@section('title', 'Accept Loan')
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
                            <h5>{{ __('Accept Loan')}}</h5>

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
                        <h3>{{ __('Accept loan')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'loan.accept-loan.store', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="loan_no">{{ __('Loan No')}}</label>
                                    <input type="text" class="form-control" id="loan_no" name="loan_no" value="{{ $loan->loan_no }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $loan->date }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="outlate_id">{{ __('Request From (Store)')}}</label>
                                    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                    <input type="text" class="form-control" id="date"  value="{{ $loan->senderStore->name }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Parts Info</th>
                                            <th>Stock In Hand</th>
                                            <th>Store</th>
                                            <th>Rack</th>
                                            <th style="width: 130px;">Bin</th>
                                            <th>Required Quantity</th>
                                            <th>Issue Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $key=>$detail)
                                        <tr>
                                            <td><input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly></td>
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                            <td>
                                                <select name="store_id" id="store_{{$key}}" class="form-control" data-store="{{$loan->store->id}}">
                                                    {{-- <option value="">Select Store</option> --}}
                                                    <option value="{{$loan->store->id}}">{{$loan->store->name}}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rack_id[]" id="rack_{{$key}}" class="form-control" onclick="getBin({{$key}})">
                                                    <option value="">Select Rack</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="bin_id[]" id="bin_{{$key}}" class="form-control select2" multiple>
                                                    <option value="">Select Bin</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $detail->required_quantity }}" min="0" readonly>
                                                <input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}">
                                            </td>
                                            <td><input type="number" class="form-control" id="issue_quantity-{{$key}}" onInput="warning({{$key}})" name="issue_quantity[]" min="0"></td>
                                        </tr>
                                        <!-- <tr>
                                            <td> <input type="text" class="form-control" value="{{ $detail->part->name }}-{{ $detail->part->code }}" readonly></td>
                                            <td>
                                                <input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly>
                                                <input type="hidden" name="model_id[]" value="{{ $detail->model_id }}">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="required_quantity-{{$key}}" name="required_quantity[]" value="{{ $detail->required_quantity }}" min="0" readonly>
                                                <input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}">
                                            </td>
                                            <td><input type="number" class="form-control" id="issue_quantity-{{$key}}" onInput="warning({{$key}})" name="issue_quantity[]" min="0"></td>
                                        </tr> -->
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
            var issue_quantity=$('#issue_quantity-'+key).val();

            var stock_qnty=parseInt(stock_in_hand);
            var required_qnty=parseInt(required_quantity);
            var issue_qnty=parseInt(issue_quantity);
            console.log(issue_qnty);

            if(issue_qnty>stock_qnty ) {
                alert('Whoops! Issuing Quantity is more than current stock');
                $('#issue_quantity-'+key).attr("disabled", "disabled");
                $(':input[type="submit"]').prop('disabled', true);
            }else if(issue_qnty>required_qnty){
                alert('Whoops! Issuing Quantity is more than Required Quantity');
                $('#issue_quantity-'+key).attr("disabled", "disabled");
                $(':input[type="submit"]').prop('disabled', true);
            }
        }

        var store_id=$("select[name='store_id'] option:selected").val();
            //  alert(store_id);
                if(store_id){
                    $.ajax({
                        url: "{{url('inventory/get/rack/')}}/"+store_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Rack</option>";
                            $("select[name='rack_id[]']").empty();
                            $.each(data, function(key, value){
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("select[name='rack_id[]']").append(html);
                            html = "";
                        }
                    });
                }

        function getBin(key){
            var rack_id=$('#rack_'+key).val();
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Bin</option>";
                            $('#bin_'+key).empty();
                            $.each(data, function(key, value){
                                // $('#bin').append("<option value="+value.id+">"+value.name+"</option>");
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#bin_"+key).append(html);
                            html = "";
                        }
                    });
                }
        }
    </script>
@endsection
