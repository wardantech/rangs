@extends('layouts.main') 
@section('title', 'Dashboard')
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
                            <h5>Ticket Management</h5>
                            <span>Create a new ticket</span>
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
                        <form class="forms-sample" method="POST" action="/inventory/products">
                        <input type="hidden" name="_token" value="R7Ddbbgxb1qEbQoTDakkow75fNl3gqY3q3qkjl94">  
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">1. Serial Number :</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Serial-Number" placeholder="Serial Number">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">3. Product Category :</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="exampleSelectGender">
                                                <option>cat1</option>
                                                <option>cat2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputMobileNumber" class="col-sm-4 col-form-label">5. Mobile Number</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Mobile-Number" placeholder="Mobile Number">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputCustomerName<" class="col-sm-4 col-form-label">7. Customer Name</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="Customer-Name" placeholder="Customer Name">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputAddress" class="col-sm-4 col-form-label">9. Address</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="Address" placeholder="Address">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputThana" class="col-sm-4 col-form-label">11. Thana</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="Thana" placeholder="Thana">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">2. Product Code</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Serial-Number" placeholder="Serial Number">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">4. Brand</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Serial-Number" placeholder="Serial Number">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputWarrantyType" class="col-sm-4 col-form-label">6. Warranty Type :</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="exampleSelectGender">
                                                <option>Type1</option>
                                                <option>Type2</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="inputMobileNumber" class="col-sm-4 col-form-label">8. Mobile Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="Mobile-Number" placeholder="Mobile Number">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="inputWarrantyType" class="col-sm-4 col-form-label">10. District :</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="exampleSelectGender">
                                                <option>dis1</option>
                                                <option>dis2</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-sm-6">
                                    <h4 class="sub-title">12. Fault Description</h4>
                                    <div class="form-radio">
                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>

                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <h4 class="sub-title">14. Service Type :</h4>
                                    <div class="form-radio">
                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>

                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-danger">
                                            <label>
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Radio 1
                                            </label>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="row mt-20">
                                
                                <div class="col-xl-12 col-md-12">
                                    <h3 class="section_title">14. Previous Job</h3>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Image</th>
                                                    <th>Status</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>HeadPhone</td>
                                                    <td><img src="../img/widget/p1.jpg" alt="" class="img-fluid img-20"></td>
                                                    <td>
                                                        <div class="p-status bg-green"></div>
                                                    </td>
                                                    <td>$10</td>
                                                    <td>
                                                        <a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
                                                        <a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Iphone 6</td>
                                                    <td><img src="../img/widget/p2.jpg" alt="" class="img-fluid img-20"></td>
                                                    <td>
                                                        <div class="p-status bg-green"></div>
                                                    </td>
                                                    <td>$20</td>
                                                    <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>Jacket</td>
                                                    <td><img src="../img/widget/p3.jpg" alt="" class="img-fluid img-20"></td>
                                                    <td>
                                                        <div class="p-status bg-green"></div>
                                                    </td>
                                                    <td>$35</td>
                                                    <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>Sofa</td>
                                                    <td><img src="../img/widget/p4.jpg" alt="" class="img-fluid img-20"></td>
                                                    <td>
                                                        <div class="p-status bg-green"></div>
                                                    </td>
                                                    <td>$85</td>
                                                    <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>Iphone 6</td>
                                                    <td><img src="../img/widget/p2.jpg" alt="" class="img-fluid img-20"></td>
                                                    <td>
                                                        <div class="p-status bg-green"></div>
                                                    </td>
                                                    <td>$20</td>
                                                    <td><a href="#!"><i class="ik ik-edit f-16 mr-15 text-green"></i></a><a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-sm-12">
                                    <h3 class="section_title">15. Expected Schedule</h3>
                                </div>
                            </div>

                            <div class="row">
                                
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">Date & Time :</label>
                                        <div class="col-sm-5 pr-0">
                                            
                                            <input class="form-control datetimepicker-input" type="date">
                                        </div>
                                        <div class="col-sm-3">
                                            <input class="form-control" type="time">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">Date & Time :</label>
                                        <div class="col-sm-5 pr-0">
                                            
                                            <input class="form-control datetimepicker-input" type="date">
                                        </div>
                                        <div class="col-sm-3">
                                            <input class="form-control" type="time">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="row">
                                
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">2. Product Code</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Serial-Number" placeholder="Serial Number">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputWarrantyType" class="col-sm-4 col-form-label">6. Warranty Type :</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="exampleSelectGender">
                                                <option>Type1</option>
                                                <option>Type2</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">

                                    <div class="row mb-3">
                                        <label for="inputSerialNumber" class="col-sm-4 col-form-label">Product Code</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" id="Serial-Number" placeholder="Serial Number">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="row mt-20">
                                <div class="col-sm-6">
                                    <h4 class="sub-title">12. Fault Description</h4>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
                                    </div>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
                                    </div>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <h4 class="sub-title">14. Service Type :</h4>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
                                    </div>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
                                    </div>
                                    <div class="checkbox-zoom zoom-danger">
                                        <label>
                                            <input type="checkbox" value="">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-danger"></i>
                                            </span>
                                            <span> Danger</span>
                                        </label>
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
	<!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>

        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection