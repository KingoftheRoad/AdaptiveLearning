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
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="school" id="school_id" disabled>
                                            <option value=''>{{ __('languages.select_school') }}</option>
                                            @if(!empty($Schools))
                                                @foreach($Schools as $school)
                                                <option value="{{$school->id}}" @if(old('school') == $school->id) selected @endif>{{$school->DecryptSchoolNameEn}}</option>
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
                                         <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_management.city') }}</label>
                                        <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.user_management.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($user->city)}}">
                                        @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
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