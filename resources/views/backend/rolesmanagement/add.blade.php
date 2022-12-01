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
								<h2 class="mb-4 main-title">{{__('languages.role_and_permission.add_new_role')}}</h2>
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
                        
							<form class="user-form" method="post" id="addRolesForm"  action="{{ route('rolesmanagement.store') }}">
							@csrf()
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.role_name') }}</label>
                                        <input type="text" class="form-control" name="role_name" id="role_name" placeholder="{{__('languages.role_and_permission.role_name')}}" value="{{old('role_name')}}">
                                        @if($errors->has('role_name'))<span class="validation_error">{{ $errors->first('role_name') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                    <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <!-- <option value=''>{{ __('Select Status') }}</option> -->
                                                <option value="active" selected>{{__('languages.active')}}</option>
                                                <option value="inactive">{{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                    </div>
                                </div>
                                
                                <div class="form-row selecct-data">
                                    <div class="form-group col-md-4">
                                        <b><label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.permissions') }}</label></b>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <b><label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.create') }}</label></b>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <b><label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.read') }}</label></b>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <b><label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.update') }}</label></b>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <b><label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.role_and_permission.delete') }}</label></b>
                                    </div>
                                </div>
                                <div class="form-row selecct-data">
                                    @if(!empty($modules))
                                    @foreach($modules as $module)
                                    <div class="form-group col-md-4">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{$module->module_name}}</label>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" class="checkbox" name="permissions[]" id="permissions" value={{$module->module_slug."_create"}}>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" class="checkbox" name="permissions[]" id="permissions" value={{$module->module_slug."_read"}}>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" class="checkbox" name="permissions[]" id="permissions" value={{$module->module_slug."_update"}}>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" class="checkbox" name="permissions[]" id="permissions" value={{$module->module_slug."_delete"}}>
                                    </div>
                                    @endforeach
                                    @endif
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