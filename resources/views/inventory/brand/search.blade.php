@extends('layouts.main')
@section('title', 'Brand Search')
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
                            <h5>{{ __('Brand Search')}}</h5>
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        <table id="brand_tables" class="table">
                            <thead>
                                <tr>
                                    <th>Serial No</th>
                                    <th>{{ __('label.CATEGORY')}}</th>
                                    <th>{{ __('label.BRAND NAME')}}</th>
                                    {{-- <th>{{ __('label.STATUS')}}</th> --}}
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                  @foreach($results as $brand)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>
                                            @if ($brand->category)
                                                {{ $brand->category->name }}
                                            @endif
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            @can('edit')
                                                <a href="{{ route('product.brand.edit',$brand->id) }}" title="Edit">
                                                    <i class='ik ik-edit f-16 mr-15 text-blue'></i>
                                                </a>
                                            @endcan

		                                    @can('delete')
                                                <form action="{{ route('product.brand.delete', $brand->id) }}" method="POST" class="delete d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                        <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            @if ($brand->status == 1)
                                                <a href="{{route('product.brand.status.change', $brand->id)}}" title="Active">
                                                    <i class="fa fa-ban fa-1x"></i>
                                                </a>
                                            @else
                                                <a href="{{route('product.brand.status.change', $brand->id)}}" title="Inactive">
                                                    <i class="far fa-check-circle fa-1x"></i>
                                                </a>
                                            @endif

                                        </td>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    <script type="text/javascript">
    $(document).ready(function(){
        function confirmDelete() {
           if (confirm("Are you sure want to delete?")) {
             return true;
           }
           return false;
         }

         $('#brand_tables').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Users',
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
                    title: 'Users',
                    // orientation:'landscape',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        // columns: ':visible',
                        stripHtml: false
                    }
                }
            ],
    });
    });
     </script>
@endsection
