@extends('layouts.main')
@section('title', 'Attachment')
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
                            <h5>{{__('label.ATTACHMENT')}}</h5>
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
                        <form method="post" action="{{url('technician/submission/photo/store')}}" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div><h6><strong>Job Info:</strong></h6></div>
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.JOB NO')}}</label>
                                    <div>
                                        <input type="hidden" class="form-control" id="job_id" name="job_id" value="{{$job->id}}">
                                        <input type="text" class="form-control" id="job_no" name="job_no" value="JSL-{{$job->id}}" placeholder="Job No" readonly>
                                        @error('job_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.TICKET_SL')}}</label>
                                    <div>
                                        <input type="text" class="form-control" id="job_no" name="job_no" value="TSL-{{$job->ticket->id}}" placeholder="Job No" readonly>
                                        @error('job_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="date">{{__('label.PRODUCT_CATEGORY')}}</label>
                                    <div>
                                        <input type="text" class="form-control" id="job_no" name="job_no" value="{{$job->ticket->purchase->category->name}}" placeholder="Job No" readonly>
                                        @error('job_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-2 mb-3"/>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group control-group increment" >
                                        <input type="file" name="filename[]" class="form-control">
                                        @error('filename')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <div class="input-group-btn"> 
                                          <button class="btn btn-success pl-1" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                                        </div>
                                    </div>
                                    <div class="clone hide">
                                        <div class="control-group input-group" style="margin-top:10px">
                                          <input type="file" name="filename[]" class="form-control">
                                          @error('filename')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                          <div class="input-group-btn"> 
                                            <button class="btn btn-danger pl-1" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <hr class="mt-2 mb-3"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-md-6">
                                        <img id="previewInvoice" class="rounded mx-auto d-block mt-3 mb-3" src="{{ asset('img/user.jpg') }}" alt="Part" height="150px" width="150px"> <br>
                                      </div>
                                </div>
                            </div> --}}

                            <hr class="mt-2 mb-3"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="parts">{{ __('label.REMARK')}}</label>
                                    <textarea name="remark" id="remark" class="form-control" cols="30" rows="2"></textarea>
                                    @error('remark')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <input type="submit" class="btn form-bg-danger mr-2">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- push external js -->


    <script type="text/javascript">
        $(document).ready(function() {

            $(".btn-success").click(function(){ 
                var html = $(".clone").html();
                $(".increment").after(html);
            });

            $("body").on("click",".btn-danger",function(){ 
                $(this).parents(".control-group").remove();
            });

        });
        //Image Preview
        function previewFile(input){

            var file = $("input[type=file]").get(0).files[0];
            if(file){
                var reader = new FileReader();

                reader.onload = function(){
                    $("#previewImg").attr("src", reader.result);
                }
                reader.readAsDataURL(file);
            }
        }
        var loadFile = function(event) {
            var output = document.getElementById('previewInvoice');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        }
    </script>
@endsection
