@extends('layouts.main')
@section('title', 'Create New Transaction')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{__('label.TRANSECTION')}}</h5>
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
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-body">

                        <form action="{{ route('cash-transections.store') }}" class="form-horizontal" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="date">
                                        {{ __('label.DATE') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="date" name="date" class="form-control" id="date" placeholder="Date" value="{{ currentDate() }}" required>
                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="outlet_id">
                                        {{ __('label.OUTLET') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    @if ($userRole->name == "Super Admin" || $userRole->name == "Admin")
                                        <select class="form-control select2" name="outlet_id" id="outlet_id" required>
                                            <option value="">{{ __('Select Branch') }}</option>
                                            @foreach ($outlets as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control" value="{{ $mystore->name }}" readonly>
                                        <input type="hidden" name="outlet_id" value="{{ $mystore->outlet_id }}">
                                    @endif

                                    @error('outlet_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="amount">
                                        {{ __('label.AMOUNT') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="number" name="amount" class="form-control" id="amount" placeholder="Amount" value="{{ old('amount') }}" min="0" required>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="purpose">{{ __('Purpose') }}</label>
                                    <select class="form-control select2" name="purpose" id="purpose">
                                        <option>{{ __('Select Purpose') }}</option>
                                        <option value="withdraw">{{ __('Withdraw') }}</option>
                                        <option value="deposit">{{ __('Deposit') }}</option>
                                        <option value="recevied_payment">{{ __('Recevied Payment') }}</option>
                                        <option value="given_payment">{{ __('Given Payment') }}</option>
                                    </select>
                                    @error('purpose')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="form-row" id="type_section">
                                <div class="form-group col-md-12">
                                    <label for="type">{{ __('Type') }}</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="balance_transfer">Balance Transfer</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="form-row" id="cheque_number_section">
                                <div class="form-group col-md-12">
                                    <label for="cheque_number">{{ __('Cheque Number') }}</label>
                                    <input class="form-control" type="text" name="cheque_number" id="cheque_number" placeholder="Cheque Number" value="{{ old('cheque_number') }}">
                                    @error('cheque_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="form-row" id="balance_transfer_section">
                                <div class="form-group col-md-12">
                                    <label for="balance_transfer">{{ __('Balance Transfer Details') }}</label>
                                    <textarea class="form-control" name="balance_transfer" id="balance_transfer" rows="3" style="height: 75px; margin-top: 0px; margin-bottom: 0px;" placeholder="Payment Details Like Cheque Number, Bank Name..." value="{{ old('balance_transfer') }}"></textarea>
                                    @error('balance_transfer')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="remarks">{{ __('label.REMARK') }}</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="3" style="height: 75px; margin-top: 0px; margin-bottom: 0px;" placeholder="Description if Needed..." value="{{ old('remarks') }}"></textarea>
                                    @error('remarks')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-danger mr-2">{{ __('label.SUBMIT') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- <script>
            $(document).ready(function(){
                var purpose = $('#purpose').val();
                if((purpose == 'recevied_payment') || (purpose == 'given_payment')) {
                    $('#type_section').show();
                    $('#balance_transfer_section').hide();
                    $('#cheque_number_section').hide();
                    var type = $('#type').val();
                    if(type == 'cheque') {
                        $('#cheque_number_section').show();
                        $('#balance_transfer_section').hide();
                    }else if(type == 'balance_transfer') {
                        $('#balance_transfer_section').show();
                        $('#cheque_number_section').hide();
                    }else {
                        $('#balance_transfer_section').hide();
                        $('#cheque_number_section').hide();
                    }
                }else {
                    $('#cheque_number_section').show();
                    $('#type_section').hide();
                    $('#balance_transfer_section').hide();
                }

                var type = $('#type').val();
                if (type == 'cheque') {
                    $('#cheque_number_section').show();
                    $('#balance_transfer_section').hide();
                }else if (type == 'balance_transfer') {
                    $('#balance_transfer_section').show();
                    $('#cheque_number_section').hide();
                }else {
                    $('#balance_transfer_section').hide();
                    $('#cheque_number_section').hide();
                }
            });

            $('#purpose').on('change', function() {
                var purpose = $('#purpose').val();
                if((purpose == 'recevied_payment') || (purpose == 'given_payment')) {
                    $('#type_section').show();
                    $('#balance_transfer_section').hide();
                    $('#cheque_number_section').hide();
                    var type = $('#type').val();
                    if(type == 'cheque') {
                        $('#cheque_number_section').show();
                        $('#balance_transfer_section').hide();
                    }else if(type == 'balance_transfer') {
                        $('#balance_transfer_section').show();
                        $('#cheque_number_section').hide();
                    }else {
                        $('#balance_transfer_section').hide();
                        $('#cheque_number_section').hide();
                    }
                }else {
                    $('#cheque_number_section').show();
                    $('#type_section').hide();
                    $('#balance_transfer_section').hide();
                }
            });

            $('#type').on('change', function(){
                var type = $('#type').val();
                if (type == 'cheque') {
                    $('#cheque_number_section').show();
                    $('#balance_transfer_section').hide();
                }
                else if (type == 'balance_transfer') {
                    $('#balance_transfer_section').show();
                    $('#cheque_number_section').hide();

                }
                else {
                    $('#balance_transfer_section').hide();
                    $('#cheque_number_section').hide();
                }
            });
        </script> --}}

@endsection
