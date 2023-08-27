@extends('layouts.main')
@section('title', 'Customer Grade')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-unlock bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Customer Grade')}}</h5>

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
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <!-- only those have manage_permission permission will get access -->

            @can('create')
                <div class="col-md-12">
                    <div class="card p-3">
                        <div class="card-header">
                            <h3>{{ __('Create Customer Grade')}}</h3>
                        </div>
                        <div class="card-body">
                            <form class="forms-sample" method="POST" action="{{ route('call-center.customer-grade.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="employee_id" class="">
                                            {{ __('Customer Grade') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="name" placeholder="Customer Grade" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="inputpc" class="">
                                            {{ __('Status') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="status" id="" class="form-control" required>
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>  <!-- end col-->
                                </div> <!-- end row -->

                                <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary mt-4">{{ __('Submit')}}</button>
                                    </div>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        <table id="group_datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{__('label.SL')}}</th>
                                    <th>{{ __('Customer Grade')}}</th>
                                    <th>{{ __('Status')}}</th>
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


    <script type="text/javascript">
        $(document).ready( function () {
            var searchable = [];
            var selectable = [];

            $.ajaxSetup({
                headers:{
                    "X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),
                }
            });

            var dTable = $('#group_datatable').DataTable({
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
                    url: "{{route('call-center.customer-grade.index')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'name', name: 'name', orderable: true, searchable: true},
                    {data:'status', name: 'status'},
                    {data:'action', name: 'action',  orderable: false, searchable: false}

                ],

                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                //     buttons: [
                //             {
                //                 extend: 'copy',
                //                 className: 'btn-sm btn-info',
                //                 title: 'Regions',
                //                 header: false,
                //                 footer: true,
                //                 exportOptions: {
                //                     // columns: ':visible'
                //                 }
                //             },
                //             {
                //                 extend: 'csv',
                //                 className: 'btn-sm btn-success',
                //                 title: 'Regions',
                //                 header: false,
                //                 footer: true,
                //                 exportOptions: {
                //                     // columns: ':visible'
                //                 }
                //             },
                //             {
                //                 extend: 'excel',
                //                 className: 'btn-sm btn-warning',
                //                 title: 'Regions',
                //                 header: false,
                //                 footer: true,
                //                 exportOptions: {
                //                     // columns: ':visible',
                //                 }
                //             },
                //             {
                //                 extend: 'pdf',
                //                 className: 'btn-sm btn-primary',
                //                 title: 'Regions',
                //                 pageSize: 'A2',
                //                 header: false,
                //                 footer: true,
                //                 exportOptions: {
                //                     // columns: ':visible'
                //                 }
                //             },
                //             {
                //                 extend: 'print',
                //                 className: 'btn-sm btn-default',
                //                 title: 'Regions',
                //                 // orientation:'landscape',
                //                 pageSize: 'A2',
                //                 header: true,
                //                 footer: false,
                //                 orientation: 'landscape',
                //                 exportOptions: {
                //                     // columns: ':visible',
                //                     stripHtml: false
                //                 }
                //             }
                //         ],
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
                    url:"customer-grade/destroy/"+id,

                    success: function (resp) {

                        console.log(resp);

                        // Reloade DataTable
                        $('#group_datatable').DataTable().ajax.reload();

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
