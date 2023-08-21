@extends('layouts.main')
@section('title', 'Receive Parts')

 <!-- push external head elements to head -->
 @push('head')

@endpush

@section('content')
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
            <div class="col-md-12" id="ajax">
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Receive Parts')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('create-inventory') }}" class="form-prevent-multiple-submits" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="invoice_number">
                                            {{ __('label.INVOICE_NUMBER')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{old('invoice_number')}}" placeholder="Invoice Number" required>

                                        @error('invoice_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="po_number">{{ __('label.PO_NUMBER')}}</label>
                                        <input type="text" class="form-control" id="po_number" name="po_number" value="{{old('po_number')}}" placeholder="PO Number">

                                        @error('po_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="lc_number">{{ __('label.LC_NUMBER')}}</label>
                                        <input type="text" class="form-control" id="lc_number" name="lc_number" value="{{old('lc_number')}}" placeholder="LC Number">

                                        @error('lc_number')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="receiveDate">
                                            {{ __('label.RECEIVE_DATE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="receiveDate" type="date" class="form-control @error('receive_date') is-invalid @enderror" name="receive_date" value="{{ currentDate() }}" required>

                                        @error('receive_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="vendor_id">
                                            {{ __('label.SELECT_VENDOR')}}
                                        </label>
                                        {!! Form::select('vendor_id', $vendors, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_VENDOR_OPT'),'id'=> 'vendor_id']) !!}

                                        @error('vendor_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="source">
                                            Select Source
                                        </label>
                                        <select name="source_id" id="" class="form-control">
                                            <option value="">Select Source</option>
                                            @foreach($sources as $source)
                                                <option value="{{$source->id}}"
                                                    @if (old('source_id') == $source->id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$source->name}}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('source_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
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
                                        @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                        <select name="store_id" id="store" class="form-control">
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input type="hidden" class="form-control" id="store" name="store_id" value="{{ $mystore->id }}" readonly>
                                        <input type="text" class="form-control" id="store_id" value="{{ $mystore->name }}" readonly>
                                        @endif

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
                                        <select name="parts_id[]" id="parts_id" class="form-control js-data-example-ajax" multiple required>

                                        </select>
                                        @error('parts_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12" id="parts_data">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="note">{{ __('Note')}}</label>
                                        <textarea name="note" id="note" class="form-control" cols="30" rows="2"></textarea>
                                        @error('note')
                                            <div class="text-danger">{{ $message }}</div>
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
<script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>

<script type="text/javascript">
        $(document).ready(function() {
            
            // Initialize select2
            $(".js-data-example-ajax").select2({
                placeholder: "Search for an Item",
                ajax: {
                    url: "{{route('inventory.get_parts')}}",
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
        });
    $(document).ready( function() {
        //Validation for part select option
        var store=$('#store').val();
        if(store){
            $("#parts_id").prop('disabled', false);
        }else{
            $("#parts_id").prop('disabled', true);
            $('#store').on('change', function(e){
                $("#parts_id").prop('disabled', false);
            }) 
        }

    $('#parts_id').on('change', function(e){
        e.preventDefault();
        var parts_id = $("#parts_id").val();
        var store_id=$('#store').val();
        var url = "{{ url('inventory/parts-receive/rows') }}";

        $.ajax({
            type: "get",
            url: url,
            data: {
                parts_id: parts_id,
                store_id: store_id,
            },
            success: function(data) {
                $("#parts_data").html(data.html);
            }
        });

    });

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
