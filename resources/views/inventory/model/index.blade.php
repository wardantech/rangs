@extends('layouts.main')
@section('title', 'Parts Model')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
         <!-- izitoast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.MODEL')}}</h5>
                            <span>{{ __('label.MODEL')}}</span>
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
                        <h3>@lang('label.MODEL')</h3>
                        {{-- <div class="card-header-left"> --}}
                            <div style="margin: 0 auto">
                                <a href="{{url('inventory/sample/part-model/excel')}}" class="btn btn-success">Sample Excel Download</a>
                            </div>
                            <div style="margin-right: 150px;">
                                <form action="{{url('inventory/import/excel/part-model')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    {{-- <label for="" class="badge badge-danger">Import</label> --}}
                                    <input type="file" name="import_file" required>
                                    <input type="submit" class="btn btn-success" value="Import">
                                </div>
                                </form>
                            </div>
                        {{-- </div> --}}
                        @can('create')
                            <div class="card-header-right">
                                <a href="{{URL::to('inventory/parts_model/create')}}" class="btn btn-primary">  @lang('label.PARTS_MODEL_CREATE')</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART_MODEL')}}</th>
                                    <th>{{ __('label.PART_CATEGORY')}}</th>
                                    <th>{{ __('label.STATUS')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @if(!$partsModels->isEmpty())
                                    <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($partsModels as $partsModel)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{$partsModel->name}}</td>
                                        <td>{{$partsModel->category->name ?? Null }}</td>
                                        <td>
                                            @if ($partsModel->status == true)
                                                <form action="{{ route('inventory.parts_model.status', $partsModel->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Inactive">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('inventory.parts_model.status', $partsModel->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Active">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='table-actions text-center'>
                                                @can('edit')
                                                    <a href="{{ route('inventory.parts_model.edit', $partsModel->id) }}" title="Edit">
                                                        <i class='ik ik-edit f-16 mr-15 text-blue'></i>
                                                    </a>
                                                @endcan

                                                @can('delete')
                                                    <form action="{{ route('inventory.parts_model.destroy', $partsModel->id) }}" method="POST" class="delete d-inline">
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

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif --}}

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
                    url: "{{route('inventory.parts_model.index')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'name', name: 'name', orderable: true, searchable: true},
                    {data:'partcategory', name: 'partcategory', orderable: true, searchable: true},
                    {data:'status', name: 'status'},
                    {data:'action', name: 'action',  orderable: false, searchable: false}

                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Part Model',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Part Model',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Part Model',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Part Model',
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
                                title: 'Part Model',
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
                    url:"parts_model/destroy/"+id,

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
        // });
</script>
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/DataTables/Cell-edit/dataTables.cellEdit.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    // izitoast
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endpush
@endsection
