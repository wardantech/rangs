@extends('layouts.main')
@section('title', 'Parts Transfer')
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
                            <h5>{{ __('Parts Transfer')}}</h5>
                            <span>{{ __('Parts Transfer List')}}</span>
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
                        <h3>@lang('Parts Transfer')</h3>
                        <div class="card-header-right">
                            <a href="{{route('loan.loan-request.create')}}" class="btn btn-primary">  @lang('label.CREATE')</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Part Transfer No')}}</th>
                                    <th>{{ __('Request From (Store)')}}</th>
                                    <th>{{ __('To Store')}}</th>
                                    <th>{{ __('Total Requested Quantity')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loans as $key=>$loan)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $loan->date->format('m/d/Y') }}</td>
                                        <td>{{ $loan->loan_no }}</td>
                                        <td>{{ $loan->senderStore->name }}</td>
                                        <td>{{ $loan->store->name }}</td>
                                        <td>{{ $loan->total_quantity }}</td>
                                        <td>
                                            @if($loan->status == 1)
                                               <span class="badge badge-danger">Pending</span>
                                            @elseif($loan->status == 2)
                                               <span class="badge badge-success">Issued</span>
                                            @elseif($loan->status == 3)
                                               <span class="badge badge-info">Received</span>
                                            @elseif($loan->status == 4)
                                               <span class="badge badge-warning">Decline</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">
                                                {{ Form::open(['route' => ['loan.loan-request.destroy', $loan->id], 'method' => 'DELETE', 'class'=>'delete d-line'] ) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                @can('edit')
                                                    @if($loan->status == 1)
                                                        <a href="{{route('loan.loan-request.edit', $loan->id)}}" title="Update" class="show-priceManagement">
                                                            <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                        </a>
                                                    @else
                                                        <span><i class='ik ik-edit f-16 mr-15 text-yellow'></i></span>
                                                    @endif
                                                @endcan
                                                    
                                                @can('show')
                                                    <a  href="#" class="showDetails" data-id="{{$loan->id}}">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                                    <a  href="{{route('loan.loan-request.show', $loan->id)}}">
                                                        <i class="fas fa-binoculars f-16 mr-15 text-green"></i>
                                                    </a>
                                                @endcan
                                                @can('delete')
                                                    @if($loan->status == 1)
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                    <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                    @else
                                                        <span><i class="ik ik-trash-2 f-16 text-yellow"></i></span>
                                                    @endif
                                                @endcan
                                                {{ Form::close() }}
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
                    <h5 class="modal-title" id="demoModalLabel">{{ __('Part Transfer Details')}}</h5>
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
                                            {{-- <th>{{ __('Model')}}</th> --}}
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
                  var loan_id = $(this).data('id');
                  var url = "{{ route('loan.details') }}";
                  $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: loan_id,
                    },
                    success: function(data) {
                        $('#demoModal').modal('show');
                        $("#requisition_data").empty();
                        console.log(data);
                        var html = "";
                        $.each(data.detail, function(key) {
                            console.log(data.detail[key].id);
                            html += "<tr><td>"+parseInt(key+1)+"</td><td>"+data.detail[key].parts.name+"</td><td>"+data.detail[key].required_quantity+"</td><td>"+data.detail[key].issued_quantity+"</td><td>"+data.detail[key].received_quantity+"</td></tr>";
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
                }
            ],
    });
        });
        </script>
@endsection
