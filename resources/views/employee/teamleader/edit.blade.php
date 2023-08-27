@extends('layouts.main')
@section('title', 'Team Leader Update')
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
                            <h5>{{ __('label.TEAM_LEADER_UPDATE')}}</h5>

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
                        <h3>{{ __('label.TEAM_LEADER_UPDATE')}}</h3>
                    </div>
                    <div class="card-body">

                        <form class="forms-sample" method="POST" action="{{ route('hrm.teamleader.update',$teamleader->id) }}">
                        @csrf
                        @method('put')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="employee_id" class="">
                                        {{ __('Employee') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                @if( old('category_id',optional($teamleader)->employee_id) == $employee->id ))
                                                    selected
                                                @endif
                                                >
                                                {{ $employee->name }}
                                            </option>
                                            @empty
                                                <option value="">No Emplyee Found</option>
                                            @endforelse
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inputpc" class="">
                                            {{ __('Group') }}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="group_id" id="group_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($groups as $group)
                                            <option value="{{ $group->id }}"
                                                @if( old('group_id',optional($teamleader)->group_id) == $group->id ))
                                                    selected
                                                @endif
                                                >
                                                {{ $group->name }}
                                            </option>
                                            @empty
                                                <option value="">No Group Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                        {{-- <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a> --}}
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


            $('#part').on('change', function(e){
                e.preventDefault();
                var part_id = $("#part").val();
                var url = "{{ url('inventory/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_id,
                    },
                    success: function(data) {
                        console.log(data);
                        // $("#available_qty").val(data.stock);
                    var html = "<option value="+null+">Select Parts Model</option>";
                    $("#model_id").empty();
                    $.each(data.partsModel, function(key) {
                    //   console.log(data.recYarn_name[key].brand);

                        html += "<option value="+data.partsModel[key].id+">"+data.partsModel[key].name+"</option>";
                    })
                    $("#model_id").append(html);
                    html = "";
                    }
                })
            });

        });
    </script>
@endsection
