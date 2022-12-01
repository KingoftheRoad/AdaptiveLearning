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
								@if (Auth::user()->role_id == 1)
									<h2 class="mb-4 main-title">{{__('languages.test_template_management.test_template_detail')}}</h2>
								@else
									<h2 class="mb-4 main-title">{{__('languages.test_template_management.question_templates')}}</h2>
								@endif
								<div class="btn-sec">
									@if (in_array('test_template_management_create', $permissions))
										<a href="{{ route('test-template.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.test_template_management.add_test_template')}}</a>
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
				<form class="addTestTemplatesFilterForm" id="addTestTemplatesFilterForm" method="get">	
					<div class="row">


                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="difficulty_level"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="filter_test_template_difficult_lvl">
                                <option value="">{{ __('languages.test_template_management.difficulty_level') }}</option>
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
						@if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
								<select name="question_type"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="filter_test_template_template_type">
									<option value="">{{ __('languages.test_template_management.template_type') }}</option>
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
						@endif
						@if (Auth::user()->role_id == 1)
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
							<select name="status" class="form-control select-option" id="status">
								<option value="">{{__("languages.test_template_management.select_status")}}</option>
								<option value="active" {{ request()->get('status') == 'active' ? 'selected' : '' }}>{{__("languages.test_template_management.active")}}</option>
								<option value="inactive" {{ request()->get('status') == 'inactive' ? 'selected' : '' }}>{{__("languages.test_template_management.inactive")}}</option>
							</select>
						</div>
						@endif
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.test_template_management.search') }}</button>
                            </div>
                        </div>
                    </div>
				</form>	
				<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table>
							    	<thead>
							        	<tr>
							          		<th class="first-head">
											  <span >{{__('languages.name')}}</span>
											</th>
											@if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
												<th>
													<span>{{__('languages.test_template_management.template_type')}}</span>
												</th>
											@endif
											<th>
												<span>{{__('languages.test_template_management.difficulty_level')}}</span>
											</th>
											@if (Auth::user()->role_id == 1)
												<th>{{__('languages.status')}}</th>
											@endif
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($TestTemplatesList))
										@foreach($TestTemplatesList as $data)
							        	<tr>
											<td>{{ $data->name }}</td>
											@if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
												<td>
													{{$QuestionTypesList[$data->template_type]}}
												</td>
											@endif
											<td>
												<span class="">
													@for($i=1; $i <= $data->difficulty_level; $i++)
													<span style="font-size:150%;color:red;">&starf;</span>
													@endfor
												</span>
											</td>
											@if (Auth::user()->role_id == 1)
												<td>
													@if($data->status == 'active')
														<span class="badge badge-success">Active</span> 
													@else
													<span class="badge badge-primary">InActive</span> 
													@endif
												</td>
											@endif
											<td class="btn-edit">
											@if (in_array('test_template_management_update', $permissions))
												<a href="{{ route('test-template.edit', $data->id) }}" class="" title="Test Templates Question">
													<i class="fa fa-pencil" aria-hidden="true"></i>
												</a>
											@endif
											@if (in_array('test_template_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteTestTemplates" data-id="{{$data->id}}" title="Delete Test Templates">
													<i class="fa fa-trash" aria-hidden="true"></i>
												</a>
											@endif
											@if (!empty($data->id))
											@if (Auth::user()->role_id == 1)
												<a href="javascript:void(0);" class="pl-2" id="testtemplateShow" data-url="" data-id="{{$data->id}}" title="{{__('Question Show')}}">
												<i class="fa fa-eye" aria-hidden="true"></i>
											</a>
											@endif
											@if (Auth::user()->role_id == 2)
												<a href="javascript:void(0);" class="pl-2" id="testtemplateShow" data-url="-teacher" data-id="{{$data->id}}" title="{{__('Question Show')}}">
												<i class="fa fa-eye" aria-hidden="true"></i>
											</a>
											@endif
											@if (Auth::user()->role_id == 3)
												<a href="javascript:void(0);" class="pl-2" id="testtemplateShow" data-url="-student" data-id="{{$data->id}}" title="{{__('Question Show')}}">
												<i class="fa fa-eye" aria-hidden="true"></i>
											</a>
											@endif
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							  		</tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($TestTemplatesList->firstItem()) ? $TestTemplatesList->firstItem() : 0}} {{__('languages.to')}} {{!empty($TestTemplatesList->lastItem()) ? $TestTemplatesList->lastItem() : 0}}
								{{__('languages.of')}}  {{$TestTemplatesList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
									@if((app('request')->input('items'))==" ")
										{{ $TestTemplatesList->appends(request()->all())->links() }}
									@else
										{{$TestTemplatesList->appends(compact('items'))->links()}}
									@endif 
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
												<option value="{{$TestTemplatesList->total()}}" @if(app('request')->input('items') == $TestTemplatesList->total()) selected @endif >{{__('languages.all')}}</option>
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
				//for per Page on filteration hidden 
				var TotalFilterData = "{!! $TotalFilterData !!}";
				if((TotalFilterData > 0 && TotalFilterData < 11)){
					document.getElementById("pagination").style.visibility = "hidden";
					document.getElementById("per_page").style.visibility = "hidden";
				}
				/*for pagination add this script added by mukesh mahanto*/ 
					document.getElementById('pagination').onchange = function() {
					// window.location = window.location.href + "&items=" + this.value;			
					window.location = "{!! $TestTemplatesList->url(1) !!}&items=" + this.value;
				}; 
		</script>
		@include('backend.layouts.footer')
@endsection