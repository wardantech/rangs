@extends('layouts.main')
@if ($id == 0)
    @section('title', 'Created Tickets')
@elseif ($id == 1)
    @section('title', 'Assigned Tickets')
@elseif ($id == 2)
    @section('title', 'Canelled Tickets')
@elseif ($id == 3)
    @section('title', 'Accepted Tickets')
@elseif ($id == 4)
    @section('title', 'Started Tickets')
@elseif ($id == 5)
    @section('title', 'Paused Tickets')
@elseif ($id == 6)
    @section('title', 'Pending Ticket')
@elseif ($id == 7)
    @section('title', 'DeliveredB By TL')
@elseif ($id == 8)
    @section('title', 'Re-Opened')
@elseif ($id == 9)
    @section('title', 'DeliveredB By CC')
@elseif ($id == 10)
    @section('title', 'Complted')
@elseif ($id == 11)
    @section('title', 'Closed')
@elseif ($id == 12)
    @section('title', 'Total')
@elseif ($id == 13)
    @section('title', 'Undelivered Closed')
@elseif ($id == 14)
    @section('title', 'Recommended')
@else
    @section('title', 'Tickets')
@endif

@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Tickets')}}</h5>
                            <span>{{ __('Tickets')}}</span>
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
                            <li class="breadcrumb-item">
                                @if ($id == 0)
                                <a href="#" class="btn btn-outline-warning">{{ __('Created Tickets')}}</a>
                                @elseif ($id == 1)
                                    <a href="#" class="btn btn-outline-info">{{ __('Assigned Tickets')}}</a>
                                @elseif ($id == 2)
                                    <a href="#" class="btn btn-outline-danger">{{ __('Canelled Tickets')}}</a>
                                @elseif ($id == 3)
                                    <a href="#" class="btn btn-outline-primary">{{ __('Accepted Tickets')}}</a>
                                @elseif ($id == 4)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Started Tickets')}}</a>
                                @elseif ($id == 5)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Paused Tickets')}}</a>
                                @elseif ($id == 6)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Pending Tickets')}}</a>
                                @elseif ($id == 7)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Delivered BY TL Tickets')}}</a>
                                @elseif ($id == 8)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Reopened Tickets')}}</a>
                                @elseif ($id == 9)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Delivered BY CC Tickets')}}</a>
                                @elseif ($id == 10)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Completed Tickets')}}</a>
                                @elseif ($id == 11)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Closed Tickets')}}</a>
                                @elseif ($id == 12)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Total Tickets')}}</a>
                                @elseif ($id == 13)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Undelivered Close')}}</a>
                                @elseif ($id == 14)
                                    <a href="#" class="btn btn-outline-warning">{{ __('Recommended')}}</a>
                                @else
                                    <a href="#" class="btn btn-outline-warning">{{ __('Tickets')}}</a>
                                @endif
                            </li>
                            <input type="hidden" id="status_id" value="{{ $id }}">
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-yellow">
                        <a href="{{ route('tickets.status', 0) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Created')}}</h6>
                                        <h2>{{ $totalTicketStatus->created }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                {{-- Start --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-blue">
                        <a href="{{ route('tickets.status', 1) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Assigned')}}</h6>
                                        <h2>{{ $totalTicketStatus->assigned }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-danger">
                        <a href="{{ route('tickets.status', 2) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Cancelled')}}</h6>
                                        <h2>{{ $totalTicketStatus->rejected }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-primary">
                        <a href="{{ route('tickets.status', 3) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Accepted')}}</h6>
                                        <h2>{{ $totalTicketStatus->jobAccepted }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-info">
                        <a href="{{ route('tickets.status', 4) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Started')}}</h6>
                                        <h2>{{ $totalTicketStatus->jobStarted }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-yellow">
                        <a href="{{ route('tickets.status', 5) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Paused')}}</h6>
                                        <h2>{{ $totalTicketStatus->jobPaused }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                {{-- End --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-orange">
                        <a href="{{ route('tickets.status', 6) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Pending')}}</h6>
                                        <h2>{{ $totalTicketStatus->pending }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-warning">
                        <a href="{{ route('tickets.status', 7) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Delivered By Team Leader')}}</h6>
                                        <h2>{{ $totalTicketStatus->deliveredby_teamleader }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-red">
                        <a href="{{ route('tickets.status', 8) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Re-opened')}}</h6>
                                        <h2>{{ $totalTicketStatus->ticketReOpened }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-green">
                        <a href="{{ route('tickets.status', 9) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Delivered By CC')}}</h6>
                                        <h2>{{ $totalTicketStatus->deliveredby_call_center }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-dark">
                        <a href="{{ route('tickets.status', 10) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Completed Job')}}</h6>
                                        <h2>{{ $totalTicketStatus->jobCompleted }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-green">
                        <a href="{{ route('tickets.status', 11) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Closed')}}</h6>
                                        <h2>{{ $totalTicketStatus->ticketClosed }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-secondary">
                        <a href="{{ route('tickets.status', 12) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Total Ticket')}}</h6>
                                        <h2>{{ $totalTicketStatus->total }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-aqua">
                        <a href="{{ route('tickets.status', 13) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Undlivered Close')}}</h6>
                                        <h2>{{ $totalTicketStatus->undelivered_close }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="widget bg-lime">
                        <a href="{{ route('tickets.status', 14) }}">
                            <div class="widget-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h6>{{ __('Recommended')}}</h6>
                                        <h2>{{ $totalTicketStatus->recommended }}</h2>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
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
                        @if ($id == 0)
                        <h3>@lang('Created Tickets list')</h3>
                        @elseif ($id == 1)
                            <h3>@lang('Assigned Tickets list')</h3>
                        @elseif ($id == 2)
                            <h3>@lang('Canelled Tickets list')</h3>
                        @elseif ($id == 3)
                            <h3>@lang('Accepted Tickets list')</h3>
                        @elseif ($id == 4)
                            <h3>@lang('Started Tickets list')</h3>
                        @elseif ($id == 5)
                            <h3>@lang('Paused Tickets list')</h3>
                        @elseif ($id == 6)
                            <h3>@lang('Pending Tickets list')</h3>
                        @elseif ($id == 7)
                            <h3>@lang('Delivered BY TL Tickets list')</h3>
                        @elseif ($id == 8)
                            <h3>@lang('Reopened Tickets list')</h3>
                        @elseif ($id == 9)
                            <h3>@lang('Delivered BY CC Tickets list')</h3>
                        @elseif ($id == 10)
                            <h3>@lang('Completed Tickets list')</h3>
                        @elseif ($id == 11)
                            <h3>@lang('Closed Tickets list')</h3>
                        @elseif ($id == 12)
                            <h3>@lang('Total Tickets list')</h3>
                        @elseif ($id == 13)
                            <h3>@lang('Undelivered Close Tickets list')</h3>
                        @else
                            <h3>@lang('Tickets list')</h3>
                        @endif
                        <div class="card-header">
                           <a href="{{ route('status.excel',$id) }}" class="btn btn-success text-center" target="_blank" > Download - Excel</a>
                        </div>

                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('NO')}}</th>
                                    <th>{{ __('Ticket Number')}}</th>
                                    <th>{{ __('Point Of Purchase')}}</th>
                                    <th>{{ __('Invoice Number')}}</th>
                                    <th>{{ __('Customer Name')}}</th>
                                    <th>{{ __('Contact')}}</th>
                                    <th>{{ __('Place(District, Thana)')}}</th>
                                    <th>{{ __('Product Category')}}</th>
                                    <th>{{ __('Product Name')}}</th>
                                    <th>{{ __('Product SL')}}</th>
                                    <th>{{ __('Service Type')}}</th>
                                    <th>{{ __('Warranty Type')}}</th>
                                    <th>{{ __('Branch')}}</th>
                                    <th>{{ __('Created By')}}</th>
                                    <th>{{ __('label.CREATED_AT')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('Delivered By CC Date')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
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
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush

    <script>
        $(function () {
            var statusId = $('#status_id').val();
            var url = '{{ route("tickets.status",":id") }}';
            var routes = '{{ route("ticket-destroy",":id") }}';
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
                scrollX: true,
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
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'ticket_sl', name: 'ticket_sl', orderable: true, searchable: true},
                    {data:'point_of_purchase', name: 'point_of_purchase', orderable: true, searchable: true},
                    {data:'invoice_number', name: 'invoice_number', orderable: true, searchable: true},
                    {data:'customer_name', name: 'customer_name', orderable: true, searchable: true},
                    {data:'customer_phone', name: 'customer_phone', orderable: true, searchable: true},
                    {data:'district_thana', name: 'district_thana', orderable: false, searchable: true},
                    {data:'product_category', name: 'product_category', orderable: false, searchable: true},
                    {data:'product_name', name: 'product_name', orderable: false, searchable: true},
                    {data:'product_sl', name: 'product_sl', orderable: false, searchable: true},
                    {data:'service_type', name: 'service_type', orderable: false, searchable: true},
                    {data:'warranty_type', name: 'warranty_type', orderable: false, searchable: true},
                    {data:'branch', name: 'branch', orderable: false, searchable: true},
                    {data:'created_by', name: 'created_by', orderable: false, searchable: true},
                    {data:'created_at', name: 'created_at', orderable: false, searchable: true},
                    {data:'status', name: 'status', orderable: false, searchable: true},
                    {data:'delivery_date_by_call_center', name: 'delivery_date_by_call_center', orderable: false, searchable: true},
                    {data:'action', name: 'action',  orderable: false, searchable: false}
                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Tickets',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Tickets',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Tickets',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Tickets',
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
                                title: 'Tickets',
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
            var route = '{{ route("ticket-destroy",":id") }}';
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
                    url: route.replace(':id', id),

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
