@extends('layouts.main')
@section('title', 'Part Withdraw Details')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
    <link rel="stylesheet" href="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.css')}}">

    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-user-md bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('PART WITHDRAW')}}</h5>
                            <span>{{ __('Part Withdraw Details')}}</span>
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
                                <h3>@lang('Part Withdraw Details')</h3>
                                <div class="card-header-right">
                                    @if (($auth_role->name == 'admin' || $auth_role->name == 'Super Admin') && $partWithdraw->status == 0)
                                        {{-- <a class="btn btn-info" onclick="approve({{ $job->id }})">
                                            Approve Now
                                        </a> --}}
                                        <a class="btn btn-info" data-id="{{ $partWithdraw->id }}"  id="approve" title="Click Approve Now">Approve Now</a>
                                    @elseif (($auth_role->name == 'admin' || $auth_role->name == 'Super Admin')&& $partWithdraw->status == 1 && $partWithdraw->job->withdraw_request == 2)
                                    <button class="btn btn-danger" disabled>
                                        Approved Already
                                    </button> 
                                    @endif
                                </div>
                            </div>
                            <div class="pt-5 pb-5">
                                <table id="table" class="table">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Parts Info</th>
                                            <th>Used Quantity</th>
                                            <th>Withdraw Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($partWithdraw->withdrawdetails as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->part->name.'-'.$item->part->code }}</td>
                                                <td>{{ $item->used_qnty }}</td>
                                                <td>{{ $item->required_qnty }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <div><h6><strong>Job info :</strong></h6></div>
                            <table class="table table-striped table-bordered table-hover">
                                @if ($partWithdraw->job->status == 2)
                                    <tr>
                                        <th style="color: rgb(255, 0, 0)" ><strong>{{trans('label.REASON_OF_REJECT')}}</strong></th>
                                        <td style="color: rgb(255, 0, 0); width:80%">{{$partWithdraw->job->rejectNote ? $partWithdraw->job->rejectNote->decline_note : 'Not Found'}}</td>
                                    </tr>
                                @endif
                                @if ($partWithdraw->job->status == 1)
                                <tr>
                                    <th style="color: rgb(2, 153, 52)" ><strong>{{trans('label.JOB_STARTED_ON')}}</strong></th>
                                    <td style="color: rgb(2, 153, 52); width:80%">
                                    @isset($partWithdraw->job->job_start_time)
                                    {{$partWithdraw->job->job_start_time->format('m/d/yy H:i:s')}}
                                        @endisset
                                    </td>
                                </tr>
                                @endif
                                @if($partWithdraw->job->status==4 && $partWithdraw->job->is_ended==1)
                                    <tr>
                                        <th style="color: rgb(2, 153, 52)" ><strong>{{trans('label.JOB_ENDED_ON')}}</strong></th>
                                        <td style="color: rgb(2, 153, 52); width:80%">
                                        @isset($partWithdraw->job->job_end_time)
                                        {{$partWithdraw->job->job_end_time->format('m/d/yy H:i:s')}}
                                            @endisset
                                        </td>
                                    </tr>
                                @endif
                                {{-- Ticket Re-Open --}}
                                @if ( $partWithdraw->job->is_ticket_reopened_job == 1 )
                                    <tr>
                                        <td style="color: rgb(255, 0, 0)" ><strong>Re-Open Note</strong></td>
                                        <td style="color: rgb(255, 0, 0)">{{ $partWithdraw->job->ticket->reopen_note }}</td>
                                    </tr>
                                @endif
                                @isset($partWithdraw->job->pendingNotes)
                                <tr>
                                    <th><strong>Pending Status</strong></th>
                                    <td>
                                        <ol>
                                            @foreach ($partWithdraw->job->pendingNotes as $item)
                                            <li style="font-weight: bold; color:red">{{ $item->job_pending_remark.'-'.$item->job_pending_note }} - {{ $item->created_at->format('l jS \\of F Y h:i:s A') }} </li> 
                                            @endforeach 
                                        </ol>
                                    </td>
                                </tr>
                                @endisset
                                <tr>
                                    <th><strong>{{trans('label.JOB_NUMBER')}}</strong></th>
                                    <td>{{'JSL-'.$partWithdraw->job->id}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TECHNICIAN')}}</strong></th>
                                    <td>{{$partWithdraw->job->employee->name ?? null}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{ __('label.ASSIGNED_DATE')}}</strong></th>
                                    <td>{{$partWithdraw->job->date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{ __('label.ASSIGNED_BY')}}</strong></th>
                                    <td>{{$partWithdraw->job->createdBy->name}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TICKET_SL')}}</strong></th>
                                    <td>TSL-{{$partWithdraw->job->ticket->id}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.TICKET_CREATED_AT')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->created_at->format('m/d/yy H:i:s')}}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CUSTOMER_NAME')}}</strong></td>
                                    <td>{{ $partWithdraw->job->ticket->purchase->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CUSTOMER_GRADE')}}</strong></td>
                                    <td>
                                        <span class="badge badge-success">
                                            @if(isset($partWithdraw->job->ticket->purchase->customer->grade->name)) {{ $partWithdraw->job->ticket->purchase->customer->grade->name }} @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.PHONE')}}</strong></td>
                                    <td>{{ $partWithdraw->job->ticket->purchase->customer->mobile}}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.ADDRESS')}}</strong></td>
                                    <td>{{ $partWithdraw->job->ticket->purchase->customer->address }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_CATEGORY')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->purchase->category->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.BRAND_NAME')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->purchase->brand->name ?? Null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_NAME')}}</strong></th>
                                    <td>
                                        @isset($partWithdraw->job->ticket->purchase->modelname)
                                            {{$partWithdraw->job->ticket->purchase->modelname->model_name ?? null}}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.PRODUCT_SERIAL')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->purchase->product_serial ?? Null }}</td>
                                </tr>
                                @isset ($partWithdraw->job->ticket->fault_description_id)
                                    <tr>
                                        <th><strong>{{trans('label.FAULT_DESCRIPTION')}}</strong></th>
                                        <?php $faults=json_decode($partWithdraw->job->ticket->fault_description_id);?>
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
                                @if ($partWithdraw->job->ticket->fault_description_note)
                                    <tr>
                                        <td><strong>{{ __('label.FAULT_DESCRIPTION_NOTE')}}</strong></td>
                                        <td style="font-weight: bold; color:red">{{ $partWithdraw->job->ticket->fault_description_note }}</td>
                                    </tr>
                                @endif
                                @isset ($partWithdraw->job->ticket->accessories_list_id)
                                    <tr>
                                        <th><strong>{{trans('label.ACCESSORIES_LIST')}}</strong></th>
                                        <?php $accessories=json_decode($partWithdraw->job->ticket->accessories_list_id)?>
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
                                    <td>{{ $partWithdraw->job->ticket->receive_mode->name ?? null }}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.EXPECTED_DELIVERY_MODE')}}</strong></th>
                                    <td>{{ $partWithdraw->job->ticket->deivery_mode->name ?? null}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.START_DATE')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->start_date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.END_DATE')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->end_date->format('m/d/Y')}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.CUSTOMER_NOTE')}}</strong></th>
                                    <td>{{$partWithdraw->job->ticket->customer_note}}</td>
                                </tr>
                                <tr>
                                    <th><strong>{{trans('label.JOB_STATUS')}}</strong></th>
                                    <td>
                                        @if ($partWithdraw->job->status == 6)
                                            <span class="badge badge-red">Paused</span>                                        
                                        @elseif( $partWithdraw->job->status == 5 )
                                            <span class="badge badge-orange">Pending</span>                                        
                                        @elseif($partWithdraw->job->status == 0)                                        
                                            <span class="badge badge-yellow">Created</span>                                       
                                        @elseif($partWithdraw->job->status == 4 )                                        
                                            <span class="badge badge-info">Job Completed</span>
                                        @elseif($partWithdraw->job->status == 3 )                                        
                                            <span class="badge badge-success">Job Started</span>                                        
                                        @elseif($partWithdraw->job->status == 1)                                        
                                            <span class="badge badge-success">Accepted</span>
                                        @elseif($partWithdraw->job->status==2)
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_GENERAL_PARTS')}}</strong></td>
                                    <td>
                                        @isset($partWithdraw->job->ticket->purchase->general_warranty_date)
                                        {{ $partWithdraw->job->ticket->purchase->general_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_SPECIAL_PARTS')}}</strong></td>
                                    <td>
                                        @isset($partWithdraw->job->ticket->purchase->general_warranty_date)
                                        {{ $partWithdraw->job->ticket->purchase->special_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.WARRANTY_END_FOR_SERVICE')}}</strong></td>
                                    <td>
                                        @isset($partWithdraw->job->ticket->purchase->service_warranty_date)
                                        {{ $partWithdraw->job->ticket->purchase->service_warranty_date->format('m/d/Y') }}
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('label.CREATED_AT')}}</strong></td>
                                    <td>{{ $partWithdraw->job->created_at->format('m/d/yy H:i:s') }}</td>
                                </tr>
                                @isset($partWithdraw->job->job_close_remark)
                                <tr>
                                    <td><strong>Job Closing Remarks</strong></td>
                                    <th>

                                        {{ $partWithdraw->job->job_close_remark }}
                                    </th>
                                </tr>
                                @endisset

                                @isset($partWithdraw->job->job_ending_remark)
                                <tr>
                                    <td><strong>{{ __('label.JOB ENDING REMARK')}}</strong></td>
                                    <th>

                                        {{ $partWithdraw->job->job_ending_remark }}
                                    </th>
                                </tr>
                                @endisset
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-toast-plugin/dist/jquery.toast.min.js')}}"></script>
    <script>
        $(document).ready(function(){
//             $("#approve").click(function(){
//                 var id=$(this).attr('data-id');
// alert(id);
//             });
        $('.card-body').on('click', '#approve', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
                swal({
                    title: `Are you sure?`,
                    text: `Want to approve the request?`,
                    buttons: true,
                    dangerMode: true,
                }).then((changeStatus) => {
                    if (changeStatus) {
                        var url = '{{ route("technician.withdraw-request.approve",":id") }}';
                        $.ajax({
                            type: "GET",
                            url: url.replace(':id', id),
                            success:function(resp)
                            {
                                // alert(resp);
                                console.log(resp);
                                window.location.reload();
                                if (resp.success === true) {
                                    $.toast({
                                        heading: 'Success',
                                        text: resp.message,
                                        position: 'top-right',
                                        showHideTransition: 'slide',
                                        stack: false,
                                        icon: 'success'
                                    })
                                }else{
                                    $.toast({
                                        heading: 'Success',
                                        text: resp.message,
                                        position: 'top-right',
                                        showHideTransition: 'slide',
                                        stack: false,
                                        icon: 'warning'
                                    })
                                }
                            },
                            error: function(jqXHR, textStatus, errorMessage) {
                                console.log(errorMessage);
                            }
                        });
                    }
                });
            })
        })
    </script>
    @endpush
@endsection
