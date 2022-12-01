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

                    <form class="displayStudentActivityForm" id="displayStudentActivityForm" method="get">	
						<div class="row">
						
							<div class="col-lg-3 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="learning_type" id="learning_type">
										<option value=''>{{ __('Select Learning Type') }}</option>
										@if(!empty($learningTypes))
											@foreach($learningTypes as $learningType)
											<option value="{{$learningType['id']}}" {{ request()->get('learning_type') == $learningType['id'] ? 'selected' : '' }}>{{ $learningType['name']}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('Search') }}</button>
								</div>
							</div>
						</div>
					</form>
                  @if(request()->get('learning_type')==3)
				  	@include('backend.student.mylearning_test')
				  @endif
				</div>
			</div>
	      </div>
		</div>
		
		@include('backend.layouts.footer')
@endsection