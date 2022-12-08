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
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.questions.question_bank')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									@if (in_array('question_bank_create', $permissions))
										<a href="{{ route('questions.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.add_new_question')}}</a>
									@endif
									@if(!App\Helpers\Helper::isExternalUserLogin())
										<a href="{{ route('questions.export') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.questions.export_question')}}</a>
									@endif
								</div>
							</div>
							<hr class="blue-line">
						</div>
					</div>
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
					<form class="addQuestionFilterForm" id="addQuestionFilterForm" method="get">	
					<div class="row">
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
							<select name="grade_id"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="question_filter_grade">
								<option value="">{{ __('languages.select_grade') }}</option>
								@if(!empty($gradeList))
									@foreach($gradeList as $grade)
									<option value="{{$grade->id}}" {{ request()->get('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name}}</option>
									@endforeach
								@endif
							</select>
							@if($errors->has('grade_id'))
								<span class="validation_error">{{ $errors->first('grade_id') }}</span>
							@endif
						</div>
						<div class="col-lg-2 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<input type="text" class="input-search-box mr-2" name="question_code" value="{{request()->get('question_code')}}" placeholder="{{__('languages.questions.search_by_question_code')}}">
							</div>
						</div>
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
							<select name="difficulty_level"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id ="question_filter_difficulty">
								<option value="">{{ __('languages.questions.difficulty_level') }}</option>
								@if(!empty($difficultyLevels))
									@foreach($difficultyLevels as $difficultyLevel)
									<option value="{{$difficultyLevel['id']}}" {{ request()->get('difficulty_level') == $difficultyLevel['id'] ? 'selected' : '' }}>{{ $difficultyLevel['name']}}</option>
									@endforeach
								@endif
							</select>
							@if($errors->has('difficulty_level'))
								<span class="validation_error">{{ $errors->first('difficulty_level') }}</span>
							@endif
						</div>
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
							<select name="question_type"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="question_filter_question_type">
								<option value="">{{ __('languages.questions.question_type') }}</option>
								@if(!empty($QuestionTypes))
									@foreach($QuestionTypes as $question_type)
									<option value="{{$question_type['id']}}" {{ request()->get('question_type') == $question_type['id'] ? 'selected' : '' }}>{{ $question_type['name']}}</option>
									@endforeach
								@endif
							</select>
							@if($errors->has('question_type'))
								<span class="validation_error">{{ $errors->first('question_type') }}</span>
							@endif
						</div>
						<div class="col-lg-2 col-md-4">
							<div class="select-lng pt-2 pb-2">
								<select class="selectpicker form-control selectpicker"  data-show-subtext="true" data-live-search="true" name="Status" id="question_filter_status">
									<option value=''>{{ __('languages.select_status') }}</option>
									@if(!empty($statusList))
										@foreach($statusList as $status)
										<option value="{{$status['id']}}" {{ request()->get('Status') == $status['id'] ? 'selected' : '' }}>{{ $status['name']}}</option>
										@endforeach
									@endif
								</select>
								@if($errors->has('Status'))
									<span class="validation_error">{{ $errors->first('Status') }}</span>
								@endif
							</div>
						</div>

						<div class="col-lg-2 col-md-4">
							<div class="select-lng pt-2 pb-2">
								<select class="selectpicker form-control selectpicker"  data-show-subtext="true" data-live-search="true" name="question_approve" id="question_approve">
										<option value="">{{__('languages.is_approved_question')}}</option>
										<option value="yes" {{ request()->get('question_approve') == 'yes' ? 'selected' : '' }}>{{__('languages.approve')}}</option>
										<option value="no" {{ request()->get('question_approve') == 'no' ? 'selected' : '' }}>{{__('languages.not_approve')}}</option>
								</select>
								@if($errors->has('question_approve'))
									<span class="validation_error">{{ $errors->first('question_approve') }}</span>
								@endif
							</div>
						</div>

						<div class="col-lg-2 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.questions.search') }}</button>
							</div>
						</div>
					</div>
					</form>
					<hr class="blue-line">
					<div class="row">
						<div class="col-lg-2 col-md-4">
							<div class="select-lng pt-2 pb-2">
								<label>{{__('languages.update_question_verification')}}</label>
								<select class="selectpicker form-control selectpicker"  data-show-subtext="true" data-live-search="true" name="question_verification_status" id="question_verification_status">
										<option value="">{{__('languages.is_approved_question')}}</option>
										<option value="yes" {{ request()->get('question_approve') == 'yes' ? 'selected' : '' }}>{{__('languages.approve')}}</option>
										<option value="no" {{ request()->get('question_approve') == 'no' ? 'selected' : '' }}>{{__('languages.not_approve')}}</option>
								</select>
							</div>
						</div>
					</div>
					<hr class="blue-line">
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table class="table-responsive">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox" id="checkbox-all-question">
											</th>
							          		<th class="first-head" style="width: 230px;">
											  <span class="question-code">@sortablelink('naming_structure_code',__('languages.questions.question_code'))</span>
											</th>
											<th>@sortablelink('question_en',__('languages.questions.question'))</th>
											<th class="selec-opt">
												<span>@sortablelink('is_approved',__('languages.is_approved_question'))</span>
											</th>
											<th class="selec-opt question-difficulty-level">
												<span>@sortablelink('dificulaty_level',__('languages.questions.difficulty_level'))</span>
											</th>
											<th class="selec-opt question-difficulty-level">
												<span>{{__('languages.pre_defined_difficulty')}}</span>
											</th>
											<th class="selec-opt question-difficulty-level">
												<span>@sortablelink('ai_difficulty_value',__('languages.ai_difficulty'))</span>
											</th>
											<th class="question-status">@sortablelink('status',__('languages.status')) </th>
											@if(!App\Helpers\Helper::isExternalUserLogin())
											<th>{{__('languages.action')}}</th>
											@endif
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($QuestionList))
										@foreach($QuestionList as $Question)
							        	<tr>
											<td><input type="checkbox" name="chk-select-question" class="checkbox chk-select-question" value="{{$Question->id}}"></td>
											<td>{{ $Question->naming_structure_code }}</td>
											<td class="table-row-text-word-wrap"><?php echo $Question->{'question_'.app()->getLocale()}; ?></td>
											<td>
												@if($Question->is_approved == 'yes')
													<span class="badge badge-success">{{__('languages.approved')}}</span> 
												@else
												<span class="badge badge-danger">{{__('languages.not_approved')}}</span> 
												@endif
											</td>
											<td>
												<span class="">
													@for($i=1; $i <= $Question->dificulaty_level; $i++)
													<span style="font-size:150%;color:red;">&starf;</span>
													@endfor
												</span>
											</td>
											<td>{{$Question->ai_difficulty_value}}</td>
											<td>
												@if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$Question->dificulaty_level]))
													{{ $PreConfigurationDifficultyLevel[$Question->dificulaty_level] }}
												@endif
											</td>
											<td>
												@if($Question->status == 1) 
													<span class="badge badge-success">{{__('languages.publish')}}</span> 
												@else 
												<span class="badge badge-primary">{{__('languages.questions.save_draft')}}</span> 
												@endif
											</td>
											@if(!App\Helpers\Helper::isExternalUserLogin())
												<td class="btn-edit">
													<a href="javascript:void(0);" class="pl-2 preview_question_list"  data-id="{{$Question->id}}" title="{{__('languages.questions.preview_question')}}">
														<i class="fa fa-eye" aria-hidden="true"></i>
													</a>
												@if (in_array('question_bank_update', $permissions))
													<a href="{{ route('questions.edit', $Question->id) }}" class="pl-2" title="{{__('languages.edit')}}">
														<i class="fa fa-pencil" aria-hidden="true"></i>
													</a>
												@endif
												@if (in_array('question_bank_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deleteQuestion" data-id="{{$Question->id}}" title="{{__('languages.delete')}}">
														<i class="fa fa-trash" aria-hidden="true"></i>
													</a>
												@endif
												
												</td>
											@endif
										</tr>
										@endforeach
										@endif
							  		</tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($QuestionList->firstItem()) ? $QuestionList->firstItem() : 0}} {{__('languages.to')}} {{!empty($QuestionList->lastItem()) ? $QuestionList->lastItem() : 0}}
									{{__('languages.of')}}  {{$QuestionList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$QuestionList->appends(request()->input())->links()}}
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if($items == 10) selected @endif >10</option>
												<option value="20" @if($items == 20) selected @endif >20</option>
												<option value="25" @if($items == 25) selected @endif >25</option>
												<option value="30" @if($items == 30) selected @endif >30</option>
												<option value="40" @if($items == 40) selected @endif >40</option>
												<option value="50" @if($items == 50) selected @endif >50</option>
												<option value="{{$QuestionList->total()}}" @if($items == $QuestionList->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
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
		      <div class="modal-body" id="question_data">
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.close')}}</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- End Preview Question Modal -->
		<script>
			$(document).on("click", ".preview_question_list", function (){
				$questionId = $(this).data('id');
				PreviewQuestionList($questionId);
			});
			/*for pagination add this script added by mukesh mahanto*/ 
				document.getElementById('pagination').onchange = function() {
				// window.location = window.location.href + "&items=" + this.value;			
				window.location = "{!! $QuestionList->url(1) !!}&items=" + this.value;
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection