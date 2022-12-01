@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{ __('languages.module_management.update_module') }}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
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
                        <form class="user-form" method="post" id="editModulesForm"  action="{{ route('modulesmanagement.update',$module->id) }}">
							@csrf()
                            @method('patch')
                            <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.module_management.module_name') }}</label>
                                        <input type="text" class="form-control" name="module_name" id="module_name" placeholder="{{__('languages.module_management.module_name')}}" value="{{$module->module_name}}">
                                        @if($errors->has('module_name'))<span class="validation_error">{{ $errors->first('module_name') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="active" {{($module->status === "active") ? 'selected' : ''}}>{{__('languages.active')}}</option>
                                                <option value="inactive" {{($module->status === "inactive") ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                    </div>
                            </div>
                             
                            <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                        </div>
                                    </div>
							    </div>  
						</form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')  
@endsection