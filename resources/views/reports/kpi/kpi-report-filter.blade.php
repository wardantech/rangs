@extends('layouts.main')
@section('title', 'Kpi Report')
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
                            <span>{{ __('label.KPI_REPORT')}}</span>
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
                        <h3>@lang('label.KPI_REPORT')</h3>
                        <div class="card-header-right">
                            <h3>{{ $product_category_name ? $product_category_name->name : '' }}</h3>
                            <h3>{{ $branch_name ? $branch_name->name : '' }}</h3>
                            <h3>{{ $technician_name ? $technician_name->name : '' }}</h3>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.TECHNICIAN')}}</th>
                                    <th>{{ __('label.OUTLET')}}</th>
                                    <th>{{ __('label.TICKET_SL')}}</th>
                                    <th>{{ __('label.TICKET_CREATED_AT')}}</th>
                                    <th>{{ __('Purchase Date')}}</th>
                                    <th>{{ __('label.JOB_NO')}}</th>
                                    <th>{{ __('label.ASSIGNED_BY')}}</th>
                                    <th>{{ __('label.BRAND_MODEL')}}</th>
                                    <th>{{ __('label.FAULT_DESCRIPTION')}}</th>
                                    <th>{{ __('label.FAULT_DESCRIPTION_NOTE')}}</th>
                                    <th>{{ __('label.JOB ENDING REMARK')}}</th>
                                    <th>{{ __('label.REPAIR_DESCRIPTION')}}</th>
                                    <th>{{ __('Ticket Date')}}</th>
                                    <th>{{ __('label.RECEIVED_DATE')}}</th>
                                    <th>{{ __('label.REPAIR_DATE')}}</th>
                                    <th>{{ __('Delivery Date TL')}}</th>
                                    <th>{{ __('label.PART_CHANGED')}}</th>
                                    <th>{{ __('label.PART_NAME')}}</th>
                                    <th>{{ __('label.PART_CODE')}}</th>
                                    <th>{{ __('label.STATUS')}}</th>
                                    <th>{{ __('label.REPAIR_TAT')}}</th>
                                    <th>{{ __('label.DELIVERY_TAT')}}</th>
                                    <th>{{ __('label.REPEAT_REPAIR')}}</th>
                                    <th>{{ __('label.LTP')}}</th>
                                    <th>{{ __('label.CMI')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sl = 0;
                                ?>
                                @foreach($jobinfo as $key => $item)
                                <tr>
                                    <td>{{++$sl}}</td>
                                    <td>{{$item['employee_name']}}</td>
                                    <td>{{$item['outlet_name']}}</td>
                                    <td>{{$item['ticket_sl']}}</td>
                                    <td>{{$item['created_at']}}</td>
                                    <td>{{$item['purchase_date']}}</td>
                                    <td>{{$item['job_number']}}</td>
                                    <td>{{$item['assigned_by']}}</td>
                                    <td>{{$item['model_name']}}</td>
                                    <td>{{$item['fault_description']}}</td>

                                    <td>{{$item['fault_description_note']}}</td>
                                    <td>{{$item['job_ending_remark']}}</td>

                                    <td>{{$item['repair_description']}}</td>
                                    <td>{{$item['ticket_date']}}</td>
                                    <td>{{$item['job_assigned_date']}}</td>
                                    <td>{{$item['repair_date']}}</td>
                                    <td>{{$item['delivery_date']}}</td>
                                    <td>{{$item['yes_no']}}</td>
                                    <td>{{$item['part_name']}}</td>
                                    <td>{{$item['part_code']}}</td>
                                    <td>
                                        @if ($item['status']== 6 && $item['is_pending'] == 1 && $item['job_pending_note'] !=null)
                                        <span class="badge bg-orange">Pending</span>
                                        @elseif ($item['status']== 0)
                                        <span class="badge bg-yellow">Created</span>
                                        @elseif($item['status']== 9 && $item['reopened'] == 1)
                                        <span class="badge bg-red">Ticket Re-Opened</span>
                                        @elseif($item['status']== 7  && $item['closedbyteamleader'] == 1 )
                                        <span class="badge bg-green">Forwarded to CC</span>
                                        @elseif($item['status'] == 10 && $item['deliveredby_call_center'] == 1 )
                                        <span class="badge bg-green">Delivered by CC</span>
                                        @elseif($item['status'] == 8 && $item['deliveredby_teamleader'] == 1 )
                                        <span class="badge bg-green">Delivered by TL</span>

                                        @elseif($item['status'] == 12 && $item['deliveredby_call_center'] == 1 && $item['ticket_closed'] == 1)
                                        <span class="badge badge-danger">Ticket Closed</span>
                                        @elseif($item['status'] == 12 && $item['deliveredby_call_center'] == 0 && $item['ticket_closed'] == 1)
                                        <span class="badge badge-danger">Ticket Undelivered Closed</span>
                                        @elseif($item['status'] == 11 && $item['ended']== 1)
                                        <span class="badge badge-success">Job Completed</span>

                                        @elseif($item['status'] == 4 && $item['started'] == 1)
                                        <span class="badge badge-info">Job Started</span>
                                        @elseif($item['status'] == 3 && $item['accepted'] == 1)
                                        <span class="badge badge-primary">Job Accepted</span>
                                        @elseif($item['status'] == 1 && $item['assigned'] == 1)
                                        <span class="badge bg-blue">Assigned</span>
                                        @elseif ($item['status'] == 2 && $item['rejected'] == 1)
                                        <span class="badge bg-red">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{$item['repair_tat']}}</td>
                                    <td>{{$item['delivery_tat']}}</td>
                                    <td>{{$item['repeat_repair']}}</td>
                                    <td>{{$item['ltp']}}</td>
                                    <td>{{$item['cmi']}}</td>

                                </tr>
                                @endforeach
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
            $('#datatable').DataTable({
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info',
                            title: 'KPI Report',
                            header: true,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            title: 'KPI Report',
                            header: true,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            title: 'KPI Report',
                            header: true,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible',
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            title: 'KPI Report',
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
                            title: 'KPI Report',
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
        // $(document).ready( function () {
        //     var searchable = [];
        //     var selectable = [];

        //     $.ajaxSetup({
        //         headers:{
        //             "X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),
        //         }
        //     });

        //     var dTable = $('#datatable').DataTable({
        //         order: [],
        //         lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        //         processing: true,
        //         responsive: false,
        //         serverSide: true,
        //         language: {
        //             processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
        //         },
        //         scroller: {
        //             loadingIndicator: false
        //         },
        //         pagingType: "full_numbers",
        //         // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        //         ajax: {
        //             url: "{{route('report.kpi-report-post')}}",
        //             type: "get"
        //         },
        //         columns: [
        //             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        //             {data:'jobNumber', name: 'jobNumber', orderable: true, searchable: true},
        //             {data:'model_name', name: 'model_name', orderable: true, searchable: true},
        //             {data:'fault_description', name: 'fault_description', orderable: true, searchable: true},
        //             {data:'repairDescription', name: 'repairDescription', orderable: true, searchable: true},
        //             {data:'received_date', name: 'received_date', orderable: true, searchable: true},
        //             {data:'repair_date', name: 'repair_date', orderable: true, searchable: true},
        //             {data:'delivery_date', name: 'delivery_date', orderable: true, searchable: true},
        //             {data:'yes_no', name: 'yes_no', orderable: true, searchable: true},
        //             {data:'part_name', name: 'part_name', orderable: true, searchable: true},
        //             {data:'part_code', name: 'part_code', orderable: true, searchable: true},
        //             {data:'repair_tat', name: 'repair_tat', orderable: true, searchable: true},
        //             {data:'delivery_tat', name: 'delivery_tat', orderable: true, searchable: true},
        //         ],

        //         dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        //             buttons: [
        //                     {
        //                         extend: 'copy',
        //                         className: 'btn-sm btn-info',
        //                         title: 'KPI Report',
        //                         header: false,
        //                         footer: true,
        //                         exportOptions: {
        //                             // columns: ':visible'
        //                         }
        //                     },
        //                     {
        //                         extend: 'csv',
        //                         className: 'btn-sm btn-success',
        //                         title: 'KPI Report',
        //                         header: false,
        //                         footer: true,
        //                         exportOptions: {
        //                             // columns: ':visible'
        //                         }
        //                     },
        //                     {
        //                         extend: 'excel',
        //                         className: 'btn-sm btn-warning',
        //                         title: 'KPI Report',
        //                         header: false,
        //                         footer: true,
        //                         exportOptions: {
        //                             // columns: ':visible',
        //                         }
        //                     },
        //                     {
        //                         extend: 'pdf',
        //                         className: 'btn-sm btn-primary',
        //                         title: 'KPI Report',
        //                         pageSize: 'A2',
        //                         header: false,
        //                         footer: true,
        //                         exportOptions: {
        //                             // columns: ':visible'
        //                         }
        //                     },
        //                     {
        //                         extend: 'print',
        //                         className: 'btn-sm btn-default',
        //                         title: 'KPI Report',
        //                         // orientation:'landscape',
        //                         pageSize: 'A2',
        //                         header: true,
        //                         footer: false,
        //                         orientation: 'landscape',
        //                         exportOptions: {
        //                             // columns: ':visible',
        //                             stripHtml: false
        //                         }
        //                     }
        //                 ],
        //     });
        // });
</script>
 @endpush
@endsection
