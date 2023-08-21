@extends('layouts.main')
@section('title', 'Financial Report')
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
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.REPORT')}}</h5>
                            <span>{{ __('label.FINANCIAL_REPORT')}}</span>
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
                @can('create')
                <form class="forms-sample" action="{{ route('report.finance-report-post') }}" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="inputpc" class="">Start Date :</label>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="inputpc" class="">End Date :</label>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-md-12 text-center">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mt-3">{{ __('Submit')}}</button>
                        </div>
                        </div>
                    </div>
                </form>
                @endcan
                <div class="card p-3">
                    <div class="card-header">
                        <h3>@lang('label.FINANCIAL_REPORT')</h3>
                        <div class="card-header-right">
                            <p>
                                {{ Carbon\Carbon::parse($startDate)->format('m/d/Y') }}
                                {{ Carbon\Carbon::parse($endDate)->format('m/d/Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th rowspan="2">{{ __('label.SL')}}</th>
                                    <th rowspan="2">{{ __('label.OUTLET')}}</th>
                                    <th colspan="2">{{ __('label.COLLECTION_TAKA')}}</th>
                                    <th colspan="4">{{ __('label.DEPOSIT')}}</th>
                                </tr>
                                <tr>
                                    <th>{{ __('label.DAYS_TOTAL')}}</th>
                                    <th>{{ __('label.PROGRESSIVE')}}</th>
                                    <th>{{ __('label.OPENING_BF')}}</th>
                                    <th>{{ __('label.DAYS_TOTAL')}}</th>
                                    <th>{{ __('label.PROGRESSIVE')}}</th>
                                    <th>{{ __('label.BALANCE')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financeInfo as $key => $item)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$item['outlet_name']}}</td>
                                    <td>{{$item['days_total_revenue']}}</td>
                                    <td>{{$item['revenue_collection']}}</td>
                                    <td>{{$item['opening_bf']}}</td>
                                    <td>{{$item['days_total_deposit']}}</td>
                                    <td>{{$item['deposit']}}</td>
                                    <td>{{$item['balance']}}</td>
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
    <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
   

<script type="text/javascript">
        $(document).ready( function () {
            $('#datatable').DataTable({
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info',
                            title: 'Financial Report',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            title: 'Financial Report',
                            header: true,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            title: 'Financial Report',
                            header: true,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible',
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            title: 'Financial Report',
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
                            title: 'Financial Report',
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
 @endpush
@endsection
