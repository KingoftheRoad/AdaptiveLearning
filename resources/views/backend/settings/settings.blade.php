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
								<h2 class="mb-4 main-title">{{__('languages.settings.configure_settings')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
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
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
                            <form class="settings-form" method="post" id="saveSettings"  action="{{ route('settings') }}" enctype='multipart/form-data'>
                                @csrf()
                                <strong>{{__('languages.settings.basic_settings')}}</strong>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="site_name">{{ __('languages.settings.site_name') }}</label>
                                        <input type="text" class="form-control" name="site_name" id="site_name" placeholder="{{__('languages.settings.site_name')}}" value="{{$settingsData->site_name ?? ''}}">
                                        @if($errors->has('site_name'))<span class="validation_error">{{ $errors->first('site_name') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="site_url">{{ __('languages.settings.site_url') }}</label>
                                        <input type="text" class="form-control" name="site_url" id="site_url" placeholder="{{__('languages.settings.site_url')}}" value="{{$settingsData->site_url ?? ''}}">
                                        @if($errors->has('site_url'))<span class="validation_error">{{ $errors->first('site_url') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="email">{{ __('languages.settings.site_email') }}</label>
                                        <input type="text" class="form-control" name="email" id="email" placeholder="{{__('languages.settings.site_email')}}" value="{{$settingsData->email ?? ''}}">
                                        @if($errors->has('email'))<span class="validation_error">{{ $errors->first('site_name') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="contact_number">{{ __('languages.settings.site_contact_number') }}</label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="{{ __('languages.settings.site_contact_number') }}" value="{{$settingsData->contact_number ?? ''}}">
                                        @if($errors->has('contact_number'))<span class="validation_error">{{ $errors->first('contact_number') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="logo_image">{{ __('languages.settings.site_logo') }}</label>
                                        <input type="file" class="form-control" name="logo_image" id="logo_image" placeholder="{{ __('languages.settings.site_logo') }}" value="{{$settingsData->logo_image ?? ''}}">
                                        @if($errors->has('logo_image'))<span class="validation_error">{{ $errors->first('logo_image') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="fav_icon">{{ __('languages.settings.site_fevicon_icon') }}</label>
                                        <input type="file" class="form-control" name="fav_icon" id="fav_icon" placeholder="{{ __('languages.settings.site_fevicon_icon') }}" value="{{$settingsData->fav_icon ?? ''}}">
                                        @if($errors->has('fav_icon'))<span class="validation_error">{{ $errors->first('fav_icon') }}</span>@endif
                                    </div>
                                </div>
                                <?php
                                if(isset($settingsData->logo_image)){
                                    $previewLogoImagePath = asset($settingsData->logo_image);
                                }else{
                                    $previewLogoImagePath = asset('uploads/settings/image_not_found.gif');
                                } 
                                if(isset($settingsData->fav_icon)){
                                    $previewFavIconPath = asset($settingsData->fav_icon);
                                }else{
                                    $previewFavIconPath = asset('uploads/settings/image_not_found.gif');
                                }
                                ?>
                                <div class="form-row select-data">
                                    <div class="col-md-6 mb-2">
                                        <img id="preview-logo-imawge" src="{{$previewLogoImagePath}}" alt="preview image" style="max-height: 250px;">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <img id="preview-fav-icon" src="{{$previewFavIconPath}}" alt="preview image" style="max-height: 250px;">
                                    </div>
                                </div>
                                <hr>
                                <strong>{{__('languages.settings.smtp_configuration')}}</strong>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_driver') }}</label>
                                        <input type="text" class="form-control" name="smtp_driver" id="smtp_driver" placeholder="{{ __('languages.settings.smtp_driver') }}" value="{{$settingsData->smtp_driver ?? '' }}">
                                        @if($errors->has('smtp_driver'))<span class="validation_error">{{ $errors->first('smtp_driver') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_host') }}</label>
                                        <input type="text" class="form-control" name="smtp_host" id="smtp_host" placeholder="{{ __('languages.settings.smtp_host') }}" value="{{$settingsData->smtp_host ?? '' }}">
                                        @if($errors->has('smtp_host'))<span class="validation_error">{{ $errors->first('smtp_host') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_port') }}</label>
                                        <input type="text" class="form-control" name="smtp_port" id="smtp_port" placeholder="{{ __('languages.settings.smtp_port') }}" value="{{$settingsData->smtp_port ?? '' }}">
                                        @if($errors->has('smtp_port'))<span class="validation_error">{{ $errors->first('smtp_port') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_username') }}</label>
                                        <input type="text" class="form-control" name="smtp_username" id="smtp_username" placeholder="{{ __('languages.settings.smtp_username') }}" value="{{$settingsData->smtp_username ?? '' }}">
                                        @if($errors->has('smtp_username'))<span class="validation_error">{{ $errors->first('smtp_username') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_email') }}</label>
                                        <input type="text" class="form-control" name="smtp_email" id="smtp_email" placeholder="{{ __('languages.settings.smtp_email') }}" value="{{$settingsData->smtp_email ?? '' }}">
                                        @if($errors->has('smtp_email'))<span class="validation_error">{{ $errors->first('smtp_email') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_password') }}</label>
                                        <input type="password" class="form-control" name="smtp_passowrd" id="smtp_passowrd" placeholder="{{ __('languages.settings.smtp_password') }}" value="{{$settingsData->smtp_passowrd ?? '' }}" autocomplete="off">
                                        @if($errors->has('smtp_passowrd'))<span class="validation_error">{{ $errors->first('smtp_passowrd') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.settings.smtp_encryption') }}</label>
                                        <input type="text" class="form-control" name="smtp_encryption" id="smtp_encryption" placeholder="{{ __('languages.settings.smtp_encryption') }}" value="{{$settingsData->smtp_encryption ?? '' }}">
                                        @if($errors->has('smtp_encryption'))<span class="validation_error">{{ $errors->first('smtp_encryption') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                        @if (in_array('setting_create', $permissions))
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