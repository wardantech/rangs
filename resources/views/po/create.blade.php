@extends('layouts.main')
@section('title', 'Add Purchase Requisition')
@section('content')

    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Create Purchase requisitions')}}</h5>

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
                        <h3 class="my-2">{{ __('Create Purchase requisitions')}}</h3>
                    </div>
                    <div class="card-body">

                    {{ Form::open(array('route' => 'purchase.requisitions.store', 'class' => 'forms-sample', 'id'=>'','method'=>'POST')) }}
                    @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="po_number">
                                        {{ __('Purchase Requisition No')}}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="po_number" name="po_number" value="{{ $sl_number }}" readonly>
                                    <div class="help-block with-errors"></div>

                                    @error('po_number')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="orderDate">{{ __('label.DATE')}}<span class="text-red">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ currentDate() }}" >
                                    <div class="help-block with-errors"></div>

                                    @error('date')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="from_store_id">{{ __('Sender / From Store')}}</label>
                                    @if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                    <select name="from_store_id" id="from_store_id" class="form-control">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{$store->id}}"
                                            @if( old('from_store_id') == $store->id )
                                                selected
                                            @endif
                                            >
                                            {{$store->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                    @else
                                    <input type="hidden" class="form-control" id="from_store_id" name="from_store_id" value="{{ $mystore->id }}" readonly>
                                    <input type="text" class="form-control" id="tech_store" name="tech_store" value="{{ $mystore->name }}" readonly>
                                    @endif

                                    <div class="help-block with-errors"></div>

                                    @error('from_store_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="store_id">{{ __('To Procurement')}}</label>
                                    <input type="text" name="store_id" value="1" class="form-control" placeholder="" readonly>

                                    @error('store_id')
                                        <span class="text-red-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-8">
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
