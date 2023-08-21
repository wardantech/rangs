@extends('layouts.main')
@section('title', 'Consumption')
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
                            <h5>{{ __('Part Consumption')}}</h5>

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
                    @isset($consumptionsdetails)
                    <div class="card-header">
                        <h3>{{ __('Consumed Part List')}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table" style="background-color: rgb(255, 218, 218)">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART_NAME')}}</th>
                                    <th>{{ __('label.QUANTITY')}}</th>
                                    <th style="text-align: center;">{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumptionsdetails as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{$item['part_name']}}</td>
                                        <td>{{$item['stock_out']}}</td>
                                        <td>
                                            <div class='' style="text-align: center;">
                                                @can('edit')
                                                    <a  href="{{ route('technician.consumption.edit', $item['id']) }}">
                                                        <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                    </a>
                                                @endcan
                                            <div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endisset

                    <div class="card-header">
                        <h3>{{ __('Part Consumption')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'technician.consumption.store', 'class' => 'forms-sample form-prevent-multiple-submits', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="job_no">{{ __('Job No')}}</label>
                                    <input type="hidden" class="form-control" name="job_id" value="{{ $job->id }}" readonly>
                                    <input type="text" class="form-control" id="job_no" name="job_no" value="JSL-{{ $job->id ?? '' }}" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('job_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ currentDate() }}" required>
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Store')}}</label>
                                        <input type="text" class="form-control"  name="" value="{{ $mystore->name }}" readonly>
                                        <input type="hidden" id="from_store_id" name="from_store_id" value="{{ $mystore->id }}">
                                    <div class="help-block with-errors"></div>

                                    @error('from_store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- Requisitions Parts Start --}}
                        <div class="col-sm-12">
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>Parts Info</th>
                                        <th>Stock In Hand</th>
                                        <th>Required Quantity</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($details as $key=>$detail)
                                    <tr>
                                        <td><input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly></td>
                                        <td><input type="number" class="form-control" data-id="{{$key}}" id='requisition_stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
            
                                        <td>
                                            <input type="number" class="form-control" id="requisition_required_quantity-{{$key}}" name="required_quantity[]" onInput="requisition_warning({{$key}})" value="" min="0" >
                                            <input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}">
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Requisitions Parts End --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Select Parts')}}</label>
                                    <select name="parts_id" id="parts_id" class="form-control select2" multiple>
                                        <option value="">Select parts</option>
                                        @foreach($parts as $part)
                                        <option value="{{$part->id}}">{{$part->code}}-{{$part->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">

                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="submit" class="mt-2 btn btn-primary button-prevent-multiple-submits">
                                        <i class="spinner fa fa-spinner fa-spin"></i>
                                        {{ __('label.SUBMIT')}}
                                    </button>
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
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush

    <script type="text/javascript">

        function requisition_warning(key){
            var requisition_stock_in_hand=$('#requisition_stock_in_hand-'+key).val();
            var requisition_required_quantity=$('#requisition_required_quantity-'+key).val();

            var requisition_stock_qnty=parseInt(requisition_stock_in_hand);
            var requisition_required_qnty=parseInt(requisition_required_quantity);
            var style ="background-color:red;";
            console.log(requisition_stock_in_hand);

            if(requisition_required_qnty > requisition_stock_qnty ) {
                alert('Whoops! Consumption Quantity is more than current stock');
                $('#requisition_required_quantity-'+key).css({"background-color":"red","color":"white"});
                $('#requisition_required_quantity-'+key).val(null);
            }
        }

        $(document).ready(function(){
            $('#parts_id').on('change', function(e){
                e.preventDefault();
                var parts_id = $("#parts_id").val();
                var from_store_id = $("#from_store_id").val();
                var url = "{{ url('inventory/parts_consumption/technician/stock') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        parts_id: parts_id,
                        from_store_id: from_store_id
                    },
                    success: function(data) {
                        console.log(data);
                        $("#parts_data").html(data.html);
                    }
                });

            });
        });
    </script>
@endsection
