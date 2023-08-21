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

                    {{ Form::open(array('route' => 'central.parts-return.receive.store', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <input type="hidden" name="parts_return_id" value="{{ $partsReturn->id }}">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="sl_number">{{ __('Serial No')}}</label>
                                    <input type="text" class="form-control" id="sl_number" name="sl_number" value="B-RSL-{{$partsReturn->id}}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.RETURN REQUEST DATE')}}<span class="text-red"></span></label>
                                    <input type="text" class="form-control" value="{{ $partsReturn->date->format('m/d/Y') }}" readonly>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.RECEIVE_DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ currentDate() }}" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="outlate_id">{{ __('Request From (Store)')}}</label>
                                    <input type="hidden" name="partsReturn_id" value="{{ $partsReturn->id }}">
                                    <input type="text" class="form-control" id="date"  value="{{ $partsReturn->senderStore->name }}" readonly>
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
                                            <th>Receiving Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $key=>$detail)
                                        <tr>
                                            <td> <input type="text" class="form-control" value="{{ $detail->part->code }}-{{ $detail->part->name }}" readonly><input type="hidden" name="part_category_id[]" class="form-control" value="{{ $detail->part->part_category_id }}"></td>
                                            <td><input type="number" class="form-control" data-id="{{$key}}" id='stock_in_hand-{{$key}}' name="stock_in_hand[]" value="{{ $stock_collect[$key] }}" min="0" readonly></td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="rack_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->rack_id : '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin->name : '' }}" readonly>
                                                <input type="hidden" class="form-control" name="bin_id[]" value="{{ $rackbinInfo[$key] ? $rackbinInfo[$key]->bin_id : '' }}" readonly>
                                            </td>
                                            <td><input type="number" class="form-control" id="issued_quantity-{{$key}}" name="issued_quantity[]" value="{{ $detail->required_quantity }}" readonly>
                                                <input type="hidden" name="part_id[]" value="{{ $detail->parts_id }}">
                                            </td>
                                            <td><input type="number" class="form-control" id="receiving_quantity-{{$key}}" onInput="warning({{$key}})" name="receiving_quantity[]" required>
                                                <input type="hidden" name="partsReturn_details_id[]" class="form-control" value="{{ $detail->id }}">
                                                @error('receiving_quantity[]')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="note">{{ __('Note')}}</label>
                                    <textarea name="note" id="note" class="form-control" cols="30" rows="2" readonly>{{ $partsReturn->description }}</textarea>
                                    @error('note')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
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

        // $('#rack').on('change', function(){
        //         var rack_id=$(this).val();
        //         if(rack_id){
        //             $.ajax({
        //                 url: "{{url('inventory/get/bin/')}}/"+rack_id,
        //                 type: 'GET',
        //                 dataType: "json",
        //                 success: function(data){
        //                     console.log(data);
        //                     var html = "<option value="+null+">Select Bin</option>";
        //                     $('#bin').empty();
        //                     $.each(data, function(key, value){
        //                         // $('#bin').append("<option value="+value.id+">"+value.name+"</option>");
        //                         html += "<option value="+value.id+">"+value.name+"</option>";
        //                     });
        //                     $("#bin").append(html);
        //                     html = "";
        //                 }
        //             });
        //         }
        //     });

        function getBin(key){
            var rack_id=$('#rack_'+key).val();
            var bin=$('#bin_'+key).val();
            // alert("rack_id: "+rack_id+" Bin_id: "+bin);
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
