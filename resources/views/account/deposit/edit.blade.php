@extends('layouts.main')
@section('title', 'Edit Deposit')
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
                            <h5>{{__('Edit Deposit')}}</h5>
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
                        <form action="{{ route('update.deposit', $deposit->id) }}" method="POST">
                            @csrf
                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="date" class="col-form-label">{{ __('label.DATE')}}
                                        <span class="text-red"> *</span>
                                    </label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', optional($deposit)->date ? date('Y-m-d', strtotime($deposit->date)) : '') }}" required>
                                    @error('date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="date" class="col-form-label">{{ __('label.BRANCH') }}
                                        <span class="text-red"> *</span>
                                    </label>
                                    @if ($userRole->name == 'Super Admin' || $userRole->name == 'Admin')
                                        <select name="outlet_id" id="outlet_id" class="form-control select2">
                                            <option value="">{{ __('Select Branch') }}</option>
                                            @foreach ($outlets as $outlet)
                                                <option value="{{ $outlet->id }}"
                                                    @if ($deposit->outlet_id == $outlet->id)
                                                        selected
                                                    @endif
                                                >{{ $outlet->name }}</option>
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
                                    <label for="account_no" class="col-form-label">{{ __('label.DEPOSIT TYPE')}} <span class="text-red"> *</span></label>
                                    <select name="deposit_type" id="deposit-type" class="form-control" required>
                                        <option>Select a deposit type</option>
                                        <option value="cash" {{ ($deposit->deposit_type == 'cash') ? 'selected' : '' }}>Cash</option>
                                        <option value="cheque" {{ ($deposit->deposit_type == 'cheque') ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                    @error('deposit_type')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row" id="chaque_section">
                                <div class="form-group col-md-12">
                                    <label for="cheque_nunber" class="col-form-label">{{ __('label.CHEQUE NUMBER')}}</label>
                                    <input type="text" class="form-control" id="cheque_nunber" name="cheque_nunber" value="{{ $deposit->cheque_nunber }}">
                                    @error('chaque_number')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="account_no" class="col-form-label">{{ __('label._SELECT ACCOUNT')}}
                                        <span class="text-red"> *</span>
                                    </label>
                                    <select name="account_id" id="" class="form-control" required>
                                        <option value="">Select an account</option>
                                        @foreach($bankAccounts as $bankAccount)
                                                <option value="{{$bankAccount->id}}"
                                                    @if ($bankAccount->id == $deposit->account_id)
                                                        selected
                                                    @endif
                                                >{{$bankAccount->bank_name}}-[A/C-{{$bankAccount->account_no}}]</option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="amount" class="col-form-label">{{ __('label.AMOUNT')}}
                                        <span class="text-red"> *</span>
                                    </label>
                                    <input type="number" class="form-control" id="amount" name="amount" value="{{ $deposit->amount }}" min="0" required>
                                    @error('amount')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="remark" class="col-form-label">{{ __('label.REMARK')}}</label>
                                    <input type="text" class="form-control" id="remark" name="remark" value="{{ $deposit->remark }}">
                                    @error('remark')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('script')
            <script src="{{ asset('plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
        @endpush

        <script>
             $(document).ready(function(){

                if($('select[name="deposit_type"]').val() == 'cheque') {
                    $("#chaque_section").show();
                }else {
                    $("#chaque_section").hide();
                }

                $('select[name="deposit_type"]').on('change', function() {
                    if($(this).val() == 'cheque'){
                        $("#chaque_section").show(500);
                        $( "#chaque_number" ).prop( "required", true );
                    }else{
                        $("#chaque_section").hide(500);
                        $( "#chaque_number" ).prop( "required", false );
                    }
                });

                $('select[name="deposit_type"]').on('change', function() {
                    if($(this).val() == 'cheque'){
                        $("#edit_chaque_section").show(500);
                        $( "#chaque_number" ).prop( "required", true );
                    }else{
                        $("#edit_chaque_section").hide(500);
                        $( "#chaque_number" ).prop( "required", false );
                    }
                });
             });
        </script>

@endsection
