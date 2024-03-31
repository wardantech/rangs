@extends('layouts.main')
@section('title', 'Ticket Details')
@section('content')
@push('head')
<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.css') }}">
@endpush
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.SHOW_THICKET') }}</h5>
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('tickets.claim', $ticket->id) }}" class="btn btn-info" title="Claim" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    CLAIM
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('tickets.slip', $ticket->id) }}" class="btn btn-success" title="Slip" target="_blank">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    SLIP
                                </a>
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
                        <h3>{{ __('label.SHOW_THICKET') }}</h3>

                        <div class="card-header-right">
                            <button id="print" class="btn btn-info">Print</button>

                            {{-- @if (($ticket->status == 0 || $ticket->status ==9) && $ticket->is_assigned == 0) --}}
                            @if (($ticket->status == 0 || $ticket->status == 2 || $ticket->status ==9 || $ticket->status ==6) && $ticket->is_assigned == 0)
                                @if ($is_teamleader!=null || $user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Call Center Admin')
                                    @if ($ticket->lastJob()->first()->created_by != Auth::user()->id)
                                        <a href="{{route('job.job_create', $ticket->id)}}" class="btn btn-primary">
                                            <i class='fas fa-tasks'></i>
                                            Assign To Technician
                                        </a>
                                    @endif
                                    @if ($ticket->lastJob()->first()->created_by == Auth::user()->id)
                                        <a href="" class="btn btn-warning" data-toggle="modal" data-target="#ticketTransferModal"  title="Click to Transfer" onclick="setType('1')">
                                            <i class="fa fa-undo" aria-hidden="true"></i>
                                            Transfer Recommendation
                                        </a>
                                    @endif
                                @endif
                            @elseif(($ticket->is_re_assigned == 0 && $ticket->status == 2) || $ticket->status == 13)
                                @if ($is_teamleader!=null || $user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Call Center Admin')
                                    <a href="{{route('job.job_create', $ticket->id)}}" class="btn btn-primary">
                                        <i class='fas fa-check-circle'></i>
                                        Assign To Technician
                                    </a>

                                @endif 
                            @else
                                <button class="btn btn-danger" title="Alredy Assigned">
                                    <i class='fas fa-check-circle'></i>
                                    Assigned
                                </button>      
                            @endif

                            @if($ticket->status == 7  && $ticket->is_delivered_by_teamleader == 0 )
                                    @if ($is_teamleader!=null || $user_role->name == 'Admin' || $user_role->name == 'Super Admin')
                                    <a href="" class="btn btn-primary"  data-toggle="modal" data-target="#ticketDeliveryByTLModal"  title="Click to Delivery">
                                        <i class='fas fa-check-circle'></i>
                                        Delivery By Team Leader
                                    </a>
                                    @endif
                            @elseif($ticket->status == 8)
                                    <button class="btn btn-danger" title="Delivered By Team Leader">
                                        <i class='fas fa-check-circle'></i>
                                    Delivered By TL
                                    </button> 
                             @endif
                            
                            @if($ticket->status == 11) 
                                    @if ($is_teamleader!=null || $user_role->name == 'Admin' || $user_role->name == 'Super Admin')
                                    <a href="{{url('tickets/close-by-teamleader', $ticket->id)}}" class="btn btn-primary" title="Click to Close">
                                        <i class='fas fa-check-circle'></i>
                                        Close By Team Leader
                                    </a>
                                    @endif
                            @elseif($ticket->status == 7)
                                    <button class="btn btn-danger" title="Closed By Team Leader">
                                        <i class='fas fa-check-circle'></i>
                                        Closed By Team Leader
                                    </button> 
                            @endif
                            
                            {{-- @if(($ticket->status == 8 || $ticket->status == 10) && $is_teamleader == null ) --}}
                            @if( $ticket->status == 8 || $ticket->status == 10 )
                                    @if ($ticket->is_delivered_by_call_center == 0 && $is_teamleader == null)
                                        <a href="" class="btn btn-primary" data-toggle="modal" data-target="#ticketDeliveryByCCModal" title="Click to Delivery">
                                            <i class='fas fa-check-circle'></i>
                                            Delivery By (CC)
                                        </a>
                                    @elseif($ticket->status == 10 && $ticket->is_delivered_by_call_center == 1 && $is_teamleader == null )
                                    <button class="btn btn-danger" title="Delivered By TL">
                                        <i class='fas fa-check-circle'></i>
                                        Delivered By CC
                                    </button> 
                                    <a href="" class="btn btn-success" data-toggle="modal" data-target="#demoModal" title="Click To Close">
                                        <i class='fas fa-check-circle'></i>
                                        Close By (CC)
                                    </a>
                                    @endif

                                <a href="{{ route('edit-ticket-details', $ticket->id)}}" class="btn btn-warning" data-toggle="modal" data-target="#ticketReopenModal" title="Click To Re-Open">
                                    <i class='fas fa-check-circle'></i>
                                    Ticket Re-Open (CC & TL)
                                </a>
                            @endif
                            @if ($ticket->status == 12)
                                <button class="btn btn-danger" title="This Ticket Is Closed">
                                    <i class='fas fa-check-circle'></i>
                                    Ticket Is Closed
                                </button>   
                            @endif
                         </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <tbody>
                                @if ( $ticket->is_reopened == 1 )
                                <tr>
                                    <td style="color: rgb(255, 0, 0)" ><strong>Re-Open Note</strong></td>
                                    <td style="color: rgb(255, 0, 0)">{{ $ticket->reopen_note ?? null }}</td>
                                </tr>
                                <tr>
                                    <td style="color: rgb(255, 0, 0)" ><strong>Re-Open Date</strong></td>
                                    <td style="color: rgb(255, 0, 0)">{{ $ticket->reopen_date ? $ticket->reopen_date->format('d/m/Y') : null }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        @if ($ticket->status == 9 && $ticket->is_reopened == 1)
                                            <span class="badge bg-red">Ticket Re-Opened</span>
                                            @elseif( $ticket->status == 0 )
                                            <span class="badge bg-yellow">Created</span>

                                            @elseif($ticket->status == 6 && $ticket->is_pending == 1)
                                            <span class="badge bg-orange">Pending</span>

                                            @elseif($ticket->status == 5 && $ticket->is_paused == 1 )
                                            <span class="badge bg-red">Paused</span>

                                            @elseif($ticket->status == 7 && $ticket->is_closed_by_teamleader == 1)
                                            <span class="badge bg-green">Forwarded to CC</span>
                                            @elseif($ticket->status == 10 && $ticket->is_delivered_by_call_center == 1)
                                            <span class="badge bg-green">Delivered by CC</span>
                                            @elseif($ticket->status == 8 && $ticket->is_delivered_by_teamleader == 1 )
                                            <span class="badge bg-green">Delivered by TL</span>

                                            @elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 1)
                                            <span class="badge badge-danger">Ticket Closed</span>
                                            @elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 0 && $ticket->is_closed == 1)
                                            <span class="badge badge-danger">Ticket Undelivered Closed</span>
                                            @elseif($ticket->status == 11 && $ticket->is_ended == 1)
                                            <span class="badge badge-success">Job Completed</span>

                                            @elseif($ticket->status == 6 && $ticket->is_accepted == 1 && $ticket->is_started == 0 && $ticket->job->job_pending_note != null)
                                            <span class="badge bg-orange">Job Pending</span>

                                            @elseif($ticket->status == 4 && $ticket->is_started == 1)
                                            <span class="badge badge-info">Job Started</span>
                                            @elseif($ticket->status == 3 && $ticket->is_accepted == 1)
                                            <span class="badge badge-primary">Job Accepted</span>
                                            @elseif($ticket->status == 1 && $ticket->is_assigned == 1)
                                            <span class="badge bg-blue">Assigned</span>
                                            @elseif ($ticket->status == 2 && $ticket->is_rejected == 1)
                                            <span class="badge bg-red">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CREATED_AT')}}</td>
                                    <td>{{ $ticket->created_at->format('m/d/Y H:i:s') }}</td>
                                </tr> 
                                <tr>
                                    <td>{{ __('label.SL')}}</td>
                                    <td>TSL-{{ $ticket->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.BRANCH')}}</strong></td>
                                    <td><strong>{{ $ticket->outlet->name ?? null }}</strong></td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_CATEGORY')}}</td>
                                    <td>{{ $ticket->purchase->category->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.BRAND')}}</td>
                                    <td>{{ $ticket->purchase->brand->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_NAME')}}</td>
                                    <td>{{ $ticket->purchase->modelname->model_name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('Product SL')}}</td>
                                    <td>{{$ticket->purchase->product_serial ?? Null}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CUSTOMER')}}</td>
                                    <td>{{ $ticket->purchase->customer->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PHONE')}}</td>
                                    <td>{{ $ticket->purchase->customer->mobile}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CUSTOMER_GRADE')}}</td>
                                    <td>
                                        <span class="badge badge-success">
                                            @if(isset($ticket->purchase->customer->grade->name )) {{ $ticket->purchase->customer->grade->name }} @endif
                                        </span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>{{ __('label.ADDRESS')}}</td>
                                    <td>{{ $ticket->purchase->customer->address }}</td>
                                </tr>
                                @isset($ticket->carrier)
                                    <tr>
                                        <td>{{ __('Carrier Name')}}</td>
                                        <td>{{ $ticket->carrier ?? Null }}</td>
                                    </tr>
                                @endisset
                                
                                @isset($ticket->carrier_phone)
                                    <tr>
                                        <td>{{ __('Carrier Phone')}}</td>
                                        <td>{{ $ticket->carrier_phone ?? Null }}</td>
                                    </tr>
                                @endisset
                                <tr>
                                    <td>{{ __('label.DISTRICT')}}</td>
                                    <td>
                                        @isset($ticket->district)
                                        {{ $ticket->district->name }}
                                         @endisset

                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.THANA')}}</td>
                                    <td>
                                        @isset($ticket->thana)
                                        {{ $ticket->thana->name }}
                                         @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('Purchase Date')}}</td>
                                    <td>
                                        @isset($ticket->purchase->purchase_date)
                                        {{ $ticket->purchase->purchase_date->format('m/d/Y') }}
                                         @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS')}}</td>
                                    <td>
                                        @isset($ticket->purchase->general_warranty_date)
                                        {{ $ticket->purchase->general_warranty_date->format('m/d/Y') }}
                                         @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS')}}</td>
                                    <td>
                                        @isset($ticket->purchase->general_warranty_date)
                                        {{ $ticket->purchase->special_warranty_date->format('m/d/Y') }}
                                         @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.WARRANTY_END_FOR_SERVICE')}}</td>
                                    <td>
                                        @isset($ticket->purchase->service_warranty_date)
                                        {{ $ticket->purchase->service_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.FAULT_DESCRIPTION')}}</td>
                                    <td style="font-weight: bold; color:red">
                                        @php
                                            $faultId = json_decode($ticket->fault_description_id);
                                        @endphp

                                        @foreach ($faults as $fault)
                                        @if ($fault != null && $faultId !=null)
                                            @if (in_array($fault->id, $faultId))
                                                {{ $fault->name }},
                                            @endif
                                        @endif

                                        @endforeach
                                    </td>
                                </tr>
                                @if ($ticket->fault_description_note)
                                    <tr>
                                        <td>{{ __('label.FAULT_DESCRIPTION_NOTE')}}</td>
                                        <td style="font-weight: bold; color:red">{{ $ticket->fault_description_note }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ __('label.CARRIER_NAME')}}</td>
                                    <td>{{ $ticket->purchase->customer->name }}</td>
                                </tr>
      
                                <tr>
                                    <td>{{ __('label.JOB_PRIORITY')}}</td>
                                    <td>{{ $ticket->jobPriority->job_priority }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.SERVICE_TYPE')}}</td>
                                    <td>
                                        <?php $selectedServiceTypeIds= json_decode($ticket->service_type_id)?>
                                        @foreach ($serviceTypes as $serviceType)
                                            @if ($serviceType != null && $selectedServiceTypeIds !=null)
                                                @if (in_array($serviceType->id, $selectedServiceTypeIds))
                                                    {{$serviceType->service_type}}
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.WARRANTY_TYPE')}}</td>
                                    <td>{{$ticket->warrantytype->warranty_type ?? null}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.EXPECTED_START_DATE')}}</td>
                                    <td>{{ $ticket->start_date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.EXPECTED_END_DATE')}}</td>
                                    <td>{{ $ticket->end_date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CUSTOMER_NOTE')}}</td>
                                    <td>{{ $ticket->customer_note }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_RECEIVE_MODE')}}</td>
                                    <td>
                                        {{ $ticket->receive_mode->name ?? null }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.EXPECTED_DELIVERY_MODE')}}</td>
                                    <td>
                                        {{ $ticket->deivery_mode->name ?? null }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.SERVICE_CHARGE')}}</td>
                                    <td>{{ $ticket->service_charge}} BDT.</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_CONDITION')}}</td>
                                    <td>
                                        @php
                                            $productConditionId = json_decode($ticket->product_condition_id);

                                        @endphp

                                        @foreach ($product_conditions as $product)
                                            @if ($product->id != null && $productConditionId != null)
                                                @if (in_array($product->id, $productConditionId))
                                                    {{ $product->product_condition }},
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ __('label.ACCESSORIES_LIST')}}</td>
                                    <td>
                                        @php
                                            $accessoriesId = json_decode($ticket->accessories_list_id);
                                        @endphp

                                        @foreach ($accessories_lists as $accessories_list)
                                            @if ($accessories_list != null && $accessoriesId !=null)
                                                @if (in_array($accessories_list->id, $accessoriesId))
                                                    {{ $accessories_list->accessories_name }},
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                @if(!$customerFeedbacks->isEmpty())
                                <tr>
                                    <td>Customer Feedback</td>
                                    <td>
                                        <table>

                                            @foreach($customerFeedbacks as $index => $customerFeedback)
                                            <tr>
                                                @if ($index == 0 )
                                                <strong>Remark: </strong><p> {{ $customerFeedback->remark }}</p>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td>{{$customerFeedback->question}}:
                                                    @if($customerFeedback->question_feedback==0)
                                                        <strong>NA</strong>
                                                    @elseif($customerFeedback->question_feedback==1)
                                                        <strong>Avarage</strong>
                                                    @elseif($customerFeedback->question_feedback==2)
                                                        <strong>Good</strong>
                                                    @elseif($customerFeedback->question_feedback==3)
                                                        <strong>Great</strong>
                                                    @endif                                                  
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                                    
                                @endif
                                @if ($ticket->createdBy)
                                        <tr>
                                            <td>Created By</td>
                                            <td>{{ $ticket->createdBy->name }}</td>
                                        </tr> 
                                @endif
                                @if ($ticket->updatedBy)
                                    <tr>
                                        <td>Updated By</td>
                                        <td>{{ $ticket->updatedBy->name }}</td>
                                    </tr> 
                                @endif
                            </tbody>
                        </table>
                        
                        @if (!empty($ticket->lastJob()->first()))
                        <hr class="mt-2 mb-3"/>
                        <fieldset class="form-group border p-3" style="background: #ffffff">
                            @if (!empty($ticket->lastJob()->first()->rejectNote))
                            <legend class="w-auto text-center">Last Job Reject Note By Technician</legend>
                            <p class="text-bold"># {{ $ticket->lastJob()->first()->rejectNote->decline_note }}</p>
                            <hr>
                            @endif
                            <div class="mb-5">
                                <h5 class="text-center">Recommendation By Team Leaders</h5>
                            </div>

                            <div class="row">
                                @foreach ($ticket->recommendations as $recommendation)
                                    <div class="col-md-4 ">
                                        <div class="card">
                                            <div class="card-body shadow-lg">
                                                <div>
                                                    <span>#</span><strong>{{ $loop->iteration }}</strong>
                                                    @if ($loop->last && $ticket->status == 2 && $ticket->lastRecommendationByCc->created_by != Auth::user()->id)
                                                    {{-- @dd($ticket->lastRecommendationByCc->created_by != Auth::user()->id) --}}
                                                    {{-- @dd(Auth::user()->roles->first()) --}}
                                                        @if (Auth::user()->roles->first()->name == "Call Centre" || Auth::user()->roles->first()->name == "Call Center Admin" || Auth::user()->roles->first()->name == "Call Center Executive")
                                                            <a href="" class="btn btn-success" data-toggle="modal" data-target="#ticketTransferModal" title="Click to Transfer" onclick="setType('2')">Transfer The Ticket</a>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="">
                                                    <p><strong>Recommended Branch:     </strong>
                                                    {{ $recommendation->outlet->name }}</p>
                                                </div>
                                                <div class="">
                                                    <p><strong>Recommended By: </strong>
                                                    {{ $recommendation->createdBy->name }}</p>
                                                </div>
                                                <div class="">
                                                    <p><strong>Recommended Note: </strong>
                                                    {{ $recommendation->recommend_note }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                        @endif

                        @if (!empty($ticket->lastJob()->first()))
                        <hr class="mt-2 mb-3"/>
                        <fieldset class="form-group border p-3" style="background: #ffffff">
                            <legend class="w-auto text-center">Transfered By Call Centers</legend>
                            {{-- <p class="text-bold"># {{ $ticket->lastJob()->first()->rejectNote->decline_note }}</p>
                            <hr>

                            <div class="mb-5">
                                <h5 class="text-center">Transfered By Call Centers</h5>
                            </div> --}}

                            <div class="row mt-5">
                                @foreach ($ticket->transfers as $transfer)
                                <div class="col-md-4 ">
                                    <div class="card">
                                        <div class="card-body box-shadow">
                                            <div>
                                                <span>#</span><strong>{{ $loop->iteration }}</strong>
                                                {{-- @if ($loop->last || (Auth::user()->roles->first()->name == "Call Center Admin" || Auth::user()->roles->first()->name =="Call Center Executive"))
                                                    <a href=""  class="btn btn-success" data-toggle="modal" data-target="#ticketTransferModal"  title="Click to Transfer" onclick="setType('2')">Transfer The Ticket</a>
                                                @endif --}}

                                            </div>
                                            <div class="">
                                                <p><strong>Recommended Branch: </strong> {{ $transfer->outlet->name }}</p>
                                            </div>
                                            <div class="">
                                                <p><strong>Recommended By: </strong>{{ $transfer->createdBy->name }}</p>
                                            </div>
                                            <div class="d-flex">
                                                <p><strong>Recommended Note: </strong>{{ $transfer->recommend_note }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>
                        @endif

                        <hr class="mt-2 mb-3"/>
                        <fieldset class="form-group border p-3" style="background: #f0f0f0">
                            <legend class="w-auto">Attachment Area</legend>
                            @if($ticket->ticketAttachments)
                                <div class="row mb-2">
                                    @foreach ($ticket->ticketAttachments as $item)
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
                    </div>
                </div>
            </div>

            @if($jobs)
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        <div class="table-responsive" id="purchaseHistoryShow"><strong> {{__('label.JOB_LIST')}}</strong>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('label.TECHNICIAN')}}</th>
                                        <th>{{ __('label.OUTLET')}}</th>
                                        <th>{{ __('label.TICKET_SL')}}</th>
                                        <th>{{ __('label.JOB_NUMBER')}}</th>
                                        <th>{{ __('label.ASSIGNED_DATE')}}</th>
                                        <th>{{ __('label.ASSIGNED_BY')}}</th>
                                        <th>{{ __('label.PRODUCT_CATEGORY')}}</th>
                                        <th>{{ __('label.BRAND_NAME')}}</th>
                                        <th>{{ __('label.PRODUCT_NAME')}}</th>
                                        <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                        <th>{{ __('label.JOB STATUS')}}</th>
                                        <th>{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jobs as $job)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                                @if ($job->employee)
                                                {{$job->employee ? $job->employee->name : 'Not Found' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($job->employee->outlet))
                                                    {{$job->employee->outlet->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @can('show')
                                                    <a href="{{route('show-ticket-details', $job->ticket_id)}}" class="badge badge-primary" title="Ticket Details">TSL-{{$job->ticket->id}}</a>
                                                @endcan
                                            </td>
                                            <td>JSL-{{$job->id}}</td>
                                            <td>{{$job->date->format('m/d/Y')}}</td>
                                            <td>{{$job->createdBy ? $job->createdBy->name : 'Not Found'}}</td>
                                            <td>{{$job->ticket->purchase->category->name}}</td>
                                            <td>{{$job->ticket->purchase->brand->name ?? null}}</td>
                                            <td>
                                                @isset($job->ticket->purchase->modelname)
                                                {{$job->ticket->purchase->modelname->model_name}}
                                                @endisset
                                            </td>
                                            <td>{{$job->ticket->purchase->product_serial}}</td>
                                            <td>
                                                @if ($job->status == 6 )
                                                    <span class="badge badge-red">Paused</span>                                        
                                                @elseif( $job->status == 5)
                                                    <span class="badge badge-orange">Pending</span>                                        
                                                @elseif($job->status == 0)                                        
                                                    <span class="badge badge-yellow">Created</span>                                       
                                                @elseif($job->status == 4 )                                        
                                                    <span class="badge badge-info">Job Completed</span>
                                                @elseif($job->status == 3 )                                        
                                                    <span class="badge badge-success">Job Started</span>                                        
                                                @elseif($job->status == 1 )                                        
                                                    <span class="badge badge-success">Accepted</span>
                                                @elseif($job->status==2)
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="width: max-content;">

                                                    @can('show')
                                                        <a  href="{{ route('job.job.show', $job->id) }}">
                                                            <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                        </a>
                                                    @endcan
                                                    @can('edit')
                                                        <a href="{{ route('job.job.edit', $job->id) }}">
                                                            <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Customer Feedback Modal --}}
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.CUSTOMER FEEDBACK')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'call-center.customer-feedback.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row mb-12">
                                    @foreach($questions as $question)
                                    <div class="col-sm-12">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td width="200">
                                                    <span>{{$question->question}} </span>
                                                    <input type="hidden" name="ticket_id" value="{{$ticketId}}">
                                                    <input type="hidden" name="question_id[]" value="{{$question->id}}">
                                                </td>
                                                <td>
                                                    <input type="radio" name="question{{$question->id}}" id="" value="0">
                                                    <label for="">NA</label>
                                                </td>
                                                <td>
                                                    <input type="radio" name="question{{$question->id}}" id="" value="1">
                                                    <label for="">Avarage</label>
                                                </td>
                                                <td>
                                                    <input type="radio" name="question{{$question->id}}" id="" value="2">
                                                    <label for="">Good</label>
                                                </td>
                                                <td>
                                                    <input type="radio" name="question{{$question->id}}" id="" value="3">
                                                    <label for="">Great</label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    @endforeach
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
                        <a href="{!! URL::to('tickets/ticket/show', $ticketId) !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- Ticket Transfer Modal --}}
    <div class="modal fade" id="ticketTransferModal" tabindex="-1" role="dialog" aria-labelledby="ticketTransferModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketTransferModal">{{ __('Ticket Transfer')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="" id="ticketTransferForm" method="POST" action="{{ url('tickets/transfer') }}">
                    @csrf
                    <input type="hidden" name="type" id="type" value=""> <!-- Hidden input field for type -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="sl_number" class="col-form-label">{{ __('label.TICKET_SL')}}</label>
                                    <input type="hidden" class="form-control" id="ticket_id" name="ticket_id" value="{{$ticket->id}}">
                                    <input type="text" class="form-control" id="sl_number" name="sl_number" value="TSL-{{$ticket->id}}" readonly>
                                    @error('sl_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="outlet_id" class="col-form-label">{{ __('label.OUTLET')}}</label>
                                    <select class="form-control select2" id="outlet_id" name="outlet_id">
                                        <option value="">Select Branch</option>
                                        @foreach($outlets as $outlet)
                                            <option value="{{$outlet->id}}">{{$outlet->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('outlet_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="note" class="col-form-label">{{ __('Recommendation Note') }}</label>
                                    <textarea name="note" class="form-control" id="note" cols="12" rows="1"></textarea>
                                    @error('note')
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

    {{-- Ticket Re Open Note --}}
    <div class="modal fade" id="ticketReopenModal" tabindex="-1" role="dialog" aria-labelledby="ticketReopenModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketReopenModal">{{ __('label.TICKET_RE_OPEN_NOTE')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="" method="POST" action="{{ url('tickets/re-open') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="sl_number" class="col-form-label">{{ __('label.TICKET_SL')}}</label>
                                    <input type="hidden" class="form-control" id="ticket_id" name="ticket_id" value="{{$ticket->id}}">
                                    <input type="text" class="form-control" id="sl_number" name="sl_number" value="TSL-{{$ticket->id}}" readonly>
                                    @error('sl_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="note" class="col-form-label">{{ __('label.NOTE')}}</label>
                                    <textarea name="note" class="form-control" id="note" cols="12" rows="1"></textarea>
                                    @error('note')
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

    {{-- Delivery BY CC Modal --}}
    <div class="modal fade" id="ticketDeliveryByCCModal" tabindex="-1" role="dialog" aria-labelledby="ticketDeliveryByCCModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDeliveryByCCModal">{{ __('Ticket Delivery By Call Center')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="" method="POST" action="{{ url('tickets/product_delivery_call_center') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="sl_number" class="col-form-label">{{ __('label.TICKET_SL')}}</label>
                                    <input type="hidden" class="form-control" id="ticket_id" name="ticket_id" value="{{$ticket->id}}">
                                    <input type="text" class="form-control" id="sl_number" name="sl_number" value="TSL-{{$ticket->id}}" readonly>
                                    @error('sl_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="position-relative form-group">
                                    <div class="border-checkbox-section">
                                        <label class="border-checkbox-label" for="send_sms">
                                            Would you like to send delivery SMS to the customer.
                                        </label>
                                        <input type="radio" id="yes" name="send_sms" value="1" class="send_sms" disabled>
                                        <label for="html">Yes</label>
                                        <input type="radio" id="no" name="send_sms" value="0" class="send_sms" checked>
                                        <label for="no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-center">{{ __('label.SUBMIT')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delivery BY Team Leader Modal --}}
    <div class="modal fade" id="ticketDeliveryByTLModal" tabindex="-1" role="dialog" aria-labelledby="ticketDeliveryByTLModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDeliveryByTLModal">{{ __('Ticket Delivery By Team Leader')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="" method="POST" action="{{ url('tickets/product_delivery_team_leader') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="sl_number" class="col-form-label">{{ __('label.TICKET_SL')}}</label>
                                    <input type="hidden" class="form-control" id="ticket_id" name="ticket_id" value="{{$ticket->id}}">
                                    <input type="text" class="form-control" id="sl_number" name="sl_number" value="TSL-{{$ticket->id}}" readonly>
                                    @error('sl_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="position-relative form-group">
                                    <div class="border-checkbox-section">
                                        <label class="border-checkbox-label" for="send_sms">
                                            Would you like to send delivery SMS to the customer.
                                        </label>
                                        <input type="radio" id="yes" name="send_sms" value="1" class="send_sms" checked>
                                        <label for="html">Yes</label>
                                        <input type="radio" id="no" name="send_sms" value="0" class="send_sms">
                                        <label for="no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-center">{{ __('label.SUBMIT')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- push external js -->
@push('script')
    <script src="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/print.js') }}"></script>
@endpush

<script type="text/javascript">

        $(document).ready( function () {
            // $('#ticketTransferModal').on('shown.bs.modal', function () {
            //     // $('#outlet_id').select2();
            //     $('#outlet_id').select2({
            //         // dropdownAutoWidth: true,
            //         minimumResultsForSearch: 0 // Set to -1 to disable search input
            //     });
            // });
            //  $(document).on("click", '.verification', function (e) {
            //      //This function use for sweetalert confirm message
            //      e.preventDefault();
            //      var form = this;

            //      swal({
            //          title: "Are you sure you want to Delete?",
            //          icon: "warning",
            //          buttons: true,
            //          dangerMode: true,
            //      })
            //      .then((willDelete) => {
            //          if (willDelete) {
            //              form.submit();
            //          }
            //      });

            //  });
        });

         
        function setType(type) {
            // Check if user_id exists in recommendations
            var user_id_exists = <?php echo json_encode($ticket->recommendations->pluck('user_id')->contains(auth()->user()->id)); ?>;
            // var user_id = <?php echo auth()->user()->id; ?>;
            console.log(user_id_exists);
            // If user_id exists, prevent transfer
            // if (user_id_exists) {
            //     alert('You are not allowed to transfer this ticket.');
            //     return false; // Prevent further execution
            // }
            document.getElementById('type').value = type;
        }

         $(document).ready( function () {
            $('#ticketTransferForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Remove previous error messages and validation classes
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                // Serialize the form data
                var formData = $(this).serialize();

                // Send an AJAX request to the server
                $.ajax({
                    url: '{{ url('tickets/transfer') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                        $('#ticketTransferModal').modal('hide');
                        toaster('Success', response.message, "success");
                        // Optionally, you can update the UI or display a success message
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                        toaster('Error', xhr.responseJSON.message, "warning");
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').text(value[0]);
                        });
                        // Optionally, you can display an error message
                    }
                });
            });

         });
         function toaster(heading, message, icon) {
            $.toast({
                heading: heading,
                text: message,
                position: 'top-right',
                showHideTransition: 'slide',
                stack: false,
                icon: icon
            })
        }
</script>
@endsection
