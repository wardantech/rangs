@extends('layouts.main')
@section('title', 'Add Requisition')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Add Requisition')}}</h5>

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
                        <h3>{{ __('Add Requisition')}}</h3>
                    </div>
                    <div class="card-body">

                    <form class="form-prevent-multiple-submits" method="POST" action="{{ route('branch.requisition.store') }}">
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    {{-- <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="{{ $sl_number }}" readonly> --}}
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="" placeholder="Unavailable" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('requisition_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">
                                        {{ __('label.DATE')}}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Date" value="{{ currentDate() }}" required>
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">
                                        {{ __('Sender / From Store')}}
                                        <span class="text-red">*</span>
                                    </label>
                                    @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                    <select name="from_store_id" id="from_store_id" class="form-control select2" required>
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
                                    <label for="store_id">
                                        {{ __('To Store')}}
                                        <span class="text-red">*</span>
                                    </label>
                                    
                                    @if ($central_stores != null )
                                        <input type="text" class="form-control" value="{{$central_stores->name}}" readonly>
                                        <input type="hidden" name="store_id" id="store_id" class="form-control" value="{{$central_stores->id}}" required>
                                    @else
                                        <select name="store_id" id="store_id" class="form-control select2" required>
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach
                                        </select>   
                                    @endif
                                        <div class="help-block with-errors"></div>
                                    @error('store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="requisition_no">
                                        {{ __('Parts Name')}}
                                        <span class="text-red">*</span>
                                    </label>
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
                                    <button class="mt-2 btn btn-primary button-prevent-multiple-submits"><i class="spinner fa fa-spinner fa-spin"></i>Submit</button>
                                </div>
                            </div>

                        </div>

                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
    <script src="{{ asset('js/sony/prevent-multiple-submit.js') }}"></script>
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
                        from_store_id: from_store_id
                    },
                    success: function(data) {
                        console.log(data);
                        $("#parts_data").html(data.html);
                    }
                });

            });
        });
    </script>
    @endpush
@endsection
