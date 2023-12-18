@extends('layouts.main')
@section('title', 'Job Details ')
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
                        <i class="fas fa-user-md bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Job Details')}}</h5>
                            <span>JSL-{{$job->id}} - {{ __('Job Details')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('job.job-claim', $job->id) }}" class="btn btn-info" title="Claim" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    CLAIM
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('job.job-slip', $job->id) }}" class="btn btn-success" title="Slip" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    SLIP
                                </a>
                            </li>

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
                        <div class="card-header-left p-1">
                            @php
                                $role=Auth::user()->roles->first();
                            @endphp
                            @if ($job->is_ended != 1 && $role->name == "Technician")
                                @if ($job->status != 2)
                                    @can('create')
                                        <a href="{{url('technician/requisition-by-job', $job->id)}}" class="btn btn-primary">
                                            <i class="fas fa-plane-departure"></i>
                                            Part Requisition
                                        </a>
                                        <a href="{{url('technician/consumption-by-job', $job->id)}}" class="btn btn-info">
                                            <i class="fas fa-dolly"></i>
                                            Consume Part
                                        </a>
                                        @if ($customerAdvancedPayment == null)
                                            <a href="{{route('customer-advanced-payment.create-payment', $job->id)}}" class="btn btn-secondary">
                                                <i class="far fa-money-bill-alt"></i>
                                                Advance Payment
                                            </a>
                                        @endif
                                        <a href="{{url('technician/submission/photo/upload', $job->id)}}" class="btn btn-danger" title="Photo Upload">
                                            <i class="fas fa-camera"></i>
                                            Attachment
                                        </a>
                                    @endcan
                                @endif
                                @if($job->status != 2 && $job->is_ended == 0)
                                    <a href="" class="btn btn-warning" data-toggle="modal" data-target="#pendingJobModal" title="Status can't be changed">
                                        <i class="fas fa-comments"></i>
                                        Pending Status
                                    </a>
                                @endif
                                @if($job->status == 0)
                                        <a href="{{route('job.accept-job', $job->id)}}" class="btn btn-success">
                                            <i class='fas fa-check-circle'></i>
                                            Accept
                                        </a>
                                        <a href="" class="btn btn-danger" id="" data-toggle="modal" data-target="#demoModal" data-jobid="{{ $job->id }}"">
                                            <i class='fas fa-times'></i>
                                            Reject
                                        </a>
                                @elseif($job->status == 2)
                                    <button class="btn btn-danger" title="This Job Is Rejeted">
                                        <i class='fas fa-times'></i>
                                        Rejected
                                    </button>
                                @endif
                                @if($job->status != 0 && $job->is_started == 1 )
                                        @if ($job->is_submitted != 1)
                                            <a href="{{ route('technician.job-submission-create', $job->id) }}" class="btn btn-info" title="End Now">
                                                <i class="far fa-smile"></i>
                                                Submit
                                            </a>    
                                        @else
                                            <button class="btn btn-info" title="Already Submitted" disabled><i class="far fa-smile"></i> Submitted </button> 
                                        @endif
                                @endif
                                @if($job->is_ended == 0 && $job->is_submitted == 1)
                                        <a href="" class="btn bg-red text-white" data-toggle="modal" data-target="#endJobModal" title="End Now">
                                            <i class='fas fa-cut'></i>
                                            End Job
                                        </a>
                                @endif
                                @if($job->status !=0 )
                                    @if ($job->is_started == 1 && $job->is_paused == 0)
                                    <a href="{{route('job.start-job', $job->id)}}" class="btn btn-success" title="Job is started, pause now">
                                        <i class='far fa-pause-circle'></i>
                                        Pause Job
                                    </a>
                                    @elseif($job->is_started == 1 && $job->is_paused == 1)
                                    <a href="{{route('job.start-job', $job->id)}}" class="btn btn-success" title="Job is paused, restart now">
                                        <i class='far fa-play-circle'></i>
                                        Re-Start Job
                                    </a> 
                                    @else
                                    <a href="{{route('job.start-job', $job->id)}}" class="btn btn-success" title="Start Job">
                                        <i class='fas fa-check'></i>
                                        Start Job
                                    </a> 
                                    @endif
                                @endif 
                            @endif
                            @if (($role->name == "Technician" || $role->name == "Team Leader") && $job->is_ended == 1 && $job->withdraw_request==0)
                                <a href="{{route('technician.withdraw', $job->id)}}" class="btn btn-danger" title="CLick To Send A Request">
                                    <i class="fas fa-dolly"></i>
                                    Withdraw
                                </a> 
                            @elseif ($job->withdraw_request==1)
                                <button class="btn btn-success" disabled="disabled" title="Request Sent Already">Withdraw</button>
                            @elseif ($job->withdraw_request==2)
                                <button class="btn btn-warning" disabled="disabled" title="Withdrawing Request Approved">Approved</button>
                            @endif

                         </div>
                    </div>
                    <div class="card-body">
                        <div class="print">
                            <div class="card-header">
                                <h3>@lang('Job Details')</h3>
                                <div class="card-header-right">
                                </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover">
                                @if ($job->status == 2)
                                    <tr>
                                        <th style="color: rgb(255, 0, 0)" ><strong>{{trans('label.REASON_OF_REJECT')}}</strong></th>
                                        <td style="color: rgb(255, 0, 0); width:80%">{{$job->rejectNote ? $job->rejectNote->decline_note : 'Not Found'}}</td>
                                    </tr>
                                @endif
                                @if ($job->status != 0)
                                <tr>
                                    <th style="color: rgb(2, 153, 52)" ><strong>{{trans('label.JOB_STARTED_ON')}}</strong></th>
                                    <td style="color: rgb(2, 153, 52); width:80%">
                                    @isset($job->job_start_time)
                                    {{$job->job_start_time->format('m/d/yy H:i:s')}}
                                        @endisset
                                    </td>
                                </tr>
                                @endif
                                @if($job->status == 4 )
                                    <tr>
                                        <th style="color: rgb(2, 153, 52)" ><strong>{{trans('label.JOB_ENDED_ON')}}</strong></th>
                                        <td style="color: rgb(2, 153, 52); width:80%">
                                        @isset($job->job_end_time)
                                        {{$job->job_end_time->format('m/d/yy H:i:s')}}
                                            @endisset
                                        </td>
                                    </tr>
                                @endif
                                {{-- Ticket Re-Open --}}
                                @if ( $job->is_ticket_reopened_job == 1 )
                                    <tr>
                                        <td style="color: rgb(255, 0, 0)" ><strong>Re-Open Note</strong></td>
                                        <td style="color: rgb(255, 0, 0)">{{ $job->ticket->reopen_note }}</td>
                                    </tr>
                                @endif
                                @isset($job->pendingNotes)
                                    <tr>
                                        <th><strong>Pending Status</strong></th>
                                        <td>
                                            <ol>
                                                @foreach ($job->pendingNotes as $item)
                                                <li style="font-weight: bold; color:red">{{ $item->job_pending_remark }}, {{$item->special_components}}, {{ $item->job_pending_note }} | {{ $item->created_at->format('l jS \\of F Y h:i:s A') }} </li> 
                                                @endforeach 
                                            </ol>

                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Pending for Special Component :</th>
                                        <td>
                                            @if ($job->pendingNotes && count($job->pendingNotes) > 0)
                                                <ul>
                                                    @foreach ($job->pendingNotes as $item)
                                                        @if (!empty($item->special_components))
                                                            @foreach (json_decode($item->special_components, true) as $special_component)
                                                                <li>{{ $special_component }}</li>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>Unavailable</p>
                                            @endif                                        
                                        </td>
                                    </tr>                                     --}}
                                @endisset
                                <tr>
                                    <th><strong>{{trans('label.JOB_NUMBER')}}</strong></th>
                                    <td>{{'JSL-'.$job->id}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TECHNICIAN')}}</strong></th>
                                    <td>{{$job->employee->name ?? null}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{ __('label.ASSIGNED_DATE')}}</strong></th>
                                    <td>{{$job->date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{ __('label.ASSIGNED_BY')}}</strong></th>
                                    <td>{{$job->createdBy->name}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TICKET_SL')}}</strong></th>
                                    <td>TSL-{{$job->ticket->id}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TICKET_CREATED_AT')}}</strong></th>
                                    <td>{{$job->ticket->created_at->format('m/d/yy H:i:s')}}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CUSTOMER_NAME')}}</strong></td>
                                    <td>{{ $job->ticket->purchase->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CUSTOMER_GRADE')}}</strong></td>
                                    <td>
                                        <span class="badge badge-success">
                                            @if(isset($job->ticket->purchase->customer->grade->name)) {{ $job->ticket->purchase->customer->grade->name }} @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.PHONE')}}</strong></td>
                                    <td>{{ $job->ticket->purchase->customer->mobile}}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.ADDRESS')}}</strong></td>
                                    <td>{{ $job->ticket->purchase->customer->address }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_CATEGORY')}}</strong></th>
                                    <td>{{$job->ticket->purchase->category->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.BRAND_NAME')}}</strong></th>
                                    <td>{{$job->ticket->purchase->brand->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_NAME')}}</strong></th>
                                    <td>
                                        @isset($job->ticket->purchase->modelname)
                                            {{$job->ticket->purchase->modelname->model_name ?? null}}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_SERIAL')}}</strong></th>
                                    <td>{{$job->ticket->purchase->product_serial ?? Null }}</td>
                                </tr>
                                @isset ($job->ticket->fault_description_id)
                                    <tr>
                                        <th><strong>{{trans('label.FAULT_DESCRIPTION')}}</strong></th>
                                        <?php $faults=json_decode($job->ticket->fault_description_id);?>
                                        <td>
                                            @foreach($allFaults as $fault)
                                                @if ($fault != null && $faults !=null)
                                                    @if(in_array($fault->id, $faults))
                                                        <span class="badge badge-warning">{{$fault->name}}</span>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endisset
                                @if ($job->ticket->fault_description_note)
                                    <tr>
                                        <td><strong>{{ __('label.FAULT_DESCRIPTION_NOTE')}}</strong></td>
                                        <td style="font-weight: bold; color:red">{{ $job->ticket->fault_description_note }}</td>
                                    </tr>
                                @endif
                                @isset ($job->ticket->accessories_list_id)
                                    <tr>
                                        <th><strong>{{trans('label.ACCESSORIES_LIST')}}</strong></th>
                                        <?php $accessories=json_decode($job->ticket->accessories_list_id)?>
                                        <td>
                                            @foreach($allAccessories as $accessory)
                                            @if ($accessory != null && $accessories !=null)
                                                @if(in_array($accessory->id, $accessories))
                                                    <span class="badge badge-success">{{$accessory->accessories_name}}</span>
                                                @endif
                                            @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endisset
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_RECEIVE_MODE')}}</strong></th>
                                    <td>{{ $job->ticket->receive_mode->name ?? null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.EXPECTED_DELIVERY_MODE')}}</strong></th>
                                    <td>{{ $job->ticket->deivery_mode->name ?? null}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.START_DATE')}}</strong></th>
                                    <td>{{$job->ticket->start_date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.END_DATE')}}</strong></th>
                                    <td>{{$job->ticket->end_date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.CUSTOMER_NOTE')}}</strong></th>
                                    <td>{{$job->ticket->customer_note}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.JOB_STATUS')}}</strong></th>
                                    <td>
                                        @if ($job->status == 6)
                                            <span class="badge badge-red">Paused</span>                                        
                                        @elseif( $job->status == 5 )
                                            <span class="badge badge-orange">Pending</span>                                        
                                        @elseif($job->status == 0)                                        
                                            <span class="badge badge-yellow">Created</span>                                       
                                        @elseif($job->status == 4 )                                        
                                            <span class="badge badge-info">Job Completed</span>
                                        @elseif($job->status == 3 )                                        
                                            <span class="badge badge-success">Job Started</span>                                        
                                        @elseif($job->status == 1)                                        
                                            <span class="badge badge-success">Accepted</span>
                                        @elseif($job->status==2)
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS')}}</strong></td>
                                    <td>
                                        @isset($job->ticket->purchase->general_warranty_date)
                                        {{ $job->ticket->purchase->general_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS')}}</strong></td>
                                    <td>
                                        @isset($job->ticket->purchase->general_warranty_date)
                                        {{ $job->ticket->purchase->special_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_SERVICE')}}</strong></td>
                                    <td>
                                        @isset($job->ticket->purchase->service_warranty_date)
                                        {{ $job->ticket->purchase->service_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CREATED_AT')}}</strong></td>
                                    <td>{{ $job->created_at->format('m/d/yy H:i:s') }}</td>
                                </tr>

                                @isset($job->job_close_remark)
                                <tr>
                                    <td><strong>Job Closing Remarks</strong></td>
                                    <th>

                                        {{ $job->job_close_remark }}
                                    </th>
                                </tr>
                                @endisset

                                @isset($job->job_ending_remark)
                                <tr>
                                    <td><strong>{{ __('label.JOB ENDING REMARK')}}</strong></td>
                                    <th>

                                        {{ $job->job_ending_remark }}
                                    </th>
                                </tr>
                                @endisset
                            </table>
                        </div>
                        {{-- Advanced Payment --}}
                        <hr class="mt-2 mb-3"/>
                        @isset($customerAdvancedPayment)
                        <div><h6><strong>Advanced Payment :</strong></h6></div>
                            <table id="datatable" class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.ADV_MR_NO')}}</th>
                                        <th>{{ __('label.ADVANCE RECEIPT DATE')}}</th>
                                        <th>{{ __('label.JOB NO')}}</th>
                                        <th>{{ __('label.BRANCH')}}</th>
                                        <th>{{ __('label.CUSTOMER NAME')}}</th>
                                        <th>{{ __('label.CUSTOMER MOBILE')}}</th>
                                        <th>{{ __('label.PRODUCT NAME')}}</th>
                                        <th>{{ __('label.PRODUCT SL')}}</th>
                                        <th>{{ __('label.ADVANCE AMOUNT')}}</th>
                                        <th>{{ __('label.ACTION')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$customerAdvancedPayment->adv_mr_no}}</td>
                                        <td>
                                            {{\Carbon\Carbon::parse($customerAdvancedPayment->advance_receipt_date)->format('m/d/Y')}}
                                        </td>
                                        <td>{{'JSL-'.$customerAdvancedPayment->job->id}}</td>
                                        <td>{{$customerAdvancedPayment->branch->name}}</td>
                                        <td>{{$customerAdvancedPayment->customer_name}}</td>
                                        <td>{{$customerAdvancedPayment->customer_phone}}</td>
                                        <td>{{$customerAdvancedPayment->product_name}}</td>
                                        <td>{{$customerAdvancedPayment->product_sl}}</td>
                                        <td>{{$customerAdvancedPayment->advance_amount}}</td>
                                        <td>
                                            <div class='text-center' style="width: max-content;">
                                                @can('edit')
                                                <a href="{{route('customer-advanced-payment.edit', $customerAdvancedPayment->id)}}" class="show-direct-parts-sell">
                                                    <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                </a>
                                                @endcan
                                                @can('show')
                                                <a href="{{route('customer-advanced-payment.show', $customerAdvancedPayment->id)}}" class="show-direct-parts-sell">
                                                    <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                </a>
                                                @endcan
                                                @can('delete')
                                                {{ Form::open(['route' => ['customer-advanced-payment.destroy', $customerAdvancedPayment->id], 'method' => 'DELETE', 'class'=>'delete d-line'] ) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                    <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                {{ Form::close() }}
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endisset
                        {{-- Submitted Job info --}}
                        <hr class="mt-2 mb-3"/>
                        <div><h6><strong>Submitted Job info :</strong></h6></div>
                        @isset($submittedJobs)
                            <table id="table" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('label.DATE')}}</th>
                                        <th>{{ __('Ticket Number')}}</th>
                                        <th>{{ __('label.JOB NUMBER')}}</th>
                                        <th>{{ __('label.STATUS')}}</th>
                                        <th>{{ __('label.ACTION')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($i=1)
                                    @foreach($submittedJobs as $submittedJob)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$submittedJob->submission_date->format('m/d/Y')}}</td>
                                            <td>TSL-{{$submittedJob->job->ticket->id}}</td>
                                            <td>{{'JSL-'.$submittedJob->job->id}}</td>
                                            <td>
                                            @if ($submittedJob->job->is_ticket_reopened_job == 1)
                                                <span class="badge badge-danger">Re Opened Ticket</span>
                                            @else
                                                <span class="badge badge-success">Normal</span>
                                            @endif
                                            </td>
                                            <td>
                                                <div style="width: max-content;">
                                                    @can('edit')
                                                        <a  href="{{ route('technician.submitted-jobs.edit', $submittedJob->id) }}">
                                                            <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                        </a>
                                                    @endcan

                                                    @can('show')
                                                        <a  href="{{ route('technician.submitted-job-show', $submittedJob->id) }}">
                                                            <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                        </a>
                                                    @endcan

                                                    @can('delete')
                                                        <form class="delete d-inline" action="{{ route('technician.submitted-jobs.destroy', $submittedJob->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                                <i class="ik ik-trash-2 f-16 text-red"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endisset
                        {{-- Ticket's Attachment --}}
                        <hr class="mt-2 mb-3"/>
                        <fieldset class="form-group border p-3" style="background: #f0f0f0">
                            <legend class="w-auto">Ticket's Attachment Info</legend>
                            @if($job->ticket->ticketAttachments)
                                <div class="row mb-2">
                                    @foreach ($job->ticket->ticketAttachments as $item)
                                        @foreach (json_decode($item->name) as $attachment)
                                        <div class="col-sm-2">
                                            <a  href="{{ route('technician.photo.download', $attachment) }}" class="ml-10">
                                                <img id="" class="rounded mx-auto d-block mt-3 mb-3" src="{{ asset('attachments/'.$attachment) }}" alt="Not Found" height="150px" width="150px" title="Download">
                                            </a>
                                        </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        </fieldset>

                        {{-- Job's Attachment --}}
                        <hr class="mt-2 mb-3"/>
                        <fieldset class="form-group border p-3" style="background: #f0f0f0">
                            <legend class="w-auto">Job's Attachment Info</legend>
                            @isset($JobAttachment)
                                <div class="row mb-2">
                                    @foreach ($JobAttachment as $item)
                                        @foreach (json_decode($item->name) as $attachment)
                                        <div class="col-sm-2">
                                            <a  href="{{ route('technician.photo.download', $attachment) }}" class="ml-10">
                                                <img id="" class="rounded mx-auto d-block mt-3 mb-3" src="{{ asset('attachments/'.$attachment) }}" alt="Not Found" height="150px" width="150px" title="Download">
                                            </a>
                                        </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @endisset
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.REJECT_NOTE')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="" method="POST" action="{{ route('job.deny-job') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="job_number" class="col-form-label">{{ __('label.JOB_NUMBER')}}</label>
                                    <input type="hidden" class="form-control" id="job_id" name="job_id" value="{{$job->id}}">
                                    <input type="text" class="form-control" id="job_number" name="job_number" placeholder="{{ __('label.JOB_NUMBER')}}" value="JSL-{{$job->id}}">
                                    @error('job_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="reject_note" class="col-form-label">{{ __('label.REASON_OF_REJECT')}}</label>
                                    <textarea name="reject_note" class="form-control" id="reject_note" cols="12" rows="1" required></textarea>
                                    @error('reject_note')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="endJobModal" tabindex="-1" role="dialog" aria-labelledby="endJobModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="endJobModalLabel">{{ __('label.JOB ENDING REMARK')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{route('job.end-job', $job->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Job Closing Remarks</label>
                            <select name="job_close_remark" id="job_close_remark" class="form-control" required>
                                <option value="">Select Job Closing Remarks</option>
                                @foreach ($jobCloseRemarks as $jobCloseRemark)
                                    <option value="{{ $jobCloseRemark->title }}">
                                        {{ $jobCloseRemark->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('job_close_remark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group my-2">
                            <label for="remark">Remark</label>
                            <textarea name="remark" id="job_close_remark_note" class="form-control" cols="60" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="ending_submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                    </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Pending Job Modal --}}
    <div class="modal fade" id="pendingJobModal" tabindex="-1" role="dialog" aria-labelledby="pendingJobModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pendingJobModal">{{ __('Job Pending Remark')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="jobPendingRemark" class="form-group" action="{{route('job.pending-job', $job->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Job Pending Remarks</label>
                            <select name="job_pending_remark" class="form-control" id="job_pending_remark" required>
                                <option value="">Select Job Pending Remarks</option>
                                @foreach ($jobpendingRemarks as $jobpendingRemark)
                                    <option value="{{ $jobpendingRemark->id }}">
                                        {{ $jobpendingRemark->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('job_pending_remark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" id="pending_for_special_components">
                            <label for="">Pending for Special Component </label>
                        </div>
                        
                        <div class="form-group my-2">
                            <label for="remark">Remark</label>
                            <textarea name="remark" id="job_pending_note" class="form-control" cols="60" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="pending_submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                    </div>
            </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            // Show modal if there are validation errors
            @if ($errors->any())
                $('#demoModal').modal('show');
            @endif

            // Function to enable/disable form elements based on job_pending_remark
            function updateFormState() {
                var job_pending_remark = $('#job_pending_remark').val();
                var isFormEnabled = job_pending_remark !== '';

                $("#job_pending_note, #pending_submit").prop('disabled', !isFormEnabled);
            }

            // Initial form state setup
            updateFormState();

            // Update form state when job_pending_remark value changes
            $('#job_pending_remark').on('change', function (e) {
                updateFormState();
            });

            $("#job_pending_remark").on('change', function (e) {
                var pendingRemark = $(this).val();
                $.ajax({
                    url: "{{ url('job/get-special-component/') }}" + "/" + pendingRemark,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        // Clear existing checkboxes
                        $("#pending_for_special_components").empty();

                        // Generate and append checkboxes based on the received data
                        $.each(response.data, function (index, specialComponent) {
                            var checkbox = $('<div class="form-check">\
                                                <input class="form-check-input" \
                                                    type="radio" \
                                                    name="special_components" \
                                                    value="' + specialComponent.name + '" \
                                                    id="special_components_' + index + '">\
                                                <label class="form-check-label" \
                                                    for="special_components_' + index + '">\
                                                    ' + specialComponent.name + '\
                                                </label>\
                                            </div>');

                            $("#pending_for_special_components").append(checkbox);
                        });
                    }
                });
            });


        });

        $(document).ready(function(){
            //Ending Validation
            var job_close_remark=$('#job_close_remark').val();

            if(job_close_remark){
                $("#job_close_remark_note").prop('disabled', false);
                $("#ending_submit").prop('disabled', false);
            }else{
                $("#job_close_remark_note").prop('disabled', true);
                $("#ending_submit").prop('disabled', true);
                $('#job_close_remark').on('change', function(e){

                var job_close_remark_new=$('#job_close_remark').val();
                if (job_close_remark_new) {
                    $("#job_close_remark_note").prop('disabled', false);
                    $("#ending_submit").prop('disabled', false);
                }else {
                    $("#job_close_remark_note").prop('disabled', true);  
                    $("#ending_submit").prop('disabled', true);
                }

                }) 
            }
        })
    </script>
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/print.js') }}"></script>
    @endpush
@endsection
