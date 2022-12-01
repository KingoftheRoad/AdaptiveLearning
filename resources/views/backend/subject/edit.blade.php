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
                            <h2 class="mb-4 main-title">{{ __('languages.subjects.update_subject') }}</h2>
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
                    <form class="subject-form" method="post" id="editSubjectsForm"  action="{{ route('subject.update',$data->id) }}">
                        @csrf()
                        @method('patch')
                        <div class="form-row select-data">
                                
                            <div class="form-group col-md-6 mb-50">
                                <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.name') }}</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('languages.name') }}" value="{{$data->name}}">
                                @if($errors->has('name'))<span class="validation_error">{{ $errors->first('name') }}</span>@endif
                            </div>
                            <div class="form-group col-md-6 mb-50">
                                <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.code') }}</label>
                                <input type="text" class="form-control" name="code" id="code" placeholder="{{ __('languages.code') }}" value="{{$data->code}}">
                                @if($errors->has('code'))<span class="validation_error">{{ $errors->first('code') }}</span>@endif
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="class_ids">{{ __('languages.class') }}</label>
                                <fieldset class="form-group">
                                    <select class="selectpicker form-control multipleclass_ids" data-show-subtext="true" data-live-search="true" name="class_ids[]" id="class_ids" multiple>
                                    @if(!empty($classList))
                                        @foreach($classList as $itam)
                                        <option value="{{$itam->grades->id}}" @if(in_array($itam->grades->id,$existingclassIds)) selected @endif>{{$itam->grades->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">{{ __('languages.no_available_class') }}</option>
                                    @endif
                                    </select>
                                    @if($errors->has('class_id'))<span class="validation_error">{{ $errors->first('class_id') }}</span>@endif
                                </fieldset>
                            </div>
                            <div class="form-group col-md-6 mb-50">
                                <label for="id_end_time">{{ __('languages.status') }}</label>
                                <select name="status" class="form-control select-option" id="status">
                                    <option value="1" {{ $data->status == "1" ? 'selected' : '' }}>{{__("languages.active")}}</option>
                                    <option value="0" {{ $data->status == "0" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
                                </select>
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