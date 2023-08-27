@extends('layouts.main')
@section('title', 'Stock')
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
                            <h5>{{ $partDetails->code }}-{{ $partDetails->name }}</h5>
                            {{-- <span>{{ $partDetails->name }}</span> --}}
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
                        <h3>@lang('label.PARTS_STOCK')</h3>
                        <div class="card-header-right">
                           {{-- <a href="{{URL::to('inventory/create')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('label.STOCK_IN')}}</th>
                                    <th>{{ __('label.STOCK_OUT')}}</th>
                                    <th>{{ __('label.CURRENT_BALANCE')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                     $sl = 0;
                                     $total=0;
                                @endphp
                                @foreach ($details as $item)
                                    <tr>
                                        <td>{{ ++$sl }}</td>
                                        <td>{{ $item->created_at->format('m/d/yy H:i:s') }}</td>
                                        <td>{{ $item->stock_in }}
                                        @php
                                            $total+=$item->stock_in
                                        @endphp
                                        </td>
                                        <td>{{ $item->stock_out }}
                                        @php
                                            $total-=$item->stock_out
                                        @endphp
                                        </td>
                                        <td>
                                            {{$total}}
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
        $(document).on('click', '.showInventory', function (e) {
            e.preventDefault();
            var inventoryId = $(this).attr('data-id'); // get id of clicked row


            $('#dynamic-info').html(''); // leave this div blank
            $.ajax({
                url: "{{ URL::to('inventory/show') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "post",
                data: {
                    inventory_id: inventoryId
                },
                success: function (response) {
                    $('#dynamic-info').html(''); // blank before load.
                    $('#dynamic-info').html(response.html); // load here
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    $('#dynamic-info').html('<i class="fa fa-info-sign"></i> Something went wrong, Please try again...');
                }
            });
         });


    });
    </script>
@endsection
