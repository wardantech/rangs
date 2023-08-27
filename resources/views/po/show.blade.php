@extends('layouts.main')
@section('title', 'Purchase requisition')
@section('content')

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Purchase requisition')}}</h5>
                            <span>{{ __('Single purchase requisition')}}</span>
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
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Date</td>
                                    <td>{{ $purchaseOrder->date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>PO Number </td>
                                    <td>{{ $purchaseOrder->po_number }}</td>
                                </tr>
                                <tr>
                                    <td>Created By</td>
                                    <td>{{ $purchaseOrder->createdBy->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="card-header">
                            <h3>@lang('Requisition Details')</h3>
                            <div class="card-header-right">
                                {{-- <a href="{{route('purchase.requisitions.create')}}" class="btn btn-primary">  @lang('label.CREATE')</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('Part')}}</th>
                                        <th>{{ __('Stock In Hand')}}</th>
                                        <th>{{ __('Required Quantity')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseOrder->purchaseOrderDetails as $key=> $purchaseOrderDetail)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $purchaseOrderDetail->part->name }}-{{$purchaseOrderDetail->part->code}}</td>
                                            <td>{{ $purchaseOrderDetail->stock_in_hand }}</td>
                                            <td>{{ $purchaseOrderDetail->required_qnty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
