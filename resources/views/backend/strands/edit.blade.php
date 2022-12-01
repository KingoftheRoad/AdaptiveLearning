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
								<h2 class="mb-4 main-title">{{__('languages.strands_management.update_strands')}}</h2>
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
                        
							<form class="user-form" method="post" id="updateStrandsForm"  action="{{ route('strands.update',$StrandsData->id) }}">
							@csrf()
                            @method('patch')
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_activity.english_name') }}</label>
                                        <input type="text" class="form-control" name="name_en" id="strand_name_en" placeholder="{{ __('languages.user_activity.english_name') }}" value="{{$StrandsData->name_en}}">
                                        @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.user_activity.chinese_name') }}</label>
                                        <input type="text" class="form-control" name="name_ch" id="strand_name_ch" placeholder="{{ __('languages.user_activity.chinese_name') }}" value="{{$StrandsData->name_ch}}">
                                        @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_ch') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.code') }}</label>
                                        <input type="text" class="form-control" name="code" id="strand_code" placeholder="{{ __('languages.code') }}" value="{{$StrandsData->code}}">
                                        @if($errors->has('code'))<span class="validation_error">{{ $errors->first('code') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6">
                                    <label for="users-list-role">{{ __('languages.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <option value="1" @if($StrandsData->status == "1") selected @else '' @endif>{{__('languages.active')}}</option>
                                                <option value="0" @if($StrandsData->status == "0") selected @else '' @endif>{{__('languages.inactive')}}</option>
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