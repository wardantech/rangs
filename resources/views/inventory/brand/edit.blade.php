@extends('layouts.main')
@section('title', 'Brand Update')
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
                            <h5>{{ __('label.BRAND_UPDATE')}}</h5>
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
                        <h3>{{ __('label.BRAND_UPDATE')}}</h3>
                    </div>
                    <div class="card-body">
                        {{-- {{ Form::model($brand,array('role' => 'form', 'url' => 'product/update/'.$brand->id, 'files'=> true, 'class' => 'form-horizontal', 'id'=>'edit', 'method'=> 'patch')) }} --}}
                        <form action="{{ route('product.brand.update', $brand->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('label.SELECT_CATEGORY')}}<span class="text-red">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">Select Category</option>
                                            @forelse ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    @if($brand->product_category_id == $category->id)
                                                        selected
                                                    @endif
                                                >
                                                    {{ $category->name }}
                                                </option>
                                            @empty
                                                No Category Here...
                                            @endforelse
                                        </select>
                                        <div class="help-block with-errors"></div>
                                        @error('category_id')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ __('label.BRAND_NAME')}}<span class="text-red">*</span></label>

                                        <input type="text" name="name" class="form-control" value="{{ $brand->name }}">

                                        @error('name')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">
                                            {{ __('label.BRAND_CODE')}}
                                            <span class="text-red">*</span>
                                        </label>

                                        <input type="text" class="form-control" name="code" value="{{ $brand->code }}">

                                        @error('code')
                                            <span class="text-red-error" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

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

        });
    </script>
@endsection
