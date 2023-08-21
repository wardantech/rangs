@extends('layouts.main')
@section('title', 'Jobs List')
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
                            <h5>{{ __('label.JOB_LIST')}}</h5>
                            <span>{{ __('label.JOB_LIST')}}</span>
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
                        <h3>{{ __('label.TICKET_DETAILS') }}</h3>

                        <div class="card-header-right">
                            @if ($ticket->status == 0 && $ticket->is_assigned == 0)
                                <a href="{{route('job.job_create', $ticket->id)}}" class="btn btn-primary">
                                    <i class='fas fa-check-circle'></i>
                                    Assign To Technician
                                </a>
                            {{-- @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed_by_teamleader==1 && $ticket->is_reopened == 1)
                                <a href="{{route('job.job_create', $ticket->id)}}" class="btn btn-primary">
                                    <i class='fas fa-check-circle'></i>
                                    Assign To Technician
                                </a> --}}
                            @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed_by_teamleader==0)
                            <a href="{{url('tickets/close-by-teamleader', $ticket->id)}}" class="btn btn-primary" title="Click to Close">
                                <i class='fas fa-check-circle'></i>
                                {{-- Job Verification By Team Leader --}}
                                Close By Team Leader
                            </a>
                            @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed_by_teamleader==1)
                                <button class="btn bg-blue" title="This Job Is Completed">
                                    <i class='fas fa-check-circle'></i>
                                    Job Completed
                                </button>
                                @if ( $ticket->is_closed == 0)
                                <a href="" class="btn btn-success" data-toggle="modal" data-target="#demoModal" title="Close">
                                    <i class='fas fa-check-circle'></i>
                                    Close Ticket (CC)
                                </a>
                                <a href="{{ route('edit-ticket-details', $ticket->id)}}" class="btn btn-warning" data-toggle="modal" data-target="#ticketReopenModal" title="Re-Open">
                                    <i class='fas fa-check-circle'></i>
                                    Ticket Re-Open (CC)
                                </a>
                                @else
                                <button class="btn btn-danger" title="This Job Is Closed">
                                    <i class='fas fa-check-circle'></i>
                                    Ticket Is Closed
                                </button>
                                @endif
                            @endif
                         </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <tbody>
                                @if ( $ticket->is_reopened == 1 )
                                <tr>
                                    <td style="color: rgb(255, 0, 0)" ><strong>Re-Open Note</strong></td>
                                    <td style="color: rgb(255, 0, 0)">{{ $ticket->reopen_note }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Date</td>
                                    <td>{{ $ticket->date->format('m/d/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.SL')}}</td>
                                    <td>TSL-{{ $ticket->id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_CATEGORY')}}</td>
                                    <td>{{ $ticket->category->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.BRAND')}}</td>
                                    <td>{{ $ticket->purchase->brand->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PRODUCT_NAME')}}</td>
                                    <td>{{ $ticket->purchase->modelname->model_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CUSTOMER')}}</td>
                                    <td>{{ $ticket->purchase->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CUSTOMER_GRADE')}}</td>
                                    <td>
                                        <span class="badge badge-success">
                                            @if(isset($ticket->purchase->customer->grade->name)) {{ $ticket->purchase->customer->grade->name }} @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.PHONE')}}</td>
                                    <td>{{ $ticket->purchase->customer->mobile}}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.ADDRESS')}}</td>
                                    <td>{{ $ticket->purchase->customer->address }}</td>
                                </tr>
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
                                        @if ($fault->id != null)
                                            @if (in_array($fault->id, $faultId))
                                                {{ $fault->name }},
                                            @endif
                                        @endif

                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.CARRIER_NAME')}}</td>
                                    <td>{{ $ticket->purchase->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.WARRANTY_TYPE')}}</td>
                                    <td>
                                        @forelse ($warrantyTypes as $warrantyType)
                                            @if ($warrantyType->id == $ticket->warranty_type_id)
                                                {{ $warrantyType->warranty_type }}
                                            @endif
                                        @empty
                                            No Data Here
                                        @endforelse
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.JOB_PRIORITY')}}</td>
                                    <td>{{ $ticket->jobPriority->job_priority }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.SERVICE_TYPE')}}</td>
                                    {{-- <td>{{ $ticket->service_type->service_type }}</td> --}}
                                    <td>
                                        <?php $selectedServiceTypeIds= json_decode($ticket->service_type_id)?>
                                        @foreach ($serviceTypes as $serviceType)
                                            @if (in_array($serviceType->id, $selectedServiceTypeIds))
                                                {{$serviceType->service_type}}
                                            @endif
                                        @endforeach
                                    </td>
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
                                        @if ($ticket->product_receive_mode_id == 1)
                                            Outet
                                        @elseif ($ticket->product_receive_mode_id == 2)
                                            Service Center
                                        @else
                                            Customer Home
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('label.EXPECTED_DELIVERY_MODE')}}</td>
                                    <td>
                                        @if ($ticket->expected_delivery_mode_id == 1)
                                            Outet
                                        @elseif ($ticket->expected_delivery_mode_id == 2)
                                            Service Center
                                        @else
                                        Home Delivery
                                        @endif
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
                                            @if ($product->id != null)
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
                                            @if ($accessories_list->id != null)
                                                @if (in_array($accessories_list->id, $accessoriesId))
                                                    {{ $accessories_list->accessories_name }},
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                {{-- {{dd($customerFeedbacks)}} --}}
                                @if(!$customerFeedbacks->isEmpty())
                                <tr>
                                    <td>Customer Feedback</td>
                                    <td>
                                        <table>

                                            @foreach($customerFeedbacks as $customerFeedback)
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
                                            {{-- <tr>
                                                <td>Remark: {{}}</td>
                                            </tr> --}}
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card p-3">
                        <div class="row">

                            {{-- <div class="col-md-6">
                               {{__('label.COUSTOMER_PHONE_NO')}} :{{ Form::text('coustormer_phone_number', null, array('id'=> 'coustormer_phone_number', 'class' => 'form-control col-md-4', 'placeholder' =>  __('label.COUSTOMER_PHONE_NO'))) }}
                            </div>
                            <div class="col-md-6">
                                {{__('label.PRODUCTS_SERIAL_NUMBER')}} :{{ Form::text('search', null, array('id'=> 'products_serial_number', 'class' => 'form-control col-md-4', 'placeholder' =>  __('label.PRODUCTS_SERIAL_NUMBER'))) }}
                            <br />

                                <div id="customerBasicInfo">


                                </div>
                            </div> --}}

                        </div>

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
                                                {{$job->employee->name}}
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
                                            <td>{{$job->createdBy->name}}</td>
                                            <td>{{$job->ticket->purchase->category->name}}</td>
                                            <td>{{$job->ticket->purchase->brand->name}}</td>
                                            <td>
                                                @isset($job->ticket->purchase->modelname)
                                                {{$job->ticket->purchase->modelname->model_name}}
                                                @endisset
                                            </td>
                                            <td>{{$job->ticket->purchase->product_serial}}</td>
                                            <td>
                                                @if($job->status==0)
                                                    <span class="badge badge-info">Pending</span>
                                                @elseif($job->status==1 && $job->is_started==1 && $job->is_ended==1)
                                                    <span class="badge badge-info">Job Completed</span>
                                                @elseif($job->status==1 && $job->is_started==1)
                                                    <span class="badge badge-success">Job Started</span>
                                                @elseif($job->status==1)
                                                    <span class="badge badge-success">Accepted</span>
                                                @elseif($job->status==2)
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class='text-center'>

                                                    @can('show')
                                                        <a  href="{{ route('job.job.show', $job->id) }}">
                                                            <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                        </a>
                                                    @endcan

                                                    {{-- {{ Form::open(array('url' => 'hrm/technician/destroy' . $job->id, 'id' => 'delete','class'=>'delete')) }}
                                                    {{ Form::hidden('_method', 'DELETE') }}
                                                    <a  href="{{ route('hrm.technician.show', $job->id) }}">
                                                        <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                    </a> --}}

                                                    @can('edit')
                                                        <a href="{{ route('job.job.edit', $job->id) }}">
                                                            <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                        </a>
                                                    @endcan

                                                    {{-- <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                       <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                    {{ Form::close() }} --}}
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
                        {{-- <div id ="serviceHistoryShow"> <strong>{{__('label.SERVICE_HISTORY')}}</strong>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{!! __('label.DATE_AND_TIME') !!}</th>
                                        <th>{{ __('label.PRODUCT')}}</th>
                                        <th>{{ __('label.BRAND')}}</th>
                                        <th>{{ __('label.STATUS')}}</th>
                                        <th>{{ __('label.SERVICE_DESCRIPTION')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    @endpush

<script type="text/javascript">
   $(document).ready( function () {

            //Get Purchase Info Using Serial No
            $('#products_serial_number').on('change', function(e){
                var products_serial_number = $("#products_serial_number").val();
                var url = "{{ url('tickets/purchase-info') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        products_serial_number: products_serial_number,
                    },
                    success: function(data) {
                        var len = 1;
                        if(data != null){
                            len = data.length;
                        }
                        if (len > 0) {
                        $("#datatable tbody td").empty()
                        $.each(data, function(key, value) {
                            var sl=1;
                            var purchase_id=value.purchase_id;
                            var url = '{{url('tickets/ticket-create')}}'+'/'+purchase_id
                            console.log(purchase_id);
                                var table ="<tr><td>"+ (key++) +"</td><td>"+value.product_serial+"</td><td>"+value.purchase_date+"</td><td>"+value.product_name+"</td><td>"+value.customer_name+"</td><td>"+value.product_brand_name+"</td><td>"+value.product_model_name+"</td><td>"+value.point_of_purchase+"</td><td><a href="+ url +" title='Create Ticket'><i class='fas fa-check-circle'></i>Ticket</a></td></tr >";
                                $("#datatable > tbody").append(table)
                            })
                        } else {
                            alert('Whoops! No Data Found');
                        }
                    }
                })
            });

            $('#coustormer_phone_number').on('change', function(e){
                var coustormer_phone_number = $("#coustormer_phone_number").val();
                var url = "{{ url('tickets/purchaseinfo') }}";
                alert(url);
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        coustormer_phone_number: coustormer_phone_number,
                    },
                    success: function(data) {
                        var len = 1;
                        if(data != null){
                            len = data.length;
                        }
                        if (len > 0) {
                        $("#datatable tbody td").empty()
                        $.each(data, function(key, value) {
                            var sl=1;
                                var table ="<tr><td>"+ (key++) +"</td><td>"+value.product_serial+"</td><td>"+value.purchase_date+"</td><td>"+value.product_name+"</td><td>"+value.customer_name+"</td><td>"+value.product_brand_name+"</td><td>"+value.product_model_name+"</td><td>"+value.point_of_purchase+"</td><td><a href='#' title='Create Ticket'><i class='fas fa-check-circle'></i>Ticket</a></td></tr >";
                                $("#datatable > tbody").append(table)
                            })
                        } else {
                            alert('Whoops! No Data Found');
                        }
                    }
                })
            });

            $('.table').DataTable({
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info',
                            title: 'Users',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            title: 'Users',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            title: 'Users',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible',
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            title: 'Users',
                            pageSize: 'A2',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn-sm btn-default',
                            title: 'Users',
                            // orientation:'landscape',
                            pageSize: 'A2',
                            header: true,
                            footer: false,
                            orientation: 'landscape',
                            exportOptions: {
                                // columns: ':visible',
                                stripHtml: false
                            }
                        },
                        {
                            extend: 'colvis',
                            className: 'btn-sm btn-primary',
                            text: '{{trans("Column visibility")}}',
                            columns: ':gt(0)'
                        },
                ],
            });
    });
    </script>
@endsection
