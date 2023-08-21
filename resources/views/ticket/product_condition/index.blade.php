@extends('layouts.main')
@section('title', 'Product Conditions')
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
                            <h5>{{ __('label.PRODUCT_CONDITION')}}</h5>
                            <span>{{ __('label.LIST_OF_PRODUCT_CONDITION')}}</span>
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
                        <h3>@lang('label.PRODUCT_CONDITION')</h3>
                        @can('create')
                            <div class="card-header-right">
                                <a class="btn btn-info" data-toggle="modal" data-target="#demoModal">  @lang('label._CREATE')</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th style="text-align: center;">{{ __('label.PRODUCT_CONDITION')}}</th>
                                    <th style="text-align: center;">{{ __('label.STATUS')}}</th>
                                    <th style="text-align: right;">{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_conditions as $key=>$item)
                                    <tr data-id="{{$item->id}}">
                                        <td>{{ $key+1 }}</td>
                                        <td style="text-align: center;">{{ $item->product_condition }}</td>
                                        <td style="text-align: center;">
                                            @if ($item->status == true)
                                                <form action="{{ route('product_conditions.status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Inactive">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('product_conditions.status', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Active">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='' style="text-align: right;">

                                                {{ Form::open(['route' => ['product_conditions.destroy', $item->id], 'method' => 'DELETE', 'class' => 'delete d-inline'] ) }}
                                                {{ Form::hidden('_method', 'DELETE') }}

                                                @can('edit')
                                                    <a  data-id="{{$item->id}}" data-service="{{$item->service_type}}" data-status="{{$item->status}}" data-toggle="modal" data-target="#editModal"  href="#" class="showProductCondition" data-id="{{$item->id}}">
                                                    <i class='ik ik-edit f-16 mr-15 text-blue'></i>
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
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.PRODUCT_CONDITION')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'product_conditions.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.PRODUCT_CONDITION')}}<span class="text-red">*</span></label>
                                        {{ Form::text('product_condition', Request::old('product_condition'), array('id'=> 'product_condition', 'class' => 'form-control', 'placeholder' => 'Enter Product Condition ...','required'=>'required')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('product_condition')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.STATUS')}}<span class="text-red">*</span></label>
                                        {!! Form::select('status',['1'=>__('label.ACTIVE'),'0'=>__('label.INACTIVE')], null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_STATUS'),'id'=> 'status']) !!}
                                        <div class="help-block with-errors"></div>

                                        @error('status')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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

    <!--Edit Warranty Type modal-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.PRODUCT_CONDITION')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'update.product_conditions', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.PRODUCT_CONDITION')}}<span class="text-red">*</span></label>
                                        {{ Form::text('product_condition', Request::old('product_condition'), array('id'=> 'product_condition', 'class' => 'form-control', 'placeholder' => 'Enter Product Condition ...','required'=>'required')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('product_condition')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="condition_id">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.STATUS')}}<span class="text-red">*</span></label>
                                        {!! Form::select('status',['1'=>__('label.ACTIVE'),'0'=>__('label.INACTIVE')], null,[ 'class'=>'form-control select2','id'=> 'status']) !!}
                                        <div class="help-block with-errors"></div>
                                        @error('status')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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


    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

<script type="text/javascript">
        @if ($errors->any())
        $('#demoModal').modal('show');
        @endif
   $(document).ready( function () {
    //    $('#datatable').DataTable();
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
    $(document).ready(function() {
        $('.showProductCondition').on('click', function() {
            var url = "product_conditions/"
            var id = $(this).data('id').toString();
            url = url.concat(id);
            $.get(url, function(data) {
                console.log(data);
                $("#editModal input[name='product_condition']").val(data['product_condition']);
                $("#editModal select[name='status']").val(data['status']).change();
                $("#editModal input[name='condition_id']").val(data['id']);
            });
        });
    })
</script>
@endsection
