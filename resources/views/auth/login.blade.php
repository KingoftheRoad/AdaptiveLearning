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
						<form id="loginform" class="login-form">
                            @csrf
                            <input type="hidden" name="login_type" value="{{$loginType}}" id="login-type">
							<div class="form-title">
								<h2 class="lg-title">{{ __('Login') }}</h2>
							</div>
						  <div class="form-group">
						    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" aria-describedby="Email" placeholder="Email" autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
						  </div>
						  <div class="form-group">
						    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" placeholder="Password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
						  </div>
						  <div class="form-check clearfix">
						  	<div class="checkbox-sec float-sm-left disabled">
						  		<input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
						    	<label class="form-check-label" for="exampleCheck1">Keep me signed in</label>
						  	</div>
						  	<div class="forgot-sec float-sm-right">
						  		<a href="{{ route('forget.password.get') }}" class="forgot-text link-text">{{ __('Forgot Password ?') }}</a>
						  	</div>
						  </div>
						  <div class="btn-sec d-block clearfix" id="enter-login-btn">
						  	<button type="submit" class="btn btn-primary d-inline blue-bg-btn" id="enter-btn">{{ __('Login') }}</button>
							<a href="#" class="btn btn-outline-primary blue-btn d-inline">Cancel</a>
						  </div>
                          <!-- <div class="btn-sec d-block clearfix" id="main-login-btn">
                            <button type="submit" name="adminlogin" class="btn btn-primary d-inline blue-bg-btn loginType" data-loginType="admin">{{ __('Admin Login') }}</button>
                            <button type="submit" name="teacherlogin" class="btn btn-primary d-inline blue-bg-btn loginType" data-loginType="teacher">{{ __('Teacher Login') }}</button>
                            <button type="submit" name="studentlogin" class="btn btn-primary d-inline blue-bg-btn loginType" data-loginType="student">{{ __('Student Login') }}</button>
						  </div> -->
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
