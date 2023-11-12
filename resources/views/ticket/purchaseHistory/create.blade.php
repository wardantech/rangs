@extends('layouts.main')
@section('title', 'Create Ticket')
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
                            <h5>{{ __('label.CREATE_NEW_TICKET') }}</h5>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-success" title="Home"><i
                                        class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-danger" title="Go Back"><i
                                        class="fa fa-arrow-left" aria-hidden="true"></i></a>
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
                        <h3>{{ __('label.CREATE_NEW_TICKET') }}</h3>
                    </div>
                    <div class="card-body">
                        <form class="form-prevent-multiple-submits" method="POST" action="{{ route('store-ticket') }}"  enctype="multipart/form-data">
                            @csrf
                            <input name="purchase_id" type="hidden" value="{{ $purchase->id }}" class="form-control">
                            <fieldset class="form-group border p-3" style="background: #f9fafb">
                                <legend class="w-auto">Customer Information</legend>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="invoice_number" class="">Invoice Number</label>
                                        <input name="invoice_number" placeholder="Invoice Number" type="text"
                                            class="form-control" value="{{ $purchase->invoice_number }}" readonly>
                                        @error('invoice_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="date" class="">Date</label>
                                        <input name="date" placeholder="Date" type="date" class="form-control"
                                            value="{{ currentDate() }}">
                                        @error('date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="sl">{{ __('label.TICKET_NUMBER') }}<span
                                                class="text-red">*</span></label>
                                        <input name="sl_number" id="sl_number" placeholder="Ticket Serial Number"
                                            type="text" class="form-control" value="{{ $ticket_sl }}" readonly>
                                        @error('sl_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="customer_id">{{ __('label.CUSTOMER') }}<span
                                                class="text-red">*</span></label>
                                        <input name="customer_id" type="hidden" class="form-control"
                                            value="{{ $purchase->customer->id }}" readonly>
                                        <input name="customer" placeholder={{ __('label.CUSTOMER') }} type="text"
                                            class="form-control" value="{{ $purchase->customer->name }}" readonly>
                                        @error('customer')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="phone">{{ __('label.PHONE') }}<span
                                                class="text-red">*</span></label>
                                        <input name="phone" id="phone" type="text" class="form-control"
                                            value="{{ $purchase->customer->mobile }}" readonly>
                                        @error('phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <label for="address">{{ __('label.ADDRESS') }}<span
                                                        class="text-red">*</span></label>
                                                <textarea class="form-control" name="address" cols="30" rows="1">{{ $purchase->customer->address }}</textarea>
                                                @error('address')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="row">
                                            <div class="col">
                                                <label>{{ __('Customer Grade') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $purchase->customer->grade ? $purchase->customer->grade->name : '' }}"
                                                    readonly>
                                            </div>
                                            <div class="col">
                                                <label for="customer_reference">{{ __('Customer Reference') }}</label>
                                                <input type="text" name="customer_reference" class="form-control"
                                                    id="customer_reference" placeholder="Customer Reference"
                                                    value="{{ old('customer_reference') }}">
                                                @error('customer_reference')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="district_id">{{ __('label.DISTRICT') }}<span
                                                    class="text-red">*</span></label>
                                            <select name="district_id" id="district" class="form-control">

                                                <option value="">Select</option>
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        @if (old('district_id') == $district->id) selected @endif>
                                                        {{ $district->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                            @error('district_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="thana">{{ __('label.SELECT_THANA') }}<span
                                                    class="text-red">*</span></label>
                                            <select name="thana_id" id="thana" class="form-control">
                                                <option value="">Select</option>

                                            </select>
                                            @error('thana_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label for="carrier">{{ __('label.CARRIER_INFO') }}</label>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="">{{ __('label.CARRIER_NAME') }}</label>
                                        <input name="carrier" id="carrier" placeholder={{ __('label.CARRIER_NAME') }}
                                            type="text" class="form-control" value="{{ old('customer') }}">

                                        @error('carrier')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="phone">{{ __('label.CARRIER_PHONE') }}</label>
                                        <input name="carrier_phone" id="" type="text" class="form-control"
                                            value="{{ old('carrier_phone') }}"
                                            placeholder={{ __('label.CARRIER_PHONE') }}>
                                        @error('carrier_phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <div class="position-relative form-group">
                                            <div class="border-checkbox-section">
                                                <div class="border-checkbox-group border-checkbox-group-success">
                                                    <input class="border-checkbox" type="checkbox" name="carrier_own"
                                                        id="carrier_own" value="1">
                                                    <label class="border-checkbox-label" for="carrier_own">
                                                        Is Carrier Own The Product
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="form-group border p-3" style="background: #fffbeb">
                                <legend class="w-auto">Product Area</legend>

                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_catagory">{{ __('label.PRODUCT_CATEGORY') }}<span
                                                    class="text-red">*</span></label>
                                            <input name="product_category_id" placeholder="" type="hidden"
                                                class="form-control" value="{{ $purchase->category->id }}" readonly>
                                            <input name="product_category" placeholder="" type="text"
                                                class="form-control" value="{{ $purchase->category->name }}" readonly>
                                            @error('product_category')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_id">{{ __('label.BRAND') }}<span
                                                    class="text-red">*</span></label>
                                            <input name="brand_id" placeholder="" type="text" class="form-control"
                                                value="{{ $purchase->brand->name }}" readonly>
                                            @error('brand_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="brand_model_id">{{ __('label.PRODUCT_NAME') }}<span
                                                    class="text-red">*</span></label>
                                            <input name="brand_model_id" placeholder="" type="text"
                                                class="form-control" value="{{ $purchase->modelname->model_name }}"
                                                readonly>
                                            @error('brand_model_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="accessories_list_id">{{ __('label.ACCESSORIES_LIST') }}</label>
                                            <div class="border-checkbox-section">
                                                @if (!empty($accessories_list))
                                                    @foreach ($accessories_list as $accessories_item)
                                                        <div class="border-checkbox-group border-checkbox-group-success">
                                                            <input class="border-checkbox" type="checkbox"
                                                                name="accessories_list_id[]"
                                                                id="checkbox_accessories_item{{ $accessories_item->id }}"
                                                                value="{{ $accessories_item->id }}">
                                                            <label class="border-checkbox-label"
                                                                for="checkbox_accessories_item{{ $accessories_item->id }}">{{ $accessories_item->accessories_name }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @error('accessories_list_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="product_condition_id">{{ __('label.PRODUCT_CONDITION') }}</label>
                                            <div class="border-checkbox-section">
                                                @if (!empty($product_conditions))
                                                    @foreach ($product_conditions as $product_condition)
                                                        <div class="border-checkbox-group border-checkbox-group-success">
                                                            <input class="border-checkbox" type="checkbox"
                                                                name="product_condition_id[]"
                                                                id="checkbox_product_condition{{ $product_condition->id }}"
                                                                value="{{ $product_condition->id }}">
                                                            <label class="border-checkbox-label"
                                                                for="checkbox_product_condition{{ $product_condition->id }}">{{ $product_condition->product_condition }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @error('product_condition_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="fault_description_id">{{ __('label.FAULT_DESCRIPTION') }}</label>
                                        <div class="border-checkbox-section">
                                            @if (!empty($faults))
                                                @foreach ($faults as $fId => $fault)
                                                    <div class="border-checkbox-group border-checkbox-group-success">
                                                        <input class="border-checkbox" type="checkbox"
                                                            name="fault_description_id[]"
                                                            id="checkbox_fault_description{{ $fId }}"
                                                            value="{{ $fId }}">
                                                        <label class="border-checkbox-label"
                                                            for="checkbox_fault_description{{ $fId }}">{{ $fault }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        @error('fault_description_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label
                                            for="fault_description_note">{{ __('label.FAULT_DESCRIPTION_NOTE') }}</label>
                                        <input name="fault_description_note" type="text" class="form-control"
                                            value="{{ old('fault_description_note') }}">
                                        @error('fault_description_note')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="brand_id">{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS') }}<span
                                                class="text-red">*</span></label>
                                        <input name="brand_id" placeholder="" type="text"
                                            class="form-control bg-success {{ $purchase->general_warranty_date->format('Y-m-d') < $currentDate ? 'bg-danger text-white' : '' }}"
                                            style="<?php ?>"
                                            value="{{ $purchase->general_warranty_date->format('m/d/Y') }}" readonly>
                                        @error('brand_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label
                                            for="special_warranty_date">{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS') }}<span
                                                class="text-red">*</span></label>
                                        <input name="special_warranty_date" placeholder="" type="text"
                                            class="form-control bg-success {{ $purchase->special_warranty_date->format('Y-m-d') < $currentDate ? 'bg-danger text-white' : '' }}"
                                            value="{{ $purchase->special_warranty_date->format('m/d/Y') }}" readonly>
                                        @error('special_warranty_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="brand_id">{{ __('label.WARRANTY_END_FOR_SERVICE') }}<span
                                                class="text-red">*</span></label>
                                        <input name="brand_id" placeholder="" type="text"
                                            class="form-control bg-success {{ $purchase->service_warranty_date->format('Y-m-d') < $currentDate ? 'bg-danger text-white' : '' }}"
                                            value="{{ $purchase->service_warranty_date->format('m/d/Y') }}" readonly>

                                        @error('brand_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="form-group border p-3" style="background: #eff6ff">
                                <legend class="w-auto">Ticket Area</legend>

                                <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <label for="outlet_id">{{ __('label.OUTLET') }}<span
                                                class="text-red">*</span></label>
                                        <select name="outlet_id" id="" class="form-control select2">
                                            <option value="">Select Branch</option>
                                            @forelse ($outlets as $outlet)
                                                <option value="{{ $outlet->id }}"
                                                    @if (old('outlet_id') == $outlet->id) selected @endif>
                                                    {{ $outlet->name }}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse

                                        </select>
                                        @error('outlet_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="job_priority_id">{{ __('label.JOB_PRIORITY') }}<span
                                                class="text-red">*</span></label>
                                        <select name="job_priority_id" id="" class="form-control select2">
                                            <option value="">Select</option>
                                            @forelse ($job_priorities as $item)
                                                <option value="{{ $item->id }}"
                                                    @if (old('job_priority_id') == $item->id) selected @endif>
                                                    {{ $item->job_priority }}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse

                                        </select>
                                        @error('job_priority_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="service_type_id">{{ __('label.SERVICE_TYPE') }}<span
                                                class="text-red">*</span></label>
                                        <select name="service_type_id[]" id="service_type_id"
                                            class="form-control select2" multiple>
                                            <option value="">Select</option>
                                            @forelse ($serviceTypes as $serviceType)
                                                <option value="{{ $serviceType->id }}"
                                                    @if (old('service_type_id') == $serviceType->id) selected @endif>
                                                    {{ $serviceType->service_type }}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse
                                        </select>
                                        @error('service_type_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="warranty_type_id">{{ __('label.WARRANTY_TYPE') }}<span
                                                class="text-red">*</span></label>
                                        <select name="warranty_type_id" id="warranty_type_id"
                                            class="form-control select2">
                                            <option value="">Select</option>
                                            @forelse ($warrantyTypes as $warrantyType)
                                                <option value="{{ $warrantyType->id }}"
                                                    @if (old('warranty_type_id') == $warrantyType->id) selected @endif>
                                                    {{ $warrantyType->warranty_type }}
                                                </option>
                                            @empty
                                                <option value="">No Data Found</option>
                                            @endforelse
                                        </select>
                                        @error('warranty_type_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <label for="address">{{ __('label.EXPECTED_SCHEDULE') }}</label>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="start_date">{{ __('label.START_DATE') }}<span
                                                class="text-red">*</span></label>
                                        <input name="start_date" type="date" class="form-control"
                                            value="{{ currentDate() }}">
                                        @error('start_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="end_date">{{ __('label.END_DATE') }}<span
                                                class="text-red">*</span></label>
                                        <input name="end_date" type="date" class="form-control"
                                            value="{{ currentDate() }}">
                                        @error('end_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="customer_note">{{ __('label.CUSTOMER_NOTE') }}</label>
                                        <input name="customer_note" type="text" class="form-control"
                                            value="{{ old('customer_note') }}">
                                        @error('customer_note')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="product_receive_mode_id">{{ __('label.PRODUCT_RECEIVE_MODE') }}<span
                                                class="text-red">*</span></label>
                                        <select name="product_receive_mode_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            @if (!empty($receiveModes))
                                                @forelse($receiveModes as $receiveMode)
                                                    <option value="{{ $receiveMode->id }}"
                                                        @if (old('product_receive_mode_id') == $receiveMode->id) selected @endif>
                                                        {{ $receiveMode->name }}
                                                    </option>
                                                @empty
                                                    <option value="">No Data Found</option>
                                                @endforelse
                                            @endif
                                        </select>
                                        @error('product_receive_mode_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label
                                            for="expected_delivery_mode_id">{{ __('label.EXPECTED_DELIVERY_MODE') }}<span
                                                class="text-red">*</span></label>
                                        <select name="expected_delivery_mode_id" id="" class="form-control">
                                            <option value="">Select</option>
                                            @if (!empty($deliveryModes))
                                                @forelse($deliveryModes as $deliveryMode)
                                                    <option value="{{ $deliveryMode->id }}"
                                                        @if (old('expected_delivery_mode_id') == $deliveryMode->id) selected @endif>
                                                        {{ $deliveryMode->name }}
                                                    </option>
                                                @empty
                                                    <option value="">No Data Found</option>
                                                @endforelse
                                            @endif
                                        </select>
                                        @error('expected_delivery_mode_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="service_charge">{{ __('label.SERVICE_CHARGE') }}<span
                                                class="text-red">*</span></label>
                                        <input name="service_charge" type="text" class="form-control"
                                            id="service_charge" value="" readonly>
                                        @error('service_charge')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group border p-3" style="background: #fffbeb">
                                <legend class="w-auto">Attachment Area</legend>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group control-group increment">
                                            <input type="file" name="image[]" class="form-control">
                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <div class="input-group-btn">
                                                <button class="btn btn-success pl-1 ml-5" type="button"><i class="fa fa-plus" aria-hidden="true"></i>
                                                </i>Add More</button>
                                            </div>
                                        </div>
                                        <div class="clone hide">
                                            <div class="control-group input-group" style="margin-top:10px">
                                                <input type="file" name="image[]" class="form-control">
                                                @error('image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="input-group-btn">
                                                    <button class="btn btn-danger pl-1 ml-5" type="button"><i class="fa fa-minus" aria-hidden="true"></i> Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group border p-3" style="background: #eff6ff">
                                <legend class="w-auto">SMS Area</legend>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="position-relative form-group">
                                            <div class="border-checkbox-section">
                                                <div class="border-checkbox-group border-checkbox-group-success">
                                                    <input class="border-checkbox" type="checkbox" name="send_sms"
                                                        id="send_sms" value="1">
                                                    <label class="border-checkbox-label" for="send_sms">
                                                        Send SMS to the customer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <button class="mt-2 btn btn-primary button-prevent-multiple-submits"><i
                                    class="spinner fa fa-spinner fa-spin"></i>Submit</button>
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
        var ses = "{{ Session::get('tahanaId') }}"
        let dist_id = $('#district').val();

        if (dist_id) {
            $.ajax({
                url: "{{ url('general/get/thana/') }}/" + dist_id,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    $('#thana').empty();
                    $.each(data, function(key, value) {
                        if (value.id == ses) {
                            $('#thana').append("<option selected value=" + value.id + ">" + value.name +
                                "</option>");
                        }
                        $('#thana').append("<option value=" + value.id + ">" + value.name +
                            "</option>");
                    });
                }
            });
        }

        $(document).ready(function() {
            $("#carrier_own").attr("disabled", true);
            $('#carrier').on('change', function() {
                var carrier = $(this).val();
                if (carrier != null) {
                    $("#carrier_own").removeAttr("disabled");
                } else {
                    $("#carrier_own").attr('disabled', 'disabled');
                }
            });


            $('#district').on('change', function() {
                var district_id = $(this).val();
                if (district_id) {
                    $.ajax({
                        url: "{{ url('general/get/thana/') }}/" + district_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data) {
                            $('#thana').empty();
                            $.each(data, function(key, value) {

                                $('#thana').append("<option value=" + value.id + ">" +
                                    value.name + "</option>");
                            });
                        }
                    });
                }
            });

            $('#service_type_id').on('change', function() {
                var service_type_id = $(this).val();
                if (service_type_id) {
                    $.ajax({
                        url: "{{ url('tickets/get/service/amount/') }}/",
                        type: 'GET',
                        data: {
                            id: service_type_id,
                        },
                        dataType: "json",
                        success: function(data) {
                            $('#service_charge').empty();
                            $('#service_charge').val(data);
                        }
                    });
                }
            });

            $("body").on("click", ".btn-success", function() {
                var html = $(".clone").html();
                $(".increment").after(html);
            });

            $("body").on("click", ".btn-danger", function() {
                $(this).closest(".control-group").remove();
            });
        });
    </script>
@endsection
