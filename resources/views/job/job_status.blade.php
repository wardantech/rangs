@extends('layouts.main')
@if ($id == 1)
    @section('title', 'Pending')
@elseif ($id == 2)
    @section('title', 'Paused')
@elseif ($id == 3)
    @section('title', 'Created')
@elseif ($id == 4)
    @section('title', 'Completed')
@elseif ($id == 5)
    @section('title', 'Started')
@elseif ($id == 6)
    @section('title', 'Accepted')
@elseif ($id == 7)
    @section('title', 'Rejected')
@elseif ($id == 8)
    @section('title', 'Total')
@else
    @section('title', 'Tickets')
@endif
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
                            @if ($id == 1)
                                <h5>{{ __('Pending Jobs')}}</h5>
                                <span>{{ __('Pending Jobs')}}</span>
                            @elseif ($id == 2)
                                <h5>{{ __('Paused Jobs')}}</h5>
                                <span>{{ __('Paused Jobs')}}</span>
                            @elseif ($id == 3)
                                <h5>{{ __('Created Jobs')}}</h5>
                                <span>{{ __('Created Jobs')}}</span>
                            @elseif ($id == 4)
                                <h5>{{ __('Completed Jobs')}}</h5>
                                <span>{{ __('Completed Jobs')}}</span>
                            @elseif ($id == 5)
                                <h5>{{ __('Started Jobs')}}</h5>
                                <span>{{ __('Started Jobs')}}</span>
                            @elseif ($id == 6)
                                <h5>{{ __('Accepted Jobs')}}</h5>
                                <span>{{ __('Accepted Jobs')}}</span>
                             @elseif ($id == 7)
                                <h5>{{ __('Rejected Jobs')}}</h5>
                                <span>{{ __('Rejected Jobs')}}</span>
                            @else
                                <h5>{{ __('Total Jobs')}}</h5>
                                <span>{{ __('Total Jobs')}}</span>
                            @endif
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
                            <input type="hidden" id="status_id" value="{{ $id }}">
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-orange">
                    <a href="{{ route('job.status', 1) }}" title="Pending">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Pending')}}</h6>
                                    <h2>{{ $totalJobStatus->pending }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-lime">
                    <a href="{{ route('job.status', 2) }}" title="Paused">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Paused')}}</h6>
                                    <h2>{{ $totalJobStatus->jobPaused }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-yellow">
                    <a href="{{ route('job.status', 3) }}" title="Created">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Created')}}</h6>
                                    <h2>{{ $totalJobStatus->jobCreated }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-info">
                    <a href="{{ route('job.status', 4) }}" title="Completed">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Completed')}}</h6>
                                    <h2>{{ $totalJobStatus->jobCompleted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-green">
                    <a href="{{ route('job.status', 5) }}" title="Started">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Started')}}</h6>
                                    <h2>{{ $totalJobStatus->jobStrated }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-blue">
                    <a href="{{ route('job.status', 6) }}" title="Accepted">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Accepted')}}</h6>
                                    <h2>{{ $totalJobStatus->jobAccepted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-danger">
                    <a href="{{ route('job.status', 7) }}" title="Rejected">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Rejected')}}</h6>
                                    <h2>{{ $totalJobStatus->jobRejected }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-secondary">
                    <a href="{{ route('job.status', 8) }}" title="Total">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Total Job')}}</h6>
                                    <h2>{{ $totalJobStatus->totalJob }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <label for="inputpc" class="">Start Date :</label>
                        <input type="date" class="form-control" name="start_date" value="">
                    </div>
                    <div class="col-md-6">
                        <label for="inputpc" class="">End Date :</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="">
                    </div>
                </div>
                <hr>
                <div class="card p-3">
                    <div class="card-header">
                        @if ($id == 1)
                                <h3>{{ __('Pending Jobs')}}</h3>
                            @elseif ($id == 2)
                                <h3>{{ __('Paused Jobs')}}</h3>
                            @elseif ($id == 3)
                                <h3>{{ __('Created Jobs')}}</h3>
                            @elseif ($id == 4)
                                <h3>{{ __('Completed Jobs')}}</h3>
                            @elseif ($id == 5)
                                <h3>{{ __('Started Jobs')}}</h3>
                            @elseif ($id == 6)
                                <h3>{{ __('Accepted Jobs')}}</h3>
                             @elseif ($id == 7)
                                <h3>{{ __('Rejected Jobs')}}</h3>
                            @else
                                <h3>{{ __('Total Jobs')}}</h3>
                            @endif
                        <div class="card-header">
                           {{-- <a href="{{ url('job/job-status/excel',$id) }}" class="btn btn-success text-center" target="_blank" > Download - Excel</a> --}}
                           <a href="{{ route('job.job-status.excel',$id) }}" class="btn btn-success text-center" target="_blank" > Download - Excel</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive"><strong> {{__('label.JOB_LIST')}}</strong>
                            <table style="margin:0px !important" id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th style="padding-left: 1rem;">{{ __('label.SL')}}</th>
                                        <th>{{ __('label.TECHNICIAN')}}</th>
                                        <th>{{ __('Technician Type')}}</th>
                                        <th>{{ __('label.OUTLET')}}</th>
                                        <th>{{ __('label.TICKET_SL')}}</th>
                                        <th>{{ __('label.TICKET_CREATED_AT')}}</th>
                                        <th>{{ __('Customer Name')}}</th>
                                        <th>{{ __('Customer Phone')}}</th>
                                        <th>{{ __('Purchase Date')}}</th>
                                        <th>{{ __('label.JOB_NUMBER')}}</th>
                                        <th>{{ __('Service Type')}}</th>
                                        <th>{{ __('Warranty Type')}}</th>
                                        <th>{{ __('label.ASSIGNED_DATE')}}</th>
                                        <th>{{ __('label.ASSIGNED_BY')}}</th>
                                        <th>{{ __('label.JOB_PRIORITY')}}</th>
                                        <th>{{ __('label.PRODUCT_CATEGORY')}}</th>
                                        <th>{{ __('label.BRAND_NAME')}}</th>
                                        <th>{{ __('label.PRODUCT_NAME')}}</th>
                                        <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                        <th>{{ __('Point Of Purchase')}}</th>
                                        <th>{{ __('Invoice Number')}}</th>
                                        <th>{{ __('label.JOB STATUS')}}</th>
                                        <th>{{ __('label.JOB_PENDING_NOTE')}}</th>
                                        <th>{{ __('Pending for Special Component')}}</th>
                                        <th>{{ __('label.CREATED_AT')}}</th>
                                        <th>{{ __('Action')}}</th>
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
    </div>
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    @endpush

<script type="text/javascript">
        $(function () {
            var statusId = $('#status_id').val();
            var url = '{{ route("job.status",":id") }}';

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
                    url: url.replace(':id', statusId),
                    type: "get",
                    data: function (d) {
                        d.start_date = $('input[name="start_date"]').val(),
                        d.end_date = $('input[name="end_date"]').val()
                    },
                },
                columns: [
                    {data:'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'emplyee_name', name: 'emplyee_name', orderable: true, searchable: true},
                    {data:'technician_type', name: 'technician_type', orderable: true, searchable: true},
                    {data:'outlet_name', name: 'outlet_name', orderable: true, searchable: true},
                    {data:'ticket_sl', name: 'ticket_sl', orderable: true, searchable: true},
                    {data:'ticket_created_at', name: 'ticket_created_at', orderable: true, searchable: true},
                    {data:'customer_name', name: 'customer_name', orderable: true, searchable: true},
                    {data:'customer_mobile', name: 'customer_mobile', orderable: true, searchable: true},
                    {data:'purchase_date', name: 'purchase_date', orderable: false, searchable: false},
                    {data:'job_number', name: 'job_number', orderable: true, searchable: true},
                    {data:'service_type', name: 'service_type', orderable: true, searchable: true},
                    {data:'warranty_type', name: 'warranty_type', orderable: true, searchable: true},
                    {data:'assigning_date', name: 'assigning_date', orderable: true, searchable: true},
                    {data:'created_by', name: 'created_by', orderable: true, searchable: true},
                    {data:'job_priority', name: 'job_priority', orderable: true, searchable: true},
                    {data:'product_category', name: 'product_category', orderable: true, searchable: true},
                    {data:'brand_name', name: 'brand_name', orderable: true, searchable: true},
                    {data:'model_name', name: 'model_name', orderable: true, searchable: true},
                    {data:'product_serial', name: 'product_serial', orderable: true, searchable: true},
                    {data:'point_of_purchase', name: 'point_of_purchase', orderable: true, searchable: true},
                    {data:'invoice_number', name: 'invoice_number', orderable: true, searchable: true},
                    {data:'status', name: 'status', orderable: true, searchable: true},
                    {data:'job_pending_remark', name: 'job_pending_remark', orderable: true, searchable: true},
                    {data:'pending_for_special_components', name: 'pending_for_special_components', orderable: true, searchable: true},
                    {data:'job_created_at', name: 'job_created_at', orderable: true, searchable: true},
                    {data:'action', name: 'action',  orderable: false, searchable: false}
                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Jobs',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Jobs',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Jobs',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Jobs',
                                pageSize: 'A2',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn-sm btn-default',
                                title: 'Jobs',
                                // orientation:'landscape',
                                pageSize: 'A2',
                                header: true,
                                footer: false,
                                orientation: 'landscape',
                                exportOptions: {
                                    // columns: ':visible',
                                    stripHtml: false
                                }
                            },
                            {            //             {
                                extend: 'colvis',
                                className: 'btn-sm btn-primary',
                                text: '{{trans("Column visibility")}}',
                                columns: ':gt(0)'
                            },
                        ],
            });
            $('#end_date').change(function(){
                dTable.draw();
            });
        });
            // delete Confirm
            function showDeleteConfirm(id) {
                var form = $(this).closest("form");
                var name = $(this).data("name");
                event.preventDefault();
                swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        deleteItem(id);
                    }
                });
            };


            // Delete Button
            function deleteItem(id) {
                $.ajax({
                    type: "GET",
                    url:"job/delete/"+id,

                    success: function (resp) {

                        console.log(resp);

                        // Reloade DataTable
                        $('#datatable').DataTable().ajax.reload();

                        if (resp.success === true) {
                            // show toast message
                            iziToast.show({
                                title: "Success!",
                                position: "topRight",
                                timeout: 4000,
                                color: "green",
                                message: resp.message,
                                messageColor: "black"
                            });
                        } else if (resp.errors) {
                            iziToast.show({
                                title: "Oopps!",
                                position: "topRight",
                                timeout: 4000,
                                color: "red",
                                message: resp.errors[0],
                                messageColor: "black"
                            });
                        } else {
                            iziToast.show({
                                title: "Oopps!",
                                position: "topRight",
                                timeout: 4000,
                                color: "red",
                                message: resp.message,
                                messageColor: "black"
                            });
                        }
                    }, // success end
                })
            }
    </script>
@endsection
