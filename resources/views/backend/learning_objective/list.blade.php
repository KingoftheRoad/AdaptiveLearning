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
								<div class="btn-sec">
								@if (in_array('strands_management_create', $permissions))
								<h2 class="mb-4 main-title">{{__('languages.learning_objectives_management.learning_objectives_detail')}}</h2>
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									<a href="{{ route('learning-objective.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.learning_objectives_management.add_learning_objectives')}}</a>
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
					<form class="addUserFilterForm" id="addUserFilterForm" method="get">	
					<div class="row">
 						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="LearningObjectiveName" value="{{request()->get('LearningObjectiveName')}}" placeholder="{{__('languages.learning_objectives_management.search_by_learning_objectives_title')}}">
								@if($errors->has('LearningObjectiveName'))
                                	<span class="validation_error">{{ $errors->first('LearningObjectiveName') }}</span>
                            	@endif
                            </div>
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="LearningObjectiveCode" value="{{request()->get('LearningObjectiveCode')}}" placeholder="{{__('languages.learning_objectives_management.search_by_learning_objectives_code')}}">
								@if($errors->has('LearningObjectiveCode'))
                                	<span class="validation_error">{{ $errors->first('LearningObjectiveCode') }}</span>
                            	@endif
                            </div>
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="Status" id="Status">
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
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="is_available_questions" id="is_available_questions">
									<option value=''>{{ __('languages.having_question') }}</option>
									<option value="yes" @if(request()->get('is_available_questions') == "yes") selected @else '' @endif>{{__('languages.yes')}}</option>
									<option value="no" @if(request()->get('is_available_questions') == "no") selected @else '' @endif>{{__('languages.no')}}</option>
								</select>
								@if($errors->has('Status'))
                                	<span class="validation_error">{{ $errors->first('Status') }}</span>
                            	@endif
                            </div>
                        </div>
						
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                            </div>
                        </div>
                    </div>
				</form>
					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec">
								<table class="display table-responsive" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
                                            <th class="first-head"><span>@sortablelink('foci_number',__('languages.learning_objectives_management.foci_number'))</span></th>
							          		<th class="first-head"><span>@sortablelink('title_en',__('languages.user_activity.english_name'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('title_ch',__('languages.user_activity.chinese_name'))</span></th>
                                            <th class="selec-opt"><span>@sortablelink('code',__('languages.code'))</span></th>
                                            <th>@sortablelink('is_available_questions',__('languages.having_question'))</th>
											<th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($LearningObjectivesData))
										@foreach($LearningObjectivesData as $learningObjective)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
                                            <td>{{ $learningObjective->foci_number}}</td>
											<td>{{ $learningObjective->title_en}}</td>
                                            <td>{{ ($learningObjective->title_ch)}}</td>
                                            <td>{{ ($learningObjective->code) }}</td>
											<td>
												@if($learningObjective->is_available_questions=="yes")
													<span class="badge badge-success">{{__('languages.yes')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.no')}}</span>
												@endif
											</td>
                                            <td>
												@if($learningObjective->status=="1")
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
											@if (in_array('learning_objectives_management_update', $permissions))
												<a href="{{ route('learning-objective.edit', $learningObjective->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif
											@if (in_array('learning_objectives_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteLearningObjective" data-id="{{$learningObjective->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($LearningObjectivesData->firstItem()) ? $LearningObjectivesData->firstItem() : 0}} {{__('languages.to')}} {{!empty($LearningObjectivesData->lastItem()) ? $LearningObjectivesData->lastItem() : 0}}
								{{__('languages.of')}}  {{$LearningObjectivesData->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$LearningObjectivesData->appends(request()->input())->links()}}
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$LearningObjectivesData->total()}}" @if(app('request')->input('items') == $LearningObjectivesData->total()) selected @endif >{{__('languages.all')}}</option>
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
		<script>
			/*for pagination add this script added by mukesh mahanto*/ 
			document.getElementById('pagination').onchange = function() {
					window.location = "{!! $LearningObjectivesData->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection