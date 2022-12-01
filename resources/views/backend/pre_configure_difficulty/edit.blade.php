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
								<h2 class="mb-4 main-title">{{__('languages.pre_configure_difficulty.update_pre_configure_difficulty')}}</h2>
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
							<form class="user-form" method="post" id="editpreconfiguredifficultForm"  action="{{ route('pre-configure-difficulty.update',$preConfigureData->id) }}">
							@csrf()
                            @method('patch')
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.pre_configure_difficulty.difficulty_level') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="difficulty_level" id="difficultyLevel">
                                                <option value=''>{{ __('languages.pre_configure_difficulty.select_difficulty_level') }}</option>
                                                @if(!empty($difficultyLevels))
                                                    @foreach($difficultyLevels as $difficultyLevel)
                                                    <option value="{{$difficultyLevel['id']}}" {{ ($preConfigureData->difficulty_level == $difficultyLevel['id']) ?  'selected' : '' }}>{{ $difficultyLevel['name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('difficulty_level'))<span class="validation_error">{{ $errors->first('difficulty_level') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.name_english') }}</label>
                                        <input type="text" class="form-control" name="difficulty_level_name_en" id="difficulty_level_name_en" placeholder="{{ __('languages.name_english') }}" value="{{$preConfigureData->difficulty_level_name_en}}">
                                        @if($errors->has('difficulty_level_name_en'))<span class="validation_error">{{ $errors->first('difficulty_level_name_en') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.name_chinese') }}</label>
                                        <input type="text" class="form-control" name="difficulty_level_name_ch" id="difficulty_level_name_ch" placeholder="{{ __('languages.name_chinese') }}" value="{{$preConfigureData->difficulty_level_name_ch}}">
                                        @if($errors->has('difficulty_level_name_ch'))<span class="validation_error">{{ $errors->first('difficulty_level_name_ch') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.pre_configure_difficulty.difficulty_value') }}</label>
                                            <input type="text" class="form-control" name="title" id="difficult_value" placeholder="{{ __('languages.pre_configure_difficulty.difficulty_value') }}" value="{{$preConfigureData->title}}">
                                            @if($errors->has('title'))<span class="validation_error">{{ $errors->first('title') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.color') }}</label>
                                            <fieldset class="form-group">
                                                <input type="color" name="difficult_level_color" id="difficult_level_color" placeholder="{{ __('color') }}" value="{{$preConfigureData->difficulty_level_color}}">
                                            </fieldset>
                                            <span id="error-color"></span>
                                            @if($errors->has('color'))<span class="validation_error">{{ $errors->first('color') }}</span>@endif
                                    </div>
                                                                    
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="active" {{($preConfigureData->status === "active") ? 'selected' : ''}}>{{__('languages.active')}}</option>
                                                <option value="inactive" {{($preConfigureData->status === "inactive") ? 'selected' : ''}}> {{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
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