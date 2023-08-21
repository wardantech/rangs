@extends('layouts.main')
@section('title', 'Product Receive Mode')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.PRODUCT_RECEIVE_MODE')}}</h5>

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
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('label.PRODUCT_RECEIVE_MODE')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="" method="POST" action="{{ route('receive-mode.store') }}">
                            @csrf

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="name">
                                            {{ __('label.PRODUCT_RECEIVE_MODE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control" placeholder="Product Receive Mode name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="status" class="">
                                            {{ __('Status') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button class="mt-2 btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){

            $(".integer-decimal-only").each(function () {
                $(this).keypress(function (e) {
                    var code = e.charCode;

                    if (((code >= 48) && (code <= 57)) || code == 0 || code == 46) {
                        return true;
                    } else {
                        return false;
                    }
                });
            });

            $('#district').on('change', function(){
                var district_id=$(this).val();
                if(district_id){
                    $.ajax({
                        url: "{{url('general/get/thana/')}}/"+district_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            $('#thana').empty();
                            $.each(data, function(key, value){
                                $('#thana').append("<option value="+value.id+">"+value.name+"</option>");
                            });
                        }
                    });
                }
            });


            $('#add-store').on('submit', function(e){
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url : "/newstore",
                    data: $('#add-store').serialize(),
                    //processData: false,
                    dataType: 'json',
                    //contentType: false,
                    //beforeSend: function(){},
                    success: function(response){
                        console.log(response);
                        alert("Data saved successfully.");
                    }
                    //error: alert("Data can not be saved.")
                });
            });



        });
    </script>
@endsection
