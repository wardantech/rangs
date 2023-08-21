@extends('layouts.main')
@section('title', 'Service Type')
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
                            <h5>{{ __('label.SERVICE_TYPE')}}</h5>
                            <span>{{ __('label.LIST_OF_SERVICE_TYPE')}}</span>
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
                        <h3>@lang('label.SERVICE_UPDATE')</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('service-types.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id" class="col-form-label">
                                            {{ __('label.CATEGORY')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="category_id" class="form-control" id="category" required>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    @if ($category->id == $service->category_id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.SERVICE_TYPE_NAME')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" name="service_type" value="{{ $service->service_type }}" class="form-control" required>

                                        <div class="help-block with-errors"></div>

                                        @error('service_type')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-1">
                                        <label for="orderDate">
                                            {{ __('label.SERVICE_AMOUNT')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="number" name="service_amount" value="{{ $service->service_amount }}" class="form-control" required>
                                        <div class="help-block with-errors"></div>

                                        @error('service_amount')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="orderDate">
                                            {{ __('label.SERVICE_TYPE')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="status" class="form-control" required>
                                            <option value="1"
                                                @if ($service->status == 1)
                                                    selected
                                                @endif
                                            >
                                                {{ __('label.ACTIVE') }}
                                            </option>
                                            <option value="0"
                                                @if ($service->status == 0)
                                                    selected
                                                @endif
                                            >
                                                {{ __('label.INACTIVE') }}
                                            </option>
                                        </select>

                                        @error('status')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-check-input mt-0 ml-0" name="is_service_warranty" type="checkbox" id="is_service_warranty" value="1"
                                            @if ($service->is_service_warranty == 1)
                                                checked
                                            @endif
                                        >
                                        <label class="form-check-label ml-3" for="is_service_warranty">
                                            Is It Under Service Warranty
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                            </div>
                        </form>
                    </div>
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

@endsection
