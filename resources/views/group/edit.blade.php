@extends('layouts.main')
@section('title', 'Group Update')
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
                            <h5>{{ __('label.GROUP_UPDATE')}}</h5>

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
                <div class="card">
                    <div class="card-header my-2">
                        <h3>{{ __('label.GROUP_UPDATE')}}</h3>
                    </div>
                    <div class="card-body">

                        <form class="forms-sample" method="POST" action="{{ route('general.group.update',$group->id) }}">
                        @csrf
                        @method('put')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category_id">
                                            {{ __('label.SELECT_CATEGORY')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="category_id[]" id="category_id" class="form-control select2" multiple="multiple" required>
                                            {{-- <option value="">Select</option> --}}
                                            <?php
                                                $selectedCategories= json_decode($group->category_id);
                                            ?>
                                            @forelse($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if(in_array($category->id, $selectedCategories))
                                                    selected
                                                @endif
                                            >{{ $category->name }}</option>
                                            @empty
                                                <option value="">No Category Found</option>
                                            @endforelse
                                        </select>
                                        @error('category_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="region_id">
                                            {{ __('Region')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <select name="region_id" id="region_id" class="form-control select2" required>
                                            <option value="">Select</option>
                                            @forelse($regions as $region)
                                            <option value="{{ $region->id }}"
                                                @if( old('region_id',optional($group)->region_id) == $region->id ))
                                                    selected
                                                @endif
                                                >
                                                {{ $region->name }}
                                            </option>
                                            @empty
                                                <option value="">No Category Found</option>
                                            @endforelse
                                        </select>
                                        @error('region_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">
                                            {{ __('Group Name')}}
                                            <span class="text-red">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Group Name" value="{{ old('name', optional($group)->name) }}" required>
                                        <div class="help-block with-errors"></div>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('label.SUBMIT')}}</button>
                                        {{-- <a href="{!! URL::to('inventory') !!}" class="btn btn-inverse js-dynamic-disable">{{ __('label.CENCEL')}}</a> --}}
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
