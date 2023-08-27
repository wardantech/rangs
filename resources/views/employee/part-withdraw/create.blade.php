@extends('layouts.main')
@section('title', 'Withdraw')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Part Withdraw')}}</h5>
                            <span>Part Withdraw</span>
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
                        <h3>{{ __('Part Withdraw')}}</h3>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="print">
                            <div class="col-sm-12">
                                <form class="forms-sample" id="" action="{{route('technician.withdraw-request.store')}}" method="POST">
                                    @csrf
                                    <input type="hidden" class="form-control" name="job_id" value="{{ $job->id }}" readonly>
                                    <table id="datatable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Parts Info</th>
                                                <th>Used Quantity</th>
                                                <th>Withdraw Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($consumption as $key=>$item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->part->code }}-{{ $item->part->name }}</td>
                                                <td>{{ $item->stock_out}}</td>
                                                <td>
                                                    <input type="number" name="withdraw_qnty[]" class="form-control" id="withdraw_qnty-{{$key}}" value="" onInput="warning({{$key}})" min="0" step="any" required>
                                                    <input type="hidden" name="inventory_stock_id[]" class="form-control" id="inventory_stock_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="part_id[]" class="form-control" id="part_id" value="{{ $item->part_id }}">
                                                    <input type="hidden" name="used_qnty[]" class="form-control" id="used_qnty-{{$key}}" value="{{ $item->stock_out }}">
                                                </td>
                                            </tr>
                                            @empty
                                            <p>No Data Found</p>  
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @if ($consumption->count() > 0)
                                        <button class="mt-2 btn btn-primary button-prevent-multiple-submits">
                                            <i class="spinner fa fa-spinner fa-spin"></i>
                                            Send Request
                                        </button>
                                    @else
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-danger" title="Go Back"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                                    @endif

                                </form>
                            </div>
                            <hr>
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
                                            <li style="font-weight: bold; color:red">{{ $item->job_pending_remark.'-'.$item->job_pending_note }} - {{ $item->created_at->format('l jS \\of F Y h:i:s A') }} </li> 
                                            @endforeach 
                                        </ol>

                                    </td>
                                </tr>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- push external js -->
    @push('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
    @endpush

    <script type="text/javascript">

        function warning(key){
            var used_qnty=$('#used_qnty-'+key).val();
            var withdraw_qnty=$('#withdraw_qnty-'+key).val();

            var new_used_qnty=parseInt(used_qnty);
            var new_withdraw_qnty=parseInt(withdraw_qnty);
            var style ="background-color:red;";
            console.log(new_used_qnty);

            if(new_withdraw_qnty > new_used_qnty ) {
                alert('Whoops! Required quantity is more than actual consumption qunatity');
                $('#withdraw_qnty-'+key).css({"background-color":"red","color":"white"});
                $('#withdraw_qnty-'+key).val(null);
            }
        }


            // function apply(id) {
            //     event.preventDefault();
            //     swal({
            //         title: `Are you sure you want to apply?`,
            //         text: "Press enter to continue...",
            //         buttons: true,
            //         dangerMode: true,
            //     }).then((willApply) => {
            //         if (willApply) {
            //             sendRequest(id);
            //         }
            //     });
            // };

            // function sendRequest(id) {

            //     $.ajax({
            //         type: "GET",
            //         url: url.replace(':id', id),

            //         success: function (resp) {

            //             console.log('returned',resp);
            //             window.location.reload();
            //             if (resp.success === true) {
            //                 // show toast message
            //                 iziToast.show({
            //                     title: "Success!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "green",
            //                     message: resp.message,
            //                     messageColor: "black"
            //                 });
            //             } else if (resp.errors) {
            //                 iziToast.show({
            //                     title: "Oopps!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "red",
            //                     message: resp.errors[0],
            //                     messageColor: "black"
            //                 });
            //             } else {
            //                 iziToast.show({
            //                     title: "Oopps!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "red",
            //                     message: resp.message,
            //                     messageColor: "black"
            //                 });
            //             }
            //         }, // success end
            //     })
            // }
            // function approve(id) {
            //     event.preventDefault();
            //     swal({
            //         title: `Are you sure you want to approve?`,
            //         text: "Press enter to continue...",
            //         buttons: true,
            //         dangerMode: true,
            //     }).then((willApply) => {
            //         if (willApply) {
            //             sendapprovalRequest(id);
            //         }
            //     });
            // };
            
            // function sendapprovalRequest(id) {

            //     $.ajax({
            //         type: "GET",
            //         url: url.replace(':id', id),

            //         success: function (resp) {

            //             console.log('returned',resp);
            //             window.location.reload();
            //             if (resp.success === true) {
            //                 // show toast message
            //                 iziToast.show({
            //                     title: "Success!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "green",
            //                     message: resp.message,
            //                     messageColor: "black"
            //                 });
            //             } else if (resp.errors) {
            //                 iziToast.show({
            //                     title: "Oopps!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "red",
            //                     message: resp.errors[0],
            //                     messageColor: "black"
            //                 });
            //             } else {
            //                 iziToast.show({
            //                     title: "Oopps!",
            //                     position: "topRight",
            //                     timeout: 4000,
            //                     color: "red",
            //                     message: resp.message,
            //                     messageColor: "black"
            //                 });
            //             }
            //         }, // success end
            //     })
            // }
    </script>
@endsection
