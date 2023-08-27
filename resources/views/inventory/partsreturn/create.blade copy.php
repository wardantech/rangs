@extends('layouts.main') 
@section('title', 'Parts Return')
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
                            <h5>{{ __('label.CREATE_PARTS_RETURN')}}</h5>
                        
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
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
                        <h3>{{ __('label.CREATE_PARTS_RETURN')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="" method="POST" action="{{ route('inventory.parts-return.store') }}">
                            @csrf
                            <input name="purchase_id" type="hidden" value="" class="form-control">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="date" class="">Date</label>
                                        <input name="date"  placeholder="Date" type="date" class="form-control">
                                        @if ($errors->has('date'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="part">{{ __('label.SELECT_PART')}}<span class="text-red">*</span></label>
                                        <select name="part_id" id="part" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($parts as $id=>$part)
                                            <option value="{{$id}}">{{$part}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('part_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('part_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="model">{{ __('label.MODEL')}}<span class="text-red">*</span></label>
                                        <select name="model_id" id="model_id" class="form-control" required>

                                        </select>
                                        @if ($errors->has('model_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('model_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="quantity">{{ __('label.QUANTITY')}}<span class="text-red">*</span></label>
                                        <input name="quantity"  placeholder="" type="text" class="form-control" value="{{ old('quantity') }}">
                                        @if ($errors->has('quantity'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('quantity') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="outlet">{{ __('label.OUTLET')}}<span class="text-red">*</span></label>
                                        <select name="outlet_id" id="outlet_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($outlets as $id=>$outlet)
                                            <option value="{{$id}}">{{$outlet}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('outlet_id'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('outlet_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="note">{{ __('label.NOTE')}}<span class="text-red">*</span></label>
                                        <input name="note"  placeholder="" type="text" class="form-control" value="{{ old('note') }}">
                                        @if ($errors->has('note'))
                                            <span class="is-invalid">
                                                <strong>{{ $errors->first('note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button class="mt-2 btn btn-primary">Submit</button>
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

            $(".integer-decimal-only").each(function () {
                $(this).keypress(function (e) {
                    var code = e.charCode;

                    if (((code >= 48) && (code <= 57)) || code == 0 || code == 46) {
                        return true;
                    } else {
                        return false;
                    }
                });
            });


            $('#part').on('change', function(e){
                e.preventDefault();
                var part_id = $("#part").val();
                var url = "{{ url('inventory/model') }}";
                $.ajax({
                    type: "get",
                    url: url,
                    data: {
                        id: part_id,
                    },
                    success: function(data) {
                        console.log(data);
                        // $("#available_qty").val(data.stock);
                    var html = "<option value="+null+">Select Parts Model</option>";
                    $("#model_id").empty();
                    $.each(data.partsModel, function(key) {
                    //   console.log(data.recYarn_name[key].brand);

                        html += "<option value="+data.partsModel[key].id+">"+data.partsModel[key].name+"</option>";
                    })
                    $("#model_id").append(html);
                    html = "";
                    }
                })
            });    
            //Rack  
            $('#store').on('change', function(){
                var store_id=$(this).val();
                if(store_id){
                    $.ajax({
                        url: "{{url('inventory/get/rack/')}}/"+store_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Rack</option>";
                            $('#rack').empty();
                            $.each(data, function(key, value){
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#rack").append(html);
                            html = "";
                        }
                    });
                }
            });
            //Bin
            $('#rack').on('change', function(){
                var rack_id=$(this).val();
                if(rack_id){
                    $.ajax({
                        url: "{{url('inventory/get/bin/')}}/"+rack_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            var html = "<option value="+null+">Select Bin</option>";
                            $('#bin').empty();
                            $.each(data, function(key, value){
                                // $('#bin').append("<option value="+value.id+">"+value.name+"</option>");
                                html += "<option value="+value.id+">"+value.name+"</option>";
                            });
                            $("#bin").append(html);
                            html = "";
                        }
                    });
                }
            });

            $('#price-details').hide();

            $('#model_id').on('change', function(){
                // $('#price-details').show(500);
                var part_id=$('#part').val();
                var model_id=$(this).val();
                if(model_id){
                    $.ajax({
                        url: "{{url('get/price/')}}/"+part_id+"/"+model_id,
                        type: 'GET',
                        dataType: "json",
                        success: function(data){
                            console.log(data);
                            
                                $('#price-details').show(500);
                                $('#cost_price_usd').val(data.cost_price_usd);
                                $('#cost_price_bdt').val(data.cost_price_bdt);
                                $('#selling_price_bdt').val(data.selling_price_bdt);
                            
                        }
                    });
                }
            });
        });
    </script>
@endsection