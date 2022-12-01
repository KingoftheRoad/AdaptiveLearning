@extends('backend.layouts.app')
    @section('content')
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

            <div class="studnt-doc-main pl-5 pr-5">
	        	<div class="coltainer ml-0 mr-0">
	        		<div class="row">
	        			<div class="col-md-12">
	        				<div class="doc-title mb-4">
                    @if(Auth::user()->role_id == 3)
	        					  <h2 class="mb-0 main-title">{{__('languages.learning_content')}}</h2>
                    @else
                      <h2 class="mb-0 main-title">{{__('languages.intelligent_tutor')}}</h2>
                    @endif
	        				</div>
                  <div class="sec-title">
                    <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                  </div>
                  <hr class="blue-line">
	        			</div>
	        		</div>
              <form class="addDocumentFilterForm" id="addDocumentFilterForm" method="get">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="select-lng pt-2 pb-2">
                            <select name="grade_id" class="form-control select-search select-option" id="grade-id" >
                                @if(!empty($Grades))
                                    <option value="">{{ __('languages.grade') }}</option>
                                    @foreach($Grades as $grade)
                                    <option value={{ $grade->id }} {{ request()->get('grade_id') == $grade->id ? 'selected' : '' }} >{{ ucfirst($grade->name) }}</option> 
                                    @endforeach
                                @else
                                    <option value="">{{ __('languages.no_grade_available') }}</option>
                                @endif
                            </select>
                            @if($errors->has('grade_id'))
                                <span class="validation_error">{{ $errors->first('grade_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <!-- <div class="col-lg-2 col-md-3">
                        <div class="select-lng pt-2 pb-2">
                            <select name="subject_id" class="form-control select-search select-option" id="subject-id" @if(isset($subjects) && !empty($subjects)) "style=display:block;" @else "style=display:none;" @endif>
                                <option value="">{{ __('languages.subject') }}</option>
                                @if(!empty($subjects))
                                    @foreach($subjects as $subject)
                                        <option value="{{$subject->id}}" {{ request()->get('subject_id') == $subject->id ? 'selected' : '' }}>{{$subject->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('subject_id'))
                                <span class="validation_error">{{ $errors->first('subject_id') }}</span>
                            @endif
                        </div>
                    </div> -->
                    <div class="col-lg-2 col-md-3">
                        <div class="select-lng pt-2 pb-2">
                            <select name="strand_id" class="form-control select-search select-option" id="strand-id">
                                <option value="">{{ __('languages.strands') }}</option>
                                @if(!empty($strands))
                                @foreach($strands as $strand)
                                <option value="{{$strand->id}}" {{ request()->get('strand_id') == $strand->id ? 'selected' : '' }}>{{$strand->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            @if($errors->has('strand_id'))
                                <span class="validation_error">{{ $errors->first('strand_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="select-lng pt-2 pb-2">                            
                            <select name="learning_unit_id"  class="form-control select-search select-option" id="learning-unit">
                                <option value="">{{ __('languages.learning_units') }}</option>
                                @if(!empty($LearningUnits))
                                @foreach($LearningUnits as $LearningUnit)
                                <option value="{{$LearningUnit->id}}" {{ request()->get('learning_unit_id') == $LearningUnit->id ? 'selected' : '' }}>{{$LearningUnit->name}}</option>
                                @endforeach
                                @endif  
                            </select>
                            @if($errors->has('learning_unit_id'))
                                <span class="validation_error">{{ $errors->first('learning_unit_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="select-lng pt-2 pb-2">
                            <select name="learning_objective_id"  class="form-control select-search select-option" id="learning-objectives">
                                <option value="">{{ __('languages.learning_objectives') }}</option>
                                @if(!empty($LearningObjectives))
                                @foreach($LearningObjectives as $LearningObjective)
                                <option value="{{$LearningObjective->id}}" {{ request()->get('learning_objective_id') == $LearningObjective->id ? 'selected' : '' }}>{{$LearningObjective->foci_number}} {{$LearningObjective->title}}</option>
                                @endforeach
                                @endif  
                            </select>
                            @if($errors->has('learning_objective_id'))
                                <span class="validation_error">{{ $errors->first('learning_objective_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                      <div class="select-lng pt-2 pb-2">
                          <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                      </div>
                    </div>
                </div>
                 
              </form>
	        		<div class="documnt-sec-main mb-5">
	        			<div class="row">
                    @if(!empty($uploadData))
                        @foreach($uploadData as $documentkey => $file)
                            @php
                              $fileName = substr(strrchr($file->file_path, '//'), 1);
                              $devideExtension = explode(".",$fileName);
                              $extension = end($devideExtension); 
                            @endphp

                            <div class="col-lg-2 col-md-3 col-sm-6 mb-5 docs">
                              <div class="document document-inners">
                                <div class="doc-image">
                                  @php
                                  $path = '';
                                  
                                  @endphp
                                  @switch(strtolower($extension))
                                      @case('png')
                                      @case('jpg')
                                      @case('jpeg') 
                                        <img src="{{asset($file->file_path)}}" alt="{{$fileName}}" class="img-fluid" id='myImg' height=100px width=100px >
                                        @break
                                      @case('pdf')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                        <img src="{{asset('images/document_images/pdf.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a>
                                        @break
                                      @case('csv')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/excel.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a>
                                        @break
                                      @case('mp4')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/video.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a> 
                                        @break 
                                      @case('txt')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/txt.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a>
                                        @break
                                      @case('ppt')
                                      @case('pptx')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/ppt.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a>
                                        @break
                                      @case('doc')
                                      @case('docx')
                                        <a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/word.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                        </a>
                                        @break;
                                      @case('url')
                                        <a href="{{$file->file_path}}" target="_blank" title="{{$file->file_path}}">
                                          <img src="{{asset('images/document_images/url.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
                                        </a>
                                        @break
                                      @default
                                        <img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" class="img-fluid myImg" id='myImg' height=100px width=100px >
                                        @break
                                  @endswitch
                                  {{-- @if(strtolower($extension) == 'png' || strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg')
                                    <img src="{{asset($file->document[0]->file_path)}}" alt="{{$fileName}}" class="img-fluid" id='myImg' height=100px width=100px >
                                  @elseif(strtolower($extension) == 'mp3' && $file->file_type == 'mp3' )
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/audio.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'pdf' && $file->document[0]->file_type == 'pdf')
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/pdf.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'csv' || strtolower($extension) == 'xlsx' && $file->document[0]->file_type == 'csv')
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/excel.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'mp4' && $file->document[0]->file_type == 'mp4' )
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/video.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'txt' && $file->document[0]->file_type == 'txt')
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/txt.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'ppt' || strtolower($extension) == 'pptx' && $file->document[0]->file_type == 'ppt'  || $file->document[0]->file_type == 'pptx' )
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/ppt.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif(strtolower($extension) == 'doc' || strtolower($extension) == 'docx' && $file->document[0]->file_type == 'doc' || $file->document[0]->file_type == 'docx')
                                  <a href="{{asset($file->document[0]->file_path)}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                    <img src="{{asset('images/document_images/word.png')}}" alt="{{$fileName}}" class="" id='myImg' height=100px width=100px >
                                  </a>
                                  @elseif($file->document[0]->file_type == 'url')
                                    <a href="{{$file->document[0]->file_path}}" target="_blank" title="{{$file->document[0]->file_path}}">
                                      <img src="{{asset('images/document_images/url.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
                                    </a>
                                  @else
                                    <img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" class="img-fluid myImg" id='myImg' height=100px width=100px >
                                  @endif --}}
                            </div>

                            <div class="doc-text">
                              <h3>{{$fileName}}</h3>
                              <a href="#"><i class="fa fa-eye" aria-hidden="true"></i></a>
                              @if($file->file_type != 'url')
                                <a href="{{route('download-files',$file->id)}}" title="{{__('languages.download_file')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                              @endif
                            </div>
                          </div>
                        </div>
                  @endforeach
                    @else
                        <p>{{__('languages.no_any_documents')}}</p>
                    @endif
                      
	        			</div>
	        		</div>
	        	</div>
	        </div>
	        <!-- code end -->

          <!-- Modal -->
          <div id="docModule" class="modal">
								<span class="close">&times;</span>
									<img class="modal-content" id="docImages">
								<div id="caption"></div>
					</div>
						</div>
	        </div>
		</div>
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
	
		@include('backend.layouts.footer')
@endsection