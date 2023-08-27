@extends('layouts.main')
@section('title', 'Requisitions')
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
                            <h5>{{ __('Requisitions')}}</h5>
                            <span>{{ __('List Of Requisitions')}}</span>
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
        <div class="row clearfix">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-orange">
                    <a href="{{ route('technician.jobs.status', 1) }}" title="Pending">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Pending')}}</h6>
                                    {{-- <h2>{{  }}</h2> --}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-green">
                    <a href="{{ route('technician.jobs.status', 2) }}" title="Paused">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Allocated')}}</h6>
                                    {{-- <h2>{{  }}</h2> --}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-info">
                    <a href="{{ route('technician.jobs.status', 3) }}" title="Created">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Received')}}</h6>
                                    {{-- <h2>{{  }}</h2> --}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-dark">
                    <a href="{{ route('technician.jobs.status', 3) }}" title="Created">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Total')}}</h6>
                                    {{-- <h2>{{  }}</h2> --}}
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
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('Requisitions')</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('Request From (Store)')}}</th>
                                    <th title="Required Quantity">{{ __('Requ Qty')}}</th>
                                    <th title="Allocated Quantity">{{ __('Allo Qty')}}</th>
                                    <th title="Balanced Quantity">{{ __('Balance/Pending')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th style="width: 130px;">{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitions as $key=>$requisition)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $requisition->date->format('m/d/Y') }}</td>
                                        <td>{{ $requisition->requisition_no }}</td>
                                        <td>
                                            @if(isset($requisition->senderStore->name))
                                            {{ $requisition->senderStore->name }}
                                            @endif
                                        </td>
                                        <td>{{ $requisition->total_quantity }}</td>
                                        <td>{{ $requisition->issued_quantity }}</td>
                                        <td>{{ ($requisition->total_quantity) - ($requisition->issued_quantity) }}</td>
                                        <td>
                                            @if($requisition->status == 0 && $requisition->is_declined == 1)
                                               <span class="badge badge-danger">Rejected</span>
                                            @elseif($requisition->status == 0)
                                               <span class="badge badge-warning">Pending</span>
                                            @elseif($requisition->status == 1 && $requisition->total_quantity == $requisition->issued_quantity)
                                               <span class="badge badge-success">Allocated</span>
                                            @elseif($requisition->status == 1 && $requisition->total_quantity > $requisition->issue_quantity)
                                                <a href="{{ route('central.re-allocate', $requisition->id) }}" class="badge badge-warning" title="Re Allocate" data-id="{{$requisition->id}}">
                                                    Partially Allocated
                                                    <i class="fa fa-reply f-16 mr-15" aria-hidden="true"></i>
                                                </a>
                                            @elseif($requisition->status == 2 && $requisition->issued_quantity > $requisition->received_quantity)
                                                @if ($requisition->total_quantity > $requisition->issued_quantity )    
                                                <a href="{{ route('central.re-allocate', $requisition->id) }}" class="badge badge-warning" title="Re Allocate" data-id="{{$requisition->id}}">
                                                    Partially Received
                                                    <i class="fa fa-reply f-16 mr-15" aria-hidden="true"></i>
                                                </a>
                                                @else
                                                <span class="badge badge-success">Fully Allocated</span>
                                                @endif
                                            @elseif($requisition->status == 2 && $requisition->total_quantity > $requisition->issued_quantity)
                                               <a href="{{ route('central.re-allocate', $requisition->id) }}" class="badge badge-warning" title="Re Allocate" data-id="{{$requisition->id}}">
                                                Received
                                                <i class="fa fa-reply f-16 mr-15" aria-hidden="true"></i>
                                            </a>
                                            @elseif($requisition->status == 2 && $requisition->total_quantity == $requisition->issued_quantity)
                                               <span class="badge badge-info">Received</span>
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">
                                            @if ($requisition->allocation_status!=1)
                                                @can('create')
                                                    <a  href="{{ route('central.requisitations.allocate',$requisition->id) }}"  title="Allocate" data-id="{{$requisition->id}}">
                                                        <i class="fa fa-reply f-16 mr-15" aria-hidden="true"></i>
                                                    </a>
                                                @endcan
                                            @else
                                                @can('create')
                                                    <i class="fa fa-reply f-16 mr-15 text-danger" aria-hidden="true" title="Already Allocated"></i>
                                                @endcan
                                            @endif
                                                @can('show')
                                                    <a  data-id="{{$requisition->id}}"  href="#" title="View Details" class="showDetails">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                                    <a href="{{ route('central.requisitions.show', $requisition->id) }}" title="View Details">
                                                        <i class="fas fa-binoculars f-16 mr-15 text-green"></i>
                                                    </a>
                                                @endcan

                                                @if ($requisition->allocation_status!=1)
                                                    <a  href="{{ route('central.requisitions.decline',$requisition->id) }}"  title="Decline" data-id="{{$requisition->id}}">
                                                        <i class="fa fa-times text-red" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('Requisition Parts Details')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('label.SL')}}</th>
                                            <th>{{ __('Parts')}}</th>
                                            <th>{{ __('Requisition Quantity')}}</th>
                                            <th>{{ __('Issued Quantity')}}</th>
                                            <th>{{ __('Received Quantity')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="requisition_data">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <!--Add Warranty Type modal-->
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

    <script type="text/javascript">
    $(document).ready(function(){
        $('.showDetails').on('click',function(){
              var requisition_id = $(this).data('id');
              var url = "{{ route('requisition.details') }}";
              $.ajax({
                type: "get",
                url: url,
                data: {
                    id: requisition_id,
                },
                success: function(data) {
                    $('#demoModal').modal('show');
                    $("#requisition_data").empty();
                    var html = "";
                    $.each(data.detail, function(key) {
                        console.log(data.detail[key].id);
                        html += "<tr><td>"+parseInt(key+1)+"</td><td>"+data.detail[key].part.code+'-'+data.detail[key].part.name+"</td><td>"+data.detail[key].required_quantity+"</td><td>"+data.detail[key].issued_quantity+"</td><td>"+data.detail[key].received_quantity+"</td></tr>";
                 })
                 $("#requisition_data").append(html);
                }
            });
        });

        $('#datatable').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Branch_Requisitions',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Branch_Requisitions',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Branch_Requisitions',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Branch_Requisitions',
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
                    title: 'Branch_Requisitions',
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
                }
            ],
        });
    });
    </script>
@endsection
