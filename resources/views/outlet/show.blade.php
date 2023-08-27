@extends('layouts.main')
@section('title', 'Show Branch Details')
@section('content')

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('label.SHOW BRANCH DETAILS') }}</h5>
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
                @php
                    $thanaId = json_decode($outlet->thana_id);
                @endphp
                <div class="card p-3">
                    <div class="card-header">
                        <h3>{{ __('label.BRANCH DETAILS') }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Branch Name</td>
                                    <td>{{$outlet->name}}</td>
                                </tr>
                                <tr>
                                    <th>Branch Code</td>
                                    <td>{{$outlet->code}}</td>
                                </tr>
                                <tr>
                                    <th>District</td>
                                    <td>{{$outlet->district->name ?? null}}</td>
                                </tr>
                                <tr>
                                    <th>Thana</td>
                                    <td>
                                        @isset($thanaId)
                                        @forelse ($thanas as $thana)
                                            @if (in_array($thana->id, $thanaId))
                                                {{ $thana->name }},
                                            @endif
                                        @empty
                                            No Thana Here...
                                        @endforelse
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <th>Branch Address</td>
                                    <td>{{$outlet->address}}</td>
                                </tr>
                                <tr>
                                    <th>Branch Owner Name</td>
                                    <td>{{$outlet->outlet_owner_name}}</td>
                                </tr>
                                <tr>
                                    <th>Market</td>
                                    <td>{{$outlet->market}}</td>
                                </tr>
                                <tr>
                                    <th>Mobile Number</td>
                                    <td>{{$outlet->mobile}}</td>
                                </tr>
                                <tr>
                                    <th>Branch Owner Address</td>
                                    <td>{{$outlet->outlet_owner_address}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
