@extends('layouts.main')
@section('title', 'Submit Job')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <style>
            .bill-section {
                margin: 2rem;
                border: 1px solid #f1f1f1;
            }
            .form-control {
                min-height: 25px !important;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-red"></i>
                        <div class="d-inline">
                            {{-- <h5>{{ __('Submit Job')}}</h5> --}}

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
                        <h3>{{ __('Submit Job')}}</h3>
                    </div>
                    <div class="card-body">

                    <form action="{{route('technician.job-submission-store')}}" method="post" class="form-prevent-multiple-submits" enctype="multipart/form-data">
                    @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="submission_date">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                            <input type="hidden" class="form-control" id="job_user_id" name="job_user_id" value="{{ $job->user_id }}">
                                            <input type="hidden" class="form-control" id="ticket_create_date" name="ticket_create_date" value="{{ $job->ticket->date }}">
                                            <input type="date" class="form-control" id="submission_date" name="submission_date" value="{{ currentDate() }}">
                                            <div class="help-block with-errors"></div>
                                            @error('submission_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="requisition_no">{{ __('Job No')}}</label>
                                            <input type="hidden" name="job_id" value="{{$job->id}}">
                                            <input type="text" class="form-control" id="job_no" name="job_no" value="JSL-{{ $job->id }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('requisition_no')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="job_start_time">{{ __('label.START_DATE')}}</label>
                                            <input type="text" class="form-control" id="job_start_time" name="job_start_time" value="{{ $job->job_start_time }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('job_start_time')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="technician">{{ __('label.TECHNICIAN')}}</label>
                                            <input type="text" class="form-control" id="technician" name="technician" value="{{ $job->employee->name }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('technician')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                {{-- Product-Part --}}
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="ticket_date">{{ __('label.TICKET_DATE')}}</label>
                                            <input type="text" class="form-control" id="ticket_date" name="ticket_date" value="{{ $job->ticket->date->format('m/d/Y') }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('ticket_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="product_catagory">{{ __('label.PRODUCT_CATEGORY')}}<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="product_catagory" name="product_catagory" value="{{ $job->ticket->purchase->category->name }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('product_catagory')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="brand_id">{{ __('label.BRAND')}}<span class="text-red">*</span></label>
                                            <input name="brand_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_id', optional($job)->ticket->purchase->brand->name) }}" readonly>
                                            @if ($errors->has('brand_id'))
                                                <span class="is-invalid">
                                                    <strong>{{ $errors->first('brand_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="brand_model_id">{{ __('label.PRODUCT_NAME')}}<span class="text-red">*</span></label>
                                            <input name="brand_model_id"  placeholder="" type="text" class="form-control" value="{{ old('brand_model_id', optional($job)->ticket->purchase->modelname->model_name) }}" readonly>
                                            @if ($errors->has('brand_model_id'))
                                                <span class="is-invalid">
                                                    <strong>{{ $errors->first('brand_model_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="service_amount">{{ __('label.SERVICE_TYPE')}}<span class="text-red">*</span></label>
                                                <?php $selectedServiceTypeIds= json_decode($job->ticket->service_type_id);
                                                ?>
                                                <table>
                                                @foreach ($serviceTypes as $serviceType)
                                                    @if(in_array($serviceType->id, $selectedServiceTypeIds))
                                                    <td><span class="badge badge-warning">{{$serviceType->service_type}}</span></td>
                                                    @endif
                                                @endforeach
                                                </table>
                                            <div class="help-block with-errors"></div>
                                            @error('service_amount')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="service_amount">{{ __('label.AMOUNT')}}<span class="text-red">*</span></label>
                                            @if ($job->is_ticket_reopened_job == 1)
                                            <input type="text" class="form-control" id="service_amount" name="service_amount" value="0" readonly>
                                            @else
                                            <input type="text" class="form-control" id="service_amount" name="service_amount" value="{{ $job->ticket->service_charge }}" readonly>
                                            @endif

                                            <div class="help-block with-errors"></div>
                                            @error('service_amount')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="general_warranty_date">{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS')}}<span class="text-red">*</span></label>
                                            <input type="text" class="form-control bg-success {{$job->ticket->purchase->general_warranty_date->format("Y-m-d") < $currentDate ? 'bg-danger text-white' : ''}}" id="general_warranty_date" name="general_warranty_date" value="{{ $job->ticket->purchase->general_warranty_date->format('m/d/Y') }}" readonly>
                                            <div class="help-block with-errors"></div>

                                            @error('general_warranty_date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="special_warranty_date">{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS')}}<span class="text-red">*</span></label>
                                            <input name="special_warranty_date" id="special_warranty_date" placeholder="" type="text" class="form-control bg-success {{$job->ticket->purchase->special_warranty_date->format("Y-m-d") < $currentDate ? 'bg-danger text-white' : ''}}" value="{{ old('special_warranty_date', optional($job)->ticket->purchase->special_warranty_date->format('m/d/Y')) }}" readonly>
                                            @if ($errors->has('special_warranty_date'))
                                                <span class="is-invalid">
                                                    <strong>{{ $errors->first('special_warranty_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="service_warranty_date">{{ __('label.WARRANTY_END_FOR_SERVICE')}}<span class="text-red">*</span></label>
                                            <input name="service_warranty_date" id="service_warranty_date" placeholder="" type="text" class="form-control bg-success {{$job->ticket->purchase->service_warranty_date->format("Y-m-d") < $currentDate ? 'bg-danger text-white' : ''}}" value="{{ old('service_warranty_date', optional($job)->ticket->purchase->service_warranty_date->format('m/d/Y')) }}" readonly>
                                            @if ($errors->has('service_warranty_date'))
                                                <span class="is-invalid">
                                                    <strong>{{ $errors->first('service_warranty_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="parts_data">
                                        <p id="general" class="text text-success"></p>
                                        <p id="special" class="text text-success"></p>
                                        <p id="service" class="text text-success"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h6 for="">Spare</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="datatable" class="table">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Parts Info</th>
                                                <th>Parts Type</th>
                                                <th>Used Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Sub Total</th>
                                                <th>Add To Bill</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $total=0;
                                            @endphp
                                            @foreach ($inventoryStocks as $key=>$value)
                                                {{-- @php
                                                    dd($job->ticket->purchase->general_warranty_date);
                                                @endphp --}}
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <input type="text" class="form-control" id="part_name" name="part_name[]" value="{{ $value['code'] }} - {{ $value['part_name'] }}" readonly>
                                                        <input type="hidden" name="part_id[]" value="{{ $value['part_id'] }}">
                                                    </td>
                                                    <td>
                                                        @if ($value['type']==1 )
                                                            <input type="text" class="form-control" id="unit_price" name="" value="General" readonly>
                                                        @elseif($value['type']==2)
                                                            <input type="text" class="form-control" id="unit_price" name="" value="Special" readonly>
                                                        @else
                                                            <input type="text" class="form-control" id="unit_price" name="" style="text-decoration:line-through" value="" readonly>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" id="used_quantity" name="used_quantity[]" value="{{ $value['stock_out'] }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="selling_price_bdt-{{$key}}" name="selling_price_bdt[]" value="{{ $value['price'] }}" readonly>
                                                        <input type="hidden" class="form-control" id="hidden_selling_price_bdt-{{$key}}" name="hidden_selling_price_bdt[]" value="{{ $value['price'] }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control sum-value get-values part-vlaue" id="subtotal_selling_price_bdt-{{$key}}" name="subtotal_selling_price_bdt[]" value="" readonly>
                                                        <input type="hidden" class="form-control" id="hidden_subtotal_selling_price_bdt-{{$key}}" name="hidden_subtotal_selling_price_bdt[]" value="{{ $value['price'] * $value['stock_out'] }}" readonly>
                                                        @php
                                                            $total +=$value['price'];
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="get-values" id="billcheckbox-{{$key}}" onInput="addToBill({{$key}})" name="checkbox[]" value="yes" style="width:20px; height:20px; margin-top: 13px;">
                                                        <label for="Yes">Yes</label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-header">
                                            <h3>Bill Description</h3>
                                        </div>
                                        <div class="bill-section">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>Subtotal For Spare:</td>
                                                        <td>
                                                            {{-- <input type="number" class="form-control get-values" name="subtotal_for_spare" id="spare_subtotal" value="{{ $total }}" readonly> --}}
                                                            <input type="number" class="form-control get-values" name="subtotal_for_spare" id="spare_subtotal" value="" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Subtotal For Servicing:</td>
                                                        <td>
                                                            @if ($job->is_ticket_reopened_job == 1)
                                                            <input class="form-control sum-value get-values" type="number" name="subtotal_for_servicing" id="subtotal_for_servicing" value="0" readonly>
                                                            @else
                                                            <input class="form-control sum-value get-values" type="number" name="subtotal_for_servicing" id="subtotal_for_servicing" value="{{ $job->ticket->service_charge }}" readonly>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Fault Finding Charges:
                                                        </td>
                                                        <td>
                                                            <input class="form-control sum-value get-values" type="number" name="fault_finding_charges" id="fault_finding_charge" min="0" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Repair Charges:
                                                        </td>
                                                        <td>
                                                            <input class="form-control sum-value get-values" type="number" name="repair_charges" id="repair_charge" min="0" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Other Charges:
                                                        </td>
                                                        <td>
                                                            <input class="form-control sum-value get-values" type="number" name="other_charges" id="other_charges" min="0" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Discount:
                                                        </td>
                                                        <td>
                                                            <input class="form-control sub-value get-values" type="number" name="discount" id="discount" min="0" value="0">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Advance Amount:
                                                        </td>
                                                        <td>
                                                            <input class="form-control sub-value" type="number" name="advance_amount" id="advance_amount" value="{{ $advance_payment ? $advance_payment->advance_amount : 0 }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            VAT:
                                                        </td>
                                                        <td>
                                                            <input class="form-control mod-value get-values" type="number" name="vat" id="vat" min="0" placeholder="Please Enter Vat" value="">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Amount:</td>
                                                        <input type="hidden" id="getTotalValue" value="{{ $total + $service_amount}}">
                                                        <input type="hidden" name="total_amount" id="storeTotalValue">
                                                        <td>
                                                            <input class="form-control" type="number" name="total" id="total" value="" readonly>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remark">Remark</label>
                            <textarea name="remark" id="" class="form-control" cols="30" rows="1"></textarea>
                        </div>
                        <div>
                            {{-- <input type="submit" class="btn btn-primary button-prevent-multiple-submits" value="Submit Job"> --}}
                            <button type="submit" class="btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>{{ __('label.SUBMIT')}}</button>
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
        <script src="{{ asset('js/sony/job-billing.js') }}"></script>
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush

    <script type="text/javascript">        
        function addToBill(key){

                var selling_price_bdt=$('#subtotal_selling_price_bdt-'+key).val();
                var hidden_selling_price_bdt=$('#hidden_subtotal_selling_price_bdt-'+key).val();
                if ($('#billcheckbox-'+key).is(':checked')) {
                    $('#subtotal_selling_price_bdt-'+key).val(hidden_selling_price_bdt);
                    $('#subtotal_selling_price_bdt-'+key).css({"background-color": "green","color":"white"});
                } else{
                    $('#subtotal_selling_price_bdt-'+key).val(0);
                    $('#subtotal_selling_price_bdt-'+key).css("background-color", "red");
                }

            // $(function() {
                // billDescription();

                $(".get-values").on('change', function(){
                    billDescription();
                });
            // });
        }
        $(".get-values").on('change', function(){
                billDescription();
            });

    </script>
@endsection
