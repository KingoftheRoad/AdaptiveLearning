@extends('backend.layouts.app')
    @section('content')
    	<style>
	    	.wrs_editor .wrs_tickContainer{display:none !important;}
	    </style>
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
		<div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<form method="post" id="editQuestionFrom" class="form1" action="{{ route('questions.update',$QuestionData->id) }}">
				<input type="hidden" name="editQuestionId" value="{{$QuestionData->id}}" id="editQuestionId">
			@csrf
			@method('patch')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4 main-title">{{ __('languages.questions.update_question_and_answer_details') }}</h4>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="sm-add-question-sec">
						<div class="select-option-sec pb-3">
							@if (session('error'))
							<div class="alert alert-danger">{{ session('error') }}</div>
							@endif
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
							
							<!-- <div class="row">
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="grade_id" class="form-control select-option" id="grade-id">
											@if(!empty($Grades))
												<option value="">{{ __('Grades') }}</option>
												@foreach($Grades as $grade)
												<option value={{ $grade->id }} {{$grade->id === $ObjectivesMapping->grade_id ? 'selected' : ''}}>{{ ucfirst($grade->name) }}</option> 
												@endforeach
											@else
												<option value="">{{ __('No Grades Available') }}</option>
											@endif
										</select>
 									</div>
								</div>
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="subject_id" class="form-control select-option" id="subject-id">
											@if(!empty($subjects))
												<option value="">{{ __('Subjects') }}</option>
												<?php //echo old('subject_id'); ?>
												@foreach($subjects as $subject)
												<option value={{ $subject->id }} {{$subject->id === $ObjectivesMapping->subject_id ? 'selected' : ''}}>{{ ucfirst($subject->name) }}</option> 
												@endforeach
											@else
												<option value="">{{ __('No Subjects Available') }}</option>
											@endif 
										</select>
										@if($errors->has('section_id'))
    										<span class="validation_error">{{ $errors->first('section_id') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="strand_id" class="form-control select-option" id="strand-id">
											@if(!empty($strands))
												<option value="">{{ __('Strands') }}</option>
												@foreach($strands as $strand)
												<option value={{ $strand->id }} {{$strand->id === $ObjectivesMapping->strand_id ? 'selected' : ''}}>{{ ucfirst($strand->name) }}</option> 
												@endforeach
											@else
												<option value="">{{ __('No Strands Available') }}</option>
											@endif  
										</select>
										@if($errors->has('section_id'))
    										<span class="validation_error">{{ $errors->first('section_id') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="learning_unit_id" class="form-control select-option" id="learning-unit">
											@if(!empty($LearningUnits))
												<option value="">{{ __('Learning Units') }}</option>
												@foreach($LearningUnits as $LearningUnit)
												<option value={{ $LearningUnit->id }} {{$LearningUnit->id === $ObjectivesMapping->learning_unit_id ? 'selected' : ''}}>{{ ucfirst($LearningUnit->name) }}</option> 
												@endforeach
											@else
												<option value="">{{ __('No Learning Units Available') }}</option>
											@endif  
										</select>
										@if($errors->has('section_id'))
    										<span class="validation_error">{{ $errors->first('section_id') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="learning_objective_id" class="form-control select-option" id="learning-objectives">
											@if(!empty($LearningObjectives))
												<option value="">{{ __('Learning Objectives') }}</option>
												@foreach($LearningObjectives as $LearningObjective)
												<option value={{ $LearningObjective->id }} {{$LearningObjective->id === $ObjectivesMapping->learning_objectives_id ? 'selected' : ''}}>{{ $LearningObjective->foci_number }} {{ $LearningObjective->title }}</option> 
												@endforeach
											@else
												<option value="">{{ __('No Learning Objectives Available') }}</option>
											@endif
										</select>
										@if($errors->has('section_id'))
    										<span class="validation_error">{{ $errors->first('section_id') }}</span>
										@endif
									</div>
								</div>
							</div> -->
							<!-- <div class="row">
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="field_e" class="form-control select-option" id="field_e" >
										<option value="">E</option>	
											@for($e=1;$e<=99;$e++)
												<option value="{{$e}}" {{$QuestionData->e == $e ? 'selected' : ''}}>{{$e}}</option>
											@endfor
										</select>
										@if($errors->has('field_e'))
    										<span class="validation_error">{{ $errors->first('field_e') }}</span>
										@endif
 									</div>
								</div>

								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="field_f" class="form-control select-option" id="field_f" >
											<option value="">F</option>
											@for($f=1;$f<=99;$f++)
												<option value="{{$f}}" {{$QuestionData->f == $f ? 'selected' : ''}}>{{$f}}</option>
											@endfor
										</select>
										@if($errors->has('field_f'))
    										<span class="validation_error">{{ $errors->first('field_f') }}</span>
										@endif
 									</div>
								</div>

								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">
										<select name="field_g" class="form-control select-option" id="field_g" >
											<option value="f" {{$QuestionData->g == "f" ? 'selected' : ''}}>F</option>
											<option value="n" {{$QuestionData->g == "n" ? 'selected' : ''}}>N</option>
										</select>
										@if($errors->has('field_g'))
    										<span class="validation_error">{{ $errors->first('field_g') }}</span>
										@endif
 									</div>
								</div>
							</div> -->
							<div class="row">
								<div class="col-lg-2 col-md-3">
									<div class="select-lng pt-2 pb-2">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 col-md-12 knwldge-que-code" style="display:none;">
									<label>{{__('languages.questions.knowledge_node')}} : </label>
									<!-- <p class="knowledge-node">4 = A, NA = B, 01 = C, 06 = D, 06 = E, 00 = F, F = G, 001 = H, 1 = Difficulty, 00 = Reserved</p> -->
									<p class="knowledge-node"></p>
								</div>
							</div>
						</div>
						<div class="sm-que-type-difficulty pb-5 fix-position-on-scroll question-type-checkbox">
							<div class="row sm-que-inner">
								<div class="col-md-6">
									<div class="que-type-sec">
										<h6>{{__('languages.questions.question_type')}}</h6>
										<div class="que-checkbox-sec d-flex">
											<div class="que-checkbox">
												<input type="checkbox" name="question_type[]" value="1" class="checkbox question_type" @if (in_array(1, $question_type_ids)) checked="checked" @endif disabled>
												<span>{{__('languages.questions.self_learning')}}</span>
											</div>										
											<div class="que-checkbox">
												<input type="checkbox" name="question_type[]" value="2" class="checkbox question_type" @if (in_array(2, $question_type_ids)) checked="checked" @endif disabled>
												<span>{{__('languages.questions.exercise_assignment')}}</span>
											</div>
											<div class="que-checkbox">
												<input type="checkbox" name="question_type[]" value="3" class="checkbox question_type" @if (in_array(3, $question_type_ids)) checked="checked" @endif disabled>
												<span>{{__('languages.questions.testing')}}</span>
											</div>
											<div class="que-checkbox">
												<input type="checkbox" name="question_type[]" value="4" class="checkbox question_type" @if (in_array(4, $question_type_ids)) checked="checked" @endif disabled>
												<span>{{__('languages.questions.seed')}}</span>
											</div>
										</div>
										@if($errors->has('question_type'))
    										<span class="validation_error">{{ $errors->first('question_type') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-2">
									<div class="que-difficulty-section">
										<h6>{{__('languages.questions.difficulty_level')}}</h6>
										<div class="que-difficulty-sec d-flex">
											<div class="que-difficulty">
												<div class="rating">
													<input type="radio" name="dificulaty_level" value="5" id="5" {{($QuestionData->dificulaty_level === 5) ? 'checked' :'' }} readonly="true">
													<label for="5">☆</label>
													<input type="radio" name="dificulaty_level" value="4" id="4" {{($QuestionData->dificulaty_level === 4) ? 'checked' :'' }} readonly="true">
													<label for="4">☆</label>
													<input type="radio" name="dificulaty_level" value="3" id="3" {{($QuestionData->dificulaty_level === 3) ? 'checked' :'' }} readonly="true">
													<label for="3">☆</label> 
													<input type="radio" name="dificulaty_level" value="2" id="2" {{($QuestionData->dificulaty_level === 2) ? 'checked' :'' }} readonly="true">
													<label for="2">☆</label> 
													<input type="radio" name="dificulaty_level" value="1" id="1" {{($QuestionData->dificulaty_level === 1) ? 'checked' :'' }} readonly="true">
													<label for="1">☆</label>
												</div>
											</div>
										</div>
										@if($errors->has('dificulaty_level'))
    										<span class="validation_error">{{ $errors->first('dificulaty_level') }}</span>
										@endif
										<span id="dificulty-value"></span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="que-code-sec">
										<h6>{{__('languages.questions.question_code')}}</h6>
										<div class="que-code">
											<input type="hidden" name="question_code" id="Question-Code" value="{{$QuestionData->question_code}}" placeholder="{{__('languages.questions.enter_question_code')}}" class="input-code">
											<input type="text" name="naming_structure_code" id="naming_structure_code" placeholder="{{__('languages.questions.enter_question_code')}}" value="{{ (old('naming_structure_code') != '') ? old('naming_structure_code') : str_replace(" ","",$QuestionData->naming_structure_code)}}" class="input-code">
											<span class="validation_error naming_structure_code_error"></span>
											@if($errors->has('naming_structure_code'))
    											<span class="validation_error">{{ $errors->first('naming_structure_code') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="add-question-sec">
							<div class="row">
								<div class="col-md-12">
									<div class="sm-add-que">
										<div class="btn-sec">
											<p class="dark-blue-btn btn btn-primary mb-4">{{ __('languages.questions.english') }}</p>
										</div>
										<form>
										<div class="btn-sec en-to-ch-btn">
												<input type="button" onclick="english_to_chinese()" value="{{__('languages.questions.english_to_chinese')}}" class="dark-blue-btn btn btn-primary mb-4"></button>
											</div>
										</form>
									</div>
									<div class="sm-textarea">
										<textarea id="question_en" class="sm-area" name="question_en" data-sample-short>{{ $QuestionData->question_en }}</textarea>
									</div>
									@if($errors->has('question_en'))
    									<span class="validation_error">{{ $errors->first('question_en') }}</span>
									@endif
								</div>
							
								<div class="col-md-12">
									<div class="sm-add-que">
										<div class="btn-sec">
											<p class="dark-blue-btn btn btn-primary mb-4">{{ __('languages.questions.chinese') }}</p>
										</div>
									</div>
									<form>
										<div class="btn-sec ch-to-en-btn">
											<input type="button" onclick="chinese_to_english()" class="dark-blue-btn btn btn-primary mb-4" value="{{__('languages.questions.chinese_to_english')}}"></button>
										</div>
									</form>
									<textarea id="question_ch" class="sm-area" name="question_ch" data-sample-short>{{$QuestionData->question_ch}}</textarea>
									@if($errors->has('question_ch'))
    									<span class="validation_error">{{ $errors->first('question_ch') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="sm-right-ans-hints-sec py-2">
							<div class="row">
								<div class="col-md-12">
									<div class="right-ans-hints-sec">
										<table class="table-for-right-ans">
										    <thead>
										        <tr>
										          <th class=""> {{__('languages.questions.right_ans')}}</th>
										          <th class="">{{__('languages.questions.ans')}}({{__('languages.questions.english')}})</th>
										          <th class="">{{__('languages.questions.hints_of_wrong_ans')}} ({{__('languages.questions.english')}})</th>
										        </tr>
										    </thead>
										    <tbody class="scroll-pane">
										        <tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_1')}}<input type="radio" name="correct_answer_en" value="1" class="radio" {{ ($QuestionData->answers->correct_answer_en=="1")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_en'))
														<span class="validation_error">{{ $errors->first('correct_answer_en') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer1_en" class="sm-area answerEditor" name="answer1_en" data-sample-short>{{ $QuestionData->answers->answer1_en }}</textarea>
														@if($errors->has('answer1_en'))
														<span class="validation_error">{{ $errors->first('answer1_en') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer1_en" class="input-text" placeholder="Enter hint for wrong answer (English)">{{$QuestionData->answers->hint_answer1_en}}</textarea>
														@if($errors->has('hint_answer1_en'))
														<span class="validation_error">{{ $errors->first('hint_answer1_en') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_1_english')}}</label>
														<textarea id="node_hint_answer1_en" class="sm-area" name="node_hint_answer1_en" data-sample-short>{{$QuestionData->answers->node_hint_answer1_en}}</textarea>
														@if($errors->has('node_hint_answer1_en'))
														<span class="validation_error">{{ $errors->first('node_hint_answer1_en') }}</span>
														@endif
														<!-- Weakness sections -->
														<label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer1_node_relation_id_en','answer1_weakness_en');">
															<input type="hidden" name="answer1_node_relation_id_en" id="answer1_node_relation_id_en" value="{{$QuestionData->answers->answer1_node_relation_id_en}}" >
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer1_weakness_en="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer1_node_relation_id_en!="" )
																@php
																	$answer1_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_en] : '';
																	if($answer1_weakness_en==""){
																		$answer1_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_en]) ?  $nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_en] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer1_weakness_en" class="input-code" id="answer1_weakness_en" value="{{ $answer1_weakness_en }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>	
															@if($errors->has('answer1_weakness_en'))
															<span class="validation_error">{{ $errors->first('answer1_weakness_en') }}</span>
															@endif
														</div>
														<!-- End Weakness sections -->
													</td>
													
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_2')}}<input type="radio" name="correct_answer_en" value="2" class="radio" {{ ($QuestionData->answers->correct_answer_en=="2")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_en'))
														<span class="validation_error">{{ $errors->first('correct_answer_en') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer2_en" class="sm-area answerEditor" name="answer2_en" data-sample-short>{{$QuestionData->answers->answer2_en}}</textarea>
														@if($errors->has('answer2_en'))
														<span class="validation_error">{{ $errors->first('answer2_en') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer2_en" class="input-text" placeholder="Enter hint for wrong answer (English)">{{$QuestionData->answers->hint_answer2_en}}</textarea>
														@if($errors->has('hint_answer2_en'))
														<span class="validation_error">{{ $errors->first('hint_answer2_en') }}</span>
														@endif
														<label>{{ $errors->first('node_hint_answer2_en') }}</label>
														<textarea id="node_hint_answer2_en" class="sm-area" name="node_hint_answer2_en" data-sample-short>{{$QuestionData->answers->node_hint_answer2_en}}</textarea>
														<!-- <input type="text" name="node_hint_answer2_en" class="input-code" id="node_hint_answer2_en" value="{{$QuestionData->answers->node_hint_answer2_en}}" placeholder="Node Hint Answer"> -->
														@if($errors->has('node_hint_answer2_en'))
														<span class="validation_error">{{ $errors->first('node_hint_answer2_en') }}</span>
														@endif
														<!-- Weakness sections -->
														<label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img"  data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer2_node_relation_id_en','answer2_weakness_en');">
															<input type="hidden" name="answer2_node_relation_id_en" id="answer2_node_relation_id_en" value="{{$QuestionData->answers->answer2_node_relation_id_en}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer2_weakness_en="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer2_node_relation_id_en!="" )
																@php
																	$answer2_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_en] : '';
																	if($answer2_weakness_en=="")
																	{
																		$answer2_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_en]) ? $nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_en] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer2_weakness_en" class="input-code" id="answer2_weakness_en" value="{{ $answer2_weakness_en }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer2_weakness_en'))
															<span class="validation_error">{{ $errors->first('answer2_weakness_en') }}</span>
															@endif
														</div>
														<!-- End Weakness sections -->
													</td>
													
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_3')}}<input type="radio" name="correct_answer_en" value="3" class="radio" {{ ($QuestionData->answers->correct_answer_en=="3")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_en'))
														<span class="validation_error">{{ $errors->first('correct_answer_en') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer3_en" class="sm-area answerEditor" name="answer3_en" data-sample-short>{{$QuestionData->answers->answer3_en}}</textarea>
														@if($errors->has('answer3_en'))
														<span class="validation_error">{{ $errors->first('answer3_en') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer3_en" class="input-text" placeholder="Enter hint for wrong answer (English)">{{$QuestionData->answers->hint_answer3_en}}</textarea>
														@if($errors->has('hint_answer3_en'))
														<span class="validation_error">{{ $errors->first('hint_answer3_en') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_3_english')}}</label>
														<textarea id="node_hint_answer3_en" class="sm-area" name="node_hint_answer3_en" data-sample-short>{{$QuestionData->answers->node_hint_answer3_en}}</textarea>
														<!-- <input type="text" name="node_hint_answer3_en" class="input-code" id="node_hint_answer3_en" value="{{$QuestionData->answers->node_hint_answer3_en}}" placeholder="Node Hint Answer">	 -->
														@if($errors->has('node_hint_answer3_en'))
														<span class="validation_error">{{ $errors->first('node_hint_answer3_en') }}</span>
														@endif

														<!-- Weakness sections -->
														<label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer3_node_relation_id_en','answer3_weakness_en');">
															<input type="hidden" name="answer3_node_relation_id_en" id="answer3_node_relation_id_en" value="{{$QuestionData->answers->answer3_node_relation_id_en}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer3_weakness_en="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer3_node_relation_id_en!="" )
																@php
																	$answer3_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_en] : '';
																	if($answer3_weakness_en=="")
																	{
																		$answer3_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_en]) ? $nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_en] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer3_weakness_en" class="input-code" id="answer3_weakness_en" value="{{ $answer3_weakness_en }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer3_weakness_en'))
															<span class="validation_error">{{ $errors->first('answer3_weakness_en') }}</span>
															@endif
														</div>
														<!-- End Weakness sections -->
													</td>
						
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_4')}}<input type="radio" name="correct_answer_en" value="4" class="radio" {{ ($QuestionData->answers->correct_answer_en=="4")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_en'))
														<span class="validation_error">{{ $errors->first('correct_answer_en') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer4_en" class="sm-area answerEditor" name="answer4_en" data-sample-short>{{$QuestionData->answers->answer4_en}}</textarea>
														@if($errors->has('answer4_en'))
														<span class="validation_error">{{ $errors->first('answer4_en') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer4_en" class="input-text" placeholder="Enter hint for wrong answer (English)">{{$QuestionData->answers->hint_answer4_en}}</textarea>
														@if($errors->has('hint_answer4_en'))
														<span class="validation_error">{{ $errors->first('hint_answer4_en') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_4_english')}}</label>
														<textarea id="node_hint_answer4_en" class="sm-area" name="node_hint_answer4_en" data-sample-short>{{$QuestionData->answers->node_hint_answer4_en}}</textarea>
														<!-- <input type="text" name="node_hint_answer4_en" class="input-code" id="node_hint_answer4_en" value="{{$QuestionData->answers->node_hint_answer4_en}}" placeholder="Node Hint Answer">	 -->
														@if($errors->has('node_hint_answer4_en'))
														<span class="validation_error">{{ $errors->first('node_hint_answer4_en') }}</span>
														@endif

														<!-- Weakness sections -->
														<label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal"  onclick="check_ans('answer4_node_relation_id_en','answer4_weakness_en');">
															<input type="hidden" name="answer4_node_relation_id_en" id="answer4_node_relation_id_en" value="{{$QuestionData->answers->answer4_node_relation_id_en}}">
														</p>
														<div class="weekness-input pl-2">

															@php
																$answer4_weakness_en="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer4_node_relation_id_en!="" )
																@php
																	$answer4_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_en] : '';
																	if($answer4_weakness_en=="")
																	{
																		$answer4_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_en]) ? $nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_en] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer4_weakness_en" class="input-code" id="answer4_weakness_en" value="{{ $answer4_weakness_en }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer4_weakness_en'))
															<span class="validation_error">{{ $errors->first('answer4_weakness_en') }}</span>
															@endif
														</div>
														<!-- End Weakness sections -->
													</td>
										        </tr>
										  </tbody>
										</table>
									</div>
								</div>
								<div class="col-md-12">
									<div class="right-ans-hints-sec">
										<table class="table-for-right-ans">
										    <thead>
										        <tr>
										          <th class="">{{__('languages.questions.right_ans')}}</th>
										          <th class="">{{__('languages.questions.ans')}} ({{__('languages.questions.chinese')}})</th>
										          <th class="">{{__('languages.questions.hints_of_wrong_ans')}} ({{__('languages.questions.chinese')}})</th>
										        </tr>
										    </thead>
										    <tbody class="scroll-pane">
										        <tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_1')}}<input type="radio" name="correct_answer_ch" value="1" class="radio" {{ ($QuestionData->answers->correct_answer_ch=="1")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_ch'))
														<span class="validation_error">{{ $errors->first('correct_answer_ch') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer1_ch" class="sm-area answerEditor" name="answer1_ch" data-sample-short>{{$QuestionData->answers->answer1_ch}}</textarea>
														@if($errors->has('answer1_ch'))
														<span class="validation_error">{{ $errors->first('answer1_ch') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer1_ch" class="input-text" placeholder="{{__('languages.enter_hint_for_wrong_answer_chinese')}}">{{$QuestionData->answers->hint_answer1_ch}}</textarea>
														@if($errors->has('hint_answer1_ch'))
														<span class="validation_error">{{ $errors->first('hint_answer1_ch') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_1_chinese')}}</label>
														<textarea id="node_hint_answer1_ch" class="sm-area" name="node_hint_answer1_ch" data-sample-short>{{$QuestionData->answers->node_hint_answer1_ch}}</textarea>
														<!-- <input type="text" name="node_hint_answer1_ch" class="input-code" id="node_hint_answer1_ch" value="{{$QuestionData->answers->node_hint_answer1_ch}}" placeholder="Node Hint Answer">	 -->
														@if($errors->has('node_hint_answer1_ch'))
														<span class="validation_error">{{ $errors->first('node_hint_answer1_ch') }}</span>
														@endif

														<!-- Weakness sections -->
														<!-- <label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer1_node_relation_id_ch','answer1_weakness_ch');">
															<input type="hidden" name="answer1_node_relation_id_ch" id="answer1_node_relation_id_ch" value="{{$QuestionData->answers->answer1_node_relation_id_ch}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer1_weakness_ch="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer1_node_relation_id_ch!="" )
																@php
																	$answer1_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_ch]) ? $nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_ch] : '';
																	if($answer1_weakness_ch=="")
																	{
																		$answer1_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_ch] : '';
																	}
																@endphp
															@endif

															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer1_weakness_ch" class="input-code" id="answer1_weakness_ch" value="{{ $answer1_weakness_ch }}" readonly>
															@if($errors->has('answer1_weakness_ch'))
															<span class="validation_error">{{ $errors->first('answer1_weakness_ch') }}</span>
															@endif
														</div> -->
														<!-- End Weakness sections -->
													</td>
											
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_2')}}<input type="radio" name="correct_answer_ch" value="2" class="radio" {{ ($QuestionData->answers->correct_answer_ch=="2")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_ch'))
														<span class="validation_error">{{ $errors->first('correct_answer_ch') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer2_ch" class="sm-area answerEditor" name="answer2_ch" data-sample-short>{{$QuestionData->answers->answer2_ch}}</textarea>
														@if($errors->has('answer2_ch'))
														<span class="validation_error">{{ $errors->first('answer2_ch') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer2_ch" class="input-text" placeholder="{{__('languages.enter_hint_for_wrong_answer_chinese')}}">{{$QuestionData->answers->hint_answer2_ch}}</textarea>
														@if($errors->has('hint_answer2_ch'))
														<span class="validation_error">{{ $errors->first('hint_answer2_ch') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_2_chinese')}}</label>
														<!-- <input type="text" name="node_hint_answer2_ch" class="input-code" id="node_hint_answer2_ch" value="{{$QuestionData->answers->node_hint_answer2_ch}}" placeholder="Node Hint Answer">-->
														<textarea id="node_hint_answer2_ch" class="sm-area" name="node_hint_answer2_ch" data-sample-short>{{$QuestionData->answers->node_hint_answer2_ch}}</textarea>
														@if($errors->has('node_hint_answer2_ch'))
														<span class="validation_error">{{ $errors->first('node_hint_answer2_ch') }}</span>
														@endif

														<!-- Weakness sections -->
														<!-- <label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer2_node_relation_id_ch','answer2_weakness_ch');">
															<input type="hidden" name="answer2_node_relation_id_ch" id="answer2_node_relation_id_ch" value="{{$QuestionData->answers->answer2_node_relation_id_ch}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer2_weakness_ch="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer2_node_relation_id_ch!="" )
																@php
																	$answer2_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_ch]) ? $nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_ch] : '';
																	if($answer2_weakness_ch=="")
																	{
																		$answer2_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_ch]) ?$nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_ch] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer2_weakness_ch" class="input-code" id="answer2_weakness_ch" value="{{ $answer2_weakness_ch }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer2_weakness_ch'))
															<span class="validation_error">{{ $errors->first('answer2_weakness_ch') }}</span>
															@endif
														</div> -->
														<!-- End Weakness sections -->
													</td>
													
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_3')}}<input type="radio" name="correct_answer_ch" value="3" class="radio" {{ ($QuestionData->answers->correct_answer_ch=="3")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_ch'))
														<span class="validation_error">{{ $errors->first('correct_answer_ch') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer3_ch" class="sm-area answerEditor" name="answer3_ch" data-sample-short>{{$QuestionData->answers->answer3_ch}}</textarea>
														@if($errors->has('answer3_ch'))
														<span class="validation_error">{{ $errors->first('answer3_ch') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer3_ch" class="input-text" placeholder="{{__('enter_hint_for_wrong_answer_chinese')}}">{{$QuestionData->answers->hint_answer3_ch}}</textarea>
														@if($errors->has('hint_answer3_ch'))
														<span class="validation_error">{{ $errors->first('hint_answer3_ch') }}</span>
														@endif
													    <label>{{__('languages.questions.node_hint_answer_3_chinese')}}</label>
														<textarea id="node_hint_answer3_ch" class="sm-area" name="node_hint_answer3_ch" data-sample-short>{{$QuestionData->answers->node_hint_answer3_ch}}</textarea>
														<!-- <input type="text" name="node_hint_answer3_ch" class="input-code" id="node_hint_answer3_ch" value="{{$QuestionData->answers->node_hint_answer3_ch}}" placeholder="Node Hint Answer">	 -->
														@if($errors->has('node_hint_answer3_ch'))
														<span class="validation_error">{{ $errors->first('node_hint_answer3_ch') }}</span>
														@endif

														<!-- Weakness sections -->
														<!-- <label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer3_node_relation_id_ch','answer3_weakness_ch');">
															<input type="hidden" name="answer3_node_relation_id_ch" id="answer3_node_relation_id_ch" value="{{$QuestionData->answers->answer3_node_relation_id_ch}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer3_weakness_ch="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer3_node_relation_id_ch!="" )
																@php
																	$answer3_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_ch]) ? $nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_ch] : '';
																	if($answer3_weakness_ch=="")
																	{
																		$answer3_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_ch] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer3_weakness_ch" class="input-code" id="answer3_weakness_ch" value="{{ $answer3_weakness_ch }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer3_weakness_ch'))
															<span class="validation_error">{{ $errors->first('answer3_weakness_ch') }}</span>
															@endif
														</div> -->
														<!-- End Weakness sections -->
													</td>
													
										        </tr>
												<tr>
										          	<td class="p-2">
													  <span class="option_label">{{__('languages.questions.ans_4')}}<input type="radio" name="correct_answer_ch" value="4" class="radio" {{ ($QuestionData->answers->correct_answer_ch=="4")? "checked" : ""}}></span>
														@if($errors->has('correct_answer_ch'))
														<span class="validation_error">{{ $errors->first('correct_answer_ch') }}</span>
														@endif
													</td>
										        	<td class="ans-td p-2">
														<textarea id="answer4_ch" class="sm-area answerEditor" name="answer4_ch" data-sample-short>{{$QuestionData->answers->answer4_ch}}</textarea>
														@if($errors->has('answer4_ch'))
														<span class="validation_error">{{ $errors->first('answer4_ch') }}</span>
														@endif
													</td>
													<td class="hint-td p-2">
														<textarea name="hint_answer4_ch" class="input-text" placeholder="{{__('languages.enter_hint_for_wrong_answer_chinese')}}">{{$QuestionData->answers->hint_answer4_ch}}</textarea>
														@if($errors->has('hint_answer4_ch'))
														<span class="validation_error">{{ $errors->first('hint_answer4_ch') }}</span>
														@endif
														<label>{{__('languages.questions.node_hint_answer_4_chinese')}}</label>
														<textarea id="node_hint_answer4_ch" class="sm-area" name="node_hint_answer4_ch" data-sample-short>{{$QuestionData->answers->node_hint_answer4_ch}}</textarea>
														<!-- <input type="text" name="node_hint_answer4_ch" class="input-code" id="node_hint_answer4_ch" value="{{$QuestionData->answers->node_hint_answer4_ch}}" placeholder="Node Hint Answer"> -->
														@if($errors->has('node_hint_answer4_ch'))
														<span class="validation_error">{{ $errors->first('node_hint_answer4_ch') }}</span>
														@endif

														<!-- Weakness sections -->
														<!-- <label class="weak-label"><strong>{{__('languages.questions.weakness')}} :</strong></label>
														<p class="know-p pl-2">{{__('languages.questions.knowledge_node_relation')}}
															<img src="{{asset('images/Chain.png')}}" class="node-relation-img" data-toggle="modal" data-target="#nodeModal" onclick="check_ans('answer4_node_relation_id_ch','answer4_weakness_ch');">
															<input type="hidden" name="answer4_node_relation_id_ch" id="answer4_node_relation_id_ch" value="{{$QuestionData->answers->answer4_node_relation_id_ch}}">
														</p>
														<div class="weekness-input pl-2">
															@php
																$answer4_weakness_ch="";
															@endphp
															@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer4_node_relation_id_ch!="" )
																@php
																	$answer4_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_ch]) ? $nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_ch] : '';
																	if($answer4_weakness_ch=="")
																	{
																		$answer4_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_ch] : '';
																	}
																@endphp
															@endif
															<label>{{__('languages.questions.weakness_name')}} :</label>
															<input type="text" name="answer4_weakness_ch" class="input-code" id="answer4_weakness_ch" value="{{ $answer4_weakness_ch }}" placeholder="{{__('languages.questions.answer_weakness')}}" readonly>
															@if($errors->has('answer4_weakness_ch'))
															<span class="validation_error">{{ $errors->first('answer4_weakness_ch') }}</span>
															@endif
														</div> -->
														<!-- End Weakness sections -->
													</td>
										        </tr>
										  </tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-12">
										<div class="sm-add-que">
											<div class="btn-sec">
												<p class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.general_hints_english')}}</p>
											</div>
										</div>
										<div class="sm-textarea">
											<textarea id="general_hints_en" class="sm-area" name="general_hints_en" data-sample-short>{{$QuestionData->general_hints_en}}</textarea>
										</div>
										@if($errors->has('general_hints_en'))
											<span class="validation_error">{{ $errors->first('general_hints_en') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-4">
										<input type="hidden" name="question_video_id_en" id= "question_video_id_en" value="{{$QuestionData->general_hints_video_id_en}}"/>
										<button type="button" class="question-video-hint-btn" name="video_hint" value="video_hint" class="" onclick="question_video_hints('en')">{{__('languages.video_hint_en')}}</button>
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-12">
										<div class="sm-add-que">
											<div class="btn-sec">
												<p class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.general_hints_chinese')}}</p>
											</div>
										</div>
										<div class="sm-textarea">
											<textarea id="general_hints_ch" class="sm-area" name="general_hints_ch" data-sample-short>{{$QuestionData->general_hints_ch}}</textarea>
										</div>
										@if($errors->has('general_hints_ch'))
											<span class="validation_error">{{ $errors->first('general_hints_ch') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-4">
										<input type="hidden" name="question_video_id_ch" id= "question_video_id_ch" value="{{$QuestionData->general_hints_video_id_ch}}"/>
										<button type="button" class="question-video-hint-btn" name="video_hint" value="video_hint" class="" onclick="question_video_hints('ch')">{{__('languages.video_hint_ch')}}</button>
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-12">
										<div class="sm-add-que">
											<div class="btn-sec">
												<p class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.full_solution_en')}}</p>
											</div>
										</div>
										<div class="sm-textarea">
											<textarea id="full_solution_en" class="sm-area" name="full_solution_en" data-sample-short>{{$QuestionData->full_solution_en}}</textarea>
										</div>
										@if($errors->has('full_solution_en'))
											<span class="validation_error">{{ $errors->first('full_solution_en') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="add-question-sec">
								<div class="row">
									<div class="col-md-12">
										<div class="sm-add-que">
											<div class="btn-sec">
												<p class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.full_solution_ch')}}</p>
											</div>
										</div>
										<div class="sm-textarea">
											<textarea id="full_solution_ch" class="sm-area" name="full_solution_ch" data-sample-short>{{$QuestionData->full_solution_ch}}</textarea>
										</div>
										@if($errors->has('full_solution_ch'))
											<span class="validation_error">{{ $errors->first('full_solution_ch') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="add-question-sec" style="padding-top:15px;">
								<div class="row">
									<div class="col-md-6">
										<div class="sm-add-que">{{__('languages.is_approved_question')}}</div>
										<select name="is_approved"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="is_approved_question">
											<option value="yes" @if($QuestionData->is_approved == 'yes') selected @endif>{{ __('languages.approve') }}</option>
											<option value="no" @if($QuestionData->is_approved == 'no') selected @endif>{{ __('languages.not_approve') }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="sm-btn-sec">
							<div class="row">
								<div class="col-md-12">
									<div class="btn-sec sm-add-que-btn">
										<button type="submit" name="save_draft" value="save_draft" class="blue-btn-outline btn btn-outline-primary">{{__('languages.questions.save_draft')}}</button>
										<button type="submit" name="publish" value="publish" class="blue-btn btn btn-primary ml-4">{{__('languages.publish')}}</button>
										<button type="button" class="blue-btn btn btn-primary ml-4 preview_question" q-id="{{ $QuestionData->id }}">{{__('languages.preview')}}</button>
										<a href="{{route('question.copy-create',$QuestionData->id)}}" class="btn-back ml-4">{{__('languages.copy')}} & {{__('languages.create')}}</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</form>
	      </div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="nodeModal" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="nodeModalLabel">{{__('languages.questions.knowledge_node_relation')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12">
								<label>{{ __('languages.questions.parent_node_id') }}</label>
								<fieldset class="">
									<select class="selectpicker form-control node-id" data-show-subtext="true" data-live-search="true" name="node_id" id="main_node_id">
										<option value="">{{__('languages.questions.select_node')}}</option>
										@if(!empty($NodesList))
											{!! $NodesList !!}
										@endif   
									</select>
								</fieldset>
							</div>
						</div>
						<p class="node-info" style="display: none">{{__('languages.questions.title')}} : <span id="node-title"></span></p>
						<p class="node-info" style="display: none">{{__('languages.questions.weakness')}} : <span id="node-weakness"></span></p>
						<p class="node-info" style="display: none">{{__('languages.questions.description')}} : <span id="node-description"></span></p>
						<span id="node-weakness-ch" style="display:none;"></span>
						<span id="node-weakness-en" style="display:none;"></span>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.close')}}</button>
						<button type="button" class="btn btn-primary" onclick="node_link()">{{__('languages.submit')}}</button>
					</div>
				</div>
			</div>
		</div>
		{{-- Start Question video Hints video list popup model --}}
		<div class="modal fade" id="QuestionVideoHintsModal" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{__('languages.select_question_video_hints')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body modal-body-video-list">
						<div class="modal-search-main">
							<div class="modal-search-inner">
								<input type="text" class="" name="filterFileName" id="filterFileName" placeholder="{{ __('languages.upload_document.search_by_file_name') }}" value="">
								<button type="button" name="filter" id="filter" value="filter" class="btn-search" onclick="question_video_hints()">{{ __('languages.search') }}</button>
							</div>
						</div>
						<hr/>
						<div class="video-hints-list"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary closepopup" data-dismiss="modal">{{__('languages.close')}}</button>
						<button type="button" class="btn btn-primary" onclick="VideoHintIsSelectOrNot()">{{__('languages.submit')}}</button>
					</div>
				</div>
			</div>
		</div>
		{{-- End Question video Hints video list popup model --}}


		<!-- Start Preview Question Modal -->
		<div class="modal fade" id="modalPreviewQuestion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		  <div class="modal-dialog  modal-xl" style="max-width: 90%;">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="staticBackdropLabel">{{__('languages.questions.preview_question')}}</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body" style="background-color: #edebeb;">
		      </div>
		    </div>
		  </div>
		</div>
		<!-- End Preview Question Modal -->
		
		<script>
			// Set Page Name
			var pageName = 'editQuestionBank';
			$(window).scroll(function(){
				if ($(window).scrollTop() >= 300) {
					$('.sm-que-type-difficulty').addClass('fixed-header');
					$('.sm-que-type-difficulty .sm-que-inner').addClass('visible-title');
				}else{
					$('.sm-que-type-difficulty').removeClass('fixed-header');
					$('.sm-que-type-difficulty .sm-que-inner').removeClass('visible-title');
				}
			});
			</script>
			<script src="https://cdn.ckeditor.com/4.16.1/standard-all/ckeditor.js"></script>
			<script src="{{ asset('ckfinder/ckfinder.js') }}"></script>
			<script src="{{ asset('js/CustomeCkeditorScript.js') }}" defer></script>
		@include('backend.layouts.footer')
			<script type="text/javascript">
				$(document).ready(function(){
					$(document).on("click", ".preview_question", function (){
						PreviewQuestion('editQuestionFrom');
					})
		    	})
		    	function getYoutubeId(url) {
			        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
			        const match = url.match(regExp);
			        return (match && match[2].length === 11) ? match[2] : null;
			    }
			    $(document).on('click', '.video-img-sec', function() {
			        var videoSrc = $(this).data( "src" );
			        var domain = videoSrc.replace('http://','').replace('https://','').split(/[/?#]/)[0];
			        if (videoSrc.indexOf("youtube") != -1) {
			            const videoId = getYoutubeId(videoSrc);
			            $("#videoDis").attr('src','//www.youtube.com/embed/'+videoId);
			        }else if (videoSrc.indexOf("vimeo") != -1) {
			            const videoId = getYoutubeId(videoSrc);
			            var matches = videoSrc.match(/vimeo.com\/(\d+)/);
			            $("#videoDis").attr('src','https://player.vimeo.com/video/'+matches[1]);
			        }else if (videoSrc.indexOf("dailymotion") != -1) {
			            var m = videoSrc.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
			            if (m !== null) {
			                if(m[4] !== undefined) {
			                    $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
			                }
			                $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
			            }
			        }else{
			            $("#videoDis").attr('src','/'+videoSrc);
			        }
			    });
			</script>
	@endsection