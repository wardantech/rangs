@extends('layouts.main')
@section('title', 'Customer Feedback')
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
                            <h5>{{ __('label.CREATE CUSTOMER FEEDBACK')}}</h5>
                            {{-- <span>{{ __('label.CREATE A NEW PARTS')}}</span> --}}
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
                    <div class="card-header">
                        <h3>{{ __('label.CREATE CUSTOMER FEEDBACK')}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('call-center.customer-feedback.store')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                       
                                        {{-- <label for="product_category" class="col-sm-4 col-form-label">Product Category</label> --}}
                                        @foreach($questions as $question)
                                        <div class="col-sm-8">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td>
                                                        <span>{{$question->question}}: </span>
                                                        <input type="hidden" name="ticket_id" value="{{$ticketId}}">
                                                        <input type="hidden" name="question_id[]" value="{{$question->id}}">
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="question{{$question->id}}" id="" value="0">
                                                        <label for="">NA</label>
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="question{{$question->id}}" id="" value="1">
                                                        <label for="">Avarage</label>
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="question{{$question->id}}" id="" value="2">
                                                        <label for="">Good</label>
                                                    </td>
                                                    <td>
                                                        <input type="radio" name="question{{$question->id}}" id="" value="3">
                                                        <label for="">Great</label>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            
                                            
                                            
                                            
                                            {{-- <input type="radio" name="question" id="" value="4">
                                            <label for="">4</label>
                                            <input type="radio" name="question" id="" value="5">
                                            <label for="">5</label> --}}
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn form-bg-danger mr-2">Submit</button>
                                    <button class="btn form-bg-inverse">Cancel</button>
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
