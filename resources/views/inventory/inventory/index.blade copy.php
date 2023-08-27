@extends('layouts.main')
@section('title', 'Parts Receive')
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
                            <h5>{{ __('label.PARTS')}}</h5>
                            <span>{{ __('label.LIST_OF_RECEIVED_PARTS')}}</span>
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
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.RECEIVED_PARTS')</h3>
                        <div class="card-header-right">
                           <a href="{{URL::to('inventory/create')}}" class="btn btn-primary">  @lang('label.RECEIVE_PARTS')</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PART')}}</th>
                                    <th>{{ __('label.MODEL')}}</th>
                                    <th>{{ __('label.INVOICE_NUMBER')}}</th>
                                    <th>{{ __('label.RECEIVE_DATE')}}</th>
                                    <th>{{ __('label.SENDING_DATE')}}</th>
                                    <th>{{ __('label.RACK')}}</th>
                                    <th>{{ __('label.QUANTITY')}}</th>
                                    <th>{{ __('label.UNIT_PRICE')}}</th>
                                    <th>{{ __('label.AMOUNT')}}</th>
                                    {{-- <th>{{ __('label.STORE')}}</th> --}}
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$inventoryArr->isEmpty())
                                    <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($inventoryArr as $inventory)
                                    <tr>
                                        <td>{{++$sl}}</td>

                                        <td>{{$inventory->part->name}}</td>
                                        <td>{{$inventory->part_model->name}}</td>
                                        <td>{{$inventory->invoice_number}}</td>
                                        <td>{{$inventory->receive_date}}</td>
                                        <td>{{$inventory->sending_date}}</td>
                                        <td>{{$inventory->rack->name}}</td>
                                        <td>{{$inventory->quantity}}</td>
                                        <td>{{number_format($inventory->selling_price,2)}}</td>
                                        <td>{{number_format($inventory->quantity * $inventory->selling_price,2)}}</td>
                                        <td>
                                            <div class='text-center'>

                                                {{ Form::open(array('url' => 'inventory/' . $inventory->id, 'id' => 'delete','class'=>'delete')) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                {{-- <a  data-toggle="modal" data-target="#demoModal"  href="#" class="showInventory" data-id="{{$inventory->id}}">
                                                    <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                </a> --}}
                                                <a class='' href="{{ URL::signedRoute('show-inventory', ['id' => $inventory->id]) }}">
                                                    <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                                </a>
                                                <a class='' href="{{ URL::signedRoute('edit-inventory', ['id' => $inventory->id]) }}">
                                                    <i class='ik ik-edit f-16 mr-15 text-green'></i>
                                                </a>

                                                <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                   <i class="ik ik-trash-2 f-16 text-red"></i>
                                                </button>
                                                {{ Form::close() }}
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

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
        @if(!$inventoryArr->isEmpty())
            $('#datatable').DataTable();
        @endif

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
