@extends('layouts.main')
@section('title', 'Pending Requisitions')
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
                            <h5>{{ __('Pending Requisitions')}}</h5>
                            <span>{{ __('List Of Pending Requisitions')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Pending Requisitions')}}</a>
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
                        <h3>@lang('Pending Requisitions')</h3>
                        <div class="card-header-right">
                           <a class="btn btn-info" data-toggle="modal" data-target="#demoModal">  @lang('label._CREATE')</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('Requisition No')}}</th>
                                    <th>{{ __('Parts Name')}}</th>
                                    <th>{{ __('Parts Model')}}</th>
                                    <th>{{ __('Required')}}</th>
                                    <th>{{ __('Issued')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                <tr>
                                    <td>1</td>
                                    <td>22/07/2021</td>
                                    <td>02</td>
                                    <td>Display</td>
                                    <td>p-d-1</td>
                                    <td>10</td>
                                    <td>5</td>
                                    <td>
                                        <div class='text-center btn btn-info'>
                                            <a href="" class="show-bankAccount" data-toggle="modal" data-target="#reveive">
                                                Receive
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Add Warranty Type modal-->

    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.BANK ACCOUNT')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'create.bank-account', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="date">{{ __('label.DATE')}}</label>
                            <input type="date" name="date" class="form-control" id="date" placeholder="Date">
                        </div>

                        <div class="form-group">
                            <label for="requisition_no">{{ __('Parts Name')}}</label>
                            <select name="parts_id" id="" class="form-control">
                                <option value="">Select parts</option>
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="requisition_no">{{ __('Requisition No')}}</label>
                            <select name="requisition_id" id="" class="form-control">
                                <option value="">Select a requisition no</option>
                                <option value="">01</option>
                                <option value="">02</option>
                                <option value="">03</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="requisition_no">{{ __('Parts Model')}}</label>
                            <select name="parts_model_id" id="" class="form-control">
                                <option value="">Select parts Model</option>
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="required">{{ __('Required Quantity')}}</label>
                            <input type="number" class="form-control" id="required" name="required" placeholder="Required Quantity">
                        </div>

                        <div class="form-group">
                            <label for="issued">{{ __('Issued Quantity')}}</label>
                            <input type="number" class="form-control" id="issued" name="issued" placeholder="Issued Quantity">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="reveive" tabindex="-1" role="dialog" aria-labelledby="reveivelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('Receive Requisition')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'create.bank-account', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="date" class="col-sm-4 col-form-label">{{ __('label.DATE')}}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="date" name="date" placeholder="Date">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="requisition_no" class="col-sm-4 col-form-label">{{ __('Parts Name')}}</label>
                                        <div class="col-sm-8">
                                            <select name="parts_id" id="" class="form-control">
                                                <option value="">Select parts</option>
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="requisition_no" class="col-sm-4 col-form-label">{{ __('Requisition No')}}</label>
                                        <div class="col-sm-8">
                                            <select name="requisition_id" id="" class="form-control">
                                                <option value="">Select a requisition no</option>
                                                <option value="">01</option>
                                                <option value="">02</option>
                                                <option value="">03</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="requisition_no" class="col-sm-4 col-form-label">{{ __('Parts Model')}}</label>
                                        <div class="col-sm-8">
                                            <select name="parts_model_id" id="" class="form-control">
                                                <option value="">Select parts Model</option>
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="required" class="col-sm-4 col-form-label">{{ __('Required Quantity')}}</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="required" name="required" placeholder="Required Quantity">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="receive_quantity" class="col-sm-4 col-form-label">{{ __('Receive Quantity')}}</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="receive_quantity" name="receive_quantity" placeholder="Receive Quantity">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="issued" class="col-sm-4 col-form-label">{{ __('Issued Quantity')}}</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="issued" name="issued" placeholder="Issued Quantity">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
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

    <script>
        $(document).ready(function(){
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
