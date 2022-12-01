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
								<h2 class="mb-4 main-title">{{ __('languages.teacher_management.update_teacher') }}</h2>
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
                        <form class="teacher-form" method="post" id="editTeacherForm"  action="{{ route('teacher.update',$user->id) }}">
							@csrf()
                            @method('patch')

                            <input type="hidden" name="role" id="role" value="2">
                            <input type="hidden" name="grade_id" id="grade_id" value="0">
                            <input type="hidden" name="school" id="school" value="{{auth()->user()->school_id}}">
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_en">{{ __('languages.user_management.name_english') }}</label>
                                    <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.name_english')}}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                    @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_ch">{{ __('languages.user_management.name_chinese') }}</label>
                                    <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{__('languages.name_chinese')}}" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                    @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                    
                                <!-- <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="user_name">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name" value="{{$user->name}}">
                                    @if($errors->has('user_name'))<span class="validation_error">{{ $errors->first('user_name') }}</span>@endif
                                </div> -->
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="email">{{ __('languages.user_management.email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="{{__('languages.email')}}" value="{{$user->email}}">
                                    @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="mobile_no">{{ __('languages.user_management.mobile_number') }}</label>
                                   <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.user_management.enter_the_number')}}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxlength="8">
                                   @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                               </div>
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.user_management.date_of_birth') }}</label>
                                    <div class="input-group date" id="id_4">
                                    <input type="text" class="form-control birthdate-date-picker" name="date_of_birth" placeholder="{{__('languages.select_date')}}" value="{{ date('d/m/Y', strtotime($user->dob)) }}" >
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
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.gender') }}</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mt-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="male" value="male" @if($user->gender == 'male') checked @endif >
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="male">{{ __('languages.user_management.male') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="female" value="female" @if($user->gender == 'female') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="female">{{ __('languages.user_management.female') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="other" value="other" @if($user->gender == 'other') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="other">{{ __('languages.user_management.other') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                    <span class="gender-select-err"></span>
                                 </div>
                                 
                               <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.address') }}</label>
                                    <textarea class="form-control" name="address" id="address" placeholder="{{__('languages.enter_the_address')}}" value="" rows=5>{{App\Helpers\Helper::decrypt($user->address)}}</textarea>
                                    @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.city') }}</label>
                                   <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($user->city)}}">
                                   @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                               </div>
                            </div>
                        
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.status') }}</label>
                                    <select name="status" class="form-control select-option" id="status">
                                        <option value="pending" {{ $user->status === "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
                                        <option value="active" {{ $user->status === "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
                                        <option value="inactive" {{ $user->status === "inactive" ? 'selected' : '' }}>{{__("languages.inActive")}}</option>
                                    </select>
                                </div>
                                
                            </div>
                        
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
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