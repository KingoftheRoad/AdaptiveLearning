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
								<h2 class="mb-4 main-title">{{__('languages.sidebar.teacher_class_assignment')}}</h2>
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
                        
							<form class="assign-form" method="post" id="addAssignForm"  action="{{ route('teacher-class-subject-assign.store') }}">
							@csrf()
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label for="teacher_id">{{ __('languages.teacher') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control teacherid" data-show-subtext="true" data-live-search="true" name="teacher_id" id="teacher_id" >
                                            <option value=''>{{ __('languages.select_teacher') }}</option>
                                            @if(!empty($teacherList))
                                                @foreach($teacherList as $itam)
                                                <option value="{{$itam->id}}" @if(old('teacher_id') == $itam->id) selected @endif>{{($itam->name_en) ? App\Helpers\Helper::decrypt($itam->name_en) : $itam->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_available_teacher') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('teacher_id'))<span class="validation_error">{{ $errors->first('teacher_id') }}</span>@endif
                                        </fieldset>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="class_id">{{ __('languages.grade') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="class_id" id="class_id">
                                            <option value=''>{{ __('languages.select_grade') }}</option>
                                            @if(!empty($gradeList))
                                                @foreach($gradeList as $itam)
                                                <option value="{{$itam->grades->id}}" @if(old('class_id') == $itam->grades->id) selected @endif>{{$itam->grades->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_grade_available') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('class_id'))<span class="validation_error">{{ $errors->first('class_id') }}</span>@endif
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="form-row select-data">
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="id_end_time">{{ __('languages.class') }}</label>
                                        <select name="class_type[]" class="form-control select-option" id="classType-select-option" multiple></select>
                                        @if($errors->has('class_type'))<span class="validation_error">{{ $errors->first('class_type') }}</span>@endif
                                    </div>
                                    <input type="hidden" name="subject_id[]" value="1">
                                    {{-- <div class="form-group col-md-6">
                                        <label for="subject_id">{{ __('languages.subject') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control multiplesubject_id" data-show-subtext="true" data-live-search="true" name="subject_id[]" id="subject_id" multiple>
                                            @if(!empty($subjectList))
                                                @foreach($subjectList as $itam)
                                                    @if($itam->subjects->name == "Mathematics")
                                                        <option value="{{$itam->subjects->id}}" selected>{{$itam->subjects->name}}</option>
                                                    @else
                                                        <option value="{{$itam->subjects->id}}"  @if(old('subject_id') == $itam->subjects->id) selected @endif>{{$itam->subjects->name}}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option value="">{{ __('languages.no_subject_available') }}</option>
                                            @endif
                                            </select>
                                            @if($errors->has('subject_id'))<span class="validation_error">{{ $errors->first('subject_id') }}</span>@endif
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="form-row select-data"> --}}
                                    <div class="form-group col-md-6 mb-50">
                                        <label for="id_end_time">{{ __('languages.status') }}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="active">{{__("languages.active")}}</option>
                                            <option value="inactive">{{__("languages.inactive")}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="subjectaddarea"></div>
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