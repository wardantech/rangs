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
                            <h3>{{ $store ? $store->name :'' }} @lang('label.PARTS_STOCK')</h3>
                            <input type="hidden" id="store_id" value="{{ $store->id }}">
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
                <div class="card p-3">
                    <div class="card-header">
                        <h3>{{ $store ? $store->name :'' }} @lang('label.PARTS_STOCK')</h3>
                        <div class="card-header-right">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-danger" title="Go Back"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
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
    @push('script')
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>


        <script type="text/javascript">
            $(document).ready( function () {
                var storeId = $('#store_id').val();
                var url = '{{ route("inventory.stock-in-hand-all-by-store",":id") }}';

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
                        ajax: {
                            url: url.replace(':id', storeId),
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
                                        title: 'Stock',
                                        header: false,
                                        footer: true,
                                        exportOptions: {
                                            // columns: ':visible'
                                        }
                                    },
                                    {
                                        extend: 'csv',
                                        className: 'btn-sm btn-success',
                                        title: 'Stock',
                                        header: true,
                                        footer: true,
                                        exportOptions: {
                                            // columns: ':visible'
                                        }
                                    },
                                    {
                                        extend: 'excel',
                                        className: 'btn-sm btn-warning',
                                        title: 'Stock',
                                        header: true,
                                        footer: true,
                                        exportOptions: {
                                            // columns: ':visible',
                                        }
                                    },
                                    {
                                        extend: 'pdf',
                                        className: 'btn-sm btn-primary',
                                        title: 'Stock',
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
                                        title: 'Stock',
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
