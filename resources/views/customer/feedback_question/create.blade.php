@extends('layouts.main')
@section('title', 'Add New Question')
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
                        <i class="ik ik-headphones bg-danger"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.QUESTIONS_ADD')}}</h5>
                            <span>{{ __('label.QUESTIONS_CREATE')}}</span>
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
            @include('include.message')
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header my-3">
                        <h3>{{ __('label.QUESTIONS_ADD')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('call-center.customer-feedback-question.store')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="question">
                                        {{ __('label.QUESTIONS') }}
                                        <span class="text-red">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="question" placeholder="Type Question" value="{{ old('question') }}" required>
                                    @error('question')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-danger mr-2">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
        $(document).ready(function(){

    });
    </script>
@endsection
