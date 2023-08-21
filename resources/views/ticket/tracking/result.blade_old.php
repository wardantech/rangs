<!doctype html>
<html class="no-js" lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Tracking | Rangs</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="{{ asset('img/title.png') }}" type="image/x-icon" />

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/ionicons/dist/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/icon-kit/dist/css/iconkit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/theme.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script src="{{ asset('src/js/vendor/modernizr-2.8.3.min.js') }}"></script>
    </head>

    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="wrapper">
            <div class="auth-wrapper">
                <div class="container-fluid h-100">
                    <div class="row h-100">
                        <div class="col-xl-4 col-lg-4 col-md-4 m-auto">
                            <div class="col-md-12">
                                <div class="card p-3">
                                    <div class="card-header">
                                        <h3>Welcome to Rangs Service Tracking</h3>
                                        <hr>
                                        <h3>TSL-{{ $ticket->ticket_id }} </h3>
                                    </div>
                                    <div class="card-body">
                                        <table id="user_table" class="table">

                                            <tbody>
                                                <tr>
                                                    <th>Name</th>
                                                    <td>{{ $ticket->customer_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Phone</th>
                                                    <td>{{ $ticket->customer_mobile }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Product</th>
                                                    <td>{{ $ticket->product_category }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    @if ($ticket->is_delivered_by_teamleader !=null)
                                                    <td style="color: rgb(231, 46, 4)">Working On Progress</td>  
                                                    @else
                                                    <td style="color: green">Waiting To Delivery</td>
                                                    @endif

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="{{ asset('src/js/vendor/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('plugins/popper.js/dist/umd/popper.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('plugins/screenfull/dist/screenfull.js') }}"></script>
        
    </body>
</html>
