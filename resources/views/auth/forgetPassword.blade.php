@extends('auth.layouts.app')

@section('content')
<div class="sm-login-page">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="login-left-sec">
						<img src="{{asset('images/Logo-tran.png') }}" alt="login-image" class="login-image">
					</div>
				</div>
				<div class="col-md-6">
					<div class="login-right-sec">
						<!-- <form id="forget-password" class="forget-password" action="{{ route('forget.password.post') }}" method="POST"> -->
						<form id="forget-password" class="forget-password" method="POST">
							@if(session()->has('success_msg'))
							<div class="alert alert-success">
								{{ session()->get('success_msg') }}
							</div>
							@endif
							@if(session()->has('error_msg'))
							<div class="alert alert-danger">
								{{ session()->get('error_msg') }}
							</div>
							@endif
                            @csrf
							<div class="form-title">
								<h2 class="lg-title">{{ __('Forgot Password') }}</h2>
							</div>
							<div class="form-group">
								<input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" aria-describedby="Email" placeholder="Email" autocomplete="email" autofocus>
								@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
							</div>
						  	<!-- <div class="form-check clearfix">
								<div class="checkbox-sec float-sm-left disabled">
									<input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
									<label class="form-check-label" for="exampleCheck1">Keep me signed in</label>
								</div>
								<div class="forgot-sec float-sm-right">
									<a href="#" class="forgot-text link-text">{{ __('Forgot Password ?') }}</a>
								</div>
						  	</div> -->
							<div class="btn-sec d-block clearfix">
								<button type="submit" class="btn btn-primary d-inline blue-bg-btn" id="submit_forget_button">{{ __('Submit') }}</button>
								<a href="javascript:void(0);" class="btn btn-outline-primary blue-btn d-inline" onclick="history.back()">Back</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
@endsection
