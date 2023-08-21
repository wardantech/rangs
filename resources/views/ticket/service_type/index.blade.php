@extends('layouts.main')
@section('title', 'Service Type')
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
                            <h5>{{ __('label.SERVICE_TYPE')}}</h5>
                            <span>{{ __('label.LIST_OF_SERVICE_TYPE')}}</span>
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
                        <h3>@lang('label.SERVICE_TYPE')</h3>
                        @can('create')
                            <div class="card-header-right">
                                <a class="btn btn-info" data-toggle="modal" data-target="#demoModal" data-categories="{{ $categories }}" id="createServiecType">  @lang('label._CREATE')</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th style="text-align: center;">{{ __('label.PRODUCT')}}</th>
                                    <th style="text-align: center;">{{ __('label.SERVICE_TYPE')}}</th>
                                    <th style="text-align: center;">{{ __('label.SERVICE_AMOUNT')}}</th>
                                    <th style="text-align: center;">{{ __('label.WARRANTY')}}</th>
                                    <th style="text-align: center;">{{ __('label.STATUS')}}</th>
                                    <th style="text-align: right;">{{ __('Action')}}</th>
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

    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.SERVICE_TYPE')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'service-types.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="category_id" class="col-form-label">
                                            {{ __('label.CATEGORY')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="category_id" class="form-control" id="category" required>
                                            <option value="">Select Category</option>
                                        </select>

                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">
                                            {{ __('label.SERVICE_TYPE_NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        {{ Form::text('service_type', Request::old('service_type'), array('id'=> 'service_type', 'class' => 'form-control', 'placeholder' => 'Service Type Name','required'=>'required')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('service_type')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">
                                            {{ __('label.SERVICE_AMOUNT')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        {{ Form::number('service_amount', Request::old('service_amount'), array('id'=> 'service_amount', 'class' => 'form-control', 'placeholder' => 'Service Amount','required'=>'required', 'min'=>'0')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('service_amount')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.SERVICE_TYPE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        {!! Form::select('status',['1'=>__('label.ACTIVE'),'0'=>__('label.INACTIVE')], null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_STATUS'),'id'=> 'status', 'required'=>'required']) !!}
                                        <div class="help-block with-errors"></div>

                                        @error('status')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-check-input mt-0 ml-0" name="is_service_warranty" type="checkbox" id="is_service_warranty" value="1">
                                        <label class="form-check-label ml-3" for="is_service_warranty">
                                            Is It Under Service Warranty
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready( function () {

             @if ($errors->any())
                $('#demoModal').modal('show');
            @endif

            $('body').on('click', '#createServiecType', function (event) {
                event.preventDefault();
                var categories = $(this).data('categories');

                var length = categories.length;
                var html = "<option value="+null+">Select category</option>";
                for(let i = 0; i < length; i++) {
                    html = '<option value="'+categories[i].id+'">'+categories[i].name+'</option>';
                    $('#category').append(html);
                }
            });


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
                    url: "{{route('service-types.index')}}",
                    type: "get"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {data:'categoryName', name: 'categoryName', orderable: true, searchable: true},
                    {data:'service_type', name: 'service_type', orderable: true, searchable: true},
                    {data:'service_amount', name: 'service_amount', orderable: true, searchable: true},
                    {data:'serviceWarrenty', name: 'serviceWarrenty', orderable: true, searchable: true},
                    {data:'status', name: 'status'},
                    {data:'action', name: 'action',  orderable: false, searchable: false}

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
                    url:"service-types/destroy/"+id,

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
