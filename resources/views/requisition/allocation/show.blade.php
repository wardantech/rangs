@extends('layouts.main')
@section('title', 'Allocation Details')
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
                            <h5>{{ __('Allocation Details')}}</h5>
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
        <div class="row print">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('Allocation')</h3>
                        <div class="card-header-right">
                            <a href="{{ route('central.requisitions.allocation.print', $allocation->id) }}" class="btn btn-outline-info" title="Print" target="_blank">
                                <i class="fa fa-print" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>{{ __('Date') }}</td>
                                    <td>{{ $allocation->date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Store') }}</td>
                                    <td>{{ $allocation->requisition->store->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('From Store') }}</td>
                                    <td>{{ $allocation->requisition->senderStore->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Requisition Number') }}</td>
                                    <td>B-RSL-{{ $allocation->requisition_id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Created By') }}</td>
                                    <td>
                                        @isset($allocation->createdBy)
                                            {{ $allocation->createdBy->name }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Updated By') }}</td>
                                    <td>
                                        @isset($allocation->updatedBy)
                                            {{ $allocation->updatedBy->name }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Created At') }}</td>
                                    <td>{{ $allocation->created_at->format('m/d/yy H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Parts')}}</th>
                                    <th>{{ __('Model No')}}</th>
                                    <th>{{ __('TSL No')}}</th>
                                    <th>{{ __('Purposed')}}</th>
                                    <th>{{ __('Rack')}}</th>
                                    <th>{{ __('Bin')}}</th>
                                    <th>{{ __('Requisition Quantity')}}</th>
                                    <th>{{ __('Issued Quantity')}}</th>
                                    <th>{{ __('Received Quantity')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allocation_details as $key=> $detail)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $detail->part->code }}-{{ $detail->part->name }}
                                        </td>
                                        <td>{{ $detail->requistionDetail ? $detail->requistionDetail->model_no : ''}}</td>
                                        <td>{{ $detail->requistionDetail ? "TSL-".$detail->requistionDetail->tsl_no : '' }}</td>
                                        <td>@purpose(optional($detail->requistionDetail)->purpose)</td>
                                        <td>{{ $detail->rack ? $detail->rack->name : '' }}</td>
                                        <td>{{ $detail->bin ? $detail->bin->name : '' }}</td>
                                        <td>{{ $detail->requisition_quantity }}</td>
                                        <td>{{ $detail->issued_quantity }}</td>
                                        <td>{{ $detail->received_quantity }}</td>
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
        <script src="{{ asset('js/print.js') }}"></script>
    @endpush
@endsection
