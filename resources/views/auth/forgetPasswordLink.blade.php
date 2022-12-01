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
						<form id="reset_password" class="reset_password" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
							<div class="form-title">
								<h2 class="lg-title">{{ __('Reset Password') }}</h2>
							</div>
                            <div class="form-group">
								<input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" aria-describedby="password" placeholder="New Password">
								@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
							</div>
                            <div class="form-group">
								<input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" aria-describedby="password_confirmation" placeholder="Confirm Password">
								@error('password_confirmation')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
							</div>
							<div class="btn-sec d-block clearfix">
								<button type="submit" class="btn btn-primary d-inline blue-bg-btn">{{ __('Submit') }}</button>
								<a href="#" class="btn btn-outline-primary blue-btn d-inline">Back</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
@endsection
