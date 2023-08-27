@extends('layouts.main')
@section('title', 'Request Parts Transfer')
@section('content')

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Request Parts Transfer')}}</h5>

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
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Request Parts Transfer')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'loan.loan-request.store', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="loan_no">{{ __('Parts Transfer No')}}</label>
                                    <input type="text" class="form-control" id="loan_no" name="loan_no" value="{{$sl_number}}" placeholder="Loan No" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('loan_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Date" value="{{ currentDate() }}">
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Sender / From Store')}}</label>
                                    @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                    <select name="from_store_id" id="from_store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    @else
                                        <input type="text" class="form-control"  name="" value="{{ $mystore->name }}" readonly>
                                        <input type="hidden" id="from_store_id" name="from_store_id" value="{{ $mystore->id }}">
                                    @endif
                                    <div class="help-block with-errors"></div>

                                    @error('from_store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="store_id">{{ __('To Store')}}</label>
                                    <select name="store_id" id="store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Parts Name')}}</label>
                                    <select name="parts_id" id="parts_id" class="form-control js-data-example-ajax" multiple required>

                                    </select>
                                    <div class="help-block with-errors"></div>

                                    @error('parts_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12" id="parts_data">

                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                </div>
                            </div>

                        </div>

                    {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize select2
            $(".js-data-example-ajax").select2({
                placeholder: "Search for an Item",
                ajax: {
                    url: "{{route('inventory.get_parts')}}",
                    type: "post",
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            query: params.term, // search term
                            "_token": "{{ csrf_token() }}",
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        });

        $(document).ready(function(){
            $('#parts_id').on('change', function(e){
                e.preventDefault();
                var parts_id = $("#parts_id").val();
                var from_store_id = $("#from_store_id").val();
                var url = "{{ url('inventory/outlet/parts/stock') }}";
                //alert(part_id);
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        parts_id: parts_id,
                        from_store_id : from_store_id
                    },
                    success: function(data) {
                        console.log(data);
                        $("#parts_data").html(data.html);
                    }
                });

            });

            $('#store_id').on('change', function(e){
                var to_store=$("#store_id").val();
                var from_store=$("#from_store_id").val();
                if(to_store == from_store){
                    alert('Whoops! Sorry! Both Store are same')
                    $("option:selected").prop("selected", false)
                }
            });

            var store=$("#store_id").val();
            if(store){
                $("#parts_id").prop('disabled', false);
            }else{
                $("#parts_id").prop('disabled', true);
                $('#store_id').on('change', function(e){
                    $("#parts_id").prop('disabled', false);
                }) 
            }
        });
    </script>
    @endpush
@endsection
