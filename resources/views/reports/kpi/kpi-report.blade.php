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
                @can('create')
                    <form class="forms-sample" action="{{ route('report.kpi-report-post') }}" method="get">
                        
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
                                <label for="outlet" class="">Branch :</label>
                                <select name="outlet" id="Select2" class="form-control select2">
                                    <option value="">Select Branch</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{$outlet->id}}"
                                            @if (old('outlet') == $outlet->id)
                                                selected
                                            @endif
                                        >
                                            {{$outlet->name}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('outlet')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="technician" class="">Technician</label>
                                <select name="technician" id="" class="form-control select2">
                                    <option value="">Select Branch</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{$technician->id}}"
                                            @if (old('technician') == $technician->id)
                                                selected
                                            @endif
                                        >
                                            {{$technician->name}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>                            
                        </div>
                        <div class="row pt-5">
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
                        <h3>@lang('label.KPI_REPORT')</h3>
                        <div class="card-header-right">

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
                    url: "{{route('report.kpi-report-get')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'emplyee_name', name: 'emplyee_name', orderable: true, searchable: true},
                    {data:'outlet_name', name: 'outlet_name', orderable: true, searchable: true},
                    {data:'ticket_sl', name: 'ticket_sl', orderable: true, searchable: true},
                    {data:'created_at', name: 'created_at', orderable: false, searchable: false},
                    {data:'purchase_date', name: 'purchase_date', orderable: false, searchable: false},
                    {data:'job_number', name: 'job_number', orderable: true, searchable: true},
                    {data:'created_by', name: 'created_by', orderable: true, searchable: true},
                    {data:'model_name', name: 'model_name', orderable: true, searchable: true},
                    {data:'fault_description', name: 'fault_description', orderable: true, searchable: true},
                    {data:'fault_description_note', name: 'fault_description_note', orderable: true, searchable: true},
                    {data:'job_ending_remark', name: 'job_ending_remark', orderable: true, searchable: true},
                    {data:'repairDescription', name: 'repairDescription', orderable: true, searchable: true},
                    {data:'ticket_date', name: 'ticket_date', orderable: true, searchable: true},
                    {data:'job_assigned_date', name: 'job_assigned_date', orderable: true, searchable: true},
                    {data:'repair_date', name: 'repair_date', orderable: true, searchable: true},
                    {data:'delivery_date', name: 'delivery_date', orderable: true, searchable: true},
                    {data:'yes_no', name: 'yes_no', orderable: true, searchable: true},
                    {data:'part_name', name: 'part_name', orderable: true, searchable: true},
                    {data:'part_code', name: 'part_code', orderable: true, searchable: true},
                    {data:'status', name: 'status', orderable: true, searchable: true},
                    {data:'repair_tat', name: 'repair_tat', orderable: true, searchable: true},
                    {data:'delivery_tat', name: 'delivery_tat', orderable: true, searchable: true},
                    {data:'repeat_repair', name: 'repeat_repair', orderable: true, searchable: true},
                    {data:'ltp', name: 'ltp', orderable: true, searchable: true},
                    {data:'cmi', name: 'cmi', orderable: true, searchable: true},


                ],

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
</script>
 @endpush
@endsection
