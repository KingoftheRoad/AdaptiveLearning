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
								<h2 class="mb-4 main-title">{{__('languages.upload_document.add_new_document')}}</h2>
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
                            
                            <form method="post" id="addIntelligentTutorFrom" class="form1"  enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    <!-- Language Name -->
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.language_name')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_language_id"  id="learning_tutor_language_id">
                                            @if(!empty($languages))
                                            @foreach ($languages as $language)
                                            <option value="{{$language->id}}">{{$language->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('language_id'))
                                            <span class="validation_error" id="error-node">{{ $errors->first('language_id') }}</span>
                                        @endif
                                    </div>

                                    <!-- Grade -->
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.grade')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_grade_id[]"  id="learning_tutor_grade_id" multiple>
                                            @if(!empty($Grades))
                                            @foreach ($Grades as $grade)
                                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('learning_tutor_grade_id'))
                                            <span class="validation_error" id="error-learning_tutor_grade_id">{{ $errors->first('learning_tutor_grade_id') }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.strands')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_strand_id[]"  id="learning_tutor_strand_id" multiple >
                                            @if(!empty($StrandList))
                                            @foreach ($StrandList as $strand)
                                                <option value="{{$strand->id}}">{{$strand->{'name_'.app()->getLocale()} }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('learning_tutor_strand_id'))
                                            <span class="validation_error" id="error-learning_tutor_strand_id">{{ $errors->first('learning_tutor_strand_id') }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.learning_units')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_learning_unit[]"  id="learning_tutor_learning_unit" multiple disabled>
                                            @if(!empty($LearningUnit))
                                            @foreach ($LearningUnit as $learning_unit)
                                                <option value="{{$learning_unit->id}}">{{$learning_unit->{'name_'.app()->getLocale()} }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('learning_tutor_learning_unit'))
                                            <span class="validation_error" id="error-learning_tutor_learning_unit">{{ $errors->first('learning_tutor_learning_unit') }}</span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.learning_objectives')}}</label>
                                        <select class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="learning_tutor_learning_objectives[]"  id="learning_tutor_learning_objectives" multiple disabled>
                                            @if(!empty($LearningObjective))
                                            @foreach ($LearningObjective as $learning_objective)
                                                <option value="{{$learning_objective->id}}">{{$learning_objective->{'title_'.app()->getLocale()} }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('learning_tutor_learning_objectives'))
                                            <span class="validation_error" id="error-learning_objectives">{{ $errors->first('learning_tutor_learning_objectives') }}</span>
                                        @endif
                                    </div>
                                    {{--Title--}}
                                    <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.title')}}</label>
                                        <input type="text" class="form-control" name="document_title" id="document_title" placeholder="{{ __('languages.title') }}" value="{{old('document_title')}}">
                                        @if($errors->has('document_title'))<span class="validation_error">{{ $errors->first('document_title') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50 description-en-sec">
                                        <label class="text-bold-600" >{{__('languages.upload_document.english_description')}}</label>
                                        <textarea class="form-control" name="file_description_en" id="file_description_en" placeholder="{{__('languages.upload_document.enter_english_description')}}" value="" rows=5>{{old('file_description_en')}}</textarea>
                                        @if($errors->has('file_description_en'))<span class="validation_error">{{ $errors->first('file_description_en') }}</span>@endif
                                    </div>
                                    <div class="form-group col-md-6 mb-50 description-ch-sec" style="display: none;">
                                        <label class="text-bold-600" >{{__('languages.upload_document.chinese_description')}}</label>
                                        <textarea class="form-control" name="file_description_ch" id="file_description_ch" placeholder="{{__('languages.upload_document.enter_chinese_description')}}" value="" rows=5>{{old('file_description_ch')}}</textarea>
                                        @if($errors->has('file_description_ch'))<span class="validation_error">{{ $errors->first('file_description_ch') }}</span>@endif
                                    </div>
                                    <div class="alert alert-danger uploadfiles" style="display:none;">{{__('languages.please_select_at_least_one_url_or_file')}}</div>
                                    <div class="upload-que-code-sec form-group col-md-6 mb-50">
                                        <label class="text-bold-600">{{__('languages.status')}}</label>
                                        <select name="status" class="form-control select-option" id="status">
                                            <option value="active">{{__("languages.active")}}</option>
                                            <option value="inactive">{{__("languages.inactive")}}</option>
                                            <option value="pending">{{__("languages.pending")}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12 mb-50 dropzone" id="learning_tutor_dropzone"> 
                                        <div class="dz-message">
                                            <div class="col-xs-8">
                                                <div class="message">
                                                    <p>{{__('languages.drop_files_here_or_click_to_upload')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="the-progress-div">
                                            <span class="the-progress-text"></span>
                                        </div>
                                    </div>
                                    <span class="text-danger dropzone-error-msg"></span>
                                </div>
                                <div class="form-row" id="document-url-cls"></div>
                                <div class="sm-btn-sec form-row btn-sec mr-4 ml-2">
                                    <button class="blue-btn btn btn-primary mt-4" name="addMoreUrl" id="addMoreDocumentUrl" type="button">{{ __('languages.upload_document.add_video_url') }}</button>
                                    <button class="blue-btn btn btn-primary mr-4 ml-2 mt-4 submitForm" data-uploadingType="submit">{{ __('languages.submit') }}</button>
                                    <button class="blue-btn btn btn-primary mr-4 ml-2 mt-4 saveAndContinue" data-uploadingType="saveAndContinue">{{ __('languages.save_and_continue') }}</button> 
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
        <script>
        $(document).ready(function(){
            var SubmitType = '';
            var DropZone = new Dropzone("#learning_tutor_dropzone", {
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 100,
                maxFiles: 20,
                paramName: 'file',
                clickable: true,
                addRemoveLinks: true, 
                acceptedFiles: ".mp4,.mkv,.avi",
                url: BASE_URL+"/intelligent-tutor",
                type:"POST",
                init: function () {
                        myDropzone = this;
                        $(document).on("click", ".submitForm",function(e,formData) {
                            SubmitType = $(this).attr('data-uploadingType');
                            e.preventDefault();
                            e.stopPropagation();
                            if(myDropzone.files.length > 0){
                                if ($('#addIntelligentTutorFrom').valid()) {
                                    $("#cover-spin").show();
                                    myDropzone.processQueue();
                                }
                            }else{
                                // Check The video url available or not
                                var IsVideoUrls = false;
                                $('.document_urls').each(function(){
                                    if($(this).val()!=""){
                                        IsVideoUrls = true;
                                    }
                                })
                                // Check the video url is available or not
                                if(!IsVideoUrls){
                                    // If video and file not uploaded then we will display error message
                                    $(".dropzone-error-msg").html(VALIDATIONS.PLEASE_ENTER_FILES);
                                }else{
                                    // If video url is available the we will submit the from via ajax
                                    IntelligentTutorUploadingFile(SubmitType);
                                }
                            }
                        });

                        // Trigger Event on click save and continue button
                        $(document).on("click", ".saveAndContinue",function(e,formData) {
                            SubmitType = $(this).attr('data-uploadingType');
                            e.preventDefault();
                            e.stopPropagation();
                            if(myDropzone.files.length > 0){
                                if ($('#addIntelligentTutorFrom').valid()) {
                                    $("#cover-spin").show();
                                    myDropzone.processQueue();
                                }
                            }else{
                                //IntelligentTutorUploadingFile(SubmitType);
                                $(".dropzone-error-msg").html(VALIDATIONS.PLEASE_ENTER_FILES); 
                            }
                        });

                    this.on('sendingmultiple', function (file, xhr, formData) {
                        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
                        formData.append("learning_tutor_language_id",$("#learning_tutor_language_id").val());
                        formData.append("learning_tutor_grade_id[]",$("#learning_tutor_grade_id").val());
                        formData.append("learning_tutor_strand_id[]",$("#learning_tutor_strand_id").val());
                        formData.append("learning_tutor_learning_unit[]",$("#learning_tutor_learning_unit").val());
                        formData.append("learning_tutor_learning_objectives[]",$("#learning_tutor_learning_objectives").val());
                        formData.append('document_title',$("#document_title").val());
                        formData.append('file_description_en',$("#file_description_en").val());
                        formData.append('file_description_ch',$("#file_description_ch").val());
                        formData.append('document_urls',$("input[name='document_urls[]']").map(function(){return $(this).val();}).get());
                        formData.append('status',$("#status").val());
                        formData.append('SubmitType',SubmitType);
                        for (let x = 0; x < file.length; x++){ 
                            formData.append('file', file[x]);
                        }
                    });
                    this.on("totaluploadprogress", function (progress) {
                        $("#the-progress-div").width(progress + '%');
                        $("[data-dz-uploadprogress]").text(Math.round(progress) + '%');
                    });
                    this.on('error', function(file, errorMessage, xhrError) {
                        this.removeFile(file);
                        toastr.error(errorMessage);
                    });
                    
                },
                error: function (file, response){
                    if ($.type(response) === "string"){
                        var message = response; //dropzone sends it's own error messages in string
                    }else{
                        var message = response.message;
                    }
                    file.previewElement.classList.add("dz-error");
                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }
                    return false;
                    return _results;
                },
                success: function (file, response) {
                    this.removeFile(file);
                },
                successmultiple: function (file, response) {
                    var data = JSON.parse(
                        JSON.stringify(response)
                    );
                    if (data.status === "success" ) { 
                        if(data.data.redirect=="intelligent-tutor"){
                            window.location.href = BASE_URL+'/intelligent-tutor';
                            toastr.success(data.message);
                            $("#cover-spin").hide();
                        }else{
                            $("#document_title").val('');
                            $("#file_description_en").val('');
                            $("#file_description_ch").val('');
                            $(".removeVideoUrl").parent("div").parent("div").remove();
                            toastr.success(data.message);
                            $("#cover-spin").hide();
                        }                       
                    }else {
                        toastr.error(data.message);
                    }    
                },
                reset: function () {
                },
                complete: function(file) {                    
                }
            });
        });
        
        function IntelligentTutorUploadingFile(formSubmitType){
            SubmitType = formSubmitType;
            if ($('#addIntelligentTutorFrom').valid()) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL+"/intelligent-tutor",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        learning_tutor_language_id: $("#learning_tutor_language_id").val(),
                        'learning_tutor_grade_id[]': $("#learning_tutor_grade_id").val(),
                        'learning_tutor_strand_id[]':$("#learning_tutor_strand_id").val(),
                        'learning_tutor_learning_unit[]':$("#learning_tutor_learning_unit").val(),
                        'learning_tutor_learning_objectives[]':$("#learning_tutor_learning_objectives").val(),
                        document_title:$("#document_title").val(),
                        file_description_en:$("#file_description_en").val(),
                        file_description_ch:$("#file_description_ch").val(),
                        document_urls:$("input[name='document_urls[]']").map(function(){return $(this).val();}).get(),
                        status:$("#status").val(),
                        SubmitType:SubmitType
                    },
                    success: function(data) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(
                            JSON.stringify(data)
                        );
                        if (data.status === "success") {
                            if(data.data.redirect=="intelligent-tutor"){
                                window.location.href = BASE_URL+'/intelligent-tutor';
                                toastr.success(data.message);
                                $("#cover-spin").hide();
                            }else{
                                $("#document_title").val('');
                                $("#file_description_en").val('');
                                $("#file_description_ch").val('');
                                $(".removeVideoUrl").parent("div").parent("div").remove();
                                toastr.success(data.message);
                                $("#cover-spin").hide();
                            }      
                        }else {
                            toastr.error(data.message);
                        }    
                    },
                    error: function (data) {
                        ErrorHandlingMessage(data);
                    },
                });
            }
        }
        
    </script>
    <script>
        $(document).on("change","#learning_tutor_language_id",function(){
            $language = $(this).val();
            if($language == 1){
                $(".description-en-sec").css("display","block");
                $(".description-ch-sec").css("display","none");
            }else{
                $(".description-en-sec").css("display","none");
                $(".description-ch-sec").css("display","block");
            }
        })
    </script>
        
@endsection