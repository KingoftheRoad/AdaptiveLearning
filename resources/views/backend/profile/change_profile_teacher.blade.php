@extends('backend.layouts.app')
    @section('content')
    @php
            $permissions = [];
            $user_id = auth()->user()->id;
            if($user_id){
                $module_permission = App\Helpers\Helper::getPermissions($user_id);
                if($module_permission && !empty($module_permission)){
                    $permissions = $module_permission;
                }
            }else{
                $permissions = [];
            }
        @endphp
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
								<h2 class="mb-4 main-title">{{__('languages.profile.profile')}}</h2>
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
                       
                        <form class="user-form" method="post" id="updateTeacherProfileForm"  action="{{ route('teacher.profile.update',$user->id) }}" enctype="multipart/form-data">
							@csrf()
                            @method('patch')
                            <div class="form-row select-data">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.profile.main_role') }} : {{ucfirst($user->roles->role_name)}}</label>
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.profile.other_role') }} : {{($otherRole) ? $otherRole->roles : 'N/A'}}</label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_en">{{ __('languages.profile.english_name') }}</label>
                                    <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.name_english')}}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                    @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_ch">{{ __('languages.profile.chinese_name') }}</label>
                                    <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{__('languages.name_chinese')}}" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                    @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_Ch') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <!-- <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name" value="{{$user->name}}">
                                    @if($errors->has('user_name'))<span class="validation_error">{{ $errors->first('user_name') }}</span>@endif
                                </div> -->
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.profile.email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="{{__('languages.email')}}" value="{{$user->email}}" readonly>
                                    @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.profile.mobile_number') }}</label>
                                    <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.enter_the_mobile_number')}}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxLength="8">
                                    @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                    
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.profile.date_of_birth') }}</label>
                                    <div class="input-group date" id="id_4">
                                    <input type="text" class="form-control birthdate-date-picker" name="date_of_birth" placeholder ="{{__('languages.select_date')}}" value="{{ date('d/m/Y', strtotime($user->dob)) }}" >
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
                                    <label class="text-bold-600">{{ __('languages.profile.gender') }}</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mt-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="male" value="male" @if($user->gender == 'male') checked @endif >
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="male">{{ __('languages.profile.male') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="female" value="female" @if($user->gender == 'female') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="female">{{ __('languages.profile.female') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="other" value="other" @if($user->gender == 'other') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="other">{{ __('languages.profile.other') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                    <span class="gender-select-err"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.profile.city') }}</label>
                                    <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($user->city)}}">
                                    @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.profile.profile_photo') }}</label>
                                    <input type="file" class="form-control" name="teacher_profile_photo" id="profile_photo"  >
                                    @if($errors->has('profile_photo'))<span class="validation_error">{{ $errors->first('profile_photo') }}</span>@endif
                                </div>
                            </div>
                            <?php
                                if(isset($user->profile_photo)){
                                    $previewProfileImagePath = asset($user->profile_photo);
                                }else{
                                    $previewProfileImagePath = asset('uploads/settings/image_not_found.gif');
                                } 
                            ?>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600">{{ __('languages.profile.address') }}</label>
                                    <textarea class="form-control" name="address" id="address" placeholder="{{__('languages.enter_the_address')}}" value="" rows=5>{{App\Helpers\Helper::decrypt($user->address)}}</textarea>
                                    @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <img id="preview-profile-image" src="{{ $previewProfileImagePath }}" alt="preview image" style="max-height: 250px;">
                                        @if($errors->has('profile_picture'))<span class="validation_error">{{ $errors->first('profile_picture') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group col-md-6 mb-50 btn-sec">
                                    @if (in_array('profile_management_create', $permissions))
                                        <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                    @endif
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