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
								<h2 class="mb-4 main-title">{{__('languages.ai_calculated_difficulty.add_new_ai_calculated_difficulty')}}</h2>
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
							<form class="user-form" method="post" id="addaiCalculatedForm"  action="{{ route('ai-calculated-difficulty.store') }}">
							@csrf()
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.ai_calculated_difficulty.difficulty_level') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="difficultyLevel" id="difficultyLevel">
                                                <option value=''>{{ __('languages.ai_calculated_difficulty.select_difficulty_level') }}</option>
                                                @if(!empty($difficultyLevels))
                                                    @foreach($difficultyLevels as $difficultyLevel)
                                                    <option value="{{$difficultyLevel['id']}}" {{ request()->get('difficulty_level') == $difficultyLevel['id'] ? 'selected' : '' }}>{{ $difficultyLevel['name']}}</option>
                                                    @endforeach
                                                @endif
                                                
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('difficultyLevel'))<span class="validation_error">{{ $errors->first('difficultyLevel') }}</span>@endif
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.ai_calculated_difficulty.difficulty_value') }}</label>
                                        <input type="text" class="form-control" name="difficult_value" id="difficult_value" placeholder="Difficulty Value" value="{{old('Difficulty Value')}}">
                                        @if($errors->has('difficult_value'))<span class="validation_error">{{ $errors->first('difficult_value') }}</span>@endif
                                    </div>
                                </div>
                                
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                    <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="active" selected>{{__('languages.active')}}</option>
                                                <option value="inactive">{{__('languages.inactive')}}</option>
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