@extends('layouts.main')
@section('title', 'Edit Part Category')
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
                            <h5>{{ __('label.PART CATEGORY')}}</h5>

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
                    <div class="card">
                        <div class="card-header"><h3>Update Category</h3></div>
                        <div class="card-body">
                            {!! Form::open(['route' => ['inventory.part-category.update',$partCategory->id], 'method' => 'put']) !!}

                            <input type="hidden" id="category_id" name="category_id">
                              <div class="form-group">
                                 <label for="permission">{{ __('Category Name')}}<span class="text-red">*</span></label>
                                 <input type="text" class="form-control" name="name" value="{{ $partCategory->name }}" required>
                                 @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                 @enderror
                              </div>
                              <div class="form-group">
                                 <button type="submit" class="btn btn-primary btn-sm">Updated</button>
                              </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>


<script type="text/javascript">
          
</script>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/Cell-edit/dataTables.cellEdit.js') }}"></script>
        <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>

    <!--server side permission table script-->

    @endpush
@endsection
