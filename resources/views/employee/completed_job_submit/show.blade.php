@extends('layouts.main')
@section('title', 'Submitted job details')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <style>
            .bill-section {
                margin: 2rem;
                border: 1px solid #f1f1f1;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush
{{-- {{dd($submittedJob)}} --}}

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Submitted Job Details')}}</h5>
                            <span>{{ __('Submitted Job Details')}}</span>
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
                        <h3>@lang('Bill Details')</h3>
                        <div class="card-header-right">
                            <a href="{{ route('technician.submitted-job-print', $jobSubmission->id) }}" class="btn btn-outline-info" title="Print" target="_blank">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th><strong>{{trans('label.DATE')}}</strong></th>
                                <td>{{$jobSubmission->submission_date->format('m/d/Y')}}</td>
                            </tr>  
                            <tr>
                                <th><strong>{{trans('label.TICKET_SL')}}</strong></th>
                                <td>TSL-{{$jobSubmission->job->ticket->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.JOB NUMBER')}}</strong></th>
                                <td>JSL-{{$jobSubmission->job->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.CUSTOMER')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->purchase->customer->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.PHONE')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->purchase->customer->mobile}}</td>
                            </tr> 
                            <tr>
                                <th><strong>{{trans('label.PRODUCT')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->category->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.BRAND')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->purchase->brand->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.BRAND_MODEL')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->purchase->modelname->model_name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.PRODUCT_SERIAL')}}</strong></th>
                                <td>{{$jobSubmission->job->ticket->purchase->product_serial}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.SERVICE_AMOUNT')}}</strong></th>
                                <td>{{$jobSubmission->service_amount}}</td>
                            </tr>
                            @isset($jobSubmission->createdBy)
                                <tr>
                                    <td><strong>{{ __('Created By') }}</strong></td>
                                    <td>{{ $jobSubmission->createdBy->name }}</td>
                                </tr>
                            @endisset
                            @isset($jobSubmission->updatedBy)
                                <tr>
                                    <td><strong>{{ __('Updated By') }}</strong></td>
                                    <td>{{ $jobSubmission->updatedBy->name }}</td>
                                </tr>
                            @endisset
                            <tr>
                                <td><strong>{{ __('Created At') }}</strong></td>
                                <td>{{ $jobSubmission->created_at->format('m/d/yy H:i:s') }}</td>
                            </tr>
                            @isset($jobSubmission->remark)
                                <tr>
                                    <th><strong>{{trans('label.REMARK')}}</strong></th>
                                    <td>{{isset($jobSubmission->remark) ? $jobSubmission->remark : ''}}</td>
                                </tr>
                            @endisset
                        </table>
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART')}}</th>
                                    <th>{{ __('label.QUANTITY')}}</th>
                                    <th>{{ __('label.UNIT_PRICE_BDT')}}</th>
                                    <th>{{ __('label.AMOUNT')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$jobSubmissionDetails->isEmpty())
                                    <?php
                                    $sl = 0;
                                    $total = 0;
                                    ?>
                                    @foreach($jobSubmissionDetails as $item)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{$item->part->code ?? null}}-{{$item->part->name ?? null}}</td>
                                        <td>{{$item->used_quantity}}</td>
                                        <td>{{number_format($item->selling_price_bdt,2)}}</td>
                                        <td>{{number_format($item->selling_price_bdt * $item->used_quantity,2)}}
                                        @php
                                            $total+=$item->selling_price_bdt;
                                        @endphp
                                        </td>
                                    </tr>
                                    @endforeach

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                        <table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Subtotal For Spare:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->subtotal_for_spare, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Subtotal For Servicing:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->subtotal_for_servicing, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Fault Finding Charges:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->fault_finding_charges, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Repair Charges:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->repair_charges, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Vat:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->vat, 2)}}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Other Charges:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->other_charges, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Discount:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Advance Amount:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->advance_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <th style="width: 50%;">
                                    <strong>Total Amount:</strong>
                                </th>
                                <td>{{ number_format($jobSubmission->total_amount, 2) }}</td>
                            </tr>
                        </table>
                        <hr class="mt-2 mb-3"/>
                        @isset($JobAttachment)
                        <div><h6><strong>Attachment Info:</strong></h6></div>
                        <div class="row mb-2">
                            @foreach ($JobAttachment as $item)
                                @foreach (json_decode($item->name) as $attachment)
                                <div class="col-sm-3">
                                    <label for="date">{{'Attachment No: '.$loop->iteration}}</label>
                                    <img id="" class="rounded mx-auto d-block mt-3 mb-3" src="{{ asset('attachments/'.$attachment) }}" alt="Unavailable" height="150px" width="150px"> <br>
                                    <a  href="{{ route('technician.photo.download', $attachment) }}">
                                        <i class="fa fa-download" aria-hidden="true" title="Download"></i>
                                    </a>
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/print.js') }}"></script>
    @endpush
@endsection
