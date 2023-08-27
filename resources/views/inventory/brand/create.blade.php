@extends('layouts.main')
@section('title', 'Brand')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-unlock bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.BRAND')}}</h5>

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
        <div class="row clearfix">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <!-- only those have manage_permission permission will get access -->

            @can('create')
                <div class="col-md-12">
                    <div class="card p-3">
                        <div class="card-header">
                            <h3>{{ __('label.ADD BRAND')}}</h3>

                        </div>
                        <div class="card-body">
                            <form class="forms-sample" method="POST" action="{{ route('product.brand.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group mb-3">
                                            <label for="category_id">
                                                {{ __('label.SELECT_CATEGORY') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <select name="category_id" id="category_id" class="form-control select2" required>
                                                <option value="">Select</option>
                                                @forelse($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        @if( old('category_id') == $category->id )
                                                            selected
                                                        @endif
                                                        >
                                                        {{ $category->name }}
                                                    </option>
                                                @empty
                                                    <option value="">No Category Found</option>
                                                @endforelse
                                            </select>
                                            @error('category_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group mb-3">
                                            <label for="inputpc">
                                                {{ __('label.BRAND_NAME') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="permission" name="name" placeholder="Brand Name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>  <!-- end col-->


                                    <div class="col-sm-4">
                                        <div class="form-group mb-3">
                                            <label for="inputpc">
                                                {{ __('label.BRAND_CODE') }}
                                                <span class="text-red">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="code" name="code" placeholder="Brand Code" value="{{ old('code') }}" required>
                                            @error('code')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> <!-- end col-->
                                </div> <!-- end row -->
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('label.SUBMIT')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
    </div>


    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/Cell-edit/dataTables.cellEdit.js') }}"></script>
        <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>

        <!--server side permission table script-->
        <script src="{{ asset('js/custom.js') }}"></script>
    @endpush

@endsection
