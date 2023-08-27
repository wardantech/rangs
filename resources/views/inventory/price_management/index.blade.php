@extends('layouts.main')
@section('title', 'Price Management')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.PRICE MANAGEMENT')}}</h5>
                            <span>{{ __('label.LIST OF PRICE MANAGEMENT')}}</span>
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
                        <h3>@lang('label.PRICE MANAGEMENT')</h3>
                        <div style="margin: 0 auto">
                            <a href="{{route('inventory.sample_price_excel')}}" class="btn btn-success">Sample Excel Download</a>
                        </div>
                        <div style="margin: 0 auto">
                            <form action="{{route('inventory.import_price')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div>
                                {{-- <label for="" class="badge badge-danger">Import</label> --}}
                                <input type="file" name="import_file" required>
                                @if ($errors->has('file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                @endif
                                <input type="submit" class="btn btn-success" value="Import">
                            </div>
                            </form>
                        </div>
                        @can('create')
                            <div class="card-header-right">
                                <a class="btn btn-info" href="{{route('inventory.price-management.create')}}">  @lang('label._CREATE')</a>
                            </div>
                        @endcan

                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL')}}</th>
                                    <th>{{ __('label.PARTS CODE')}}</th>
                                    <th>{{ __('label.PARTS_DESCRIPTION')}}</th>
                                    <th>{{ __('label.PARTS MODEL')}}</th>
                                    <th>{{ __('label.BUYING PRICE (USD)')}}</th>
                                    <th>{{ __('label.BUYING PRICE (BDT)')}}</th>
                                    <th>{{ __('label.SELLING PRICE (BDT)')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @php($i=1)
                                @foreach($priceManagementRows as $priceManagement)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$priceManagement['part_code']}}</td>
                                        <td>{{$priceManagement['part_name']}}</td>
                                        <td>{{$priceManagement['model_name']}}</td>
                                        <td>{{$priceManagement['cost_price_usd']}}</td>
                                        <td>{{$priceManagement['cost_price_bdt']}}</td>
                                        <td>{{$priceManagement['selling_price_bdt']}}</td>
                                        <td>
                                            <div class='text-center'>
                                                    <a href="{{route('inventory.price-management-history', $priceManagement['id'])}}" class="show-priceManagement" title="History">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
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
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

    <script>
        $(document).ready( function () {
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
                    url: "{{route('inventory.price-management.index')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'name', name: 'name', orderable: true, searchable: true},
                    {data:'code', name: 'code', orderable: true, searchable: true},
                    {data:'partmodel', name: 'partmodel', orderable: true, searchable: true},
                    {data:'cost_price_usd', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'cost_price_bdt', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'selling_price_bdt', name: 'selling_price_bdt', orderable: true, searchable: true},
                    {data:'action', name: 'action',  orderable: false, searchable: false}

                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Price',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Price',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Price',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Price',
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
                                title: 'Price',
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

@endsection
