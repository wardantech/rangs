@extends('layouts.main')
@section('title', 'Employee Job List')
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
                            <h5>{{ __('label.JOB LIST')}}</h5>
                            <span>{{ __('label.LIST_OF_JOBS')}}</span>
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
        <div>
            <h5 class="text-success">{{Session::get('message')}}</h5>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header">
                        <h3>{{ __('label.JOB LIST')}}</h3>
                        <div class="card-header-right">
                            <a href="{{URL::to('product/purchase/create')}}" class="btn btn-primary">  @lang('label.CREATE')</a>
                         </div>
                    
                    </div>
                    <div class="card-body">
                        <table id="table" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('label.SL')}}</th>
                                    <th>{{ __('label.DATE')}}</th>
                                    <th>{{ __('label.JOB NUMBER')}}</th>
                                    <th>{{ __('label.CATEGORY')}}</th>
                                    <th>{{ __('label.BRAND')}}</th>
                                    <th>{{ __('label.MODEL')}}</th>
                                    <th>{{ __('label.JOB_STATUS')}}</th>
                                    <th>{{ __('label.ACTION')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=1)
                                @foreach($jobs as $job)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$job->date}}</td>
                                    <td>JSL-{{$job->id}}</td>
                                    <td>{{$job->ticket->purchase->category->name}}</td>
                                    <td>{{$job->ticket->purchase->brand->name}}</td>
                                    <td>{{$job->ticket->purchase->modelname->model_name}}</td>
                                    <td>
                                        @if($job->status==1)
                                                <span class="badge badge-info">Pending</span>
                                                @elseif($job->status==2)
                                                    <span class="badge badge-success">Accepted</span>
                                                @elseif($job->status==3)
                                                    <span class="badge badge-danger">Declined</span>
                                                @endif
                                    </td>
                                    <td>
                                        <div class='text-center'>
                                            <a  href="{{ route('job.show.employee-job.details', $job->id) }}">
                                                <i class='ik ik-eye f-16 mr-15 text-blue'></i>
                                            </a>
                                            @if($job->status==2)
                                            <a  href="{{ route('job.job-submission-create', $job->id) }}" class="btn btn-danger">
                                                Submit Job
                                            </a>
                                            @endif
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

    <!-- push external js -->
    @push('script')
    <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
    <!--server side users table script-->
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
    @endpush

    <script>
    $(document).ready( function () {
        
        $(document).on("submit", '.delete', function (e) {
            //This function use for sweetalert confirm message
            e.preventDefault();
            var form = this;
            
            swal({
                title: "Are you sure you want to Delete?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();  
                }
            });

        });

        // $('.table').DataTable();
        $('#table').DataTable({
        dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
        buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Jobs',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Jobs',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Jobs',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Jobs',
                    pageSize: 'A2',
                    header: true,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Job',
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
