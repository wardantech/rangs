@extends('layouts.main')
@section('title', 'Part Category')
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
                        <div class="card-header"><h3>{{ __('label.ADD PART CATEGORY')}}</h3></div>
                        <div class="card-body">
                            <form class="forms-sample" method="POST" action="{{ route('inventory.part-category.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="inputpc">
                                        {{ __('Category Name') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control" id="inputpc" placeholder="Category Name" required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    @can('create')
                        <div style="margin: 0 auto" class="mb-2">
                            <a href="{{route('inventory.sample-part-category-excel')}}" class="btn btn-success">Sample Excel Download</a>
                        </div>
                        <div style="margin: 0 auto">
                            {{-- <a href="{{route('export-inventory')}}" class="btn btn-success">Export</a> --}}
                            <form action="{{route('inventory.import-part-category')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div>
                                {{-- <label for="" class="badge badge-danger">Import</label> --}}
                                <input type="file" name="import_file">
                                <input type="submit" class="btn btn-success" value="Import">
                            </div>
                            </form>
                        </div>
                    @endcan
                    <div class="card-body">
                        <table id="datatable"  class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL') }}</th>
                                    <th>{{ __('label.PART CATEGORY LIST')}}</th>
                                    <th>{{ __('label.STATUS') }}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                  @php($i=1)
                                  @foreach($partCategories as $category)
                                  <tr>
                                      <td>{{ $i++ }}</td>
                                      <td>{{ $category->name }}</td>
                                      <td>
                                        @if ($category->status == true)
                                            <form action="{{ route('inventory.part-category.status', $category->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="0">
                                                <button type="submit" class="btn btn-sm btn-success" title="Inactive">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('inventory.part-category.status', $category->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Active">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            </form>
                                        @endif
                                      </td>
                                      <td>
                                        <div class='text-center'>
                                            @can('edit')
                                                <a href="" data-id="{{$category->id}}"  data-name="{{$category->name}}" class="edit-btn" data-toggle="modal" data-target="#categoryModal"><i class="ik ik-edit f-16 mr-15 text-blue" aria-hidden="true"></i></a>
                                            @endcan

                                            @can('delete')
                                                <form action="{{ route('inventory.part-category.destroy', $category->id) }}" method="POST" class="delete d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                        <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
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

    <!-- Modal -->
 <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
   <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Edit Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => ['inventory.part-category.update', 1], 'method' => 'put']) !!}

        <input type="hidden" id="category_id" name="category_id">
          <div class="form-group">
             <label for="permission">{{ __('Category Name')}}<span class="text-red">*</span></label>
             <input type="text" class="form-control" name="name" required>
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
</div>

<script type="text/javascript">
$(document).ready(function(){
     $('.edit-btn').on('click', function() {
        $("#categoryModal input[name='name']").val( $(this).data('name') );
        $("#categoryModal input[name='category_id']").val( $(this).data('id') );

        $('.selectpicker').selectpicker('refresh');
    });

    $('#datatable').DataTable({
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
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('plugins/DataTables/Cell-edit/dataTables.cellEdit.js') }}"></script>
        <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>

    <!--server side permission table script-->

    @endpush
@endsection
