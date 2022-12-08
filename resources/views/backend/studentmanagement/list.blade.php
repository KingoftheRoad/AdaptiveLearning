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
								<h2 class="mb-4 main-title">{{__('languages.student_management.student_details')}}</h2>	
								<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('student_management_create', $permissions))
									<a href="{{ route('Student.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.student_management.add_new_student')}}</a>
								@endif
								{{-- <a href="{{ url('school/class/assign-students',auth()->user()->school_id) }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.student_management.assign_students')}}</a> --}}
								<a href="{{ route('ImportStudents') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.student_management.assign_student_with_import')}}</a>
								<a href="{{ route('students-export') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.export_students')}}</a>
								<a href="{{ route('student.import.upgrade-school-year') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.upgrade_year_via_csv')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					{{-- Class Promtion in year need to upgrade so some time this code is comment --}}
					{{-- <div class="row">
						<input type="hidden" id="school-id" value="{{ Auth::user()->id }}">
						<div class="col-lg-3 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<select name="grade_id" class="form-control select-option" id="class-promotion-grade-id">
									@if(!empty($gradeList))
										<option value="">{{ __('languages.select_grade') }}</option>
										@if(!empty($gradeList))
											@foreach($gradeList as $grade)
												<option value="{{$grade->grades->id}}">{{ $grade->grades->name}}</option>
											@endforeach
										@endif
									@else
										<option value="">{{ __('languages.no_grade_available') }}</option>
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <select name="class_id" class="form-control select-option" id="classType-option" >
									<option value="">{{ __('languages.select_class') }}</option>
                                </select>
                            </div>
                        </div>
						<div class="col-lg-2 col-md-3">
							<button type="button" id="class-promotion" class="dark-blue-btn btn btn-primary mt-2 mb-2">{{ __('languages.student_management.class_promotion') }}</button>
						</div>
					</div> --}}
					
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
					<!-- for filtration -->
					<form class="StudentFilterForm" id="StudentFilterForm" method="get">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" id="search" name="search" value="{{request()->get('search')}}" placeholder="{{__('languages.search_by_name')}} | {{__('languages.email')}}">
                            </div>
                        </div>
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">
							<select name="student_grade_id"  class="form-control select-option" id ="student_grade_id">
								<option value="">{{ __('languages.user_management.grade') }}</option>
								@if(!empty($gradeList))
									@foreach($gradeList as $grade)
										<option value="{{$grade->grades->id}}" {{ request()->get('student_grade_id') == $grade->grades->id ? 'selected' : '' }}>{{ $grade->grades->name}}</option>
									@endforeach
								@endif
							</select>
							@if($errors->has('student_grade_id'))
								<span class="validation_error">{{ $errors->first('student_grade_id') }}</span>
							@endif
						</div>

						<div class="col-lg-2 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<select name="class_type_id[]" class="form-control select-option" id="classType-select-option" multiple >
									{!!$classTypeOptions!!}
								</select>
							@if($errors->has('class_type_id'))<span class="validation_error">{{ $errors->first('class_type_id') }}</span>@endif
							</div>
						</div>
                       
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="status"  class="form-control select-option">
                                <option value="">{{ __('languages.status') }}</option>
                                <option value="active" {{ request()->get('status') == 'active' ? 'selected' : '' }}>{{__('languages.active')}}</option>
                                <option value="inactive" {{ request()->get('status') == 'inactive' ? 'selected' : '' }}>{{__('languages.inactive')}}</option>
                                <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>{{__('languages.pending')}}</option>
                            </select>
                            @if($errors->has('status'))
                                <span class="validation_error">{{ $errors->first('status') }}</span>
                            @endif
                        </div>
						<div class="col-lg-2 col-md-2">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" id="classStudentNumber" name="classStudentNumber" value="{{request()->get('classStudentNumber')}}" placeholder="{{__('languages.profile.class_student_number')}}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter_data" value="filter_data" class="btn-search">{{ __('languages.search') }}</button>
                            </div>
                        </div>
                    </div>
                    </form>

					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec restrict-overflow">
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="student-class-promotion-check-all" id="student-class-promotion-check-all" class="checkbox" value="">
											</th>
											<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('email',__('languages.email'))</span></th>
											<th class="selec-head">@sortablelink('grade_id',__('languages.grade'))</th>
											<th class="selec-head">@sortablelink('class_id',__('languages.class_within_grade'))</th>

											<th class="selec-head">@sortablelink('class_student_number',__('languages.profile.class_student_number'))</th>
											<th class="selec-head">@sortablelink('permanent_reference_number',__('languages.std_number'))</th>
											<th class="selec-head">@sortablelink('student_number_within_class',__('languages.student_number_with_class'))</th>
											<th class="selec-head">@sortablelink('class',__('languages.class_and_grade'))</th>

											<th class="selec-head">@sortablelink('status',__('languages.status'))</th>
											<th class="selec-head">{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($UsersList))
										@foreach($UsersList as $User)
							        	<tr>
											<td><input type="checkbox" name="student-class-promotion" class="checkbox student-Ids" value="{{$User->id}}"></td>
											<td>{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name }}</td>
											<td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
											<td>{{ $User->email }}</td>
											<td class="gradesname_{{$User->id}}">{{$User->grades->name ?? 'N/A'}}</td>
											<td class="classname_{{$User->id}}">{{ $User->getClassname($User->id) }}</td>
											<td>
												{{ ($User->class_student_number) ? $User->class_student_number : 'N/A'}}
											</td>
											
											<td>{{ ($User->permanent_reference_number) ? $User->permanent_reference_number : 'N/A'}}</td>
											<td>{{ ($User->student_number_within_class) ? $User->student_number_within_class : 'N/A'}}</td>
											<td>{{ ($User->class) ? $User->class : 'N/A'}}</td>

											<td>
												@if($User->status === 'pending')
													<span class="badge badge-warning">{{__('languages.pending')}}</span>
												@elseif($User->status == 'active')
													<span class="badge badge-success">{{__('languages.active')}}</span> 
												@else
												<span class="badge badge-primary">{{__('languages.inactive')}}</span> 
												@endif
											</td>
											<td class="btn-edit">
												@if (in_array('student_management_update', $permissions))
												<a href="{{ route('Student.edit', $User->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
												@endif
												@if (in_array('student_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteStudent" data-id="{{$User->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
												@endif
												<a href="{{ route('class-promotion-history', $User->id) }}" class="pl-2" title="{{__('languages.class_promotion')}}"><i class="fa fa-history" aria-hidden="true"></i></a>
												@if(Auth::user()->role_id == 5)
													@if (in_array('change_password_update', $permissions))
														<a href="javascript:void(0);" class="pl-2 changeUserPassword"  data-id="{{$User->id}}" title="{{__('languages.change_password')}}"><i class="fa fa-unlock" aria-hidden="true"></i></a>
													@endif
												@endif
												{{-- <a href="{{ route('student.student-profiles', $User->id) }}" class="pl-2" title="{{__('languages.my_class.view_profile')}}"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
												<a href="{{ route('student-profiles', $User->id) }}" class="pl-2" title="{{__('languages.my_class.view_profile')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
											</td>
										</tr>
										@endforeach
									@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($UsersList->firstItem()) ? $UsersList->firstItem() : 0 }} {{__('languages.to')}} {{!empty($UsersList->lastItem()) ? $UsersList->lastItem() : 0}}
								{{__('languages.of')}}  {{$UsersList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$UsersList->appends(request()->input())->links()}}
										@else
											{{$UsersList->appends(compact('items'))->links()}}
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
												<option value="{{$UsersList->total()}}" @if(app('request')->input('items') == $UsersList->total()) selected @endif >{{__('languages.all')}}</option>
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
						window.location = "{!! $UsersList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
		@include('backend.layouts.footer')
@endsection