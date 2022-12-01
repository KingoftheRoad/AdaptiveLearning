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
								<h2 class="mb-4 main-title">{{__('languages.ai_calculated_difficulty.ai_calculated_difficulty_detail')}}</h2>
								<div class="btn-sec">
								 @if (in_array('ai_calculate_difficulty_create', $permissions))
									<a href="{{ route('ai-calculated-difficulty.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.ai_calculated_difficulty.add_new_difficulty')}}</a>
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
					<form class="addAiCalculatedFilterForm" id="addAiCalculatedFilterForm" method="get">	
					<div class="row">
 						
                        <div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="difficulty_lvl" id="difficulty_lvl">
                                    <option value=''>{{ __('languages.ai_calculated_difficulty.select_difficulty_level') }}</option>
                                    @if(!empty($difficultyLevels))
                                        @foreach($difficultyLevels as $difficultyLevel)
                                        <option value="{{$difficultyLevel['id']}}" {{ request()->get('difficulty_lvl') == $difficultyLevel['id'] ? 'selected' : '' }}>{{ $difficultyLevel['name']}}</option>
                                        @endforeach
                                    @endif
                                    
                                </select>
                            </div>
                            <span id="error-status"></span>
                            @if($errors->has('difficulty_lvl'))<span class="validation_error">{{ $errors->first('difficulty_lvl') }}</span>@endif
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="difficult_value" value="{{request()->get('difficult_value')}}" placeholder="Search By Difficult Value">
								@if($errors->has('difficult_value'))
                                	<span class="validation_error">{{ $errors->first('difficult_value') }}</span>
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
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>@sortablelink('difficulty_level','Difficulty Level')</span></th>
							          		<th class="first-head"><span>@sortablelink('title','Difficulty Value')</span></th>
                                            <th>@sortablelink('status','Status')</th>
											<th>{{__('Action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($AicalculatedList))
										@foreach($AicalculatedList as $aiCalculatedList)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<!-- <td>{{ $aiCalculatedList->difficulty_level}}</td> -->
                                            <td>
                                                <span class="">
													@for($i=1; $i <= $aiCalculatedList->difficulty_level; $i++)
													<span style="font-size:150%;color:red;">&starf;</span>
													@endfor
												</span>
                                            </td>
											<td>{{ $aiCalculatedList->title}}</td>
                                            <td>
												@if($aiCalculatedList->status=="active")
													<span class="badge badge-success">{{ucfirst($aiCalculatedList->status)}}</span>
												@else
													<span class="badge badge-danger">{{ucfirst($aiCalculatedList->status)}}</span>
												@endif
											</td>
											<td class="btn-edit">
											 @if (in_array('ai_calculate_difficulty_update', $permissions))
												<a href="{{ route('ai-calculated-difficulty.edit', $aiCalculatedList->id) }}" class=""><i class="fa fa-pencil" aria-hidden="true"></i></a>
											 @endif 
											 @if (in_array('ai_calculate_difficulty_delete', $permissions)) 
												<a href="javascript:void(0);" class="pl-2" id="deleteAiCalculatedDifficulty" data-id="{{$aiCalculatedList->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											 @endif 
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($AicalculatedList->firstItem()) ? $AicalculatedList->firstItem() : 0 }} {{__('languages.to')}} {{!empty($AicalculatedList->lastItem()) : $AicalculatedList->lastItem() : 0}}
								{{__('languages.of')}}  {{$AicalculatedList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$AicalculatedList->appends(request()->input())->links()}}
										@else
											{{$AicalculatedList->appends(compact('items'))->links()}}
										@endif 
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
												<option value="{{$AicalculatedList->total()}}" @if(app('request')->input('items') == $AicalculatedList->total()) selected @endif >{{__('languages.all')}}</option>
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
				//for per Page on filteration hidden 
				var TotalFilterData = "{!! $TotalFilterData !!}";
                        if((TotalFilterData > 0 && TotalFilterData <= 10)){
                            document.getElementById("pagination").style.visibility = "hidden";
                            document.getElementById("per_page").style.visibility = "hidden";
                        }
				/*for pagination add this script added by mukesh mahanto*/ 
				document.getElementById('pagination').onchange = function() {
						window.location = "{!! $AicalculatedList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
		@include('backend.layouts.footer')
@endsection