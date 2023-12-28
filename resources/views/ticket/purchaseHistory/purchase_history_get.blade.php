@extends('layouts.main')
@section('title', 'Purchase History')
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
                            <h5>{{ __('label.PURCHASE_HISTORY')}}</h5>
                            <span>{{ __('label.LIST_OF_PURCHASE_HISTORY')}}</span>
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

                                @can('create')
                                <form class="forms-sample" method="POST" action="{{ url('tickets/customer-purchase-history') }}">
                                    @csrf
                                    <div class="row pt-5">
                                        <div class="col-md-3">
                                                <label for="inputpc" class="">{{__('label.COUSTOMER_PHONE_NO')}}</label>
                                                <input type="text" class="form-control" id="customer_phone_number" name="customer_phone_number" placeholder="Customer Phone Number" value="{{ old('customer_phone_number') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="inputpc" class="">{{__('label.COUSTOMER_NAME')}}</label>
                                                <input type="text" class="form-control" name="customer_name" placeholder="Customer Name" value="{{ old('customer_name') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="inputpc" class="">{{__('label.PRODUCTS_SERIAL_NUMBER')}}</label>
                                            <input type="text" class="form-control" name="products_serial_number" placeholder="Product Serial Number" value="{{ old('products_serial_number') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="invoice_number" class="">Invoice Number</label>
                                            <input type="text" class="form-control" name="invoice_number" placeholder="Invoice Number" value="{{ old('invoice_number') }}">
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

                    <div class="card-body">
                        <div id ="purchaseHistoryShow"><strong> {{__('label.PURCHASE_HISTORY')}}</strong>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                        <th>Invoice Number</th>
                                        <th>{{ __('label.PURCHASE_DATE')}}</th>
                                        <th>{{ __('label.PRODUCT_NAME')}}</th>
                                        <th>{{ __('label.CUSTOMER_NAME')}}</th>
                                        <th>{{ __('label.BRAND_NAME')}}</th>
                                        <th>{{ __('label.MODEL_NAME')}}</th>
                                        <th>{{ __('label.LOCATION')}}</th>
                                        <th style="width: 100px;">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($purchase_info !=null)
                                    @foreach ($purchase_info as $item)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$item['product_serial']}}</td>
                                        <td>{{$item['invoice_number']}}</td>
                                        <td>{{$item['purchase_date']}}</td>
                                        <td>{{$item['product_name']}}</td>
                                        <td>{{$item['customer_name']}}</td>
                                        <td>{{$item['product_brand_name']}}</td>
                                        <td>{{$item['product_model_name']}}</td>
                                        <td>{{$item['point_of_purchase']}}</td>
                                        <td>
                                            <div class='table-actions d-flex'>
                                                @can('create')
                                                    <a title='Create Ticket' href="{{ url('tickets/ticket-create',$item['purchase_id']) }}">
                                                        <i class='fas fa-check-circle text-green'></i>
                                                    </a>
                                                @endcan
                                                @can('show')
                                                    <a title='View Details' href="{{ url('tickets/ticket-purchase-show',$item['purchase_id']) }}">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if ($purchases!=null)
                        <div id ="serviceHistoryShow">
                            <p class="mb-1">Service History</p>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{!! __('label.DATE_AND_TIME') !!}</th>
                                        <th>{{ __('label.PRODUCT')}}</th>
                                        <th>{{ __('label.BRAND')}}</th>
                                        <th>{{ __('label.STATUS')}}</th>
                                        <th>{{ __('label.SERVICE_DESCRIPTION')}}</th>
                                        <th>{{ __('label.DETAILS')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchases as $purchase)
                                        @foreach($purchase->ticket as $ticket)
                                            <tr>
                                                <td>{{ $ticket->date->format('m/d/Y')}}</td>
                                                <td>{{ $ticket->purchase->category->name }}</td>
                                                <td>{{ $ticket->purchase->brand->name }}</td>
                                                <td>
                                                    @if ($ticket->status == 9 && $ticket->is_reopened == 1)
                                                    <span class="badge bg-red">Ticket Re-Opened</span>
                                                    @elseif( $ticket->status == 0 )
                                                    <span class="badge bg-yellow">Created</span>
        
                                                    @elseif($ticket->status == 6 && $ticket->is_pending == 1)
                                                    <span class="badge bg-orange">Pending</span>
        
                                                    @elseif($ticket->status == 5 && $ticket->is_paused == 1 )
                                                    <span class="badge bg-red">Paused</span>
        
                                                    @elseif($ticket->status == 7 && $ticket->is_closed_by_teamleader == 1)
                                                    <span class="badge bg-green">Forwarded to CC</span>
                                                    @elseif($ticket->status == 10 && $ticket->is_delivered_by_call_center == 1)
                                                    <span class="badge bg-green">Delivered by CC</span>
                                                    @elseif($ticket->status == 8 && $ticket->is_delivered_by_teamleader == 1 )
                                                    <span class="badge bg-green">Delivered by TL</span>
        
                                                    @elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 1)
                                                    <span class="badge badge-danger">Ticket Closed</span>
                                                    @elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 0 && $ticket->is_closed == 1)
                                                    <span class="badge badge-danger">Ticket Undelivered Closed</span>
                                                    @elseif($ticket->status == 11 && $ticket->is_ended == 1)
                                                    <span class="badge badge-success">Job Completed</span>
        
                                                    @elseif($ticket->status == 6 && $ticket->is_accepted == 1 && $ticket->is_started == 0 && $ticket->job->job_pending_note != null)
                                                    <span class="badge bg-orange">Job Pending</span>
        
                                                    @elseif($ticket->status == 4 && $ticket->is_started == 1)
                                                    <span class="badge badge-info">Job Started</span>
                                                    @elseif($ticket->status == 3 && $ticket->is_accepted == 1)
                                                    <span class="badge badge-primary">Job Accepted</span>
                                                    @elseif($ticket->status == 1 && $ticket->is_assigned == 1)
                                                    <span class="badge bg-blue">Assigned</span>
                                                    @elseif ($ticket->status == 2 && $ticket->is_rejected == 1)
                                                    <span class="badge bg-red">Rejected</span>
                                                @endif
                                                </td>
                                                <td>
                                                @php
                                                    $faultId = json_decode($ticket->fault_description_id);
                                                @endphp
        
                                                @foreach ($faults as $fault)
                                                    @if ($fault != null && $faultId !=null)
                                                        @if (in_array($fault->id, $faultId))
                                                            {{ $fault->name }},
                                                        @endif
                                                    @endif
                                                @endforeach
                                                </td>
                                                <td>
                                                    <div class="table-actions d-flex">
                                                        @can('show')
                                                            <a href="{{route('show-ticket-details', $ticket->id)}}" class="btn btn-success text-dark" title="Ticket Details">{{ __('label.TICKET')}}</a>
                                                        @endcan
                                                        @can('show')
                                                            @if ($ticket->job)
                                                                <a href="{{ route('job.job.show', $ticket->job->id) }}" class="btn btn-info text-dark" title="Job Details">{{ __('label.JOB')}}</a>  
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            <tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
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

    <script>
        $(document).ready( function () {
            $(document).on("submit", '.delete', function (e) {
                //This function use for sweetalert confirm message
                e.preventDefault();
                var form = this;

                swal({
                    title: "Are you sure you want to Delete?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });

            });

            $('#datatable').DataTable({
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info',
                            title: 'Purchase History',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            title: 'Purchase History',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            title: 'Purchase History',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible',
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            title: 'Purchase History',
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
                            title: 'Purchase History',
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
                        {
                            extend: 'colvis',
                            className: 'btn-sm btn-primary',
                            text: '{{trans("Column visibility")}}',
                            columns: ':gt(0)'
                        },
                ],
            });
        });
    </script>
@endsection
