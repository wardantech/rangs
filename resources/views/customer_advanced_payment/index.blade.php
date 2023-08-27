@extends('layouts.main')
@section('title', 'Advanced Payment')
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
                            <h5>{{ __('label.CUSTOMER ADVANCE PAYMENT INFO')}}</h5>
                            <span>{{ __('label.LIST OF CUSTOMER ADVANCE PAYMENTS')}}</span>
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
                        <h3>@lang('label.CUSTOMER ADVANCE PAYMENT INFO')</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ __('SL')}}</th>
                                    <th>{{ __('label.ADV_MR_NO')}}</th>
                                    <th>{{ __('label.ADVANCE RECEIPT DATE')}}</th>
                                    <th>{{ __('label.JOB NO')}}</th>
                                    <th>{{ __('label.BRANCH')}}</th>
                                    <th>{{ __('label.CUSTOMER NAME')}}</th>
                                    <th>{{ __('label.CUSTOMER MOBILE')}}</th>
                                    {{-- <th>{{ __('label.CUSTOMER ADDRESS')}}</th> --}}
                                    <th>{{ __('label.RECEIVE DATE')}}</th>
                                    <th>{{ __('label.PRODUCT NAME')}}</th>
                                    <th>{{ __('label.PRODUCT SL')}}</th>
                                    <th>{{ __('label.ADVANCE AMOUNT')}}</th>
                                    {{-- <th>{{ __('label.PAY TYPE')}}</th> --}}
                                    <th style="width: 96px;">{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                @foreach($customerAdvancePayments as $customerAdvancePayment)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$customerAdvancePayment->adv_mr_no}}</td>
                                    <td>
                                        {{\Carbon\Carbon::parse($customerAdvancePayment->advance_receipt_date)->format('m/d/Y')}}
                                    </td>
                                    <td>{{$customerAdvancePayment->jobnumber}}</td>
                                    <td>{{$customerAdvancePayment->branch_name}}</td>
                                    <td>{{$customerAdvancePayment->customer_name}}</td>
                                    <td>{{$customerAdvancePayment->customer_phone}}</td>
                                    {{-- <td>{{$customerAdvancePayment->customer_address}}</td> --}}
                                    <td>{{$customerAdvancePayment->receive_date}}</td>
                                    <td>{{$customerAdvancePayment->product_name}}</td>
                                    <td>{{$customerAdvancePayment->product_sl}}</td>
                                    <td>{{$customerAdvancePayment->advance_amount}}</td>
                                    {{-- <td>
                                        @if($customerAdvancePayment->pay_type==1)
                                            Cash
                                        @endif
                                    </td> --}}
                                    <td>
                                        <div style="display: flex;" class="text-center">
                                            @can('edit')
                                            <a href="{{route('customer-advanced-payment.edit', $customerAdvancePayment->id)}}" class="show-direct-parts-sell">
                                                <i class='ik ik-edit f-16 mr-15 text-green' title="Edit"></i>
                                            </a>
                                            @endcan
                                            @can('show')
                                            <a href="{{route('customer-advanced-payment.show', $customerAdvancePayment->id)}}" class="show-direct-parts-sell">
                                                <i class='ik ik-eye f-16 mr-15 text-blue' title="View"></i>
                                            </a>
                                            @endcan
                                            @can('delete')
                                            {{ Form::open(['route' => ['customer-advanced-payment.destroy', $customerAdvancePayment->id], 'method' => 'DELETE', 'class'=>'delete d-line'] ) }}
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                               <i class="ik ik-trash-2 f-16 text-red" title="Delete"></i>
                                            </button>
                                            {{ Form::close() }}
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
    <script>
        $(document).ready( function () {
            // $('.table').DataTable();
            $('#datatable').DataTable({
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        title: 'Advanced Payment',
                        header: false,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        title: 'Advanced Payment',
                        header: true,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        title: 'Advanced Payment',
                        header: true,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible',
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        title: 'Advanced Payment',
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
                        title: 'Advanced Payment',
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
