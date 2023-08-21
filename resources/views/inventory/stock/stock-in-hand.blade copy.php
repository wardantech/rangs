@extends('layouts.main')
@section('title', 'Total Stock')
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
                                <a href="{{route('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('label.INVENTORY')}}</a>
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
                <d iv class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.PARTS_STOCK')</h3>
                        <div class="card-header">
                           {{-- <a href="{{URL::to('inventory/create')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a> --}}
                        </div>
                    </div>
                     <!-- <form class="forms-sample" method="POST" action="{{ route('general.group.store') }}">
                        @csrf
                        <div class="row p-5">
                            <div class="col-md-6">
                                    <label for="inputpc" class="">Parts Model :</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Parts Model" value="{{ old('name') }}" required>
                            </div> <!-- end col-->
                            <div class="col-md-6">
                                <label for="inputpc" class="">Outlet:</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Outlet" value="{{ old('name') }}" required>
                        </div>

                        </div> <!-- end row -->
                        <div class="row">
                           <div class="col-md-12 text-center">
                              <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-rounded mt-4">{{ __('Submit')}}</button>
                              </div>
                           </div> 
                        </div>
                    </form>  -->
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART')}}</th>
                                    <th>{{ __('label.MODEL')}}</th>
                                    <th>{{ __('label.PRESENT_BALANCE_QNTY')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                       <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($stocks as $key => $item)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{$item['parts_name']}}</td>
                                        <td>{{$item['model_name']}}</td>
                                        <td>{{$item['stock']}}</td>
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
