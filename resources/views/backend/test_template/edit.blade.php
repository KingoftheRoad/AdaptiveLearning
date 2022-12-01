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
								<h2 class="mb-4 main-title">{{__('languages.test_template_management.update_test_template')}}</h2>
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
                        <form method="post" id="editTestTemplate"  action="{{ route('test-template.update',$data['TestTemplatesData']->id) }}">
                            @csrf()
                            @method('patch')
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="users-list-role">{{ __('languages.test_template_management.template_name') }}</label>
                                    <fieldset class="form-group">
                                        <input type="text" name="name" id="name" class="form-control" placeholder = "{{__('languages.test_template_management.template_name')}}" value="{{ $data['TestTemplatesData']->name }}" required />
                                        @if($errors->has('name'))<span class="validation_error">{{ $errors->first('name') }}</span>@endif
                                    </fieldset>
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test_template_management.template_type') }}</label>
                                    <select name="template_type" class="form-control" id="template_type" old-template-type="{{$data['TestTemplatesData']->template_type}}" required >
                                        <option value="">{{__('languages.test_template_management.select_test_template')}}</option>
                                        @if(!empty($data['TemplateTypes']))
                                            @foreach($data['TemplateTypes'] as $templateType)
                                                <option {{($data['TestTemplatesData']->template_type == $templateType['id']) ? 'selected' : ''}} value="{{$templateType['id']}}">{{$templateType['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.test_template_management.difficulty_level') }}</label>
                                    <select name="difficulty_level" class="form-control" id="difficulty_level" old-difficulty-level="{{$data['TestTemplatesData']->difficulty_level}}" required>
                                        <option value="">{{__('languages.test_template_management.select_difficulty_level')}}</option>
                                        @if(!empty($data['DifficultyLevels']))
                                            @foreach($data['DifficultyLevels'] as $difficultyLevel)
                                                <option {{($data['TestTemplatesData']->difficulty_level == $difficultyLevel['id']) ? 'selected' : ''}}  value="{{$difficultyLevel['id']}}">{{$difficultyLevel['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <p class="questionlisttitle"><strong>{{ __('languages.test_template_management.question_list') }}</strong></p>
                            <div class="row selectallcheckbox">
                                <div class="sm-que-list pl-4 mb-3">
                                    <div class="sm-que">
                                        <input type="checkbox" name="select-all-question" id="select-all-question" class="checkbox" checked>
                                        <span class="font-weight-bold pl-2"> {{__('languages.test_template_management.check_all')}}</span><br>
                                    </div>
                                </div>
                            </div>
                            <div class="row testquestion">
                                @if (!empty($data['Question']))
                                    @foreach ($data['Question'] as $item)
                                        <div class="sm-que-list pl-4">
                                            <div class="sm-que">
                                                <input type="checkbox" name="question_ids[]" class="checkbox" value="{{$item->id}}"  data-examid="{{$item->id}}" checked />
                                                <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                                <span class="font-weight-bold pl-2">{{__('languages.test_template_management.q_id')}} : {{$item->id}}</span>
                                                <span class="pl-2"><b>{{__('languages.test_template_management.question_code')}} :</b> {{$item->question_code}}</span>
                                            </div>
                                            <div class="sm-answer pl-4 pt-2">
                                                {!! $item->question_en !!}
                                            </div>
                                            <div class="pt5 pl-4">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="email">{{__('languages.test_template_management.question_type')}} : {{$item->question_type}}</label>   
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="email">
                                                            {{__('languages.test_template_management.difficulty_level')}} :
                                                            @for($i=1; $i <= $item->dificulaty_level; $i++)
                                                            <span style="font-size:100%;color:red;">&starf;</span>
                                                            @endfor
                                                        </label>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="email">{{__('languages.test_template_management.subject')}} : {{$item->SunjectNameFromQuestion->subjectName ?? ''}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="row question_not_nound" style="display:none;">
                                <div class="col-md-12"><h4>{{__('languages.test_template_management.question_not_found_add_question')}}</h4></div>
                            </div>
                            <div class="form-row select-data read_more_etc text-center" @if ($data['QuestionOtherCount'] == 0) style="display: none;" @endif >
                                <div class="form-row w-100">
                                    <div class="form-group btn-sec w-100">
                                        <button class="blue-btn btn btn-primary mt-4 read_more_question" type="button" data-skip="0" data-old-id="{{$data['TestTemplatesData']->id}}" >{{ __('languages.test_template_management.regenerate') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group mb-50 btn-sec">
                                        <button class="blue-btn btn btn-primary" >{{ __('languages.test_template_management.submit') }}</button>
                                        <a href="javascript:void(0);" class="btn btn-danger" id="backButton">{{__('languages.test_template_management.back')}}</a>
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
@include('backend.layouts.footer');
@endsection
