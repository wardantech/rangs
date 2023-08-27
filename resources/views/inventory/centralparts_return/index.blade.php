@extends('layouts.main')
@section('title', 'Return Parts')
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
                            <h5>{{ __('Parts Transfer From Outlet')}}</h5>
                            <span>{{ __('Parts Transfer From Outlet')}}</span>
                            <h4 class="text-danger mt-2">{{Session::get('message')}}</h4>
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
                        <h3>{{ __('label.RETURN_PARTS')}}</h3>
                        <div class="card-header-right">
                            {{-- <a href="{{URL::to('inventory/parts-return/create')}}" class="btn btn-primary">  @lang('label.CREATE')</a> --}}
                         </div>
                    
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <table id="table" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('label.DATE')}}</th>
                                        <th>{{ __('label.SENT_FROM')}}</th>
                                        <th>{{ __('label.QUANTITY')}}</th>
                                        <th>{{ __('label.STATUS')}}</th>
                                        <th>{{ __('label.ACTION')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($partsreturns as $partsreturn)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$partsreturn->date->format('m/d/Y')}}</td>
                                    <td>{{$partsreturn->outlet->name}}</td>
                                    <td>{{$partsreturn->quantity}}</td>
                                    <td>Pending</td>
                                    
                                    <td>
                                        <div class='text-center'>
                                            {{ Form::open(['route' => ['inventory.parts-return.destroy', $partsreturn->id], 'method' => 'DELETE'] ) }}
                                            {{ Form::hidden('_method', 'DELETE') }}
                                            <a href="{{url('inventory/return/parts/show', $partsreturn->id)}}" class="show-priceManagement">
                                                    <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                            </a>
                                            {{-- <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                   <i class="ik ik-trash-2 f-16 text-red"></i>
                                            </button> --}}
                                            {{ Form::close() }}
                                        </div>
                                    </td>
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
