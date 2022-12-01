@extends('backend.layouts.app')
    @section('content')
    <style>
        ul, li{
            margin:0;
            padding:0;
            list-style:none;
        }
        label{
            color:#000;
            font-size:16px;
        }
        .ms-selectall{
            color: #6767e7 !important;
            font-size: 16px !important;
            font-weight: 500;
        }
        </style>
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
								<h2 class="mb-4 main-title">{{__('languages.test.add_test_detail')}}</h2>
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
                        <form class="exam-form" method="post" id="addExamForm"  action="{{ route('exams.store') }}">
                            @csrf()
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test.test_type') }}</label>
                                    <select name="exam_type" class="form-control select-option" id="exam_type">
                                        <option value="">{{__('languages.test.select_test_type')}}</option>
                                        @if(!empty($examTypes))
                                            @foreach($examTypes as $examType)
                                                <option value="{{$examType['id']}}" @if(old('exam_type') == $examType['id']) selected @endif> {{$examType['name']}} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('exam_type'))<span class="validation_error">{{ $errors->first('exam_type') }}</span>@endif
                                </div>
                                <!-- <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test.templates') }}</label>
                                    <select name="template" class="form-control select-option" id="select_template">
                                        <option value="">{{__('languages.test.select_template')}}</option>
                                        @if(!empty($testTemplates))
                                            @foreach($testTemplates as $testTemplate)
                                                <option value="{{$testTemplate->id}}">{{ $testTemplate->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('template'))<span class="validation_error">{{ $errors->first('template') }}</span>@endif
                                </div> -->
                            </div>
                            <div class="form-row select-data">
                                <div class="form-group col-md-6">
                                    <label for="users-list-role">{{ __('languages.test.title') }}</label>
                                    <fieldset class="form-group">
                                        <input type="text" name="title" id="title" class="form-control" placeholder = "{{__('languages.test.title')}}"/>
                                        @if($errors->has('title'))<span class="validation_error">{{ $errors->first('title') }}</span>@endif
                                    </fieldset>
                                </div>
                                <div class="form-group col-md-4 mb-50">
                                    <label class="text-bold-600">{{ __('languages.test.time_duration') }} {{ __(('(HH:MM:SS)')) }} </label>
                                    <input type="text" class="form-control mask time" name="time_duration" id="test_time_duration" placeholder="{{__('languages.test.time_duration')}} HH:MM:SS" value="{{old('time_duration')}}">
                                    @if($errors->has('time_duration'))<span class="validation_error">{{ $errors->first('time_duration') }}</span>@endif
                                </div>
                                <div class="form-group col-md-2 add-text-checkbox mb-50">
                                    <input type="checkbox" class="form-control" name="unlimited_time" id="unlimited_time" value="1"/>
                                    <label class="text-bold-600" for="exampleInputUsername2">{{ __('languages.test.unlimited_duration') }}</label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test.from_date') }}</label>
                                    <div class="input-group date">
                                    <input type="text" class="form-control date-picker" name="from_date" value="{{old('from_date')}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="from-date-error"></span>
                                    @if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test.to_date') }}</label>
                                    <div class="input-group date">
                                    <input type="text" class="form-control date-picker" name="to_date" value="{{old('to_date')}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="to-date-error"></span>
                                    @if($errors->has('to_date'))<span class="validation_error">{{ $errors->first('to_date') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test.result_date') }}</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control date-picker" name="result_date" value="{{old('result_date')}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="error-result-date"></span>
                                    @if($errors->has('result_date'))<span class="validation_error">{{ $errors->first('result_date') }}</span>@endif
                                </div>
                                @if(Auth::user()->role_id == 1)
                                <div class="form-group col-md-6 mb-50">
                                    <label for="multi_select_option">{{ __('languages.test.school') }}</label>
                                    <select name="school[]" class="form-control select-option" id="school-select-option" multiple >
                                        @foreach($SchoolList as $school)
                                            <option value="{{$school->id}}" @if(old('school') == $school->id) selected @endif>
                                                @if(app()->getLocale() == 'en')
                                                    {{$school->DecryptSchoolNameEn}}
                                                @else
                                                    {{$school->DecryptSchoolNameCh}}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="school-error"></span>
                                    @if($errors->has('school'))<span class="validation_error">{{ $errors->first('school') }}</span>@endif
                                </div>
                                @endif
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.test.description') }}</label>
                                    <textarea class="form-control" name="description" id="description" placeholder="{{__('languages.enter_the_description')}}" value="" rows=5>{{old('description')}}</textarea>
                                    @if($errors->has('description'))<span class="validation_error">{{ $errors->first('description') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group mb-50 btn-sec">
                                        <button class="blue-btn btn btn-primary">{{ __('languages.test.submit') }}</button>
                                        <a href="javascript:void(0);" class="btn btn-danger" id="backButton">{{__('languages.test.back')}}</a>
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
        <!-- Modal -->
        <div class="modal fade template-modal" id="testTemplateModal" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('languages.test_template_management.template_question_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body view-template-question"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.close')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        <script>
            $("#school-select-option").on('change', function () {
                if(this.value == "all"){
                    $('#school-select-option option').prop('selected', true); 
                }
            });
        </script>
        @include('backend.layouts.footer')
@endsection

