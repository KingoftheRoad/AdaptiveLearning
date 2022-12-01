<div class="sm-add-question-sec">
	<div class="select-option-sec pb-3">
		<div class="row">
			<div class="col-lg-12 col-md-12 knwldge-que-code" style="display:none;">
				<label class="font-weight-bold text-dark">{{__('languages.questions.knowledge_node')}} : </label>
				<p class="knowledge-node text-dark"></p>
			</div>
		</div>
	</div>
	<div class=" pb-5 ">
		<div class="row">
			<div class="col-md-12">
				<div class="w-100">
					<label class="font-weight-bold">{{__('languages.questions.question_type')}}: </label>
					@if (in_array(1, $question_type_ids))
						<span>{{__('languages.questions.self_learning')}}</span>
					@endif										
					@if (in_array(2, $question_type_ids))
						<span>{{__('languages.questions.exercise_assignment')}}</span>
					@endif
					@if (in_array(3, $question_type_ids))
						<span>{{__('languages.questions.testing')}}</span>
					@endif
					@if (in_array(4, $question_type_ids))
						<span>{{__('languages.questions.seed')}}</span>
					@endif
				</div>
			</div>
			<div class="col-md-12">
				<div class="w-100">
					<label class="font-weight-bold" style="float: left;">{{__('languages.questions.difficulty_level')}}: </label>
					
					<div class="que-difficulty-sec" style="float: left;width: auto;padding: 0;top: -3px;position: relative;margin-left: 10px;">
						<div class="que-difficulty" style="float: left;width: auto;">
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
					<span id="dificulty-value"></span>
				</div>
			</div>
			<div class="col-md-12">
				<div class="w-100">
					<label class="font-weight-bold">{{__('languages.questions.question_code')}}: </label>
					{{ $QuestionData->naming_structure_code }}
				</div>
			</div>
		</div>
	</div>
	<div class="add-question-sec">
		<div class="row">
			<div class="col-md-6 mt-2 border-right">
					<h5 class="font-weight-bold text-center">{{ __('languages.questions.english') }} </h5>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">Question:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->question_en !!}
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_1')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer1_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer1_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_1_english')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer1_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
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
							{{ $answer1_weakness_en  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>

					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_2')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer2_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer2_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_2_english')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer2_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer2_weakness_en="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer2_node_relation_id_en!="" )
								@php
									$answer2_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_en] : '';
									if($answer2_weakness_en==""){
										$answer2_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_en]) ?  $nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_en] : '';
									}
								@endphp
							@endif
							{{ $answer2_weakness_en  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_3')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer3_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer3_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_3_english')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer3_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer3_weakness_en="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer3_node_relation_id_en!="" )
								@php
									$answer3_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_en] : '';
									if($answer3_weakness_en==""){
										$answer3_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_en]) ?  $nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_en] : '';
									}
								@endphp
							@endif
							{{ $answer3_weakness_en  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_4')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer4_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer4_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_4_english')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer4_en !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer4_weakness_en="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer4_node_relation_id_en!="" )
								@php
									$answer4_weakness_en=!empty($nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_en]) ? $nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_en] : '';
									if($answer4_weakness_en==""){
										$answer4_weakness_en=!empty($nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_en]) ?  $nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_en] : '';
									}
								@endphp
							@endif
							{{ $answer4_weakness_en  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
			</div>
			<div class="col-md-6 mt-2">
					<h5 class="font-weight-bold text-center">{{ __('languages.questions.chinese') }} </h5>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.question')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->question_ch !!}
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_1')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer1_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer1_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_1_chinese')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer1_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer1_weakness_ch="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer1_node_relation_id_ch!="" )
								@php
									$answer1_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer1_node_relation_id_ch] : '';
									if($answer1_weakness_ch==""){
										$answer1_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_ch]) ?  $nodeWeaknessChList[$QuestionData->answers->answer1_node_relation_id_ch] : '';
									}
								@endphp
							@endif
							{{ $answer1_weakness_ch  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>

					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_2')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer2_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer2_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_2_chinese')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer2_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer2_weakness_ch="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer2_node_relation_id_ch!="" )
								@php
									$answer2_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer2_node_relation_id_ch] : '';
									if($answer2_weakness_ch==""){
										$answer2_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_ch]) ?  $nodeWeaknessChList[$QuestionData->answers->answer2_node_relation_id_ch] : '';
									}
								@endphp
							@endif
							{{ $answer2_weakness_ch  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_3')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer3_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer3_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_3_chinese')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer3_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer3_weakness_ch="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer3_node_relation_id_ch!="" )
								@php
									$answer3_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer3_node_relation_id_ch] : '';
									if($answer3_weakness_ch==""){
										$answer3_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_ch]) ?  $nodeWeaknessChList[$QuestionData->answers->answer3_node_relation_id_ch] : '';
									}
								@endphp
							@endif
							{{ $answer3_weakness_ch  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<label class="font-weight-bold">{{__('languages.questions.ans_4')}}:</label>
						</div>
						<div class="col-md-10">
							{!! $QuestionData->answers->answer4_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.enter_hint_for_wrong_answer')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->hint_answer4_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.node_hint_answer_4_chinese')}}:</label>
						</div>
						<div class="col-md-8">
							{!! $QuestionData->answers->node_hint_answer4_ch !!}
						</div>
						<div class="col-md-4">
							<label class="font-weight-bold">{{__('languages.questions.weakness_name')}}:</label>
						</div>
						<div class="col-md-8">
							@php
								$answer4_weakness_ch="";
							@endphp
							@if(!empty($nodeWeaknessList) && !empty($nodeWeaknessChList) &&  $QuestionData->answers->answer4_node_relation_id_ch!="" )
								@php
									$answer4_weakness_ch=!empty($nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_ch]) ? $nodeWeaknessList[$QuestionData->answers->answer4_node_relation_id_ch] : '';
									if($answer4_weakness_ch==""){
										$answer4_weakness_ch=!empty($nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_ch]) ?  $nodeWeaknessChList[$QuestionData->answers->answer4_node_relation_id_ch] : '';
									}
								@endphp
							@endif
							{{ $answer4_weakness_ch  }}
						</div>
						<div class="col-md-12"><hr></div>
					</div>
			</div>
		</div>
	</div>
	<div class="sm-right-ans-hints-sec py-2">
		<div class="add-question-sec">
			<div class="row">
				<div class="col-md-4">
					<label class="font-weight-bold">{{__('languages.questions.general_hints_english')}}</label>
				</div>
				<div class="col-md-8">

					{!! $QuestionData->general_hints_en !!}
				</div>
			</div>
		</div>
		<div class="add-question-sec">
			<div class="row">
				<div class="col-md-4">
						<label class="font-weight-bold">{{__('languages.questions.general_hints_chinese')}}:</label>
				</div>
				<div class="col-md-8">
					{!! $QuestionData->general_hints_ch !!}
				</div>
			</div>
		</div>
		{{-- <div class="add-question-sec">
			<div class="row">
				<div class="col-md-12">
					<hr>
				</div>
				<div class="col-md-2">
						<label class="font-weight-bold">{{__('languages.questions.full_solution_en')}}:</label>
				</div>
				<div class="col-md-10">
					{!! $QuestionData->full_solution_en !!}
				</div>
				</div>
			</div>
		</div>
		<div class="add-question-sec mt-2">
			<div class="row">
				<div class="col-md-2">
					<label class="font-weight-bold">{{__('languages.questions.full_solution_ch')}}:</label>
				</div>
				<div class="col-md-10">
					{!! $QuestionData->full_solution_ch !!}
				</div>
			</div>
		</div> --}}
	</div>
</div>
<script type="text/javascript">
	knowledgeNode('{{$QuestionData->naming_structure_code}}');
</script>