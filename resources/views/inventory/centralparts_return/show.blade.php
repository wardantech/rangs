@extends('layouts.main')
@section('title', 'Details Parts Transfer ')
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
                            <h5>{{ __('Parts Transfer Details')}}</h5>
                            <span>{{ __('Parts Transfer Details')}}</span>
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
                           {{-- <a href="{{URL::to('inventory/parts-return')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a> --}}
                           <a href="{{route('inventory.central.returnReceive',$partsreturn->id)}}">  <i class="fa fa-check-square f-16 mr-15 text-green" aria-hidden="true">Receive</i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-hover">    
                            <tr>
                                <th><strong>{{trans('label.TRANSFER_SL')}}</strong></th>
                                <td>{{$partsreturn->id}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.SENT_FROM')}}</strong></th>
                                <td>{{$partsreturn->outlet->name}}</td>
                            </tr>
                            <tr>
                                <th><strong>{{trans('label.QUANTITY')}}</strong></th>
                                <td>{{$partsreturn->quantity}}</td>
                            </tr>                       
                        </table>
                        <table id="table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Parts Name')}}</th>
                                    <th>{{ __('Parts Model')}}</th>
                                    <th>{{ __('Quantity')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($partsReturnDetails as $partsreturn)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$partsreturn->part->name}}</td>
                                <td>{{$partsreturn->part_model->name}}</td>
                                <td>{{$partsreturn->quantity}}</td>
                                {{-- <td>{{$partsreturn}}</td> --}}
                                {{-- <td>{{$partsreturn->outlet->name}}</td>
                                <td>{{$partsreturn->quantity}}</td>
                                
                                <td>
                                    <div class='text-center'>
                                        {{ Form::open(['route' => ['inventory.parts-return.destroy', $partsreturn->id], 'method' => 'DELETE'] ) }}
                                        {{ Form::hidden('_method', 'DELETE') }}
                                        <a href="{{route('inventory.parts-return.show', $partsreturn->id)}}" class="show-priceManagement">
                                                <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                        </a>
                                        <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                               <i class="ik ik-trash-2 f-16 text-red"></i>
                                        </button>
                                        {{ Form::close() }}
                                    </div>
                                </td> --}}
                            </tr> 
                            @endforeach 
                            </tbody>
                        </table>
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
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

    <script>
    $(document).ready( function () {
        
        $(document).on("submit", '.delete', function (e) {
            //This function use for sweetalert confirm message
            e.preventDefault();
            var form = this;
            
            swal({
                title: "Are you sure you want to Delete?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();  
                }
            });

        });
        $('.table').DataTable();
    });
    </script>
@endsection
