@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
				<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.user_activity.user_activities')}}</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
				<form class="addUserFilterForm" id="addUserFilterForm" method="get">	
					<div class="row">
					<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                            <select name="school_id"  class="form-control select-option">
                                <option value="">{{ __('languages.select_school') }}</option>
                                @if(!empty($schoolList))
                                    @foreach($schoolList as $school)
                                    <option value="{{$school->id}}" {{ request()->get('school_id') == $school['id'] ? 'selected' : '' }}>{{ $school->school_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('school_id'))
                                <span class="validation_error">{{ $errors->first('school_id') }}</span>
                            @endif
                        </div>

                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                            <select name="grade_id"  class="form-control select-option">
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

						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                            <select name="role_id"  class="form-control select-option">
                                <option value="">{{ __('languages.select_role') }}</option>
                                @if(!empty($roleList))
                                    @foreach($roleList as $role)
                                    <option value="{{$role->id}}" {{ request()->get('role_id') == $role->id ? 'selected' : '' }}>{{ $role->role_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('role_id'))
                                <span class="validation_error">{{ $errors->first('role_id') }}</span>
                            @endif
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.submit') }}</button>
                            </div>
                        </div>
                    </div>
				</form>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table class="table-responsive">
							    	<thead>
							        	<tr>
											<th class="first-head"><span>{{__('languages.user_activity.english_name')}}</span></th>
											<th class="first-head"><span>{{__('languages.user_activity.chinese_name')}}</span></th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">

									@if(!empty($userList))
										@foreach($userList as $user)
							        	<tr>
											<td>{{ ($user->name_en) ? App\Helpers\Helper::decrypt($user->name_en) : $user->name }}</td>
											<td>{{ ($user->name_ch) ? App\Helpers\Helper::decrypt($user->name_ch) : 'N/A'}}</td>
                                            <td class="btn-edit">
												<a href="{{ route('useractivity.show', $user->id)}}" class="" title="{{__('languages.view')}}">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</a>
											</td>
										</tr>
										@endforeach
										@endif	
							  </tbody>
							</table>
								<div>{{__('languages.showing')}} {{!empty($userList->firstItem()) ? $userList->firstItem() : 0}} {{__('languages.to')}} {{!empty($userList->lastItem()) ? $userList->lastItem() : 0}}
									{{__('languages.of')}}  {{$userList->total()}} {{__('languages.entries')}}
								</div>
									<div class="pagination-data">
										<div class="col-lg-9 col-md-9 pagintn">
											{{$userList->appends(request()->input())->links()}}
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
													<option value="{{$userList->total()}}" @if(app('request')->input('items') == $userList->total()) selected @endif >{{__('languages.all')}}</option>
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
					window.location = "{!! $userList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection