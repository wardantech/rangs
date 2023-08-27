@extends('layouts.main')
@section('title', 'Parts List')
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
                            <h5>{{ __('label.PARTS')}}</h5>
                            <span>{{ __('label.LIST OF PARTS')}}</span>
                            <h4 class="text-danger mt-2">{{Session::get('message')}}</h4>
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
                        <h3>{{ __('label.PARTS')}}</h3>
                        <div class="card-header-right">
                            <a href="{{URL::to('inventory/parts/create')}}" class="btn btn-primary">  @lang('label.PARTS_CREATE')</a>
                         </div>
                    </div>
                    <div class="card-body">
                        <table id="parts_table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.PARTS NAME')}}</th>
                                    <th>{{ __('label.PARTS CATEGORY')}}</th>
                                    <th>{{ __('label.CODE')}}</th>
                                    <th>{{ __('label.UNIT')}}</th>
                                    <th>{{ __('label.STATUS')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$partsDetails->isEmpty())
                                    <?php
                                    $sl = 0;
                                    ?>
                                    @foreach($partsDetails as $partsDetail)
                                    <tr>
                                        <td>{{++$sl}}</td>
                                        <td>{{$partsDetail->name}}</td>
                                        <td>{{$partsDetail->category->name}}</td>
                                        <td>{{$partsDetail->code}}</td>
                                        <td>{{$partsDetail->unit}}</td>
                                        <td>
                                            @if ($partsDetail->status == 1)
                                                <form action="{{ route('inventory.parts.inactive', $partsDetail->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Inactive">
                                                        <i class="far fa-thumbs-up"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('inventory.parts.active', $partsDetail->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Active">
                                                        <i class="far fa-thumbs-down"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <div class='table-actions text-center'>
                                                <a href="{{ route('inventory.parts.edit', $partsDetail->id ) }}" title="Edit">
                                                    <i class='ik ik-edit f-16 mr-15 text-blue'></i>
                                                </a>
                                                <form action="{{ route('inventory.parts.destroy', $partsDetail->id) }}" method="POST" class="delete d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-placement="top" data-rel="tooltip" data-original-title="Delete" style="border: none;background-color: #fff;">
                                                        <i class="ik ik-trash-2 f-16 text-red"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach

                                @else

                                <tr>
                                   <td>{{__('label.DATA_NOT_FOUND')}}</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
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
    @endpush

    <script>
        $(document).ready(function()
        {
            $('#parts_table').DataTable({
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        title: 'Parts',
                        header: false,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        title: 'Parts',
                        header: false,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        title: 'Parts',
                        header: false,
                        footer: true,
                        exportOptions: {
                            // columns: ':visible',
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        title: 'Parts',
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
                        title: 'Parts',
                        // orientation:'landscape',
                        pageSize: 'A2',
                        header: true,
                        footer: false,
                        orientation: 'landscape',
                        exportOptions: {
                            // columns: ':visible',
                            stripHtml: false
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-sm btn-primary',
                        text: '{{trans("Column visibility")}}',
                        columns: ':gt(0)'
                    }
                ],
        });
        });
        </script>
@endsection
