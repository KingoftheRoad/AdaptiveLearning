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
								<h2 class="mb-4 main-title">{{__('languages.learning_objectives_management.add_learning_objectives')}}</h2>
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
                        
							<form class="user-form" method="post" id="addLearningObjectiveForm"  action="{{ route('learning-objective.store') }}">
							@csrf()
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.learning_objectives_management.foci_number') }}</label>
                                        <input type="text" class="form-control" name="foci_number" id="foci_number" placeholder="{{__('languages.learning_objectives_management.foci_number')}}" value="{{old('foci_number')}}">
                                        @if($errors->has('foci_number'))<span class="validation_error">{{ $errors->first('foci_number') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.learning_objectives_management.learning_unit') }}</label>
                                        <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="learning_unit_id" id="LearningUnits">
                                            <option value ="">{{__('languages.learning_objectives_management.select_learning_unit')}}</option>   
                                            @if(!empty($learningUnitsList))
                                                @foreach($learningUnitsList as $learningUnit)
                                                    <option value="{{$learningUnit->id}}">{{$learningUnit->name_en}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('learning_unit_id'))<span class="validation_error">{{ $errors->first('learning_unit_id') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_activity.english_name') }}</label>
                                        <input type="text" class="form-control" name="title_en" id="learning_objective_name_en" placeholder="{{__('languages.user_activity.english_name')}}" value="{{old('strand_name_en')}}">
                                        @if($errors->has('title_en'))<span class="validation_error">{{ $errors->first('title_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_activity.chinese_name') }}</label>
                                        <input type="text" class="form-control" name="title_ch" id="learning_objective_name_ch" placeholder="{{ __('languages.user_activity.chinese_name') }}" value="{{old('strand_name_ch')}}">
                                        @if($errors->has('title_ch'))<span class="validation_error">{{ $errors->first('title_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.code') }}</label>
                                        <input type="text" class="form-control" name="code" id="learning_objective_code" placeholder="{{ __('languages.code') }}" value="{{old('strand_code')}}">
                                        @if($errors->has('code'))<span class="validation_error">{{ $errors->first('code') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.having_question') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="is_available_questions" id="is_available_questions">
                                                <option value="yes" selected>{{__('languages.yes')}}</option>
                                                <option value="no">{{__('languages.no')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-is_available_questions"></span>
                                        @if($errors->has('is_available_questions'))<span class="validation_error">{{ $errors->first('is_available_questions') }}</span>@endif
                                    </div>
                                </div>
                               
                                <div class="form-row select-data">
                                    
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <option value="1" selected>{{__('languages.active')}}</option>
                                                <option value="0">{{__('languages.inactive')}}</option>
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