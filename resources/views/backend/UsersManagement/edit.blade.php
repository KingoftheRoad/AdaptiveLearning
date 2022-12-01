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
								<h2 class="mb-4 main-title">{{ __('languages.user_management.update_user') }}</h2>
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
                        <form class="user-form" method="post" id="editUsersForm"  action="{{ route('users.update',$user->id) }}">
							@csrf()
                            @method('patch')
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.user_management.role') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="role" id="role">
                                                <option value=''>{{ __('languages.select_role') }}</option>
                                                @if(!empty($Roles))
                                                @foreach($Roles as $role)
                                                @if ($role->id != '1')
                                                <option value="{{$role->id}}" {{$role->id === $user->role_id ? 'selected' : ''}}>{{$role->role_name}}</option>
                                                @endif
                                                @endforeach
                                                @else
                                                <option value="">{{ __('languages.no_available_roles') }}</option>
                                                @endif
                                            </select>
                                            <span id="error-role"></span>
                                            @if($errors->has('role'))<span class="validation_error">{{ $errors->first('role') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6 school">
                                        <label for="users-list-role">{{ __('languages.user_management.school') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="school" id="school_id">
                                                <option value=''>{{ __('languages.select_school') }}</option>
                                                @if(!empty($Schools))
                                                @foreach($Schools as $school)
                                                <option value="{{$school->id}}" {{$school->id === $user->school_id ? 'selected' : ''}}>{{$school->DecryptSchoolNameEn}}</option>
                                                @endforeach
                                                @else
                                                <option value="">{{ __('languages.no_available_school') }}</option>
                                                @endif
                                            </select>
                                            <span id="error-school"></span>
                                            @if($errors->has('school'))<span class="validation_error">{{ $errors->first('school') }}</span>@endif
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="form-row select-data grade">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.user_management.grade') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="grade_id" id="grade_id">
                                            
                                            </select>
                                            <span id="error-grade"></span>
                                            @if($errors->has('grade_id'))<span class="validation_error">{{ $errors->first('grade_id') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6 student">
                                        <label for="users-list-role">{{ __('languages.user_management.student') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control multiplestudent_ids" data-show-subtext="true" data-live-search="true" name="student_ids[]" id="student_ids" multiple></select>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="form-row studentroll" style="display: none">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_en">{{ __('languages.user_management.student_number') }}</label>
                                        <input type="text" class="form-control" name="student_number" id="student_number" placeholder="{{ __('languages.user_management.student_number') }}" value="{{$user->student_number}}">
                                        @if($errors->has('student_number'))<span class="validation_error">{{ $errors->first('student_number') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_ch">{{ __('languages.user_management.class_class_number') }} {{__('(Ex: A+123456)')}}</label>
                                        <input type="text" class="form-control" id="class_number" name="class_number" placeholder="{{ __('languages.user_management.class_class_number') }}" value= @if($user->class_id) {{ App\Helpers\Helper::getSingleClassName($user->class_id) }}+{{$user->class_class_student_number}} @endif>
                                        @if($errors->has('class_number'))<span class="validation_error">{{ $errors->first('class_number') }}</span>@endif
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_en">{{ __('languages.user_management.name_english') }}</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('languages.user_management.enter_english_name')}}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                        @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="name_ch">{{ __('languages.user_management.name_chinese') }}</label>
                                        <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{__('languages.user_management.enter_chinese_name')}}" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                        @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <!-- <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('Name') }}</label>
                                        <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name" value="{{$user->name}}">
                                        @if($errors->has('user_name'))<span class="validation_error">{{ $errors->first('user_name') }}</span>@endif
                                    </div> -->
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('languages.user_management.email') }}" value="{{$user->email}}">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.mobile_number') }}</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.user_management.enter_the_number')}}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxLength="8">
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
                                    <div class="form-group col-md-6 mb-50 gender">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.gender') }}</label>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mt-1 mr-1 mb-1">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" name="gender" id="male" value="male" @if($user->gender == 'male') checked @endif>
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
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                         <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.city') }}</label>
                                        <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.user_management.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($user->city)}}">
                                        @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.address') }}</label>
                                        <textarea class="form-control" name="address" id="address" placeholder="{{__('languages.user_management.enter_the_address')}}" value="" rows=5>{{App\Helpers\Helper::decrypt($user->address)}}</textarea>
                                        @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    @php
                                    $existingOtherRoleIds = ($user->other_roles_id) ? explode(',',$user->other_roles_id) : [];
                                    @endphp
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="multi_select_option">{{ __('languages.user_management.other_roles') }}</label>
                                        <select name="other_role[]" class="form-control select-option" id="other-roles-select-option" multiple>
                                            @foreach($SubRoleList as $subRole)
                                            <option value="{{$subRole->id}}" @if(in_array($subRole->id,$existingOtherRoleIds)) selected @endif>{{$subRole->role_name}}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('other_role'))<span class="validation_error">{{ $errors->first('other_role') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="id_end_time">{{ __('languages.status') }}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="pending" {{ $user->status === "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
                                            <option value="active" {{ $user->status === "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
                                            <option value="inactive" {{ $user->status === "inactive" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
                                        </select>
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
            var isUserPanal = 1;
            var isUserPanalEdit = 1;
            @if(!empty($user))
                var oldUserData = JSON.parse('{!! json_encode($user) !!}');
            @endif
            var old_stu_ids='';
            @if(!empty($ParentChildMapping))
                old_stu_ids=JSON.parse('{!! json_encode($ParentChildMapping) !!}');
            @endif
        </script>
@endsection