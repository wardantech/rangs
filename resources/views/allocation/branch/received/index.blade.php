@extends('layouts.main')
@section('title', 'Received Requisitions')
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
                            <h5>{{ __('Received Requisitions')}}</h5>
                            <span>{{ __('Received Requisitions list')}}</span>
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
                        <h3>@lang('Branch Received Requisitions')</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('Branch')}}</th>
                                    <th title="Required Quantity">{{ __('Requ Qty')}}</th>
                                    <th title="Received Quantity">{{ __('Rec Qty')}}</th>
                                    <th title="Allocated Quantity">{{ __('Allo Qty')}}</th>
                                    <th>{{ __('Status')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receives as $key=> $received)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $received->date->format('m/d/Y'); }}
                                        </td>
                                        <td>
                                            B-RSL-{{ $received->allocation->requisition_id}}
                                        </td>
                                        <td>{{ $received->allocation->requisition->senderStore->name ?? null}}</td>
                                        <td>
                                            {{ $received->allocation->requisition->total_quantity ?? null}}
                                        </td>
                                        <td>
                                            {{ $received->allocation->received_quantity ?? null}}
                                        </td>
                                        <td>
                                            {{ $received->allocation->allocate_quantity ?? null}}
                                        </td>
                                        <td>
                                            @isset($received->allocation->requisition)
                                            @if($received->allocation->requisition->status == 2)
                                                <span class="badge badge-info">Received</span>
                                            @elseif($received->allocation->status == 1 && $received->allocation->requisition->total_quantity > $received->allocation->allocate_quantity)
                                                <span class="badge badge-danger">Partially Allocated</span>
                                            @endif  
                                            @endisset

                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">
                                                @can('edit')
                                                    <a href="{{ route('branch.allocation.received.edit', $received->id) }}">
                                                        <i class="ik ik-edit f-16 mr-15 text-green"></i>
                                                    </a>
                                                @endcan

                                                @can('show')
                                                    <a  href="{{ route('branch.allocation.received.show', $received->id) }}">
                                                        <i class="ik ik-eye f-16 mr-15 text-blue"></i>
                                                    </a>
                                                @endcan

                                                @can('delete')
                                                    <form action="{{ route('branch.allocation.received.destroy', $received->id) }}" method="POST" class="delete d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                            <i class="ik ik-trash-2 f-16 text-red"></i>
                                                        </button>
                                                    </form>
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
            $('#datatable').DataTable({
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        title: 'Received Requisitions',
                        header: false,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        title: 'Received Requisitions',
                        header: true,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        title: 'Received Requisitions',
                        header: true,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible',
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        title: 'Received Requisitions',
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
                        title: 'Received Requisitions',
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
