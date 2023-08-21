@extends('layouts.main')
@section('title', 'Re-Allocated Requisitions')
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
                            <h5>{{ __('Re-Allocated Requisitions')}}</h5>
                            <span>{{ __('Re-Allocated Requisitions')}}</span>
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
                        <h3>@lang('Re-Allocated')</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('Branch')}}</th>
                                    <th title="Required Quantity">{{ __('Required Qty')}}</th>
                                    {{-- <th title="Received Quantity">{{ __('Rec Qty')}}</th>
                                    <th title="Allocated Quantity">{{ __('Allo Qty')}}</th> --}}
                                    <th>{{ __('Status')}}</th>
                                    <th style="width: 100px">{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($allocations as $key=>$allocation)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $allocation->date->format('m/d/Y') }}</td>
                                        <td>{{ $allocation->requisition->requisition_no }}</td>
                                        <td>{{ $allocation->requisition->senderStore->name }}</td>
                                        <td>{{ $allocation->requisition->total_quantity }}</td>
                                        <td>{{ $allocation->received_quantity }}</td>
                                        <td>{{ $allocation->allocate_quantity }}</td>
                                        <td>
                                            @if($allocation->is_received !=1 )
                                               <span class="badge badge-danger">Pending</span>
                                            @elseif( $allocation->is_received ==1 && $allocation->allocate_quantity == $allocation->received_quantity)
                                               <span class="badge badge-info">Fully Received</span>
                                            @elseif($allocation->is_received ==1 && $allocation->allocate_quantity > $allocation->received_quantity)
                                               <span class="badge badge-warning">Partially Received</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">
                                                @can('edit')
                                                    @if ($allocation->is_received !=1)
                                                        <a href="{{route('central.re-allocation.edit',$allocation->id)}}"><i class='ik ik-edit f-16 mr-15 text-green'></i></a>   
                                                    @else
                                                        <button style="border: none;background-color: #fff;" title="You Can't Edit">
                                                            <i class='ik ik-edit f-16 mr-15 text-yellow'></i>
                                                        </button>  
                                                    @endif
                                                @endcan

                                                @can('show')
                                                    <a  href="{{ route('central.re-allocation.show', $allocation->id) }}">
                                                        <i class="fas fa-eye f-16 mr-15 text-blue"></i>
                                                    </a>
                                                @endcan

                                                @can('delete')
                                                    @if ($allocation->is_received !=1)
                                                        <form action="{{ route('central.re-allocation.destroy', $allocation->id) }}" method="POST" class="delete d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" data-placement="top" data-rel="tooltip" title="Delete" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                                <i class="ik ik-trash-2 f-16 text-red"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button style="border: none;background-color: #fff;" title="You Can't Delete">
                                                            <i class="ik ik-trash-2 f-16 text-yellow"></i>
                                                        </button>  
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}
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
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{url('central/re-allocations')}}",
                    type: "get"
                },
                columns: [
                    {data:'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'date', name: 'date', orderable: true, searchable: true},
                    {data:'requisition_no', name: 'requisition_no', orderable: true, searchable: true},
                    {data:'sender_store', name: 'sender_store', orderable: true, searchable: true},
                    {data:'required', name: 'required', orderable: true, searchable: true},
                    // {data:'received_quantity', name: 'received_quantity', orderable: false, searchable: false},
                    // {data:'issued_quantity', name: 'issued_quantity', orderable: false, searchable: false},
                    // {data:'balance', name: 'balance', orderable: false, searchable: false},
                    {data:'status', name: 'status', orderable: false, searchable: false},
                    {data:'action', name: 'action',  orderable: false, searchable: false}
                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Re-Allocations',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Re-Allocations',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Re-Allocations',
                                header: true,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Re-Allocations',
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
                                title: 'Re-Allocations',
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
                    url:"re-allocation/destroy/"+id,

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
