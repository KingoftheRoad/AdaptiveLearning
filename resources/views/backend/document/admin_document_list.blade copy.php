@extends('backend.layouts.app')
    @section('content')
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
			<form method="post" id="addDocumentFrom" class="form1" action="{{route('upload-documents.store')}}" enctype="multipart/form-data">
				@csrf
				<div class="sm-right-detail-sec pl-5 pr-5">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">
								<div class="sec-title">
									<h4 class="mb-4 main-title">{{ __('languages.upload_document.upload_documents') }}</h4>
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
								<div class="form-row">
									<div class="col-lg-4 col-md-4">
										<div class="form-row">
											<div class="form-group col-md-12">
												<select required class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="node_id[]" multiple id="doc_node_id">
													@if(!empty($NodesList))
														{!! $NodesList !!}
													@endif
												</select>
											</div>
										</div>
									</div>
									<div class="upload-que-code-sec  form-group col-lg-3 col-md-3">
										<div class="">
											<input type="file" class="form-control" name="upload[]" id="upload" multiple>
										</div>
											<div class="alert alert-danger uploadfiles" style="display:none;">Please select at least one url or files</div>
									</div>
									<div class="upload-que-code-sec col-lg-3 col-md-3">
										<select name="status" class="form-control select-option" id="status">
											<option value="active">{{__("languages.active")}}</option>
											<option value="inactive">{{__("languages.inactive")}}</option>
											<option value="pending">{{__("languages.pending")}}</option>
										</select>
									</div>
								</div>
								<div class="form-row" id="document-url-cls"></div>
								<div class="form-row">
									<div class="col-md-3">
										<div class="sm-btn-sec form-row btn-sec">
											<button class="blue-btn btn btn-primary mt-4" name="addMoreUrl" id="addMoreDocumentUrl" type="button">{{ __('languages.upload_document.add_url') }}</button>
										</div>
									</div>
									<div class="col-md-3">
										<div class="sm-btn-sec form-row btn-sec">
											<button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
										</div>
									</div>
								</div>								
								<hr/>
								<div class="row">
									<div class="col-md-12">
										<div class="sec-title">
											<h5 class="mb-4 sub-title">{{ __('languages.upload_document.document_list') }}</h5>
										</div>
										<hr class="blue-line">
									</div>
								</div>
							</div>
					<div class="documnt-sec-main mb-5">
	        			<div class="row">
							@if(!empty($uploadData))
								@foreach($uploadData as $file)
								@php
									$fileName = substr(strrchr($file->file_path, '/'), 1);
									$devideExtension = explode(".",$fileName);
									$extension = end($devideExtension);
								@endphp
									<div class="col-lg-2 col-md-3 col-sm-6 mb-5 docs hide-doc-list">
										<div class="document document-inners">
										<div class="doc-image">
											@if(strtolower($extension) == 'png' || strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg')
											<img src="{{asset($file->file_path)}}" alt="{{$fileName}}" class="img-fluid myImg" id='myDocument' height=100px width=100px >
											@elseif(strtolower($extension) == 'mp3' && $file->file_type == 'mp3')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/audio.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif(strtolower($extension) == 'pdf' && $file->file_type == 'pdf')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/pdf.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>	
											@elseif(strtolower($extension) == 'csv' || strtolower($extension) == 'xlsx' && $file->file_type == 'csv' || $file->file_type == 'xlsx')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/excel.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif(strtolower($extension) == 'mp4' && $file->file_type == 'mp4')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/video.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif(strtolower($extension) == 'txt' && $file->file_type == 'txt')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/txt.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif(strtolower($extension) == 'ppt' || strtolower($extension) == 'pptx' && $file->file_type == 'ppt'  || $file->file_type == 'pptx')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/ppt.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif(strtolower($extension) == 'doc' || strtolower($extension) == 'docx' && $file->file_type == 'doc' || $file->file_type == 'docx')
											<a href="{{asset($file->file_path)}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/word.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@elseif($file->file_type == 'url')
											<a href="{{$file->file_path}}" target="_blank" title="{{$file->file_path}}">
												<img src="{{asset('images/document_images/YouTube-logo-hero-1.png')}}" alt="{{$fileName}}" class="" id='myDocument' height=100px width=100px >
											</a>
											@else
											<img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" class="img-fluid myImg" id='myDocument' height=100px width=100px >
											@endif
										</div>
										<div class="doc-text">
											<h3>{{$fileName}}</h3>
											@if($file->file_type != 'url')
											<a href="{{route('download-files',$file->id)}}">
												<i class="fa fa-download" aria-hidden="true"></i>
											</a>
											@endif
											<a href="javascript:void(0);" class="pl-2" id="deleteDocument" data-id="{{$file->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
				</div>
			</form>
			</div>
		</div>
	</div>
		<!-- Modal -->
			<div id="docModule" class="modal">
			<span class="close">&times;</span>
				<img class="modal-content" id="docImages">
			<div id="caption"></div>
		</div>
</div>
</div>
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
@endsection