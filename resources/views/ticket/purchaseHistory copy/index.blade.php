@extends('layouts.main')
@section('title', 'Users')
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
                            <div class="col-md-6">
                               {{__('label.COUSTOMER_PHONE_NO')}} :{{ Form::text('coustomer_phone_no', null, array('id'=> 'coustormerPhoneNumber', 'class' => 'form-control col-md-4', 'placeholder' =>  __('label.COUSTOMER_PHONE_NO'))) }}
                            </div>    
                            <div class="col-md-6">
                                {{__('label.COUSTOMER_NAME')}} :{{ Form::text('coustomer_name', null, array('id'=> 'coustomerName', 'class' => 'form-control col-md-4', 'placeholder' =>  __('label.COUSTOMER_NAME'))) }}
                            <br />
                            <div id="customerBasicInfo">
                               

                            </div>
                            </div>
                            
                        </div>
                   
                    <div class="card-body">
                        <div id ="purchaseHistoryShow"><strong> {{__('label.PURCHASE_HISTORY')}}</strong>
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('label.SERVICE')}}</th>
                                        <th>{{ __('label.PRODUCT_SERIAL')}}</th>
                                        <th>{{ __('label.PRODUCT_DATE')}}</th>
                                        <th>{{ __('label.PRODUCT_NAME')}}</th>
                                        <th>{{ __('label.PRODUCT_CODE')}}</th>
                                        <th>{{ __('label.BRAND_NAME')}}</th>
                                        <th>{{ __('label.MODEL_NAME')}}</th>
                                        <th>{{ __('label.LOCATION')}}</th>
                                        <th>{{ __('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    
                                </tbody>
                            </table>
                        </div>
                        <div id ="serviceHistoryShow"> <strong>{{__('label.SERVICE_HISTORY')}}</strong>
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
      
            $('.table').DataTable();
      

        $(document).on("blur", '#coustormerPhoneNumber', function (e) {
            var phoneNumber = $('input[name="coustomer_phone_no"]').val();
            intRegex = /[0-9 -()+]+$/; // 
            if((phoneNumber.length < 6) || (!intRegex.test(phoneNumber)))
            {
                swal('Please enter a valid phone number.');
                $('#purchaseHistoryShow').html(''); // load here
                    $('#serviceHistoryShow').html(''); // load here
                    $('#customerBasicInfo').html(''); // l
                return false;
            }

            $.ajax({
                url: "{{ URL::to('tickets/show-api') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "post",
                data: {
                    phone_number: phoneNumber
                },
                success: function (response) {

                    $('#purchaseHistoryShow').html(response.purchaseHistory); // load here
                    $('#serviceHistoryShow').html(response.serviceHistory); // load here
                    $('#customerBasicInfo').html(response.customerBasicInfo); // load here
                    $('.table').DataTable();
                },
                error: function (jqXhr, ajaxOptions, thrownError) {
                    $('#dynamic-info').html('<i class="fa fa-info-sign"></i> Something went wrong, Please try again...');
                }
            });

        });


    });
    </script>
@endsection
