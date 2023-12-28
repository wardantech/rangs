@extends('layouts.main') 
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	{{-- <div class="row clearfix">
            @include('include.message')
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget bg-primary">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>{{ __('Works')}}</h6>
                                <h2>543</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget bg-success">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>{{ __('Sales')}}</h6>
                                <h2>3510</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget bg-warning">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>{{ __('Earnings')}}</h6>
                                <h2>$43,567.53</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-inbox"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget bg-danger">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>{{ __('New Users')}}</h6>
                                <h2>11</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        @canany(['access_to_technician_jobs_list','access_to_technician_jobs','access_to_ticket_job_list'])
        <h1>Job</h1>
        <div class="row clearfix">
            @include('include.message')
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-orange">
                    <a href="{{ route('job.status', 1) }}" title="Pending">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Pending')}}</h6>
                                    <h2>{{ $totalJobStatus->pending }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-lime">
                    <a href="{{ route('job.status', 2) }}" title="Paused">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Paused')}}</h6>
                                    <h2>{{ $totalJobStatus->jobPaused }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-yellow">
                    <a href="{{ route('job.status', 3) }}" title="Created">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Created')}}</h6>
                                    <h2>{{ $totalJobStatus->jobCreated }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-info">
                    <a href="{{ route('job.status', 4) }}" title="Completed">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Completed')}}</h6>
                                    <h2>{{ $totalJobStatus->jobCompleted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-green">
                    <a href="{{ route('job.status', 5) }}" title="Started">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Started')}}</h6>
                                    <h2>{{ $totalJobStatus->jobStrated }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-blue">
                    <a href="{{ route('job.status', 6) }}" title="Accepted">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Accepted')}}</h6>
                                    <h2>{{ $totalJobStatus->jobAccepted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-danger">
                    <a href="{{ route('job.status', 7) }}" title="Rejected">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Job Rejected')}}</h6>
                                    <h2>{{ $totalJobStatus->jobRejected }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-secondary">
                    <a href="{{ route('job.status', 8) }}" title="Total">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Total Job')}}</h6>
                                    <h2>{{ $totalJobStatus->totalJob }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>   
        @endcanany
        @canany(['access_to_tickets','access_to_ticket_list'])
        <h1>Ticket</h1>
        <div class="row clearfix">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-yellow">
                    <a href="{{ route('tickets.status', 0) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Created')}}</h6>
                                    <h2>{{ $totalTicketStatus->created }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- Start --}}
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-blue">
                    <a href="{{ route('tickets.status', 1) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Assigned')}}</h6>
                                    <h2>{{ $totalTicketStatus->assigned }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-danger">
                    <a href="{{ route('tickets.status', 2) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Cancelled')}}</h6>
                                    <h2>{{ $totalTicketStatus->rejected }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-primary">
                    <a href="{{ route('tickets.status', 3) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Accepted')}}</h6>
                                    <h2>{{ $totalTicketStatus->jobAccepted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-info">
                    <a href="{{ route('tickets.status', 4) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Started')}}</h6>
                                    <h2>{{ $totalTicketStatus->jobStarted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-yellow">
                    <a href="{{ route('tickets.status', 5) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Paused')}}</h6>
                                    <h2>{{ $totalTicketStatus->jobPaused }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- End --}}
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-orange">
                    <a href="{{ route('tickets.status', 6) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Pending')}}</h6>
                                    <h2>{{ $totalTicketStatus->pending }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-warning">
                    <a href="{{ route('tickets.status', 7) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Delivered By Team Leader')}}</h6>
                                    <h2>{{ $totalTicketStatus->deliveredby_teamleader }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-red">
                    <a href="{{ route('tickets.status', 8) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Re-opened')}}</h6>
                                    <h2>{{ $totalTicketStatus->ticketReOpened }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-green">
                    <a href="{{ route('tickets.status', 9) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Delivered By CC')}}</h6>
                                    <h2>{{ $totalTicketStatus->deliveredby_call_center }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-dark">
                    <a href="{{ route('tickets.status', 10) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Completed Job')}}</h6>
                                    <h2>{{ $totalTicketStatus->jobCompleted }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-green">
                    <a href="{{ route('tickets.status', 11) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Closed')}}</h6>
                                    <h2>{{ $totalTicketStatus->ticketClosed }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-secondary">
                    <a href="{{ route('tickets.status', 12) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Total Ticket')}}</h6>
                                    <h2>{{ $totalTicketStatus->total }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="widget bg-aqua">
                    <a href="{{ route('tickets.status', 13) }}">
                        <div class="widget-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="state">
                                    <h6>{{ __('Undlivered Close')}}</h6>
                                    <h2>{{ $totalTicketStatus->undelivered_close }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @endcanany
    </div>
	<!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>
       
        
        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection