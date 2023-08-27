@extends('layouts.main')
@section('title', 'Purchase History')
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
                            <h5>{{ __('label.PURCHASE_HISTORY')}}</h5>
                            <span>{{ __('label.LIST_OF_PURCHASE_HISTORY')}}</span>
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
                                <a href="#">{{ __('label.PURCHASE_HISTORY')}}</a>
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
                        <div class="row">

                            <div class="col-md-4">
                               {{__('label.COUSTOMER_PHONE_NO')}} :{{ Form::text('coustormer_phone_number', null, array('id'=> 'coustormer_phone_number', 'class' => 'form-control', 'placeholder' =>  __('label.COUSTOMER_PHONE_NO'))) }}
                            </div>
                            <div class="col-md-4">
                                {{__('label.COUSTOMER_NAME')}} :{{ Form::text('coustormer_name', null, array('id'=> 'coustormer_name', 'class' => 'form-control', 'placeholder' =>  __('label.COUSTOMER_NAME'))) }}
                             </div>
                            <div class="col-md-4">
                                {{__('label.PRODUCTS_SERIAL_NUMBER')}} :{{ Form::text('search', null, array('id'=> 'products_serial_number', 'class' => 'form-control', 'placeholder' =>  __('label.PRODUCTS_SERIAL_NUMBER'))) }}
                            <br />

                            </div>

                            <div id="customerBasicInfo">


                            </div>
                        </div>

                    <div class="card-body">
                        <div id ="purchaseHistoryShow"><strong> {{__('label.PURCHASE_HISTORY')}}</strong>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SL')}}</th>
                                        <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                        <th>{{ __('label.PURCHASE_DATE')}}</th>
                                        <th>{{ __('label.PRODUCT_NAME')}}</th>
                                        <th>{{ __('label.CUSTOMER_NAME')}}</th>
                                        <th>{{ __('label.BRAND_NAME')}}</th>
                                        <th>{{ __('label.MODEL_NAME')}}</th>
                                        <th>{{ __('label.LOCATION')}}</th>
                                        <th style="width: 100px;">{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                        <div id ="serviceHistoryShow">

                        </div>
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

            // $('.table').DataTable();
            $('#datatable').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Purchase',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Purchase',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Purchase',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Purchase',
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
                    title: 'Purchase',
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
                }
            ],
    });
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
                            len = data.purchase_info.length;
                        }
                        if (len > 0) {
                            $("#datatable tbody").empty()
                            $("#serviceHistoryShow").empty()
                            $.each(data.purchase_info, function(key, value) {
                                var sl=1;
                                var purchase_id=value.purchase_id;
                                var url = '{{url('tickets/ticket-create')}}'+'/'+purchase_id
                                var urlShow = '{{ url('tickets/ticket-purchase-show') }}'+'/'+purchase_id
                                var table ="<tr><td>"+ (key+1) +"</td><td>"+value.product_serial+"</td><td>"+value.purchase_date+"</td><td>"+value.product_name+"</td><td>"+value.customer_name+"</td><td>"+value.product_brand_name+"</td><td>"+value.product_model_name+"</td><td>"+value.point_of_purchase+"</td><td>"+ @if(auth()->user()->can('create')) "<a href="+ url +" title='Create Ticket'><i class='fas fa-check-circle'></i>Ticket</a>" @else "" @endif+ @if(auth()->user()->can('show')) "<a href="+ urlShow +" title='Create Ticket'><i class='ik ik-eye f-16 mr-15 text-blue ml-2'></i></a>" @else "" @endif +"</td></tr>";

                                $("#datatable > tbody").append(table);
                            })

                            $("#serviceHistoryShow").append(data.serviceHistory);
                        } else {
                            alert('Whoops! No Data Found');
                        }
                    }
                })
            });

            $('#coustormer_name').on('change', function(e){
                var coustormer_name = $("#coustormer_name").val();
                var url = "{{ url('tickets/purchaseinfo-name') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        coustormer_name: coustormer_name,
                    },
                    success: function(data) {
                        var len = 1;
                        if(data != null){
                            len = data.length;
                        }
                        if (len > 0) {
                        $("#datatable tbody").empty()
                        $.each(data, function(key, value) {
                            var sl=1;
                            var purchase_id=value.purchase_id;
                            var url = '{{url('tickets/ticket-create')}}'+'/'+purchase_id
                            var urlShow = '{{ url('tickets/ticket-purchase-show') }}'+'/'+purchase_id
                                var table ="<tr><td>"+ (key+1) +"</td><td>"+value.product_serial+"</td><td>"+value.purchase_date+"</td><td>"+value.product_name+"</td><td>"+value.customer_name+"</td><td>"+value.product_brand_name+"</td><td>"+value.product_model_name+"</td><td>"+value.point_of_purchase+"</td><td>"+ @if(auth()->user()->can('create')) "<a href="+ url +" title='Create Ticket'><i class='fas fa-check-circle'></i>Ticket</a>" @else "" @endif+ @if(auth()->user()->can('show')) "<a href="+ urlShow +" title='Create Ticket'><i class='ik ik-eye f-16 mr-15 text-blue ml-2'></i></a>" @else "" @endif +"</td></tr>";
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
                // alert(url);
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
                        $("#datatable tbody").empty()
                        $.each(data, function(key, value) {
                            var sl=1;
                            var purchase_id=value.purchase_id;
                            var url = '{{url('tickets/ticket-create')}}'+'/'+purchase_id
                            var urlShow = '{{ url('tickets/ticket-purchase-show') }}'+'/'+purchase_id
                                var table ="<tr><td>"+ (key+1) +"</td><td>"+value.product_serial+"</td><td>"+value.purchase_date+"</td><td>"+value.product_name+"</td><td>"+value.customer_name+"</td><td>"+value.product_brand_name+"</td><td>"+value.product_model_name+"</td><td>"+value.point_of_purchase+"</td><td>"+ @if(auth()->user()->can('create')) "<a href="+ url +" title='Create Ticket'><i class='fas fa-check-circle'></i>Ticket</a>" @else "" @endif+ @if(auth()->user()->can('show')) "<a href="+ urlShow +" title='Create Ticket'><i class='ik ik-eye f-16 mr-15 text-blue ml-2'></i></a>" @else "" @endif +"</td></tr>";
                                $("#datatable > tbody").append(table)
                            })
                        } else {
                            alert('Whoops! No Data Found');
                        }
                    }
                })
            });
    });
    </script>
@endsection
