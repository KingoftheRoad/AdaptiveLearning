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
								<h2 class="mb-4 main-title">{{__('languages.nodes.add_new_node')}}</h2>
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
							<form class="user-form" method="post" id="addNodesForm"  action="{{ route('nodes.store') }}">
							@csrf()
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('languages.nodes.parent_node') }}</label>
                                        <fieldset class="">
                                            <select class="selectpicker form-control main_node_id_add" data-show-subtext="true" data-live-search="true" name="main_node_id[]" id="main_node_id" multiple>
                                                @if(!empty($NodesList))
                                                {!! $NodesList !!}
                                                @endif   
                                            </select>
                                            @if($errors->has('main_node_id'))<span class="validation_error">{{ $errors->first('main_node_id') }}</span>@endif
                                        </fieldset>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.nodes.nodes_id') }}</label>
                                        <input type="text" class="form-control" name="node_id" id="node_id" placeholder="{{__('languages.nodes.node_id')}}" value="{{old('node_id')}}">
                                        @if($errors->has('node_id'))<span class="validation_error">{{ $errors->first('node_id') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.nodes.title_en') }}</label>
                                        <input type="text" class="form-control" name="node_title_en" id="title_en" placeholder="{{ __('languages.nodes.title_en') }}" value="{{old('title_en')}}">
                                        @if($errors->has('node_title_en'))<span class="validation_error">{{ $errors->first('node_title_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{ __('languages.nodes.title_ch') }}</label>
                                        <input type="text" class="form-control" name="node_title_ch" id="title_ch" placeholder="{{ __('languages.nodes.title_ch') }}" value="{{old('title_ch')}}">
                                        @if($errors->has('node_title_ch'))<span class="validation_error">{{ $errors->first('node_title_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.nodes.weakness_name_en') }}</label>
                                        <input type="text" class="form-control" id="weakness_name_en" name="weakness_name_en" placeholder="{{ __('languages.nodes.weakness_name_en') }}" value="{{old('weakness_name_en')}}">
                                        @if($errors->has('weakness_name_en'))<span class="validation_error">{{ $errors->first('weakness_name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.nodes.weakness_name_ch') }}</label>
                                        <input type="text" class="form-control" id="weakness_name_ch" name="weakness_name_ch" placeholder="{{ __('languages.nodes.weakness_name_ch') }}" value="{{old('weakness_name_ch')}}">
                                        @if($errors->has('weakness_name_ch'))<span class="validation_error">{{ $errors->first('weakness_name_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.nodes.description_en') }}</label>
                                        <textarea class="form-control" name="description_en" id="description_en" placeholder="{{ __('languages.nodes.description_en') }}" value="">{{old('description_en')}}</textarea>
                                        @if($errors->has('description_en'))<span class="validation_error">{{ $errors->first('description_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.nodes.description_ch') }}</label>
                                        <textarea class="form-control" name="description_ch" id="description_ch" placeholder="{{ __('languages.nodes.description_ch') }}" value="">{{old('description_ch')}}</textarea>
                                        @if($errors->has('description_ch'))<span class="validation_error">{{ $errors->first('description_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <option value="active" selected>{{__('languages.active')}}</option>
                                                <option value="inactive">{{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row">
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