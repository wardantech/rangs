@extends('layouts.main')
@section('title', 'Edit Requisition')
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
                            <h5>{{ __('Edit Requisition')}}</h5>

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
                        <h3>{{ __('Edit Requisition')}}</h3>
                    </div>
                    <div class="card-body">

                    <form action="{{ route('branch.requisition.update', $requistion->id) }}" class="forms-sample" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="requistion-id" value="{{ $requistion->id }}">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="requisition_no">{{ __('Requisition No')}}</label>
                                    <input type="text" class="form-control" id="requisition_no" name="requisition_no" value="B-RSL-{{ $requistion->id }}" readonly>
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
                                    <input type="date" class="form-control" id="date" name="date" placeholder="Date" value="{{ $requistion->date->toDateString() }}" required>
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
                                    <select name="from_store_id" id="from_store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{$store->id}}"
                                            @if ($requistion->from_store_id == $store->id)
                                               selected 
                                            @endif
                                            >{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    @else
                                        <input type="text" class="form-control"  name="" value="{{ $requistion->senderStore->name }}" readonly>
                                        <input type="hidden" id="from_store_id" name="from_store_id" value="{{ $requistion->from_store_id }}">
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
                                    <input type="text" class="form-control" value="{{$requistion->store->name}}" readonly>
                                    <input type="hidden" name="store_id" id="store_id" class="form-control" value="{{$requistion->store_id}}" required>
                                    {{--<select name="store_id" id="store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->id}}"
                                                @if ($store->id == $requistion->store_id)
                                                    selected
                                                @endif
                                            >{{$store->name}}</option>
                                        @endforeach
                                    </select>--}}
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
                                    <select name="parts_id" id="parts_id" class="form-control select2" multiple disabled>
                                        <option value="">Select parts</option>
                                        @foreach($parts as $key=> $part)
                                            <option value="{{$part->id}}"
                                                @foreach ($partsId as $partId)
                                                    @if ($part->id == $partId)
                                                        selected
                                                    @endif
                                                @endforeach
                                            >{{$part->code}}-{{$part->name}}</option>
                                        @endforeach
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

                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    @endpush

    <script type="text/javascript">
        $(document).ready(function(){
            var parts_id = $("#parts_id").val();
            var requistion_id = $("#requistion-id").val();
            var from_store_id = $("#from_store_id").val();
            var url = "{{ url('inventory/outlet/parts/stock/edit') }}";
            $.ajax({
                type: "get",
                url: url,
                data: {
                    parts_id: parts_id,
                    requistion_id: requistion_id,
                    from_store_id: from_store_id
                },
                success: function(data) {
                    $("#parts_data").html('');
                    $("#parts_data").html(data.html);
                }
            });

            $('#parts_id').on('change', function(e){
                e.preventDefault();
                var parts_id = $("#parts_id").val();
                var requistion_id = $("#requistion-id").val();
                var from_store_id = $("#from_store_id").val();
                var url = "{{ url('inventory/outlet/parts/stock/edit') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        parts_id: parts_id,
                        requistion_id: requistion_id,
                        from_store_id: from_store_id
                    },
                    success: function(data) {
                        $("#parts_data").html('');
                        $("#parts_data").html(data.html);
                    }
                });
            });
        });
    </script>
@endsection
