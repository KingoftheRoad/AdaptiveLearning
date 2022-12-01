@extends('backend.layouts.app')
    @section('content')
	<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.change_password')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
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
                            <form class="change-password" method="post" id="change-password" action="{{ route('change-password') }}">
                                @csrf()
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.current_password') }}</label>
                                        <input type="password" class="form-control" name="current_password" id="current_password" placeholder="{{__('languages.current_password')}}" value="{{old('current_password')}}">
                                        @if($errors->has('current_password'))<span class="validation_error">{{ $errors->first('current_password') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.new_password') }}</label>
                                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="{{__('languages.new_password')}}" value="{{old('new_password')}}">
                                        @if($errors->has('new_password'))<span class="validation_error">{{ $errors->first('new_password') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.confirm_password') }}</label>
                                        <input type="password" class="form-control" name="new_confirm_password" id="new_confirm_password" placeholder="{{__('languages.confirm_password')}}" value="{{old('new_confirm_password')}}">
                                        @if($errors->has('new_confirm_password'))<span class="validation_error">{{ $errors->first('new_confirm_password') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.user_management.submit') }}</button>
                                        </div>
                                    </div>
							    </div>
							</form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')  
        <script>
            var isUserPanal = 1 ;   
            @if(old('grade_id'))
                var isUserPanalEdit = 1;
                var oldUserData = JSON.parse('{!! json_encode(Session()->getOldInput()) !!}');
                console.log(oldUserData);
            @endif      
        </script>
@endsection