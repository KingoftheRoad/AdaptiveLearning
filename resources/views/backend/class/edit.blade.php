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
                            <h2 class="mb-4 main-title">{{ __('languages.grade_management.update_class') }}</h2>
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
                    <form class="class-form" method="post" id="editClassForm"  action="{{ route('class.update',$data->id) }}">
                        @csrf()
                        @method('patch')
                        <div class="form-row select-data">
                            <div class="form-group col-md-6 mb-50">
                                <label class="text-bold-600" for="name">{{ __('languages.grade') }}</label>
                                <select name="name" class="form-control select-option" id="name"  >
                                @if(!empty($GradeList))
                                <option value="" >{{__('languages.select_grade')}}</option>
                                    @foreach($GradeList as $grade)
                                        <option value="{{$grade->id}}" {{($data->id == $grade->id) ? 'selected' : ''}}>{{ $grade->name}}</option>
                                    @endforeach
                                @endif
                                </select>
                                @if($errors->has('name'))<span class="validation_error">{{ $errors->first('name') }}</span>@endif
                            </div>
                            {{-- <div class="form-group col-md-6 mb-50">
                                <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.grade') }}</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="{{ __('languages.grade') }} {{__('languages.grade_sample_example')}}" value="{{$data->name}}">
                                @if($errors->has('name'))<span class="validation_error">{{ $errors->first('name') }}</span>@endif
                            </div> --}}
                            <div class="form-group col-md-6 mb-50">
                                <label for="status">{{ __('languages.class') }}</label>
                                @if(!empty($data->classes))
                                    @php
                                    $className = array_column($data->classes->toArray(), 'name');
                                    @endphp
                                    <select name="class_type[]" class="form-control select-option" id="classType-select-option" multiple>
                                        @foreach(range('A', 'Z') as $alphabet)
                                        <option value="{{$alphabet}}" @if(in_array($alphabet,$className)) selected @endif>{{ strtoupper($alphabet)}}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('class_type'))<span class="validation_error">{{ $errors->first('class_type') }}</span>@endif
                                @endif
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