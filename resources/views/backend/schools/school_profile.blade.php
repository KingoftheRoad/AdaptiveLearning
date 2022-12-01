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
                            <h2 class="mb-4 main-title">{{__('languages.profile.school_profile')}}</h2>

                            {{-- <h2 class="mb-4 main-title">{{__('languages.principal_profile')}}</h2> --}}
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
                        <form class="user-form" method="post" id="addSchoolsForm"  action="{{ route('schoolprofileupdate') }}" enctype="multipart/form-data">
                            @csrf()
                            <div class="form-row select-data">
                                <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.school_name') }}</label>
                                    <input type="text" class="form-control" name="school_name_en"  id="school_name_en" placeholder="{{ __('languages.profile.school_name_english') }}" value="{{($SchoolData->school_name_en) ? App\Helpers\Helper::decrypt($SchoolData->school_name_en) : $SchoolData->school_name}}" required="">
                                    @if($errors->has('school_name_en'))<span class="validation_error">{{ $errors->first('school_name_en') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.school_name') }}</label>
                                    <input type="text" class="form-control" name="school_name_ch"  id="school_name_ch" placeholder="{{ __('languages.profile.school_name_chinese') }}" value="{{($SchoolData->school_name_ch) ? App\Helpers\Helper::decrypt($SchoolData->school_name_ch) : $SchoolData->school_name}}" required="">
                                    @if($errors->has('school_name_ch'))<span class="validation_error">{{ $errors->first('school_name_ch') }}</span>@endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.school_code') }}</label>
                                    <input type="text" class="form-control" name="school_code"  id="school_code" placeholder="{{ __('languages.profile.school_code') }}" value="{{$SchoolData->school_code}}" required="">
                                    @if($errors->has('school_code'))<span class="validation_error">{{ $errors->first('school_code') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.email') }}</label>
                                    <input type="email" class="form-control" id="email" readonly="" name="email" placeholder="{{ __('languages.email') }}" value="{{$Schoolemail['email']}}">
                                    @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <div class="form-group col-md-6">
                                    <label>{{ __('languages.profile.school_year_start_date') }}</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control date-picker-year"name="starttime" id="starttime" placeholder="{{ __('languages.select_date') }}" value="{{ ($SchoolData->school_start_time!='0000-00-00' ? date('d/m/Y', strtotime($SchoolData->school_start_time)) : '') }}" autocomplete="off">
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="to-date-error"></span>
                                    @if($errors->has('to_date'))<span class="validation_error">{{ $errors->first('to_date') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.city') }}</label>
                                    <input type="text" class="form-control" name="city" id="city" placeholder="{{ __('languages.enter_the_city') }}" value="{{App\Helpers\Helper::decrypt($SchoolData->city)}}">
                                    @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                </div>
                               
                            </div>
                            <?php
                            if(isset($UserData->profile_photo)){
                                $previewProfileImagePath = asset($UserData->profile_photo);
                            }else{
                                $previewProfileImagePath = asset('uploads/settings/image_not_found.gif');
                            }?>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.profile_photo') }}</label>
                                    <input type="file" class="form-control" name="profile_photo" id="profile_photo">
                                    <br>
                                    <img id="preview-profile-image" src="{{ $previewProfileImagePath }}" alt="preview image" style="max-height: 250px;">
                                    @if($errors->has('profile_photo'))<span class="validation_error">{{ $errors->first('profile_photo') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.address') }}</label>
                                    <textarea class="form-control" name="address" id="address" placeholder="{{ __('languages.enter_the_address') }}" value="" rows=5>{{App\Helpers\Helper::decrypt($SchoolData->school_address)}}</textarea>
                                    @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
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