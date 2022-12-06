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
								<h2 class="mb-4 main-title">{{__('languages.my_class.my_classes')}}</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
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
					{{-- <div class="btn-sec">
						<a href="{{ route('manual-assign-credit-point') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.manual_assign_credit_point')}}</a>
					</div> --}}
					<form class="displayStudentProfileFilterForm" id="displayStudentProfileFilterForm" method="get">	
						<div class="row">
							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="student_grade_id" id="student_grade_id">
										<option value='all'>{{ __('languages.all') }}</option>
										@if(!empty($gradesList))
											@foreach($gradesList as $grade)
											<option value="{{$grade->getClass->id}}" {{ (request()->get('student_grade_id')) == $grade->getClass->id ? 'selected' : '' }}>{{ $grade->getClass->name}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <select name="class_type_id[]" class="form-control select-option" id="classType-select-option" multiple >
                                        {!!$classTypeOptions!!}
                                    </select>
                                </div>
                            </div>
							<!-- For a Filtration on name,email & city -->
							<div class="col-lg-4 col-md-5">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="searchtext" value="{{request()->get('searchtext')}}" placeholder="{{__('languages.search_by_name')}},{{__('languages.email')}},{{__('languages.user_management.city')}}">
									@if($errors->has('searchtext'))
										<span class="validation_error">{{ $errors->first('searchtext') }}</span>
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
					<hr/>
					<div class="row">
						<div class="col-lg-3 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<label for="assignStudentIntoGroup">{{__('languages.assigned_student_to_group')}}</label>
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="assignStudentIntoGroup" id="assignStudentIntoGroup">
									<option value=''>{{ __('Select Group') }}</option>
									@if(!empty($GroupData))
										@foreach($GroupData as $group)
										<option value="{{$group->id}}">{{ $group->group_name}}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
							<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="selectAllStudentIntoGroup" class="selectAllStudentIntoGroup">
											</th>
											<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('email',__('languages.email'))</span></th>
											<th class="selec-head">@sortablelink('grade_id',__('languages.grade'))</th>
											<th class="selec-head">{{__('languages.class')}}</th>
											<th class="selec-head">{{__('languages.profile.class_student_number')}}</th>
											<th class="selec-head">@sortablelink('overall_ability',__('languages.overall_ability'))</th>
											<th class="selec-head">{{__('languages.credit_points')}}</th>
											<th class="selec-head">{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($studentList))
										@foreach($studentList as $User)
							        	<tr>
											<td><input type="checkbox" name="selectStudentIntoGroup"  class="selectStudentIntoGroup" data-userId="{{$User->id}}"></td>
											<td>{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name }}</td>
											<td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
											<td>{{ $User->email }}</td>
											<td class="classname_{{$User->id}}">{{$User->grades->name ?? 'N/A'}}</td>
											<td>{{ $User->getClassname($User->id) }}</td>
											<td>{{ ($User->class_student_number) ? $User->class_student_number : ''}}</td>
											@if($User->overall_ability != "")
												<td>{{number_format(round($User->overall_ability,2),2)}} <strong>({{$User->NormalizedOverAllAbility.'%'}})</strong></td>
											@else
												<td>---</td>
											@endif
											<td>{{ $User->getUserCreditPoints->no_of_credit_points ?? 0 }}</td>
											<td class="btn-edit">
												{{-- <a href="{{ route('teacher.student-profiles', $User->id) }}" class="" title="{{__('languages.my_class.view_profile')}}"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
												<a href="{{ route('student-profiles', $User->id) }}" class="" title="{{__('languages.my_class.view_profile')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
								<div>{{__('languages.showing')}} {{!empty($studentList->firstItem()) ? $studentList->firstItem() : 0}} {{__('languages.to')}} {{!empty($studentList->lastItem()) ? $studentList->lastItem() : 0}}
									{{__('languages.of')}}  {{$studentList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$studentList->appends(request()->input())->links()}}
										@elseif((app('request')->input('items'))!= null)
											{{$studentList->appends(request()->input())->links()}}
										@else
											{{$studentList->appends(compact('items'))->links()}}
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
												<option value="{{$studentList->total()}}" @if(app('request')->input('items') == $studentList->total()) selected @endif >{{__('languages.all')}}</option>
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
						window.location = "{!! $studentList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
		<script>
			var studentIds = [];
			//Select or Deselect Student into Group
			$(document).on("click",".selectStudentIntoGroup",function () {
				var studentid = $(this).data("userid");
				if($(this).is(":checked")){
					studentIds.push(studentid);
				}else{
					let index = studentIds.indexOf(studentid)
					let numberOfElementToRemove = 1;
					if (index !== -1) { studentIds.splice(index,numberOfElementToRemove)}
				}
			});
			// Assign Students into Group
			$(document).on("change","#assignStudentIntoGroup",function(){
				var peergroupid = $(this).val();
				if($(this).val() != ""){
					if(studentIds.length == 0){
						toastr.error(PLEASE_SELECT_STUDENT_FIRST)
						$("#assignStudentIntoGroup").val('').trigger('change');;

					}else{
						$.confirm({
							title: ARE_YOU_SURE_TO_ASSIGN_GROUP,
							content: CONFIRMATION,
							autoClose: "Cancellation|8000",
							buttons: {
								YES: {
									text: YES,
									action: function () {
										$("#cover-spin").show();
										$.ajax({
											url:BASE_URL +"/assign-student-in-group",
											type: "POST",
											data:{
												_token: $('meta[name="csrf-token"]').attr("content"),
												studentIds:studentIds,
												peergroupid:peergroupid,
											},
											success: function (response) {
												$("#cover-spin").hide();
													var data = JSON.parse(JSON.stringify(response));
												if (data.status === "success") {
													toastr.success(data.message);
												} else {
													toastr.error(data.message);
												}
											},
											error: function (response) {
												ErrorHandlingMessage(response);
											},
										});
									},
                    			},
                    		Cancellation: function () {},
                			},
            			});
					}
				}
			});

			//SelectAll Students At atime
			$(document).on("click",'.selectAllStudentIntoGroup',function(){
				if($(this).is(":checked")){
					$(".selectStudentIntoGroup").prop("checked", true);
					$("input:checkbox[name=selectStudentIntoGroup]:checked").each(function(){
						studentIds.push($(this).data('userid'));
					});
				}else{
					studentIds = [];
					$(".selectStudentIntoGroup").prop("checked", false);
				}
			});
		</script>
		@include('backend.layouts.footer')
@endsection