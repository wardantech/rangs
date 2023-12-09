@extends('layouts.main')
@section('title', 'Submitted Job List')
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
                            <h5>{{ __('label.SUBMITTED JOB LIST')}}</h5>
                            <span>{{ __('label.LIST_OF_SUBMITTED_JOBS')}}</span>
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
                    <div class="card-header">
                        <h3>{{ __('label.SUBMITTED JOB LIST')}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Job Submission Date')}}</th>
                                    <th>{{ __('Ticket Number')}}</th>
                                    <th>{{ __('Branch')}}</th>
                                    <th>{{ __('Ticket Date')}}</th>
                                    <th>{{ __('label.JOB NUMBER')}}</th>
                                    <th>{{ __('Job Assign Date')}}</th>
                                    <th>{{ __('Team Leader Delivery Date')}}</th>
                                    <th>{{ __('Call Center Delivery Date')}}</th>
                                    <th>{{ __('label.AMOUNT')}}</th>
                                    <th>{{ __('label.STATUS')}}</th>
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
    <script type="text/javascript">
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
                scrollX:true,
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{url('technician/submitted-jobs')}}",
                    type: "get",
                    data: function (d) {
                        d.start_date = $('input[name="start_date"]').val(),
                        d.end_date = $('input[name="end_date"]').val()
                    },
                },
                columns: [
                    {data:'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'date', name: 'date', orderable: true, searchable: true},
                    {data:'ticket_number', name: 'ticket_number', orderable: true, searchable: true},
                    {data:'branch', name: 'branch', orderable: true, searchable: true},
                    {data:'ticket_date', name: 'ticket_date', orderable: true, searchable: true},
                    {data:'job_number', name: 'job_number', orderable: true, searchable: true},
                    {data:'job_assigned_date', name: 'job_assigned_date', orderable: true, searchable: true},
                    {data:'ticket_delivery_date_by_team_leader', name: 'ticket_delivery_date_by_team_leader', orderable: true, searchable: true},
                    {data:'ticket_delivery_date_by_callcenter', name: 'ticket_delivery_date_by_callcenter', orderable: true, searchable: true},
                    {data:'amount', name: 'amount', orderable: true, searchable: true},
                    {data:'status', name: 'status', orderable: false, searchable: false},
                    {data:'action', name: 'action',  orderable: false, searchable: false}
                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Submitted Jobs',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Submitted Jobs',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Submitted Jobs',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Submitted Jobs',
                                pageSize: 'A2',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn-sm btn-default',
                                title: 'Submitted Jobs',
                                // orientation:'landscape',
                                pageSize: 'A2',
                                header: true,
                                footer: false,
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: ':visible',
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
                    url:"submitted-jobs/destroy/"+id,

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
