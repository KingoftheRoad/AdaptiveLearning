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
								<h2 class="mb-4 main-title">{{ __('Edit User') }}</h2>
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
                        <form class="user-form" method="post" id="editUsersForm"  action="{{ route('users.update',$user->id) }}">
							@csrf()
                            @method('patch')
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('Role') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="role" id="role">
                                            <option value=''>{{ __('Select Role') }}</option>
                                            @if(!empty($Roles))
                                                @foreach($Roles as $role)
                                                <option value="{{$role->id}}" {{$role->id === $user->role_id ? 'selected' : ''}}>{{$role->role_name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('No available roles') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('role'))<span class="validation_error">{{ $errors->first('role') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('Grade') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="grade_id" id="grade_id">
                                            <option value=''>{{ __('Select Grade') }}</option>
                                            @if(!empty($Grades))
                                                @foreach($Grades as $grade)
                                                <option value='{{$grade->id}}' {{$grade->id === $user->grade_id ? 'selected' : ''}}>{{$grade->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('No available grade') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('class'))<span class="validation_error">{{ $errors->first('class') }}</span>@endif
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('Section') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="section" id="section">
                                            <option value=''>{{ __('Select Section') }}</option>
                                            @if(!empty($Sections))
                                                @foreach($Sections as $section)
                                                <option value='{{$section->id}}' {{$section->id === $user->section_id ? 'selected' : ''}}>{{$section->section_name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('No available section') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('section'))<span class="validation_error">{{ $errors->first('section') }}</span>@endif
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('Name') }}</label>
                                        <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name" value="{{$user->name}}">
                                        @if($errors->has('user_name'))<span class="validation_error">{{ $errors->first('user_name') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{$user->email}}">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                    </div>
                                     <div class="form-group col-md-6 mb-50">
                                         <label class="text-bold-600" for="exampleInputUsername1">{{ __('Mobile Number') }}</label>
                                        <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter the number" value="{{$user->mobile_no}}">
                                        @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
				                        <label for="id_end_time">{{ __('Date of Birth') }}</label>
				                        <div class="input-group date" id="id_4">
                                        <input type="text" class="form-control date-picker" name="date_of_birth" value="{{ date('Y/m/d', strtotime($user->dob)) }}" >
                                            @if($errors->has('date_of_birth'))<span class="validation_error">{{ $errors->first('date_of_birth') }}</span>@endif
				                            <div class="input-group-addon input-group-append">
				                                <div class="input-group-text">
				                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
				                                </div>
				                            </div>
				                        </div>
                                        <span id="error-dateof-birth"></span>
				                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('Gender') }}</label>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mt-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="gender" id="male" @if($user->gender == 'male') checked @endif value="male">
                                                        @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                        <label class="custom-control-label" for="male">{{ __('Male') }}</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block my-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="gender" id="female" value="female" @if($user->gender == 'female') checked @endif>
                                                        @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                        <label class="custom-control-label" for="female">{{ __('Female') }}</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block my-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="gender" id="other" value="other" @if($user->gender == 'other') checked @endif>
                                                        @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                        <label class="custom-control-label" for="other">{{ __('Other') }}</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                        </ul>
                                     </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                         <label class="text-bold-600" for="exampleInputUsername1">{{ __('City') }}</label>
                                        <input type="text" class="form-control" name="city" id="city" placeholder="Enter the city.." value="{{$user->city}}">
                                        @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('Address') }}</label>
                                        <textarea class="form-control" name="address" id="address" placeholder="Enter the addres.." value="" rows=5>{{$user->address}}</textarea>
                                        @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('Submit') }}</button>
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
@endsection