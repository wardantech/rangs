@extends('layouts.main')
@section('title', 'View Branch Transections')
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
                            <h5>{{ $transection->outlet->name ?? null}}</h5>
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
                        <h3>{{ __('Cash Transactions Details')}}</h3>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <tbody>
                                <tr>
                                    <td>Date</td>
                                    <td>{{ \Carbon\Carbon::parse($transection->date)->format('m/d/Y'); }}</td>
                                </tr>
                                <tr>
                                    <td>Branch</td>
                                    <td>{{ $transection->outlet->name ?? null }}</td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        @if ($transection->cash_in)
                                            {{ number_format($transection->cash_in) }}
                                        @else
                                            {{ number_format($transection->cash_out) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Purpose</td>
                                    <td class="text-capitalize">
                                        @if ($transection->deposit_id)
                                            Deposit
                                        @elseif ($transection->expense_id)
                                            Expense
                                        @elseif ($transection->revenue_id)
                                            Revenue
                                        @else
                                            Cash Received
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td>
                                        {{ $transection->remarks }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Created By</td>
                                    <td>
                                        {{ $transection->user->name }}
                                    </td>
                                </tr>
                                @if ($transection->updated_by)
                                    <tr>
                                        <td>Updated By</td>
                                        <td>
                                            {{ $transection->user->name }}
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
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

    <script>
        $(document).ready(function(){
            $('#datatable').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Cash Transactions',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Cash Transactionssers',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Cash Transactions',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Cash Transactions',
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
                    title: 'Cash Transactions',
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
