@extends('layouts.main') 
@section('title', 'Data Tables')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/DataTables/datatables.min.css') }}">
    @endpush
 


    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-users bg-danger"></i>
                        <div class="d-inline">
                            <h5>Customer Info</h5>
                            <span>Search by Phone / Customer name</span>
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

        <div class="card">
           
            <div class="card-body">

                <div class="row">
                    <div class="col-sm-6">

                        <div class="row mb-3">
                            <label for="inputcpn" class="col-sm-4 col-form-label">1. Customer Phone No :</label>
                            <div class="col-sm-7 mr-20">
                                <input type="search" class="form-control" id="CPN" placeholder="Customer Phone No">
                            </div>
                        </div>
                        

                    </div>

                    <div class="col-sm-6">

                        <div class="row mb-3">
                            <label for="inputcn" class="col-sm-4 col-form-label">2. Customer Name :</label>
                            <div class="col-sm-7 mr-20">
                                <input type="search" class="form-control" id="CN" placeholder="Customer Name">
                            </div>
                        </div>

                    </div>

                </div>

                
                
                <div class="row invoice-info mt-20 mb-20">
                    
                    <div class="col-sm-4 invoice-col">
                       
                    </div>
                    <div class="col-sm-4 invoice-col">
                       
                    </div>
                    <div class="col-sm-4 invoice-col">
                        
                        <b>Customer Name :</b> Mr.Rahim<br>
                        <b>Customer Address : </b> Dhaka, Uttara -1230<br>
                        <b>Customer Mobile :</b> 01725785460
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="section_title">Purchase History</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Product sl</th>
                                    <th>Purchase data</th>
                                    <th>product name</th>
                                    <th>Product Code</th>
                                    <th>Brand Name</th>
                                    <th>Model Name</th>
                                    <th>Point of Purchase / Outlet / Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr >
                                    <td>
		                    			<label class="custom-control custom-checkbox">
		                    				<input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
		                    				<span class="custom-control-label">&nbsp;</span>
		                    			</label>
		                    		</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                    <td>d6</td>
                                    <td>d7</td>
                                    <td>d8</td>
                                    <td><a class="btn btn-light" href="">Create New ticket</a> </td>
                                </tr>
                                <tr >
                                    <td>
		                    			<label class="custom-control custom-checkbox">
		                    				<input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
		                    				<span class="custom-control-label">&nbsp;</span>
		                    			</label>
		                    		</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                    <td>d6</td>
                                    <td>d7</td>
                                    <td>d8</td>
                                    <td><a class="btn btn-light" href="">Create New ticket</a> </td>
                                </tr>
                                <tr >
                                    <td>
		                    			<label class="custom-control custom-checkbox">
		                    				<input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
		                    				<span class="custom-control-label">&nbsp;</span>
		                    			</label>
		                    		</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                    <td>d6</td>
                                    <td>d7</td>
                                    <td>d8</td>
                                    <td><a class="btn btn-light" href="">Create New ticket</a> </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="section_title">Service History</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive text-center">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    <th>Service Discription</th>
                                   
                                </tr>
                            </thead>
                            <tbody align="center">
                                <tr >
                                    <td>d1</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                </tr>

                                <tr >
                                    <td>d1</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                </tr>
                                <tr >
                                    <td>d1</td>
                                    <td>d2</td>
                                    <td>d3</td>
                                    <td>d4</td>
                                    <td>d5</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>


               
            </div>
        </div>
    </div>
               

    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/DataTables/datatables.min.js') }}"></script>
        <script src="{{ asset('js/datatables.js') }}"></script>
    @endpush
@endsection
      
