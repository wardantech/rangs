@extends('layouts.main')
@section('title', 'Allocated Part Details')
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
                            <h5>{{ __('Allocated Part Details')}}</h5>
                            {{--<span>{{ __('List Of Requisitions Details')}}</span>--}}
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
                        <h3>@lang('Allocated Part Details')</h3>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>{{ __('Date') }}</td>
                                    <td>{{ $acceptedLoans->date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Part Transfer No') }}</td>
                                    <td>{{ $acceptedLoans->loan->loan_no }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('To Store') }}</td>
                                    <td>{{ $acceptedLoans->toStore->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Total Requested Quantity') }}</td>
                                    <td>{{ $acceptedLoans->loan->total_quantity }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Total Issued Quantity') }}</td>
                                    @if(isset($acceptedLoans->issue_quantity))
                                    <td>{{ $acceptedLoans->issue_quantity }}</td>
                                    @else
                                    <td>-</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>{{ __('Total Received Quantity') }}</td>
                                    @if(isset($acceptedLoans->loan))
                                    <td>{{ $acceptedLoans->loan->received_quantity }}</td>
                                    @else
                                    <td>-</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>{{ __('Created By') }}</td>
                                    <td>{{ $acceptedLoans->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Updated By') }}</td>
                                    <td>{{ $acceptedLoans->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Created At') }}</td>
                                    <td>{{ $acceptedLoans->created_at->format('m/d/yy H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Parts')}}</th>
                                    <th>{{ __('Rack')}}</th>
                                    <th>{{ __('Bin')}}</th>
                                    <th>{{ __('Requested Quantity')}}</th>
                                    <th>{{ __('Issued Quantity')}}</th>
                                    <th>{{ __('Received Quantity')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $key=> $detail)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $detail->part->code }}-{{ $detail->part->name }}
                                        </td>
                                        <td>{{ $detail->rack->name ?? null }}</td>
                                        <td>{{ $detail->bin->name ?? null }}</td>
                                        <td>{{ $detail->requisition_quantity }}</td>
                                        <td>{{ $detail->issued_quantity }}</td>
                                        <td>
                                            @if($detail->received_quantity)
                                                {{ $detail->received_quantity }}
                                            @else
                                                <span>-</span>
                                            @endif
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
    @endpush
@endsection
