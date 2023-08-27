<!DOCTYPE html>
<html lang="en">
<head>
	<title>Rangs | Ticket Tracking</title>
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
    margin: 0 auto;
    text-align: center;
	}
	.login-pic{
    width: 50%;
    text-align: center;
	}
	.wrap-login100{
		padding: 100px;
	}
	@media (max-width: 1024px){
		.login-pic{
	    text-align: center;
    width: 100%;
	}
	.login-pic img{
	    width: 191px;
    margin: 0 auto;
    text-align: center;
	}
	.wrap-login100{
		padding:20px;
	}
	}

</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login-pic js-tilt" data-tilt>
					
						<img src="{{ asset('img/rangs-logo.png') }}" alt="IMG">

				</div>
                <form method="POST" class="login100-form validate-form" action="{{ route('ticket.tracking') }}">
				@csrf
					<span class="login100-form-title">
						Welcome To RANGS Service
					</span>
                    
					<div class="wrap-input100 validate-input">
						<div class="wrap-input100 validate-input" >
						<input class="input100" type="text" name="tsl" placeholder="Ticket SL Number">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-search" aria-hidden="true"></i>
						</span>
                        @error('tsl')
                            <span class="alert alert-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Search
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	

</body>
</html>