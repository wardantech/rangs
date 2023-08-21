<!DOCTYPE html>
<html lang="en">
<head>
	<title>Rangs | Ticket Tracking Result</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
<link rel="icon" href="{{ asset('img/title.png') }}" type="image/x-icon" />

<link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
<!--===============================================================================================-->
<link rel="stylesheet" href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css') }}">
<!--===============================================================================================-->
<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

	<link rel="stylesheet" type="text/css" href="{{ asset('css/tracking.css') }}">
<!--===============================================================================================-->

<style>
	.login-pic img{
		width: 139px;
    margin: 0 auto;
    text-align: center;
	}
    .h-btn{
        width: 54px;
    height: 37px;
    text-align: center;
    padding: 9px;
    }
	.login-pic{
		padding-top: 121px;
    width: 50%;
    text-align: center;
	}
	.wrap-login100{
		padding: 100px;
	}
    #user_table{
        width: 359px;
    margin: 0 auto;  
    }
	@media (max-width: 1024px){
		.login-pic{
	    text-align: center;
    width: 100%;
	}
	.login-pic img{
		width: 139px;
    margin: 0 auto;
    text-align: center;
	}
	.wrap-login100{
		padding:20px;
	}
    #user_table{
        width: 100%;
    margin: 0 auto;  
    }
	}

</style>
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="col-12">
                        <div class="col-12 d-flex justify-content-between mb-5">
                                <a href="{{url('ticket/tracking')}}" class="" title="Home"><i class="fa fa-home btn btn-outline-success h-btn"></i></a>
                                 <h2>Welcome To RANGS Service</h2>
                                <a href="{{ url()->previous() }}" class=" text-right" title="Go Back"><i class="fa fa-arrow-left btn h-btn btn-outline-danger" aria-hidden="true"></i></a>
                        </div>                    
                    <table id="user_table" class="table table-responsive">

                        <tbody>
                            @if ($ticket!=null)
                            <tr>
                                <th>Name</th>
                                <td>{{ $ticket->customer_name ?? null }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $ticket->customer_mobile ?? null }}</td>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <td>{{ $ticket->product_category ?? null}}</td>
                            </tr>
                            <tr>
                                <th>Ticket No</th>
                                <td>TSL-{{ $ticket->ticket_id ?? null}}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                @if ($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 1)
                                <td style="color: green">Product is delivered</td>
                                @elseif ($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 0)
                                <td style="color: green">Product is delivered</td>
                                @elseif($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_delivered_by_teamleader == 1 && $ticket->is_closed == 0)
                                <td style="color: green">Waiting for delivery</td>
                                @else
                                <td style="color: rgb(231, 46, 4)">Work in Progress</td> 
                                @endif

                            </tr>
                            @else
                                <tr>
                                    <td>Invalid Ticket !</td>
                                </tr>
                            @endif
                        
                        </tbody>
                    </table>
                </div> 
			</div>
		</div>
	</div>
	

</body>
</html>