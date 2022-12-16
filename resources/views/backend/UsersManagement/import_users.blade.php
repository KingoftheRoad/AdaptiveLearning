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
								<h2 class="mb-4">{{__('languages.user_management.import_users')}}</h2>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
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
                        <form class="user-form" method="post" id="importUsers"  action="{{ route('users.import') }}" enctype="multipart/form-data">
                        @csrf()
                        <div class="form-row select-data">
                            <div class="form-group col-md-4">
                                <label for="users-list-role">{{__('languages.user_management.user_role')}}</label>
                                <fieldset class="form-group">
                                    <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="role" id="role">
                                        <option value=''>{{ __('languages.select_role') }}</option>
                                        @if(!empty($Roles))
                                        @foreach($Roles as $role)
                                        <option value="{{$role->id}}" @if(old('role') == $role->id) selected @endif>{{$role->role_name}}</option>
                                        @endforeach
                                        @else
                                        <option value="">{{ __('languages.no_available_roles') }}</option>
                                        @endif
                                    </select>
                                    @if($errors->has('role'))<span class="validation_error">{{ $errors->first('role') }}</span>@endif
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="users-list-role">{{ __('languages.upload_csv_file') }}</label>
                                <fieldset class="form-group">
                                    <input type="file" name="user_file" value="">
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="form-group col-md-6 mb-50 btn-sec">
                                    <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                </div>
                            </div>
                        </div>
                        </form>
                        <hr class="blue-line">
                        <div class="form-row p-3">
                            <div id="csv-instructions">
                                <h2 class="wv-heading--subtitle">{{__('languages.student_csv_template_file')}}</h2>
                                <p class="wv-text--body imp-info">
                                    <a class="wv-text--link" href="{{asset('Schools-Import.csv')}}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                        {{ __('languages.download_and_view_student_csv')}}
                                    </a>
                                    {{ __('languages.sample_student_template')}}
                                </p>            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.layouts.footer')  
@endsection