@extends('layouts.main')
@section('title', 'Details Parts Receive ')
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
                            <h5>{{ __('Job Details')}}</h5>
                            <span>{{ __('Job Details')}}</span>
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
                        <h3>@lang('Job Details')</h3>
                        <div class="card-header-right">
                            @if ($job->status==2)
                            <a href="{{route('job.decline-job', $job->id)}}" class="btn btn-primary">  @lang('Decline')</a>
                            @else
                            <a href="{{route('job.accept-job', $job->id)}}" class="btn btn-primary">  @lang('Accept')</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">
                                  
                            <tr>
                                <th><strong>{{trans('label.TICKET_SL')}}</strong></th>
                                <td>TSL-{{$job->ticket->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.JOB_NUMBER')}}</strong></th>
                                <td>JSL-{{$job->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.PRODUCT')}}</strong></th>
                                <td>{{$job->ticket->purchase->category->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.BRAND_NAME')}}</strong></th>
                                <td>{{$job->ticket->purchase->brand->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.MODEL_NAME')}}</strong></th>
                                <td>{{$job->ticket->purchase->modelname->model_name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.FAULT_DESCRIPTION')}}</strong></th>
                                <?php $faults=json_decode($job->ticket->fault_description_id)?>
                                <td>
                                    @foreach($allFaults as $fault)
                                        @if(in_array($fault->id, $faults))
                                            <span class="badge badge-info">{{$fault->name}}</span>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.ACCESSORIES_LIST')}}</strong></th>
                                <?php $accessories=json_decode($job->ticket->accessories_list_id)?>
                                <td>
                                    @foreach($allAccessories as $accessory)
                                        @if(in_array($accessory->id, $accessories))
                                            <span class="badge badge-danger">{{$accessory->accessories_name}}</span>
                                        @endif
                                    @endforeach
                                </td>
                                {{-- <td>{{$job->ticket->accessories_list_id}}</td> --}}
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.PRODUCT_RECEIVE_MODE')}}</strong></th>
                                <td>{{$job->ticket->product_receive_mode_id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.EXPECTED_DELIVERY_MODE')}}</strong></th>
                                <td>{{$job->ticket->expected_delivery_mode_id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.START_DATE')}}</strong></th>
                                <td>{{$job->ticket->start_date}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.END_DATE')}}</strong></th>
                                <td>{{$job->ticket->end_date}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.CUSTOMER_NOTE')}}</strong></th>
                                <td>{{$job->ticket->customer_note}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.JOB_STATUS')}}</strong></th>
                                <td>
                                    @if($job->status==1)
                                        <span>Pending</span>
                                    @elseif($job->status==2)
                                        <span>Accepted</span>
                                    @elseif($job->status==3)
                                        <span>Declined</span>
                                    @endif
                                </td>
                            </tr>
                           
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="note-modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="demoModalLabel">{{ __('Decline Note')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('job.decline-job', $job->id)}}" method="POST">
                            @csrf
                            <input type="hidden" name="job_id" value="{{$job->id}}">
                            <div class="form-group">
                                <label for="" class="col-form-label">Decline Note</label>
                                <textarea name="decline_note" id="" class="form-control @error('decline_note') is-invalid @enderror" cols="10" rows="2" required="required"></textarea>
                                <div class="help-block with-errors"></div>
                                @error('decline_note')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <input type="submit" class="btn btn-success" value="Submit Decline Note">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
