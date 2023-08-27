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
                            <h5>{{ __("Technician's Requisitions")}}</h5>
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
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <h3>{{ $mystore ? $mystore->name :"Technician's" }}-@lang('Requisitions')</h3>
                        <div class="card-header-right">
                            {{-- <a href="{{route('technician.requisition.create')}}" class="btn btn-primary">  @lang('label.CREATE')</a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Job Number')}}</th>
                                    <th>{{ __('Ticket Number')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('To Store')}}</th>
                                    <th title="Required Quantity">{{ __('Req Qty')}}</th>
                                    <th title="Received Quantity">{{ __('Rece Qty')}}</th>
                                    <th title="Balanced Quantity">{{ __('Balance/Pending')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitions as $key=>$requisition)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $requisition->date->format('m/d/Y') }}</td>
                                        <td>JSL-{{ $requisition->job->id ?? null }}</td>
                                        <td>TSL-{{ $requisition->job->ticket->id ?? null }}</td>
                                        <td>{{ $requisition->requisition_no }}</td>
                                        <td>{{ $requisition->store->name }}</td>
                                        <td>{{ $requisition->total_quantity }}</td>
                                        <td>{{ $requisition->received_quantity }}</td>
                                        <td>{{ ($requisition->total_quantity) - ($requisition->received_quantity) }}</td>
                                        <td>
                                            @if($requisition->status == 0)
                                               <span class="badge badge-danger">Pending</span>
                                            @elseif($requisition->status == 1 && $requisition->total_quantity == $requisition->issued_quantity)
                                               <span class="badge badge-success">Allocated</span>
                                            @elseif($requisition->status == 1 && $requisition->total_quantity > $requisition->issued_quantity)
                                               <span class="badge badge-warning">Partially Allocated</span>
                                            @elseif($requisition->status == 2 && $requisition->issued_quantity > $requisition->received_quantity)
                                               <span class="badge badge-warning">Partially Received</span>
                                            @elseif($requisition->status == 2)
                                               <span class="badge badge-info">Received</span>
                                            @elseif($requisition->status == 3)
                                               <span class="badge badge-warning">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right; display:flex">
                                                @can('edit')
                                                    @if($requisition->is_issued == 0)
                                                        <a  href="{{ route('technician.requisition.edit', $requisition->id) }}">
                                                            <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                        </a>
                                                    @else
                                                        <i class='ik ik-edit f-16 mr-15 text-yellow'></i>   
                                                    @endif
                                                @endcan
                                                @can('show')
                                                    <a  href="#" class="showDetails" data-id="{{$requisition->id}}">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                                    <a  href="{{ route('technician.requisition.show', $requisition->id) }}">
                                                        <i class="fas fa-binoculars f-16 mr-15 text-green"></i>
                                                    </a>
                                                @endcan

                                                @can('delete')
                                                    @if($requisition->is_issued == 0)
                                                        <form action="{{ route('technician.requisitionDestroy', $requisition->id) }}" method="POST" class="delete d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                                <i class="ik ik-trash-2 f-16 text-red"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                            <i class="ik ik-trash-2 f-16 text-yellow"></i>
                                                        </button>
                                                    @endif
                                                @endcan
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
                        console.log(data.detail[key].part);
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
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Users',
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
                    title: 'Users',
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
    @endpush
@endsection
