@extends('layouts.main')
@section('title', 'Edit Revenue')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{__('label.EDIT REVENUE')}}</h5>
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
                    <div class="card-body">
                        {{-- <form class="forms-sample">
                            {{ csrf_field() }} --}}
                            {{Form::open(['route'=>array('update.revenue', 'id'=>$revenue->id), 'method'=>'POST', "class"=>"form-horizontal"])}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row mb-3">
                                        <label for="date" class="col-sm-4 col-form-label">
                                            {{ __('label.BRANCH') }}
                                            <span class="text-red"> *</span>
                                        </label>
                                        <div class="col-sm-8">
                                            @if ($userRole->name == "Super Admin" || $userRole->name == "Admin")
                                                <select name="outlet_id" id="outlet_id" class="form-control select2" required>
                                                    <option value="">{{ __('Select Branch') }}</option>
                                                    @foreach ($outlets as $outlet)
                                                        <option value="{{ $outlet->id }}"
                                                            @if ($outlet->id == $revenue->outlet_id)
                                                                selected
                                                            @endif
                                                        >{{ $outlet->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" class="form-control" value="{{ $mystore->name }}" readonly>
                                                <input type="hidden" name="outlet_id" value="{{ $mystore->outlet_id }}">
                                            @endif

                                            @error('outlet_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="date" class="col-sm-4 col-form-label">
                                            {{__('label.DATE')}}
                                            <span class="text-red"> *</span>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', optional($revenue)->date ? date('Y-m-d', strtotime($revenue->date)) : '') }}" placeholder="Date" required>
                                            @error('date')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="name" class="col-sm-4 col-form-label">
                                            {{__('label.REVENUE NAME')}}
                                            <span class="text-red"> *</span>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="name" name="name" value="{{$revenue->name}}" placeholder="Revenue Name" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    {{-- <div class="row mb-3">
                                        <label for="amount" class="col-sm-4 col-form-label">{{__('label.JOB ID')}}</label>
                                        <div class="col-sm-8">
                                            <select name="job_id" id="" class="form-control">
                                                <option value="">Select a job id</option>
                                                @foreach ($jobs as $job)
                                                    <option value="{{ $job->id }}"
                                                        @if ($job->id == $revenue->job_id)
                                                            selected
                                                        @endif
                                                    >{{ $job->job_number }}</option>
                                                @endforeach
                                            </select>
                                            @error('job_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <div class="row mb-3">
                                        <label for="amount" class="col-sm-4 col-form-label">
                                            {{__('label.AMOUNT')}}
                                            <span class="text-red"> *</span>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="amount" name="amount" value="{{$revenue->amount}}" placeholder="Amount" min="0" required>
                                            @error('amount')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="remark" class="col-sm-4 col-form-label">{{__('label.REMARK')}}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="remark" name="remark" value="{{$revenue->remark}}" placeholder="Remark">
                                            @error('remark')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <input type="submit" class="btn form-bg-danger mr-2">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
@endsection
