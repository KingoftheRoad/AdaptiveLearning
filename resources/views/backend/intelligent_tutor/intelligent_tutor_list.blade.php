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
        <style>
            .wrs_editor .wrs_tickContainer{display:none !important;}
        </style>
        <style>
            /* audio width set start*/
            audio { width: 150px; display: block; margin:20px; }
            /* audio width set end */
            body {font-family: Arial, Helvetica, sans-serif;}

            #myImg {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            }

            #myImg:hover {opacity: 0.7;}

            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                padding-top: 100px; /* Location of the box */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
            }

            /* Modal Content (image) */
            .modal-content {
                margin: auto;
                display: block;
                width: 80%;
                max-width: 700px;
            }

            /* Caption of Modal Image */
            #caption {
                margin: auto;
                display: block;
                width: 80%;
                max-width: 700px;
                text-align: center;
                color: #ccc;
                padding: 10px 0;
                height: 150px;
            }

            /* Add Animation */
            .modal-content, #caption {  
                -webkit-animation-name: zoom;
                -webkit-animation-duration: 0.6s;
                animation-name: zoom;
                animation-duration: 0.6s;
            }

            @-webkit-keyframes zoom {
                from {-webkit-transform:scale(0)} 
                to {-webkit-transform:scale(1)}
            }

            @keyframes zoom {
                from {transform:scale(0)} 
                to {transform:scale(1)}
            }

            /* The Close Button */
            .close {
                position: absolute;
                top: 15px;
                right: 35px;
                color: #f1f1f1;
                font-size: 40px;
                font-weight: bold;
                transition: 0.3s;
            }

            .close:hover,
            .close:focus {
                color: #bbb;
                text-decoration: none;
                cursor: pointer;
            }

            /* 100% Image Width on Smaller Screens */
            @media only screen and (max-width: 700px){
                .modal-content {
                    width: 100%;
                }
            }
        </style>
        <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
            @include('backend.layouts.sidebar')
            <div id="content" class="pl-2 pb-5">
                @include('backend.layouts.header')
                    <div class="sm-right-detail-sec pl-5 pr-5">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="sec-title">
                                        <h4 class="mb-4 main-title">{{ __('languages.intelligent_tutor') }}</h4>
                                    </div>
                                    <div class="btn-sec">
                                        <a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
                                        @if(in_array('upload_documents_create',$permissions))
                                            <a href="{{ route('intelligent-tutor.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.upload_document.add_new_document')}}</a>
                                        @endif
                                    </div>
                                    <hr class="blue-line">
                                </div>
                            </div>
                            <div class="sm-add-question-sec">
                                <div class="select-option-sec pb-3">
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
                                    
                                    <form method="get" id="filtration-Intelligent-tutor">	
                                        <div class="row">
                                            @if(Auth::user()->role_id != 3)
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_grade_id[]"  id="learning_tutor_grade_id" multiple>
                                                    @if(!empty($Grades))
                                                        @foreach ($Grades as $gradekey => $grade)
                                                            @if(isset($requestData['learning_tutor_grade_id']))
                                                                <option value="{{$grade->id}}" {{in_array($grade->id,$requestData['learning_tutor_grade_id']) ? 'selected' : ''}}>{{$grade->name }}</option>
                                                            @else
                                                            <option value="{{$grade->id}}" selected>{{$grade->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="">{{__("languages.no_grade_available")}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            @endif
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_strand_id[]"  id="learning_tutor_strand_id" multiple >
                                                    @if(!empty($StrandList))
                                                        @foreach ($StrandList as $strandkey => $strand)
                                                            @if(isset($requestData['learning_tutor_strand_id']))
                                                                <option value="{{$strand->id}}" {{(in_array($strand->id,$requestData['learning_tutor_strand_id'])) ? 'selected' : ''}}>{{$strand->{'name_'.app()->getLocale()} }}</option>  
                                                            @else
                                                                <option value="{{$strand->id}}" selected>{{$strand->{'name_'.app()->getLocale()} }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="">{{__("languages.no_strands_available")}}</option>
                                                    @endif
                                                </select>
                                            </div>
        
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_learning_unit[]"  id="learning_tutor_learning_unit" multiple>
                                                    @if(!empty($LearningUnit))
                                                        @foreach ($LearningUnit as $learningUnitKey => $learning_unit)
                                                            @if(isset($requestData['learning_tutor_learning_unit']))
                                                                <option value="{{$learning_unit->id}}" {{(in_array($learning_unit->id,$requestData['learning_tutor_learning_unit'])) ? 'selected' : ''}}>{{$learning_unit->{'name_'.app()->getLocale()} }}</option>
                                                            @else
                                                            <option value="{{$learning_unit->id}}" selected>{{$learning_unit->{'name_'.app()->getLocale()} }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="">{{__("languages.no_learning_units_available")}}</option>
                                                    @endif
                                                </select>
                                            </div>
        
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_learning_objectives[]"  id="learning_tutor_learning_objectives" multiple>
                                                    @if(!empty($LearningObjective))
                                                        @foreach ($LearningObjective as $learningObjectiveKey => $learning_objective)
                                                            @if(isset($requestData['learning_tutor_learning_objectives']))
                                                                <option value="{{$learning_objective->id}}" {{(in_array($learning_objective->id,$requestData['learning_tutor_learning_objectives'])) ? 'selected' : ''}}>{{$learning_objective->foci_number }} {{$learning_objective->{'title_'.app()->getLocale()} }}</option>
                                                            @else
                                                                <option value="{{$learning_objective->id}}" selected>{{$learning_objective->foci_number }} {{$learning_objective->{'title_'.app()->getLocale()} }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="">{{__("languages.no_learning_objectives_available")}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_language_id[]"  id="filter_learning_tutor_language_id" multiple>
                                                    @if(!empty($languages))
                                                        @foreach ($languages as $language)
                                                            @if(isset($requestData['learning_tutor_language_id']))
                                                                <option value="{{$language->id}}" {{(in_array($language->id,$requestData['learning_tutor_language_id'])) ? 'selected' : ''}}>{{$language->name}}</option>
                                                            @else
                                                                <option value="{{$language->id}}" selected>{{$language->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_status"  id="learning_tutor_status">
                                                    @if(isset($requestData['learning_tutor_learning_objectives']))
                                                        <option value="pending" {{($requestData['learning_tutor_status'] == "pending") ? 'selected' : ''}}>{{__('languages.pending')}}</option>
                                                        <option value="active" {{($requestData['learning_tutor_status'] == "active") ? 'selected' : ''}}>{{__('languages.active')}}</option>
                                                        <option value="inactive" {{($requestData['learning_tutor_status'] == "inactive") ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
                                                    @else
                                                    <option value="pending">{{__('languages.pending')}}</option>
                                                    <option value="active" selected>{{__('languages.active')}}</option>
                                                    <option value="inactive">{{__('languages.inactive')}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 mb-50 d-flex">
                                                <button type="submit" name="filter" value="filter" class="btn-search mr-2">{{ __('languages.search') }}</button>
                                                <button type="button" class="btn btn-info" id="reset_filter_btn">{{ __('languages.reset') }} {{ __('languages.filter') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                    <hr/>
                                    @if(!empty($uploadData))   
                                        <div class="row load-more-files">
                                            @foreach($uploadData as $key => $content)
                                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                                    <div class="video-card deleteFile_{{$content->id}}">
                                                        @switch(strtolower($content->file_type))
                                                            @case('png')
                                                            @case('jpg')
                                                            @case('jpeg') 
                                                                <img src="{{asset($content->file_path)}}" alt="{{$content->file_name}}" class="img-fluid" id='myImg'>  
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif                                                            
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('pdf')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/pdf.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('csv')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/excel.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('mp4')
                                                                {{-- <img src="{{asset('images/document_images/video.png')}}" alt="{{$content->file_name}}" class="playVideo" id='myImg' data-filepath="{{$content->file_path}}"> --}}
                                                                <video class="playVideo" id='myImg' data-filepath="{{$content->file_path}}">
                                                                    <source src="{{$content->file_path}}" type="video/mp4" />
                                                                </video>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash"  aria-hidden="true"></i></span>
                                                                @endif
                                                                @break 
                                                            @case('txt')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/txt.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('ppt')
                                                            @case('pptx')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/ppt.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('mp3')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/audio.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                            @case('doc')
                                                            @case('docx')
                                                                <a href="{{asset($content->file_path)}}" target="_blank" title="{{$content->file_path}}">
                                                                    <img src="{{asset('images/document_images/word.png')}}" alt="{{$content->file_name}}" class="" id='myImg'>
                                                                </a>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break;
                                                            @case('url')
                                                                <img src="{{asset($content->thumbnail_file_path)}}" allow="encrypted-media" alt="{{$content->file_name}}" data-filepath="{{$content->file_path}}" class="playVideo">
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                {{-- <span class="intelligent-tutor-files-feedback-button feedbackFile" data-id="{{$content->id}}"><i class="fa fa-heart" aria-hidden="true"></i></span> --}}
                                                                @break
                                                            @default
                                                                <img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" class="img-fluid myImg" id='myImg'>
                                                                @if(in_array('upload_documents_update',$permissions))
                                                                    <span class="intelligent-tutor-files-edit-button editFile" data-id="{{$content->id}}"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                                                @endif 
                                                                @if(in_array('upload_documents_delete',$permissions))
                                                                    <span class="intelligent-tutor-files-delete-button deleteFile" data-id="{{$content->id}}"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                                @endif
                                                                @break
                                                        @endswitch
                                                        <div class="intelligent_tutor_title">{{$content->title ?? '----'}}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="col-lg-2 col-md-4">
                                        @if( $countUploadData > 12)
                                            <input type="button" data-countUploaded="12" value="{{__('languages.show_more')}}" class="btn-search" id="add_more_document"/>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                
                    </div>
            </div>
        </div>

        <!-- File Edit Modal -->
		<div class="modal fade" id="FileEditModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl" style="max-width: 90%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{__('languages.edit_intelligent_tutor_detail')}}</h5>
                        <button type="button" class="close close-FileEditModal-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="EditIntelligentTutorForm">
                            @csrf
                            @method('POST')	
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <label class="text-bold-600">{{__('languages.language_name')}}</label>
                                    <input type="hidden" data-FileId="" name="FileId" id="FileId" />
                                    <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="update_learning_tutor_language_id"  id="update_learning_tutor_language_id">
                                        @if(!empty($languages))
                                        @foreach ($languages as $language)
                                            <option value="{{$language->id}}">{{$language->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('update_learning_tutor_language_id'))
                                        <span class="validation_error" id="error-node">{{ $errors->first('update_learning_tutor_language_id') }}</span>
                                    @endif
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label class="text-bold-600">{{__('languages.title')}}</label>
                                    <input type="text" class="form-control" name="update_document_title" id="update_document_title" placeholder="{{ __('languages.title') }}">
                                    @if($errors->has('update_document_title'))<span class="validation_error">{{ $errors->first('update_document_title') }}</span>@endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-50 description-en-sec">
                                    <label class="text-bold-600" >{{__('languages.upload_document.english_description')}}</label>
                                    <textarea class="form-control" name="update_file_description_en" id="update_file_description_en" placeholder="{{__('languages.upload_document.enter_english_description')}}" value="" rows=5></textarea>
                                    @if($errors->has('update_file_description_en'))<span class="validation_error">{{ $errors->first('update_file_description_en') }}</span>@endif
                                </div>
                                <div class="col-md-6 mt-10 mb-50 description-ch-sec" style="display: none;">
                                    <label class="text-bold-600" >{{__('languages.upload_document.chinese_description')}}</label>
                                    <textarea class="form-control" name="update_file_description_ch" id="update_file_description_ch" placeholder="{{__('languages.upload_document.enter_chinese_description')}}" value="" rows=5></textarea>
                                    @if($errors->has('update_file_description_ch'))<span class="validation_error">{{ $errors->first('update_file_description_ch') }}</span>@endif
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label class="text-bold-600">{{__('languages.status')}}</label>
                                    <select name="status" class="form-control select-option" id="update_learning_status">
                                        <option value="pending">{{__("languages.pending")}}</option>
                                        <option value="active">{{__("languages.active")}}</option>
                                        <option value="inactive">{{__("languages.inactive")}}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button class="btn btn-primary UpdateLearningTutorFile">{{ __('languages.submit') }}</button>
                    <button type="button" class="btn btn-secondary close-FileEditModal-modal" data-dismiss="modal">{{__('languages.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
          <!-- File Edit Modal -->
         <!-- Modal -->
        <div id="docModule" class="modal">
            <span class="close">&times;</span>
                <img class="modal-content" id="docImages">
            <div id="caption"></div>
        </div>
        
        <!-- Youtube Video Modal -->
		<div class="modal fade" id="videoPlayer" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl" style="max-width: 90%;">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{__('languages.video_player')}}</h5>
                    <button type="button" class="close close-videoPlayer-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body embed-responsive embed-responsive-16by9">
                        <iframe id="youTubeVideoPlay" class="embed-responsive-item" width="450" height="350" src='' frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"  allowfullscreen></iframe>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-videoPlayer-modal" data-dismiss="modal">{{__('languages.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
          <!-- Youtube Video Modal -->
          
        @include('backend.layouts.footer')
        <script>
            // Get the modal
            var modal = document.getElementById("docModule");
            
            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementsByClassName("img-fluid");
            var modalImg = document.getElementById("docImages");
            $(document).on('click', '.img-fluid', function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
            });
            
            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];
            
            // When the user clicks on <span> (x), close the modal
            span.onclick = function() { 
            modal.style.display = "none";
            }
        </script>
        <script>
            //Edit Particular File title Or Description
            $(document).on("click",".editFile",function(){
                $("#cover-spin").show();
                $FileId = $(this).data('id');
                $.ajax({
                    url: BASE_URL + "/intelligent-tutor/"+$FileId+"/edit",
                    method: "get",
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(
                            JSON.stringify(response)
                        );
                        if (data.status === "success") {
                            $("#FileId").val($FileId);
                            $("#update_document_title").val(data.data.title);
                            $("#update_file_description_en").val(data.data.description_en);
                            $("#update_file_description_ch").val(data.data.description_ch);
                            $("#update_learning_status").val(data.data.status);
                            $("#cover-spin").hide();
                            if(data.data.language_id==1){
                                $(".description-en-sec").css("display","block");
                                $(".description-ch-sec").css("display","none");
                                $('#update_learning_tutor_language_id').val(data.data.language_id);
                            }else{
                                $(".description-en-sec").css("display","none");
                                $(".description-ch-sec").css("display","block");
                                $('#update_learning_tutor_language_id').val(data.data.language_id);    
                            }
                            $('#FileEditModal').modal("show");
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            })

            //Update ON language Change File Description
            $(document).on("change",'#update_learning_tutor_language_id',function(){
                $languageId = $(this).val()
                if($languageId == 1){
                    $(".description-en-sec").css("display","block");
                    $(".description-ch-sec").css("display","none");
                }else{
                    $(".description-en-sec").css("display","none");
                    $(".description-ch-sec").css("display","block");
                }
            });

            // on Show More Click Display Other Avvailable Files
            $(document).on("click","#add_more_document",function (){
                $("#cover-spin").show();
                $count = $(this).attr('data-countUploaded');
                $.ajax({
                    url: BASE_URL + "/add-more-document",
                    method: "get",
                    data: {
                        count: $count,
                        formData : $("#filtration-Intelligent-tutor").serialize()
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(
                            JSON.stringify(response)
                        );
                        if (data.status === "success") {
                            $(".load-more-files").append(data.data[0]);
                            if(data.data[1] < data.data[2]){
                                $("#add_more_document").attr('data-countUploaded',data.data[1]);
                            }else{
                                $("#add_more_document").hide();
                            }
                            
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            });
            //Update File With Title or Description
            $(document).on("click",'.UpdateLearningTutorFile',function(){
                if($("#EditIntelligentTutorForm").valid()){
                    $("#cover-spin").show();
                    $FileId = $("#FileId").val();
                    //Edit Learning-tutor Points Validate
                    $("#EditIntelligentTutorForm").validate({
                        rules: {
                            update_document_title: {
                                required: true,
                            },
                        },
                        messages: {
                            update_document_title: {
                                required: VALIDATIONS.PLEASE_ENTER_TITLE,
                            },
                        },
                        errorPlacement: function (error, element) {
                            error.insertAfter(element);
                        },
                    });
                    $.ajax({
                        url: BASE_URL + "/intelligent-tutor/"+$FileId,
                        method: "put",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            languageId: $("#update_learning_tutor_language_id").val(),
                            title: $("#update_document_title").val(),
                            description_en : $("#update_file_description_en").val(),
                            description_ch : $("#update_file_description_ch").val(),
                            status:$("#update_learning_status").val(),
                        },
                        success: function (response) {
                            $("#cover-spin").hide();
                            var data = JSON.parse(
                                JSON.stringify(response)
                            );
                            if (data.status === "success") {
                                $('#FileEditModal').modal("hide");   
                                $("#cover-spin").hide();
                                location.reload();
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                }
            });

            // Delete Particular File
            $(document).on("click",".deleteFile",function(){
                $documentId = $(this).data('id');
                $className="deleteFile_"+$documentId;
                $.confirm({
                    title: DELETE_FILE + "?",
                    content: CONFIRMATION,
                    autoClose: "Cancellation|8000",
                    buttons: {
                        deleteDocument: {
                            text: DELETE_FILE,
                            action: function () {
                                $("#cover-spin").show();
                                $.ajax({
                                    url:
                                        BASE_URL +
                                        "/intelligent-tutor/delete/" +
                                        $documentId,
                                    type: "GET",
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var data = JSON.parse(
                                            JSON.stringify(response)
                                        );
                                        if (data.status === "success") {
                                            toastr.success(data.message);
                                            $('.'+$className).fadeOut(500, function () {
                                                $(this).remove();
                                            });
                                            //$('.load-more-files').load(window.location.href+' .load-more-files');
                                             location.reload(true);
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
            })
            // Play Video In Modal 
            $(document).on("click",".playVideo",function(){
                var videoSRC = $(this).data("filepath");
                var MergeAutoplay = videoSRC + "?autoplay=1";
                var NewvideoSRCauto= MergeAutoplay.replace("watch?v=", "embed/")
                var domain = videoSRC.replace('http://','').replace('https://','').split(/[/?#]/)[0];
                if (videoSRC.indexOf("youtube") != -1) {
                    const videoId = getYoutubeId(videoSRC);
                    $("#youTubeVideoPlay").attr('src',NewvideoSRCauto);
                    $("#videoPlayer").modal("show");
                }else if (videoSRC.indexOf("vimeo") != -1) {
                    const videoId = getYoutubeId(videoSRC);
                    var matches = videoSRC.match(/vimeo.com\/(\d+)/);
                    $("#youTubeVideoPlay").attr('src','https://player.vimeo.com/video/'+matches[1]+'?autoplay=1');
                    $("#videoPlayer").modal("show");
                }else if (videoSRC.indexOf("dailymotion") != -1) {
                    var m = videoSRC.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
                    if (m !== null) {
                        if(m[4] !== undefined) {
                            
                            $("#youTubeVideoPlay").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
                            $("#videoPlayer").modal("show");
                        }
                        $("#youTubeVideoPlay").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
                        $("#videoPlayer").modal("show");
                    }
                }else{
                    $("#youTubeVideoPlay").attr('src',videoSRC);
                    $("#videoPlayer").modal("show");
                }
            });
            // On Modal Close Stop Playing Video 
            $("#videoPlayer").on("hide.bs.modal", function(e) {
                $("#youTubeVideoPlay").attr("src", ""); 
            });
            // Get Uploded Video Url to Youtube Video Id
            function getYoutubeId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }
        </script>
        {{-- Feedback Modal --}}
        {{-- <script>
            $(document).on("click",".feedbackFile",function(){
                $("#feedbackFileModal").modal("show");
            });
        </script> --}}
    @endsection