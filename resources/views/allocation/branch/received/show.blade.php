@extends('layouts.main')
@section('title', 'Branch Allocated Received Details')
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
                            <h5>{{ __('Branch Allocated Received Details')}}</h5>
                            <span>{{ __('List Of Branch Allocated Received Details')}}</span>
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
                        <h3>{{ __('Received') }}</h3>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-hover">
                            <tbody>
                                <tr>
                                    <td>{{ __('Date') }}</td>
                                    <td>{{ $received->date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('From Store') }}</td>
                                    <td>{{ $received->store->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Requisition Number') }}</td>
                                    <td>B-RSL-{{ $received->allocation->requisition_id}}</td>
                                    
                                </tr>
                                <tr>
                                    <td>{{ __('Created By') }}</td>
                                    <td>{{ $received->createdBy->name }}</td>
                                </tr>
                                @if ($received->updatedBy)
                                <tr>
                                    <td>{{ __('Updated By') }}</td>
                                    <td>{{ $received->updatedBy->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>{{ __('Created At') }}</td>
                                    <td>{{ $received->created_at->format('m/d/yy H:i:s') }}</td>
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
                                    <th>{{ __('Issued Quantity')}}</th>
                                    <th>{{ __('Received Quantity')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receivedDetails as $key=> $detail)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $detail->part->code }}-{{ $detail->part->name }}
                                        </td>
                                        <td>{{ $detail->allocationDetail->requistionDetail->model_no ?? null}}</td>
                                        <td>{{ $detail->allocationDetail->requistionDetail ? "TSL-".$detail->allocationDetail->requistionDetail->tsl_no : '' }}</td>
                                        <td>@purpose(optional($detail->allocationDetail->requistionDetail)->purpose)</td>
                                        <td>{{ $detail->rack ? $detail->rack ->name : '' }}</td>
                                        <td>{{ $detail->bin ? $detail->bin ->name : '' }}</td>
                                        <td>{{ $detail->issued_quantity }}</td>
                                        <td>{{ $detail->receiving_quantity }}</td>
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
