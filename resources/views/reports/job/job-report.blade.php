@extends('layouts.main')
@section('title', 'Job Report')
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
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.REPORT')}}</h5>
                            <span>{{ __('label.JOB_REPORT')}}</span>
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
                    <form class="forms-sample" action="{{ route('report.job-report-post') }}" method="get">
                        
                        <div class="row pt-5">
                            <div class="col-md-4">
                                <label for="inputpc" class="">Product Category :</label>
                                <select name="product_category" id="mySelect2" class="form-control js-data-example-ajax">

                                </select>
                                @error('product_category')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="inputpc" class="">Start Date :</label>
                                <input type="date" class="form-control" name="start_date" value="">
                            </div>
                            <div class="col-md-4">
                                <label for="inputpc" class="">End Date :</label>
                                <input type="date" class="form-control" name="end_date" value="">
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
                        <h3>@lang('label.JOB_REPORT')</h3>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th rowspan="2">{{ __('label.SL')}}</th>
                                    <th rowspan="2">{{ __('label.OUTLET')}}</th>
                                    <th colspan="10">{{ __('label.RECEIVED')}}</th>
                                    <th colspan="10">{{ __('label.REPAIRED')}}</th>
                                    <th colspan="10">{{ __('label.DELIVERED')}}</th>
                                    <th colspan="10">{{ __('label.PENDING')}}</th>
                                </tr>
                                <tr>
                                    <th>{{ __('label.AC')}}</th>
                                    <th>{{ __('label.APPLIANCE')}}</th>
                                    <th>{{ __('label.AV SYSTEM')}}</th>
                                    <th>{{ __('label.CAMERA')}}</th>
                                    <th>{{ __('label.MOBILE')}}</th>
                                    <th>{{ __('label.LTV')}}</th>
                                    <th>{{ __('label.PROFESSIONAL')}}</th>
                                    <th>{{ __('label.WATER_PURIFIER')}}</th>
                                    <th>{{ __('label.OTH')}}</th>
                                    <th>{{ __('label.DAYS_TT')}}</th>

                                    <th>{{ __('label.AC')}}</th>
                                    <th>{{ __('label.APPLIANCE')}}</th>
                                    <th>{{ __('label.AV SYSTEM')}}</th>
                                    <th>{{ __('label.CAMERA')}}</th>
                                    <th>{{ __('label.MOBILE')}}</th>
                                    <th>{{ __('label.LTV')}}</th>
                                    <th>{{ __('label.PROFESSIONAL')}}</th>
                                    <th>{{ __('label.WATER_PURIFIER')}}</th>
                                    <th>{{ __('label.OTH')}}</th>
                                    <th>{{ __('label.DAYS_TT')}}</th>

                                    <th>{{ __('label.AC')}}</th>
                                    <th>{{ __('label.APPLIANCE')}}</th>
                                    <th>{{ __('label.AV SYSTEM')}}</th>
                                    <th>{{ __('label.CAMERA')}}</th>
                                    <th>{{ __('label.MOBILE')}}</th>
                                    <th>{{ __('label.LTV')}}</th>
                                    <th>{{ __('label.PROFESSIONAL')}}</th>
                                    <th>{{ __('label.WATER_PURIFIER')}}</th>
                                    <th>{{ __('label.OTH')}}</th>
                                    <th>{{ __('label.DAYS_TT')}}</th>

                                    <th>{{ __('label.AC')}}</th>
                                    <th>{{ __('label.APPLIANCE')}}</th>
                                    <th>{{ __('label.AV SYSTEM')}}</th>
                                    <th>{{ __('label.CAMERA')}}</th>
                                    <th>{{ __('label.MOBILE')}}</th>
                                    <th>{{ __('label.LTV')}}</th>
                                    <th>{{ __('label.PROFESSIONAL')}}</th>
                                    <th>{{ __('label.WATER_PURIFIER')}}</th>
                                    <th>{{ __('label.OTH')}}</th>
                                    <th>{{ __('label.DAYS_TT')}}</th>
                                    {{-- <th>{{ __('label.OUTLET')}}</th>
                                    <th>{{ __('label.PRODUCT_CATEGORY')}}</th>
                                    <th>{{ __('label.RECEIVED')}}</th>
                                    <th>{{ __('label.REPAIRED')}}</th>
                                    <th>{{ __('label.DELIVERD')}}</th>
                                    <th>{{ __('label.PENDING')}}</th> --}}
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
            // $('input[name="dates"]').daterangepicker();
            // Initialize select2
            $(".js-data-example-ajax").select2({
                placeholder: "Search for a Category",
                ajax: {
                    url: "{{route('inventory.get_categories')}}",
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
                    url: "{{route('report.job-report-get')}}",
                    type: "POST"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'name', name: 'name', orderable: true, searchable: true},
                    // {data:'ltv_category', name: 'ltv_category', orderable: true, searchable: true},
                    
                    {data:'ac_received', name: 'ac_received', orderable: true, searchable: true},
                    {data:'appliance_received', name: 'appliance_received', orderable: true, searchable: true},
                    {data:'av_systems_received', name: 'av_systems_received', orderable: true, searchable: true},
                    {data:'camera_received', name: 'camera_received', orderable: true, searchable: true},
                    {data:'mobiles_received', name: 'mobiles_received', orderable: true, searchable: true},
                    {data:'ltv_received', name: 'ltv_received', orderable: true, searchable: true},
                    {data:'professionals_received', name: 'professionals_received', orderable: true, searchable: true},
                    {data:'water_purifier_received', name: 'water_purifier_received', orderable: true, searchable: true},
                    {data:'others_received', name: 'others_received', orderable: true, searchable: true},
                    {data:'days_tat_received', name: 'days_tat', orderable: true, searchable: true},


                    {data:'ac_repaired', name: 'ac_repaired', orderable: true, searchable: true},
                    {data:'appliance_repaired', name: 'appliance_repaired', orderable: true, searchable: true},
                    {data:'av_systems_repaired', name: 'av_systems_repaired', orderable: true, searchable: true},
                    {data:'camera_repaired', name: 'camera_repaired', orderable: true, searchable: true},
                    {data:'mobiles_repaired', name: 'mobiles_repaired', orderable: true, searchable: true},
                    {data:'ltv_repaired', name: 'ltv_repaired', orderable: true, searchable: true},
                    {data:'professionals_repaired', name: 'professionals_repaired', orderable: true, searchable: true},
                    {data:'water_purifier_repaired', name: 'water_purifier_repaired', orderable: true, searchable: true},
                    {data:'others_repaired', name: 'others_repaired', orderable: true, searchable: true},
                    {data:'days_tat_repaired', name: 'others_repaired', orderable: true, searchable: true},


                    {data:'ac_delivered', name: 'ac_delivered', orderable: true, searchable: true},
                    {data:'appliance_delivered', name: 'appliance_delivered', orderable: true, searchable: true},
                    {data:'av_systems_delivered', name: 'av_systems_delivered', orderable: true, searchable: true},
                    {data:'camera_delivered', name: 'camera_delivered', orderable: true, searchable: true},
                    {data:'mobiles_delivered', name: 'mobiles_delivered', orderable: true, searchable: true},
                    {data:'ltv_delivered', name: 'ltv_delivered', orderable: true, searchable: true}, 
                    {data:'others_delivered', name: 'others_delivered', orderable: true, searchable: true},
                    {data:'professionals_delivered', name: 'professionals_delivered', orderable: true, searchable: true},
                    {data:'water_purifier_delivered', name: 'water_purifier_delivered', orderable: true, searchable: true},
                    {data:'days_tat_delivered', name: 'water_purifier_delivered', orderable: true, searchable: true},      


                    {data:'ac_pending', name: 'ac_pending', orderable: true, searchable: true},
                    // {data:'ac_pending', name: 'ac_pending', orderable: true, searchable: true},
                    {data:'appliance_pending', name: 'appliance_pending', orderable: true, searchable: true},
                    {data:'av_systems_pending', name: 'av_systems_pending', orderable: true, searchable: true},
                    {data:'camera_pending', name: 'camera_pending', orderable: true, searchable: true},
                    {data:'mobiles_pending', name: 'mobiles_pending', orderable: true, searchable: true},
                    {data:'ltv_pending', name: 'ltv_pending', orderable: true, searchable: true},
                    {data:'professionals_pending', name: 'professionals_pending', orderable: true, searchable: true},
                    {data:'water_purifier_pending', name: 'water_purifier_pending', orderable: true, searchable: true},
                    {data:'others_pending', name: 'others_pending', orderable: true, searchable: true},
                    {data:'days_tat_pending', name: 'others_pending', orderable: true, searchable: true},

                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Job',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Job',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Job',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Job',
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
                                title: 'Job',
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
