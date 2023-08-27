@extends('layouts.main')
@section('title', 'Employee')
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
                            <h5>{{ __('label.EMPLOYEE_UPDATE')}}</h5>
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
                        <h3>{{ __('label.EMPLOYEE_UPDATE')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="forms-sample" id="createRank" action="{{route('hrm.technician.update', $employee->id)}}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label for="designation">
                                            {{ __('label.SELECT_DESIGNATION')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="designation_name" name="designation_name" type="hidden" class="form-control" value="" placeholder="">
                                        <select name="designation_id" id="designation" class="form-control select2" id="designation" required>
                                            <option value="">{{ __('label.SELECT_DESIGNATION_OPT') }}</option>
                                            @foreach ($designations as $designation)
                                                <option value="{{ $designation->id }}"
                                                    @if ($employee->designation_id == $designation->id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $designation->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- {!! Form::select('designation_id', $designations, null,[ 'class'=>'form-control select2', 'placeholder' => __('label.SELECT_DESIGNATION_OPT'),'id'=> 'designation']) !!}
                                        <div class="help-block with-errors"></div> --}}

                                        @error('designation_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="name">
                                            {{ __('label.EMPLOYEE_NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $employee->name }}" required>
                                        <div class="help-block with-errors"></div>
                                        @error('name')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="employee_address">
                                            {{ __('label.EMPLOYEE_ADDRESS')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="employee_address" type="text" class="form-control @error('employee_address') is-invalid @enderror" name="employee_address" value="{{ $employee->address }}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('employee_address')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">
                                            {{ __('label.EMPLOYEE_EMAIL')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $employee->email }}" required>
                                        <div class="help-block with-errors" ></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">
                                            {{ __('label.EMPLOYEE_PHONE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror integer-decimal-only" name="phone" value="{{ $employee->mobile }}" required>
                                        @error('phone')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="check_team_leader" class="form-check-input" id="check_team_leader" value="1"
                                        style="margin-top: 2px"
                                            @if($employee->team_leader_id)
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label" for="check_team_leader">
                                            {{ __('label.BELONG_TO_TEAM_LEADER') }} ?
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="form-group" id="teamleader_id">
                                        <label for="teamleader_id">{{ __('label.TEAM_LEADER')}}</label>
                                        <select name="teamleader_id" id="teamleader_id" class="form-control select2" multiple="multiple">
                                            <option value="">Empty</option>
                                            @forelse($teamleaders as $teamleader)
                                                <option value="{{ $teamleader->id }}"
                                                    @if( $employee->team_leader_id == $teamleader->id )
                                                        selected
                                                    @endif
                                                    >
                                                    {{ $teamleader->user->name }}
                                                </option>
                                            @empty
                                                <option value="">No Group Found</option>
                                            @endforelse
                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('status')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" name="check_team_branch" class="form-check-input" id="check_team_branch" value="1"
                                        style="margin-top: 2px"
                                            @if($employee->outlet_id)
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label" for="check_team_branch">
                                            {{ __('label.BRANCH')}} ?
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="form-group" id="branch_hide_show">
                                        <label for="servicecenter">
                                            {{ __('label.BRANCH')}}
                                            <span class="text-red">*</span>
                                        </label>

                                        <select name="outlet_id" id="outlet" class="form-control select2" required>
                                            <option value="">Empty</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{$outlet->id}}"
                                                    @if ($outlet->id == $employee->outlet_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{$outlet->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('servicecenter_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="store_hide_show">
                                        <label for="store">
                                            {{ __('label.STORE')}}
                                            <span class="text-red">*</span>
                                        </label>

                                        <select name="store_id" id="store" class="form-control select2">
                                            <option value="">Empty</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}"
                                                    @if ($store->id == $employee->store_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $store->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors" ></div>
                                        @error('store_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="callcenter">{{ __('label.CALL_CENTER')}}</label>

                                        <select name="callcenter_id" id="callcenter" class="form-control select2">
                                            <option value="">Empty</option>
                                            @foreach ($callCenters as $callCenter)
                                                <option value="{{ $callCenter->id }}"
                                                    @if ($callCenter->id == $employee->call_center_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $callCenter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors" ></div>
                                        @error('callcenter_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div> --}}

                                    <div class="form-check">
                                        <input type="checkbox" name="check_team_vendor" class="form-check-input" id="check_team_vendor" value="1"
                                        style="margin-top: 2px"
                                            @if($employee->vendor_id)
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label" for="check_team_vendor">
                                            {{ __('Select Vendor')}} ?
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="form-group" id="vendor_id">
                                        <label for="vendor_id">{{ __('label.SELECT_VENDOR')}}</label>

                                        <select name="vendor_id" id="vendor" class="form-control select2">
                                            <option value="">Empty</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}"
                                                    @if($vendor->id == $employee->vendor_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('vendor_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 pt-5">
                                    <div class="card ">
                                        <div class="card-header">
                                            <h3>{{ __('Update User')}}</h3>
                                            <input class="ml-2" type="checkbox" id="add_user" name="add_user" value="1"
                                                @if ($employee->user_id)
                                                    checked
                                                @endif
                                            >
                                        </div>
                                        <div class="card-body" id="user_access">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="username">Username</label>
                                                        <input type="text" class="form-control" name="username" value="@if (isset($employee->user_id)){{ $employee->user->name }}@endif">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="password">{{ __('Password')}}<span class="text-red">*</span></label>
                                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter password">
                                                        <div class="help-block with-errors"></div>

                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password-confirm">{{ __('Confirm Password')}}<span class="text-red">*</span></label>
                                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Retype password">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- Assign role & view role permisions -->
                                                    <div class="form-group">
                                                        <label for="role">{{ __('Assign Role')}}<span class="text-red">*</span></label>
                                                        <select name="role" id="role" class="form-control select2">
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    @if($user)
                                                                        @if(!empty($user->roles->first()->id))
                                                                            @if ($user->roles->first()->id == $role->id)
                                                                            selected
                                                                            @endif  
                                                                        @endif
                                                                    @endif
                                                                >
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group" >
                                                        <label for="role">{{ __('Permissions')}}</label>
                                                        <div id="permission" class="form-group" style="border-left: 2px solid #d1d1d1;">
                                                            <span class="text-red pl-3">Select role first</span>
                                                        </div>
                                                        <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                    </div>
                                </div>
                            </div>
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

            var isCheckedTeamLeader = $('#check_team_leader').is(':checked');
            if(isCheckedTeamLeader) {
                $("#teamleader_id").show();
            }else {
                $("#teamleader_id").hide();
            }

            $("#check_team_leader").change(function() {
                if($(this).prop('checked')) {
                    $("#teamleader_id").show();
                } else {
                    $("#teamleader_id").hide();
                }
            });

            var isCheckedTeamBranch = $('#check_team_branch').is(':checked');
            if(isCheckedTeamBranch) {
                $("#branch_hide_show").show();
                $("#store_hide_show").show();
            }else {
                $("#branch_hide_show").hide();
                $("#store_hide_show").hide();
            }

            $("#check_team_branch").change(function() {
                if($(this).prop('checked')) {
                    $("#branch_hide_show").show();
                    $("#store_hide_show").show();
                } else {
                    $("#branch_hide_show").hide();
                    $("#store_hide_show").hide();
                }
            });

            var isCheckedTeamVendor = $('#check_team_vendor').is(':checked');
            if(isCheckedTeamVendor) {
                $("#vendor_id").show();
            }else {
                $("#vendor_id").hide();
            }

            $("#check_team_vendor").change(function() {
                if($(this).prop('checked')) {
                    $("#vendor_id").show();
                } else {
                    $("#vendor_id").hide();
                }
            });

            var isCheckedAddUser = $('#add_user').is(':checked');
            if(isCheckedAddUser) {
                $("#user_access").show();
            }else {
                $("#user_access").hide();
            }

            $("#add_user").change(function() {
                if($(this).prop('checked')) {
                    $("#user_access").show();
                } else {
                    $("#user_access").hide();
                }
            });

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

            var token = $('#token').val();
            var url = "{{ url('/get-role-permissions-badge') }}";
            $.ajax({
                url : url,
                type: 'get',
                data: {
                    id : $("#role").val(),
                    _token : token
                },
                success: function(res)
                {
                    $('#permission').html(res);
                },
                error: function()
                {
                    alert('failed...');
                }
            });

            $(document).on('change', '#role', function(){
                var token = $('#token').val();
                var url = "{{ url('/get-role-permissions-badge') }}";
                $.ajax({
                    // url : "/get-role-permissions-badge",
                    url : url,
                    type: 'get',
                    data: {
                        id : $(this).val(),
                        _token : token
                    },
                    success: function(res)
                    {
                        $('#permission').html(res);
                    },
                    error: function()
                    {
                        alert('failed...');

                    }
                });
            });

            $('#outlet').on('change', function(){
                var outlet_id=$(this).val();
                if(outlet_id){
                    $.ajax({
                        url: "{{url('hrm/get/store')}}/"+outlet_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data){
                            $('#store').empty();
                            $.each(data, function(key, value){
                            $('#store').append("<option value="+value.id+">"+value.name+"</option>");
                        });
                        }
                    });
                }
            });
        });
    </script>
@endsection
