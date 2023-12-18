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
                            <span>{{ __('Job Details')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <button id="print" class="btn btn-info">Print</button>
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
            <div class="col-md-12">
                <div class="card p-3">
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
                                @if ($job->status == 1)
                                <tr>
                                    <th style="color: rgb(2, 153, 52)" ><strong>{{trans('label.JOB_STARTED_ON')}}</strong></th>
                                    <td style="color: rgb(2, 153, 52); width:80%">
                                    @isset($job->job_start_time)
                                    {{$job->job_start_time->format('m/d/yy H:i:s')}}
                                        @endisset
                                    </td>
                                </tr>
                                @endif
                                @if($job->status==4 && $job->is_ended==1)
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
                                        @if ($job->pendingNotes && count($job->pendingNotes) > 0)
                                            <ol>
                                                @foreach ($job->pendingNotes as $item)
                                                <li style="font-weight: bold; color:red">{{ $item->job_pending_remark }}, {{$item->special_components}}, {{ $item->job_pending_note }} | {{ $item->created_at->format('l jS \\of F Y h:i:s A') }} </li> 
                                                @endforeach 
                                            </ol>
                                        @else
                                            <p>No Job Pending Remark Available.</p>
                                        @endif
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
                                </tr>  --}}
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
                                    <td>{{ $job->created_at->format('m/d/Y H:i:s') }}</td>
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
                        <div><h6><strong>Advanced Payment :</strong></h6></div>
                        @isset($customerAdvancedPayment)
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
                        <hr class="mt-2 mb-3"/>
                        @isset($JobAttachment)
                            <div><h6><strong>Attachment Info:</strong></h6></div>
                            <div class="row mb-2">
                                @foreach ($JobAttachment as $item)
                                    @foreach (json_decode($item->name) as $attachment)
                                    <div class="col-sm-3">
                                        <label for="date">{{'Attachment No: '.$loop->iteration}}</label>
                                        <img id="" class="rounded mx-auto d-block mt-3 mb-3" src="{{ asset('attachments/'.$attachment) }}" alt="Attachment" height="150px" width="150px"> <br>
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
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.REJECT_NOTE')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{-- {{ Form::open(array('url' => 'employee/job/deny',$job->job_number, 'class' => 'forms-sample', 'id'=>'reject_note','method'=>'POST')) }} --}}
                <form class="" method="POST" action="{{ route('job.deny-job') }}">
                    @csrf
                    {{-- @method('PUT') --}}
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
                                    <textarea name="reject_note" class="form-control" id="reject_note" cols="12" rows="1"></textarea>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row mb-12">
                                    <div class="form-group ml-2">
                                        <label for="remark">Remark</label>
                                        <textarea name="remark" id="" class="form-control" cols="60" rows="1"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                        {{-- <a href="{!! URL::to('tickets/ticket/show', $ticketId) !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/print.js') }}"></script>
    @endpush
@endsection
