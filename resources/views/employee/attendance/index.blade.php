@extends('layouts.main')
@section('title', 'Technician')
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
                            <h5>{{ __('label.ATTENDANCE')}}</h5>
                            <span>{{ __('label.EMPLOYEE_ATTENDANCE')}}</span>
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
                    <div class="card-header row">
                        <h3 style="font-weight: bold">{{Auth::user()->name}}'s @lang('label.ATTENDANCE') for the month of - {{$current_date->isoFormat('MMMM YYYY'); }}</h3>
                        <div class="card-header-right attendance">
                           @can('create')
                                <a class="btn btn-info" data-toggle="modal" data-target="#demoModal">
                                    @lang('label.ATTENDANCE')
                                </a>
                           @endcan
                        </div>
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
            </div>
        </div>
    </div>
    <div class="modal fade" id="demoModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">{{ __('label.ATTENDANCE')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(array('route' => 'hrm.attendance.store', 'class' => 'forms-sample', 'id'=>'createRank','method'=>'POST')) }}
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                        {{ Form::text('date', Request::old('date',$current_date->toDateString()), array('id'=> 'date', 'class' => 'form-control', 'placeholder' => '','required'=>'required','readonly'=>'readonly')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('label.EMPLOYEE')}}<span class="text-red">*</span></label>
                                        {{ Form::text('name', Request::old('name',$employee ), array('id'=> 'name', 'class' => 'form-control', 'placeholder' => '','required'=>'required','readonly'=>'readonly')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="time">{{ __('label.TIME')}}<span class="text-red">*</span></label>
                                        {{ Form::text('time', Request::old('time'), array('id'=> 'time', 'class' => 'form-control', 'placeholder' => 'Enter warranty type ...','readonly'=>'readonly', 'required'=>'required')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('time')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="type">{{ __('label.ATTENDANCE_TYPE')}}<span class="text-red">*</span></label>
                                        {{-- {{Form::select('type', [  'Check Out' => 'Check Out', 'Check In' => 'Check IN'], $checkstatus->attendance_type, ['class' => 'form-control'])}} --}}
                                        <select name="type" id="type" class="form-control select2">
                                            <option value="">Select</option>
                                            <option value="1"
                                            @if ($checkstatus == null )
                                            selected
                                            @endif
                                            >Check In</option>
                                            <option value="2"
                                            @if (isset($checkstatus) && $checkstatus->attendance_type ==1)
                                            selected
                                            @endif
                                            >Check Out</option>
                                        </select>
                                        <div class="help-block with-errors"></div>

                                        @error('type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="note">{{ __('label.NOTE')}}<span class="text-red">*</span></label>
                                        {{ Form::text('note', Request::old('note'), array('id'=> 'note', 'class' => 'form-control', 'placeholder' => 'Enter Note ...')) }}
                                        <div class="help-block with-errors"></div>

                                        @error('note')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                    </div>
                {!! Form::close() !!}
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

<script type="text/javascript">
   $(document).ready( function () {
        //Set time
        var time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        $("#time").val(time);

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
                    pageSize: 'A4',
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
