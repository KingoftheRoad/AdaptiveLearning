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
								    <h2 class="mb-4 main-title">{{__('languages.upload_document.update_document')}}</h2>
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
                            <form method="post" id="addDocumentFrom" class="form1" action="{{route('upload-documents.update',$Document->id)}}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.language_name')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="language_id"  id="language_id">
                                            @if(!empty($languages))
                                                @foreach ($languages as $language)
                                                    <option value="{{$language->id}}" @if($Document->language_id == $language->id) selected @endif>{{$language->name}}</option>    
                                                @endforeach
                                                
                                            @endif
                                        </select>
                                        @if($errors->has('language_id'))
                                            <span class="validation_error" id="error-node">{{ $errors->first('language_id') }}</span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50 ">
                                        <label class="text-bold-600">{{__('languages.nodes.node_id')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="node_id[]" multiple id="doc_node_id">
                                            @if(!empty($NodesList))
                                                {!! $NodesList !!}
                                            @endif
                                        </select>
                                        @if($errors->has('node_id'))
                                            <span class="validation_error" id="error-node">{{ $errors->first('node_id') }}</span>
                                        @endif
                                    </div>
                                    {{-- <div class="select-lng  form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.upload_document.file_name')}}</label>
                                        <input type="text" class="input-search-box mr-2 " name="FileName" id="FileName" value="{{$Document->file_name}}" placeholder="{{__('languages.upload_document.enter_file_name')}}">
                                        @if($errors->has('FileName'))
                                            <span class="validation_error">{{ $errors->first('FileName') }}</span>
                                        @endif
                                    </div> --}}
                                    <div class="form-group col-md-6 mb-50 description-en-sec" @if($Document->language_id != 1) style="display: none;" @endif>
                                        <label class="text-bold-600" >{{__('languages.upload_document.english_description')}}</label>
                                        <textarea class="form-control" name="file_description_en" id="file_description_en" placeholder="{{__('languages.upload_document.enter_english_description')}}" value="" rows=5>{{$Document->description_en}}</textarea>
                                        @if($errors->has('file_description_en'))<span class="validation_error">{{ $errors->first('file_description_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50 description-ch-sec" @if($Document->language_id != 2) style="display: none;" @endif>
                                        <label class="text-bold-600" >{{__('languages.upload_document.chinese_description')}}</label>
                                        <textarea class="form-control" name="file_description_ch" id="file_description_ch" placeholder="{{__('languages.upload_document.enter_chinese_description')}}" value="" rows=5>{{$Document->description_ch}}</textarea>
                                        @if($errors->has('file_description_ch'))<span class="validation_error">{{ $errors->first('file_description_ch') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.sidebar.upload_documents')}}</label>
                                        <input type="file" class="form-control" name="upload[]" id="upload" multiple>
                                    </div>
                                        <div class="alert alert-danger uploadfiles" style="display:none;">{{__('languages.please_select_at_least_one_url_or_file')}}</div>
                                    <div class="upload-que-code-sec form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.status')}}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="active" @if($Document->status == 'active') selected @endif>{{__("languages.active")}}</option>
                                            <option value="inactive" @if($Document->status == 'inactive') selected @endif>{{__("languages.inactive")}}</option>
                                            <option value="pending" @if($Document->status == 'pending') selected @endif>{{__("languages.pending")}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row database-all-data">
                                    <div class="form-group" style="width: 100%;">
                                        <label class="text-bold-600">{{__('Uploded Files')}}</label>
                                        @if($Document->document->isNotEmpty())
                                            @foreach($Document->document as $files)
                                                @if(!empty($files->file_type) && $files->file_type != 'url')
                                                    <div class="image-icons-db">
                                                        @if($files->file_type == 'png' || $files->file_type == 'jpg' || $files->file_type == 'jpeg')
                                                            <img src="{{asset($files->file_path)}}"  height="100px" width="auto">
                                                            <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                            <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'mp3')
                                                            <img src="{{asset('images/document_images/audio.png')}}"  height="auto" width="auto">
                                                            <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'pdf')
                                                            <img src="{{asset('images/document_images/pdf.png')}}" height="auto" width="auto" >
                                                            <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'csv' || $files->file_type == 'xlsx')
                                                            <img src="{{asset('images/document_images/excel.png')}}"  height="auto" width="auto">
                                                            <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'mp4')
                                                            <img src="{{asset('images/document_images/video.png')}}"  height="auto" width="auto" >
                                                                <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'txt')
                                                            <img src="{{asset('images/document_images/txt.png')}}"   height="auto" width="auto" >
                                                                <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'ppt' || $files->file_type == 'pptx')
                                                            <img src="{{asset('images/document_images/ppt.png')}}"   height="auto" width="auto">
                                                                <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @elseif($files->file_type == 'doc' || $files->file_type == 'docx')
                                                            <img src="{{asset('images/document_images/word.png')}}"  height="auto" width="auto">
                                                                <a href="javascript:void(0);" data-id="{{$files->id}}" class="db-remove-image">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                                <span>
                                                                <a href="{{asset($files->file_path)}}" download>
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @endforeach
                                        @else
                                            <p>{{__('languages.no_any_documents')}}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row" id="document-url-cls">
                                    @if(!empty($Document->document))
                                        @foreach($Document->document as $files)
                                            @if($files->file_type == 'url')
                                                <div class="col-md-3 col-sm-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="document_urls[]" placeholder="{{__('languages.enter_video_url')}}" id="{{$files->id}}" value="{{$files->file_path}}">
                                                        <a class="removeExistingVideoUrl btn btn-primary btn-sm" id="{{$files->id}}">X</a>
                                                    </div>
                                                    <div class="alert alert-danger uploadUrl" style="display:none;">{{__('languages.please_enter_video_url')}}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div class="form-row">
                                    <div class="form-row sm-btn-sec btn-sec mr-4 ml-2">
                                        <button class="blue-btn btn btn-primary mt-4" name="addMoreUrl" id="addMoreDocumentUrl" type="button">{{ __('languages.upload_document.add_video_url') }}</button>
                                    </div>
                                    <div class="form-row sm-btn-sec btn-sec">
                                        <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                    </div>
                                </div>
                            </form>	
						</div>
					</div>
				</div>
			</div>
	    </div>
		</div>
        <script type="text/javascript">
            var file_type="{!! $file_type !!}";
        </script>
        @include('backend.layouts.footer')  
@endsection