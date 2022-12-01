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
								<h2 class="mb-4 main-title">{{__('languages.school_management.update_school')}}</h2>
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
							<form class="user-form" method="post" id="updateSchoolsForm"  action="{{ route('schoolmanagement.update',$SchoolData->id) }}">
							@csrf()
                            @method('patch')
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.school_name_en') }}</label>
                                        <input type="text" class="form-control" name="school_name_en" id="school_name_en" placeholder="{{__('languages.school_management.school_name_en')}}" value="{{App\Helpers\Helper::decrypt($SchoolData->school_name_en)}}">
                                        @if($errors->has('school_name_en'))<span class="validation_error">{{ $errors->first('school_name_en') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.school_name_ch') }}</label>
                                        <input type="text" class="form-control" name="school_name_ch" id="school_name_ch" placeholder="{{__('languages.school_management.school_name_ch')}}" value="{{App\Helpers\Helper::decrypt($SchoolData->school_name_ch)}}">
                                        @if($errors->has('school_name_ch'))<span class="validation_error">{{ $errors->first('school_name_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.school_code') }}</label>
                                        <input type="text" class="form-control" name="school_code" id="school_code" placeholder="{{__('languages.school_management.school_code')}}" value="{{$SchoolData->school_code}}">
                                        @if($errors->has('school_code'))<span class="validation_error">{{ $errors->first('school_code') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.school_management.city') }}</label>
                                        <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($SchoolData->city)}}">
                                        @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.address_en') }}</label>
                                        <textarea class="form-control" name="address_en" id="address_en" placeholder="{{__('languages.school_management.enter_the_address_en')}}" value="" rows=5>{{($SchoolData->school_address_en) ? App\Helpers\Helper::decrypt($SchoolData->school_address_en) : ''}}</textarea>
                                        @if($errors->has('address_en'))<span class="validation_error">{{ $errors->first('address_en') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.address_ch') }}</label>
                                        <textarea class="form-control" name="address_ch" id="address_ch" placeholder="{{__('languages.school_management.enter_the_address_ch')}}" value="" rows=5>{{($SchoolData->school_address_ch) ? App\Helpers\Helper::decrypt($SchoolData->school_address_ch) : ''}}</textarea>
                                        @if($errors->has('address_ch'))<span class="validation_error">{{ $errors->first('address_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.school_management.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="{{__('languages.email')}}" value="{{$Schoolemail['email']}}">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="active" {{($SchoolData->status === "active") ? 'selected' : ''}}>Active</option>
                                                <option value="inactive" {{($SchoolData->status === "inactive") ? 'selected' : ''}}>Inactive</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                    </div>
                                </div>

                                <div class="form-row select-data add-sub-admin-row">
                                    <input type="checkbox" id="addAdmins" name="addAdmins" value="Add Admin" @if(isset($UserOtherData) && !empty($UserOtherData) && sizeof($UserOtherData)>=2) checked="checked" @endif>
                                    <label for="addAdmins">{{__('languages.school_management.add_more_admins')}}</label>
                                </div>
                                <div class="sub-admin-portion"  @if(isset($UserOtherData) && !empty($UserOtherData) && sizeof($UserOtherData)>=2) style="display: block;" @endif>
                                    <fieldset>
                                        <legend class="sub-admin">{{__('languages.school_management.sub_admins')}}</legend> 
                                        <div class="form-row select-data main-more-admin">
                                            <div id="more-admin">
                                                @for ($u = 1; $u < sizeof($UserOtherData); $u++)
                                                    <div class="add-more-admin row">
                                                        <input type="hidden" name="u_id[]" value="{{ $UserOtherData[$u]['id'] }}">
                                                        <div class="form-group col-md-3">
                                                            <label class="text-bold-600">{{__('languages.school_management.english_name')}}</label>
                                                            <input type="text" class="form-control subAdminName" name="subAdminName[]" placeholder="{{__('languages.school_management.enter_english_name')}}" value="{{ ($UserOtherData[$u]['name_ch']) ? App\Helpers\Helper::decrypt($UserOtherData[$u]['name_en']) : ''}}">
                                                            <span class="error-msg subadminname_err"></span>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label class="text-bold-600">{{__('languages.school_management.chinese_name')}}</label>
                                                            <input type="text" class="form-control subAdminNameCh" name="subAdminNameCh[]" placeholder="{{__('languages.school_management.enter_chinese_name')}}" value="{{ ($UserOtherData[$u]['name_ch']) ? App\Helpers\Helper::decrypt($UserOtherData[$u]['name_ch']) : '' }}">
                                                            <span class="error-msg subadminnamech_err"></span>
                                                        </div>
                                                        <div class="form-group col-md-3"> 
                                                            <label class="text-bold-600">{{__('languages.profile.email')}}</label>
                                                            <input type="text" class="form-control subAdminEmail" name="subAdminEmail[]" placeholder="{{__('languages.school_management.enter_email')}}"  value="{{ $UserOtherData[$u]['email'] }}">
                                                            <span class="error-msg subadminemail_err"></span>
                                                        </div> 
                                                        <div class="form-group col-md-2"> 
                                                            <label class="text-bold-600">{{__('languages.profile.password')}}</label>
                                                            <input type="password" class="form-control subAdminPassword" name="subAdminPassword[]" id="subAdminPassword" placeholder="****" value="">
                                                            <span class="error-msg subadminpassword_err"></span>
                                                        </div>
                                                        <div class="form-group col-md-1 add-admin-remove"> 
                                                            <a class="removeMoreAdmin btn btn-sm" data-id="{{ $UserOtherData[$u]['id'] }}">X</a>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                            <div class="form-group col-md-6 btn-sec">
                                                <button name="add_more_admin" id="addMoreAdmin" class="blue-btn btn btn-primary mt-4" type="button">{{__('languages.school_management.add_more_admin')}}</button>
                                            </div>
                                        </div>
                                    </fieldset>
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