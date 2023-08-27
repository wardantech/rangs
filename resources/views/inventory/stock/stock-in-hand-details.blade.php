@extends('layouts.main')
@section('title', 'Stock in Hand')
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
                            <h5>{{ __('Stock In Hand')}}</h5>
                            <span>{{ __('label.PARTS_STOCK')}}</span>
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
                <form class="forms-sample" method="POST" action="{{ route('inventory.stock-in-hand') }}">
                    @csrf
                    <div class="row pb-5">
                        <div class="col-md-12">
                                <label for="inputpc" class="">Parts Model :</label>
                                <input type="text" class="form-control" id="part_model" name="part_model" placeholder="Please Enter Parts Model" value="{{ old('name') }}" required>
                        </div> <!-- end col-->
                        {{-- <div class="col-md-6">
                            <label for="inputpc" class="">Outlet:</label>
                            <input type="text" class="form-control" id="outlet_name" name="outlet_name" placeholder="Outlet" value="{{ old('name') }}" required>
                        </div> --}}
                    {{-- <div class="row"> --}}
                        <div class="col-md-12 text-center">
                           <div class="form-group">
                             <button type="submit" class="btn btn-primary btn-rounded mt-4">{{ __('Submit')}}</button>
                           </div>
                        </div> 
                     {{-- </div> --}}
                    </div>
                </form>
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.PARTS_STOCK')</h3>
                        <div class="card-header-right">
                           <p><span style="font-weight: bold"> Name: </span> {{ $partDetails->code }}-{{ $partDetails->name }}, <span style="font-weight: bold">Part Model:</span> {{ $partDetails->name }}</p>
                           {{-- <p>{{ $partsModel->name }}</p> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('Store')}}</th>
                                    <th>{{ __('Total Stock In')}}</th>
                                    <th>{{ __('Total Stock Out')}}</th>
                                    <th>{{ __('label.PRESENT_BALANCE_QNTY')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQty = 0;   
                                @endphp
                                @foreach($stocks as $key => $item)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        @if ($item->store)
                                        {{ $item->store->name }}
                                        @endif
                                        
                                    </td>
                                    <td>{{ $item->stock_in }}</td>
                                    <td>{{ $item->stock_out }}</td>
                                    <td>{{ $item->stock_in - $item->stock_out  }}
                                    @php
                                        $totalQty+= $item->stock_in - $item->stock_out;
                                    @endphp
                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: center; font-weight:bold">Total Stock in Hand : {{ $totalQty  }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div id="dynamic-info">
                   <!-- Load Ajax -->
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

<script type="text/javascript">
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
    });
    </script>
@endsection
