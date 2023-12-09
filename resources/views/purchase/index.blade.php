@extends('layouts.main')
@section('title', 'Purchase List')
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
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.PURCHASE')}}</h5>
                            <span>{{ __('label.LIST_OF_PURCHASE')}}</span>
                            <h4 class="text-danger mt-2">{{Session::get('message')}}</h4>
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
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h3>{{ __('label.PURCHASE')}}</h3>
                        @can('create')
                            <div class="d-flex mt-2 mt-md-0">
                                <div class="mr-2">
                                    <a href="{{route('product.sample-purchase-excel')}}" class="btn btn-success" title="Click To Downaload">Sample Excel</a>
                                </div>
                    
                                <div class="mr-2">
                                    <form action="{{route('product.import-purchase')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="import_file" required>
                                            <input type="submit" class="btn btn-success" value="Import">
                                        </div>
                                    </form>
                                </div>
                    
                                <div class="mr-2">
                                    <a href="{{URL::to('product/purchase/create')}}" class="btn btn-primary">  @lang('label.CREATE')</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mx-auto text-center mt-5">
                            <label for="inputpc">Enter Search Term:</label>
                            <input type="text" class="form-control bg-light" id="custom_search" name="custom_search" value="" placeholder="Type your search term here">
                        </div>
                    </div>
                    
                    <div class="card-body table-responsive">
                        <table id="table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.CUSTOMER_NAME')}}</th>
                                    <th>{{ __('label.CUSTOMER MOBILE')}}</th>
                                    <th>{{ __('label.PRODUCT_NAME')}}</th>
                                    <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                    <th>{{ __('Invoice Number') }}</th>
                                    <th>{{ __('label.BRAND_NAME')}}</th>
                                    <th>{{ __('label.MODEL_NAME')}}</th>
                                    <th>{{ __('label.POINT_OF_PURCHASE')}}</th>
                                    <th style="width: 94px;">{{ __('label.ACTION')}}</th>
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

            var dTable = $('#table').DataTable({
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
                    url: "{{route('product.purchase.index')}}",
                    type: "get",
                    data: function (d) {
                        d.custom_search = $('input[name="custom_search"]').val()
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'customer_name', name: 'customer_name', orderable: true, searchable: true},
                    {data:'customer_mobile', name: 'customer_mobile', orderable: true, searchable: true},
                    {data:'category_name', name: 'category_name', orderable: true, searchable: true},
                    {data:'product_serial', name: 'product_serial', orderable: true, searchable: true},
                    {data:'invoice_number', name: 'invoice_number', orderable: true, searchable: true},
                    {data:'brand_name', name: 'brand_name', orderable: true, searchable: true},
                    {data:'model_name', name: 'model_name', orderable: true, searchable: true},
                    {data:'outlet_name', name: 'outlet_name', orderable: true, searchable: true},
                    {data:'action', name: 'action',  orderable: false, searchable: false}

                ],

                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                    buttons: [
                            {
                                extend: 'copy',
                                className: 'btn-sm btn-info',
                                title: 'Purchase',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'csv',
                                className: 'btn-sm btn-success',
                                title: 'Purchase',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn-sm btn-warning',
                                title: 'Purchase',
                                header: false,
                                footer: true,
                                exportOptions: {
                                    // columns: ':visible',
                                }
                            },
                            {
                                extend: 'pdf',
                                className: 'btn-sm btn-primary',
                                title: 'Purchase',
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
                                title: 'Purchase',
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
            $('#custom_search').on('keyup', function () {
                dTable.draw();
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
                    url:"purchase/destroy/"+id,

                    success: function (resp) {

                        console.log(resp);

                        // Reloade DataTable
                        $('#table').DataTable().ajax.reload();

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
