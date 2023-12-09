@extends('layouts.main')
@section('title', 'Edit Part Sale')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.EDIT PARTS SELL') }}</h5>
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
            @include('include.message')
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-body">
                        {{ Form::open(['route' => ['sell.update.direct-parts-sell', $partSell->id], 'method' => 'POST', 'class' => 'form-horizontal form-prevent-multiple-submits']) }}
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <label for="date">{{ __('label.MR_NO') }}</label>
                                <div>
                                    <input type="text" class="form-control" id="mr_no" name="mr_no"
                                        value="{{ $partSell->mr_no }}" placeholder="MR No" readonly>
                                </div>
                                @error('mr_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="sales_by">{{ __('label.SALES BY') }}</label>
                                <div>
                                    <input type="text" class="form-control" id="sales_by" name="sales_by"
                                        value="{{ $partSell->sales_by }}" placeholder="Sales by" readonly>
                                    <input type="hidden" name="part_sell_id" id="part_sell_id"
                                        value="{{ $partSell->id }}">
                                </div>
                                @error('sales_by')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="date">{{ __('label.DATE') }}</label>
                                <div>
                                    <input type="date" class="form-control" id="date" name="date"
                                        value="{{ $partSell->date->format('Y-m-d') }}" placeholder="Date Name" required>
                                </div>
                                @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="date">{{ __('label.STORE') }}</label>
                                @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                    <select name="store_id" id="store" class="form-control" required>
                                        <option value="">Select a store</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ $store->id == $partSell->store_id ? 'selected' : '' }}>
                                                {{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" name="" value="{{ $mystore->name }}"
                                        readonly>
                                    <input type="hidden" id="store" name="store_id" value="{{ $mystore->id }}">
                                @endif
                                @error('store_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-2">
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div>
                            <h6><strong>Customer Info:</strong></h6>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="customer_name">{{ __('label.CUSTOMER NAME') }}</label>
                                <div>
                                    <select name="customer_id" id="customer_id" class="form-control customer_search"
                                        required>
                                        <option value="{{ $partSell->customer_id }}">
                                            {{ $partSell->customer->name . '-' . $partSell->customer->mobile }}</option>
                                    </select>
                                </div>
                                @error('customer_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_phone">{{ __('label.CUSTOMER MOBILE') }}</label>
                                <div>
                                    <input type="number" class="form-control" id="customer_phone" name="customer_phone"
                                        value="{{ $partSell->customer_phone }}" placeholder="Customer Phone" readonly
                                        required>
                                </div>
                                @error('customer_phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-4">
                                <label for="customer_address">{{ __('label.CUSTOMER ADDRESS') }}</label>
                                <div>
                                    <textarea name="customer_address" id="customer_address" class="form-control" cols="12" rows="1" readonly
                                        required>{{ $partSell->customer_address }}</textarea>
                                </div>
                                @error('customer_address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="parts">{{ __('Parts Name') }}</label>
                                <select name="parts_id[]" id="parts_id" class="form-control select2" multiple disabled>
                                    <option value="">Select parts</option>

                                    @foreach ($parts as $key => $part)
                                        <option value="{{ $part->id }}"
                                            @foreach ($selectedParts as $selectedPart)
                                                    @if ($selectedPart->id == $part->id)
                                                        selected
                                                    @endif @endforeach>
                                            {{ $part->code }}-{{ $part->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('parts_id[]')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12" id="parts_data">

                            </div>
                        </div>
                        <div class="row mt-30">
                            <div class="col-sm-12 text-center">
                                <button class="mt-2 btn btn-primary button-prevent-multiple-submits"><i
                                        class="spinner fa fa-spinner fa-spin"></i>Submit</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $(".customer_search").select2({
                    placeholder: "Search for a customer...",
                    ajax: {
                        url: "{{ route('call-center.customer_data') }}",
                        type: "post",
                        delay: 250,
                        dataType: 'json',
                        data: function(params) {
                            return {
                                query: params.term, // search term
                                "_token": "{{ csrf_token() }}",
                            };
                        },
                        processResults: function(response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
                $('#customer_id').on('change', function() {
                    var customer_id = $('#customer_id').val();
                    var url = "{{ route('sell.get-customer-info') }}";

                    $.ajax({
                        type: "get",
                        url: url,
                        data: {
                            customer_id: customer_id
                        },
                        success: function(data) {
                            $('#customer_phone').val(data.customer.mobile);
                            $('#customer_address').val(data.customer.address);
                        }
                    });
                });
            });

            var parts_id = $("#parts_id").val();
            // alert(parts_id);
            var store_id = $('#store').val();
            var part_sell_id = $('#part_sell_id').val();
            var url = "{{ url('sell/parts-sell/row-for-edit') }}";

            $.ajax({
                type: "get",
                url: url,
                data: {
                    parts_id: parts_id,
                    // id:id,
                    store_id: store_id,
                    part_sell_id: part_sell_id,
                },
                success: function(data) {
                    // console.log(data);
                    $("#parts_data").html(data.html);
                }
            });

            $('#parts_id').on('change', function(e) {
                // var id = row_counter++;
                e.preventDefault();
                var parts_id = $("#parts_id").val();
                // alert(parts_id);
                var store_id = $('#store').val();
                var part_sell_id = $('#part_sell_id').val();
                var url = "{{ url('sell/parts-sell/row-for-edit') }}";

                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        parts_id: parts_id,
                        // id:id,
                        store_id: store_id,
                        part_sell_id: part_sell_id,
                    },
                    success: function(data) {
                        $("#parts_data").html(data.html);
                        // window.location = url;
                    }
                });

            });
        </script>
    @endpush
@endsection
