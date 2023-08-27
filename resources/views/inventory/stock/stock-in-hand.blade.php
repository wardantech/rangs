@extends('layouts.main')
@section('title', 'Stock In Hand')
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
                            <h5>{{ __('Stock In Hand')}}</h5>
                            <span>{{ __('label.PARTS_STOCK')}}</span>
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
                @can('create')
                    <form class="forms-sample" method="get" action="{{ route('inventory.stock-in-hand-all') }}">
                        
                        <div class="row pt-5">
                            <div class="col-md-3">
                                <label for="inputpc" class="">Part :</label>
                                <select name="part" id="mySelect2" class="form-control js-data-example-ajax">

                                </select>
                                @error('part')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="inputpc" class="">Start Date :</label>
                                <input type="date" class="form-control" name="start_date" value="{{ currentDate() }}">
                            </div>
                            <div class="col-md-3">
                                <label for="inputpc" class="">End Date :</label>
                                <input type="date" class="form-control" name="end_date" value="{{ currentDate() }}">
                            </div>
                            <div class="col-md-3">
                                <label for="inputpc" class="">Store :</label>
                                {{-- <input type="text" class="form-control" id="part_model" name="part_model" placeholder="Parts Model" value="{{ old('name') }}"> --}}
                                <select name="store" id="store" class="form-control select2">
                                    <option value="">Select</option>
                                    @foreach ($stores as $store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('store'))
                                    <span class="is-invalid"
                                        <strong>{{ $errors->first('store') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-md-12 text-center">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary mt-3">{{ __('Submit')}}</button>
                            </div>
                            </div>
                        </div>
                    </form>
                @endcan
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('Total Part Stock')</h3>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PARTS CODE')}}</th>
                                    <th>{{ __('Part Decription')}}</th>
                                    <th>{{ __('label.MODEL')}}</th>
                                    <th>{{ __('Purchase Price USD')}}</th>
                                    <th>{{ __('Purchase Price BDT')}}</th>
                                    <th>{{ __('label.SELLING_PRICE')}}</th>
                                    <th>{{ __('label.PRESENT_BALANCE_QNTY')}}</th>
                                    <th>{{ __('label.STOCK_VALUE_USD')}}</th>
                                    <th>{{ __('label.STOCK_VALUE_BDT')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
   

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

            var searchable = [];
            var selectable = [];

            $.ajaxSetup({
                headers:{
                    "X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),
                }
            });

            var dTable = $('#datatable').DataTable({
                order: [],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                processing: true,
                responsive: false,
                serverSide: true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('inventory.stock-in-hand')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'code', name: 'code', orderable: true, searchable: true},
                    {data:'name', name: 'name', orderable: true, searchable: true},
                    {data:'partmodel', name: 'partmodel', orderable: true, searchable: true},
                    {data:'cost_price_usd', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'cost_price_bdt', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'selling_price_bdt', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'balance', name: 'balance', orderable: true, searchable: true},
                    {data:'stockvalueusd', name: 'stockvalueusd', orderable: true, searchable: true},
                    {data:'stockvaluebdt', name: 'stockvaluebdt', orderable: true, searchable: true},

                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Stock in Hand',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Stock in Hand',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Stock in Hand',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Stock in Hand',
                                pageSize: 'A2',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn-sm btn-default',
                                title: 'Stock in Hand',
                                // orientation:'landscape',
                                pageSize: 'A2',
                                header: true,
                                footer: false,
                                orientation: 'landscape',
                                exportOptions: {
                                    // columns: ':visible',
                                    stripHtml: false
                                }
                            }
                        ],
            });
        });
</script>
 @endpush
@endsection
