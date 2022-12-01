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
								<h2 class="mb-4 main-title">{{__('languages.pre_configure_difficulty.pre_configure_difficulty_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								 @if (in_array('pre_configure_difficulty_create', $permissions)) 
									<a href="{{ route('pre-configure-difficulty.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.pre_configure_difficulty.add_new_difficulty')}}</a>
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
					<form class="addpreConfigureFilterForm" id="addpreConfigureFilterForm" method="get">	
					{{-- <div class="row">
 						
                        <div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="difficulty_lvl" id="difficulty_lvl">
                                    <option value=''>{{ __('languages.pre_configure_difficulty.select_difficulty_level') }}</option>
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
                                <input type="text" class="input-search-box mr-2" name="difficult_value" value="{{request()->get('difficult_value')}}" placeholder="{{__('languages.search_by_difficult_value')}}">
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
                    </div> --}}
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
											<th class="first-head"><span>@sortablelink('difficulty_level_name_en',__('languages.english_name'))</span></th>
											<th class="first-head"><span>@sortablelink('difficulty_level_name_ch',__('languages.chinese_name'))</span></th>
											<th class="first-head"><span>@sortablelink('difficulty_level',__('languages.difficulty_level'))</span></th>
											<th class="first-head"><span>{{__('languages.difficulty_value')}}</span></th>
											<th class="first-head"><span>{{__('languages.color')}}</span></th>
                                            <th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($preConfigureLists))
										@foreach($preConfigureLists as $preConfigureList)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{$preConfigureList->difficulty_level_name_en}}</td>
											<td>{{$preConfigureList->difficulty_level_name_ch}}</td>
                                            <td>
                                                <span class="">
													@for($i=1; $i <= $preConfigureList->difficulty_level; $i++)
													<span style="font-size:150%;color:red;">&starf;</span>
													@endfor
												</span>
                                            </td>
											<td>{{$preConfigureList->title}}</td>
											<td>
												<span class="dot-color" style="background-color:{{$preConfigureList->difficulty_level_color}};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
												{{$preConfigureList->difficulty_level_color}}
											</td>
                                            <td>
												@if($preConfigureList->status=="active")
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
											 @if (in_array('pre_configure_difficulty_update', $permissions)) 
												<a href="{{ route('pre-configure-difficulty.edit', $preConfigureList->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											 @endif 
											@if (in_array('pre_configure_difficulty_delete', $permissions)) 
												<a href="javascript:void(0);" class="pl-2" id="deletePreconfigureDifficulty" data-id="{{$preConfigureList->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											 @endif 
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($preConfigureLists->firstItem()) ? $preConfigureLists->firstItem() : 0 }} {{__('languages.to')}} {{!empty($preConfigureLists->lastItem()) ? $preConfigureLists->lastItem() : 0 }}
								{{__('languages.of')}}  {{$preConfigureLists->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$preConfigureLists->appends(request()->input())->links()}}
										@else
											{{$preConfigureLists->appends(compact('items'))->links()}}
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
												<option value="{{$preConfigureLists->total()}}" @if(app('request')->input('items') == $preConfigureLists->total()) selected @endif >{{__('languages.all')}}</option>
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
						window.location = "{!! $preConfigureLists->url(1) !!}&items=" + this.value;	
				}; 
		</script>
		@include('backend.layouts.footer')
@endsection