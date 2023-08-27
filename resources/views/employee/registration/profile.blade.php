@extends('layouts.main')
@section('title', 'Profile')
@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Profile')}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{route('dashboard')}}" class="btn btn-outline-success" title="Home"><i class="ik ik-home"></i></a>
                            </li>
                            {{-- <li class="breadcrumb-item">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-danger" title="Go Back"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                            </li> --}}
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="card-title mt-10">{{ $employee->name}}</h4>
                            <p class="card-subtitle">
                                @isset($employee->designation->name)
                                    {{ $employee->designation->name }}
                                @endisset
                            </p>
                            <p>
                                @isset($employee->teamLeader)
                                <span class="card-title text-blue">Team Leader : </span>
                                <span class="card-title text-blue">{{ $employee->teamLeader->employee->name }}</span>
                                @endisset 
                            </p>
                        </div>
                    </div>
                    <hr class="mb-0">
                    <div class="card-body">
                        <small class="text-muted d-block">{{ __('Email address')}} </small>
                        <h6>{{ $employee->email}}</h6>
                        <small class="text-muted d-block pt-10">{{ __('Phone')}}</small>
                        <h6>{{ $employee->mobile}}</h6>
                        <small class="text-muted d-block pt-10">{{ __('Address')}}</small>
                        <h6>{{ $employee->address}}</h6>
                        <small class="text-muted d-block pt-30">{{ __('Social Profile')}}</small>
                        <br/>
                        <button class="btn btn-icon btn-facebook"><i class="fab fa-facebook-f"></i></button>
                        <button class="btn btn-icon btn-twitter"><i class="fab fa-twitter"></i></button>
                        <button class="btn btn-icon btn-instagram"><i class="fab fa-instagram"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <div class="card">
                    <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#last-month" role="tab" aria-controls="pills-profile" aria-selected="tru">{{ __('Profile')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-setting-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-setting" aria-selected="false">{{ __('Setting')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-setting-tab" data-toggle="pill" href="#attendance" role="tab" aria-controls="pills-setting" aria-selected="false">{{ __('Attendance')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="last-month" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-6"> <strong>{{ __('Full Name')}}</strong>
                                        <br>
                                        <p class="text-muted">{{ $employee->name}}</p>
                                    </div>
                                    <div class="col-md-3 col-6"> <strong>{{ __('Mobile')}}</strong>
                                        <br>
                                        <p class="text-muted">{{ $employee->mobile}}</p>
                                    </div>
                                    <div class="col-md-3 col-6"> <strong>{{ __('Email')}}</strong>
                                        <br>
                                        <p class="text-muted">{{ $employee->email}}</p>
                                    </div>
                                    <div class="col-md-3 col-6"> <strong>{{ __('Location')}}</strong>
                                        <br>
                                        <p class="text-muted">{{ $employee->address}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-6"> <strong>Store</strong>
                                        <br>
                                        <p class="text-muted">
                                            @if (isset($employee->store->name))
                                                {{ $employee->store->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="previous-month" role="tabpanel" aria-labelledby="pills-setting-tab">
                            <div class="card-body">
                                <form class="form-horizontal" method="POST" action="{{ route('hrm.technician.update',$employee->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="example-name">{{ __('Full Name')}}</label>
                                        <input type="text" class="form-control" name="" value="{{old('name',optional($employee)->name)}}" id="example-name" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="example-email">{{ __('Email')}}</label>
                                        <input type="email" class="form-control" name="" value="{{old('email',optional($employee)->email)}}" id="example-email" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="example-password">{{ __('Password')}}</label>
                                        <input type="password" class="form-control" name="password" id="password">
                                    </div>
                                    <button class="btn btn-success" type="submit">Update Profile</button>
                                </form>
                            </div>
                        </div>
                        @if($admin!=1)
                        {{-- Attendance --}}
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="pills-setting-tab">
                            <div class="card-header">
                                <h3 style="font-weight: bold">{{$employee->name}}'s @lang('label.ATTENDANCE') for the month of - {{$current_date->isoFormat('MMMM YYYY'); }}</h3>
                            </div>
                            <div class="card-body">
                                <table id="datatable" class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('label.SL')}}</th>
                                            <th>{{ __('label.DATE')}}</th>
                                            <th>{{ __('label.CHECK_IN')}}</th>
                                            <th>{{ __('label.CHECK_OUT')}}</th>
                                            <th>{{ __('label.WORKING_HOUR')}}</th>
                                            <th>{{ __('label.NOTE')}}</th>
                                            <th>{{ __('label.STATUS')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_present=null;
                                            $present = 1;
                                        @endphp
                                        @foreach ($dates as $date)
                                            @php
                                                $day = \Carbon\Carbon::parse($date)->format('l');
                                                $startDate = date("Y-m-d", strtotime($date));
                                                $attendance = App\Models\Employee\Attendance::where('employee_id',Auth::id())->whereDate('date',$startDate)->first();
                                            @endphp
            
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$date}}</td>
            
                                                @if(isset($attendance))
                                                    <th>{{Carbon\Carbon::parse($attendance['checkin'])->format('g:i A')}}</th>
            
                                                    @if($attendance->checkout)
                                                    <td>
                                                        {{Carbon\Carbon::parse($attendance['checkout'])->format('g:i A')}}
                                                        @php
                                                        $total_present+=$present;
                                                        @endphp
            
                                                    </td>
                                                    @else
                                                    <td></td>
                                                    @endif
            
                                                    @php
                                                        $wh = \Carbon\Carbon::parse($attendance['checkout'])->diff(\Carbon\Carbon::parse($attendance['checkin']))->format('%H:%I:%S');
                                                        $dh = "08:00:00";
                                                    @endphp
            
                                                    <td>
                                                        @if ($attendance['checkout'])
                                                            {{ \Carbon\Carbon::parse($attendance['checkin'])->diff(\Carbon\Carbon::parse($attendance['checkout']))->format('%H:%I:%S')}} 
                                                        @elseif($date == \Carbon\Carbon::now('Asia/Dhaka')->format("Y-m-d"))
                                                        {{ \Carbon\Carbon::parse($attendance['checkin'])->diff(\Carbon\Carbon::now('Asia/Dhaka')->format('g:i A'))->format('%H:%I:%S')}} 
                                                        @else
                                                        N / A
                                                        @endif
                                                    </td>
                                                    <td class="text">{{$attendance->note}}</td>
                                                    <td class="text text-success">Present</td>
                                                @else
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
            
                                                @endif
                                                    @if ($attendance == null)
                                                        @if(Carbon\Carbon::parse($date)->format('l') == "Friday")
                                                    <td class="text text-info">
                                                        Friday
                                                    </td>
                                                    @elseif($attendance != null)
                                                    <td class="text text-success">
                                                        Present
                                                    </td>
                                                    @else
                                                    <td class="text text-danger">
                                                        Absent
                                                    </td>
                                                    @endif
                                                @else
                                                    <td> </td>
                                                @endif
            
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="font-family: Times New Roman, Times, serif; font-weight: bold">Total Present:</td>
                                            <td style="font-family: Times New Roman, Times, serif;font-weight: bold">{{$total_present}} Days</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                                                    
                        @endif
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

            $('#datatable').DataTable({
                dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                buttons: [
                        {
                            extend: 'copy',
                            className: 'btn-sm btn-info',
                            title: 'Monthly Attendance',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            className: 'btn-sm btn-success',
                            title: 'Monthly Attendance',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn-sm btn-warning',
                            title: 'Monthly Attendance',
                            header: false,
                            footer: true,
                            exportOptions: {
                                // columns: ':visible',
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'btn-sm btn-primary',
                            title: 'Monthly Attendance',
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
                            title: 'Monthly Attendance',
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
                        },
                ],
            });
        });
    </script>
@endsection
