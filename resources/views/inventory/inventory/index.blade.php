@extends('layouts.main')
@section('title', 'Parts Receive')
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
                            <h5>{{ __('label.PARTS')}}</h5>
                            <span>{{ __('label.LIST_OF_RECEIVED_PARTS')}}</span>
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
                        <h3>@lang('label.RECEIVED_PARTS')</h3>
                        <div style="margin: 0 auto">
                            <a href="{{route('sample-parts-receive-excel')}}" class="btn btn-success">Sample Excel Download</a>
                        </div>
                        <div style="margin: 0 auto">
                            <form action="{{route('import-receive-inventory')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div>
                                {{-- <label for="" class="badge badge-danger">Import</label> --}}
                                <input type="file" name="import_file" required>
                                @if ($errors->has('import_file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('import_file') }}</strong>
                                    </span>
                                @endif
                                <input type="submit" class="btn btn-success" value="Import">
                            </div>
                            </form>
                        </div>
                        @can('create')
                            <div class="card-header-right">
                                <a href="{{URL::to('inventory/create')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('label.INVOICE_NUMBER')}}</th>
                                    <th>{{ __('label.RECEIVE_DATE')}}</th>
                                    <th>{{ __('Order Number')}}</th>
                                    <th>{{ __('Supplier Name')}}</th>
                                    <th>{{ __('LC#')}}</th>
                                    <th>{{ __('Source')}}</th>
                                    <th>{{ __('Store')}}</th>
                                    <!-- <th>{{ __('label.SENDING_DATE')}}</th> -->
                                    {{-- <th>{{ __('label.STORE')}}</th> --}}
                                    <th style="width: 100px;">{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$inventoryArr->isEmpty())
                                    <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($inventoryArr as $inventory)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{$inventory->created_at->format('m/d/yy H:i:s')}}</td>
                                        <td>{{$inventory->invoice_number}}</td>
                                        <td>{{$inventory->receive_date->format('m/d/Y')}}</td>
                                        <td>{{ $inventory->po_number ? $inventory->po_number : '' }}</td>
                                        <td>{{ $inventory->productVendor ? $inventory->productVendor->name : '' }}</td>
                                        <td>{{ $inventory->lc_number ? $inventory->lc_number : '' }}</td>
                                        <td>
                                            @isset($inventory->source)
                                            {{ $inventory->source->name }}
                                            @endisset
                                        </td>
                                        <td>@if(isset($inventory->store->name))
                                            {{ $inventory->store->name }}
                                            @endif
                                        </td>
                                        {{-- <td>{{$inventory->sending_date->format('m/d/Y')}}</td> --}}
                                        <td>
                                            <div class='text-center'>

                                                {{ Form::open(array('url' => 'inventory/' . $inventory->id, 'id' => 'delete','class'=>'delete')) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                @can('show')
                                                    <a class='' href="{{ URL::signedRoute('show-inventory', ['id' => $inventory->id]) }}">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a>
                                                @endcan

                                                @can('edit')
                                                    <a class='' href="{{ URL::signedRoute('edit-inventory', ['id' => $inventory->id]) }}">
                                                        <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                    </a>
                                                @endcan

                                                @can('delete')
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                        <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                @endcan
                                                {{ Form::close() }}
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

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
               <div id="dynamic-info">
                   <!-- Load Ajax -->
               </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

<script type="text/javascript">
   $(document).ready( function () {
        @if(!$inventoryArr->isEmpty())
            $('#datatable').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Receive_Parts',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Receive_Parts',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Receive_Parts',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Receive_Parts',
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
                    title: 'Receive_Parts',
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
        @endif

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
        $(document).on('click', '.showInventory', function (e) {
            e.preventDefault();
            var inventoryId = $(this).attr('data-id'); // get id of clicked row


            $('#dynamic-info').html(''); // leave this div blank
            $.ajax({
                url: "{{ URL::to('inventory/show') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "post",
                data: {
                    inventory_id: inventoryId
                },
                success: function (response) {
                    $('#dynamic-info').html(''); // blank before load.
                    $('#dynamic-info').html(response.html); // load here
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    $('#dynamic-info').html('<i class="fa fa-info-sign"></i> Something went wrong, Please try again...');
                }
            });
         });
    });
    </script>
@endsection
