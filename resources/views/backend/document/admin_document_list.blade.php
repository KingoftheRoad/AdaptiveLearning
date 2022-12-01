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
									{{-- <h4 class="mb-4 main-title">{{ __('languages.upload_document.document_detail') }}</h4> --}}
									<h4 class="mb-4 main-title">{{ __('languages.intelligent_tutor') }}</h4>
								</div>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									@if(in_array('upload_documents_create',$permissions))
										<a href="{{ route('upload-documents.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.upload_document.add_new_document')}}</a>
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
								{{-- <form method="post" id="addDocumentFrom" class="form1" action="{{route('upload-documents.store')}}" enctype="multipart/form-data">
								@csrf
								<div class="form-row">
									<div class="col-lg-4 col-md-4">
										<div class="form-row">
											<div class="form-group col-md-12 ">
												<select required class="form-control js-states w-100" data-show-subtext="true"  data-live-search="true" name="node_id[]" multiple id="doc_node_id">
													@if(!empty($NodesList))
														{!! $NodesList !!}
													@endif
												</select>
											</div>
										</div>
									</div>
									<div class="select-lng pt-2 pb-2">
										<input type="text" class="input-search-box mr-2 " name="FileName" id="FileName" value="{{old('FileName')}}" placeholder="{{__('Enter File Name')}}">
										@if($errors->has('FileName'))
											<span class="validation_error">{{ $errors->first('FileName') }}</span>
										@endif
									</div>
									<div class="upload-que-code-sec  form-group col-lg-3 col-md-3">
										<div class="">
											<input type="file" class="form-control" name="upload[]" id="upload" multiple>
										</div>
											<div class="alert alert-danger uploadfiles" style="display:none;">Please select at least one url or files</div>
									</div>
								</div>
								
								<div class="form-row">
									<div class="form-group col-lg-4 col-md-4">
										<textarea class="form-control" name="file_description_en" id="file_description_en" placeholder="{{__('Enter English File Description')}}" value="" rows=5>{{old('file_description_en')}}</textarea>
										@if($errors->has('file_description_en'))<span class="validation_error">{{ $errors->first('file_description_en') }}</span>@endif
									</div>
									<div class="form-group col-lg-4 col-md-4">
										<textarea class="form-control" name="file_description_ch" id="file_description_ch" placeholder="{{__('Enter Chinese File Description')}}" value="" rows=5>{{old('file_description_ch')}}</textarea>
										@if($errors->has('file_description_ch'))<span class="validation_error">{{ $errors->first('file_description_ch') }}</span>@endif
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
								</form>							
								<hr/> --}}
								{{-- <div class="row">
									<div class="col-md-12">
										<div class="sec-title">
											<h5 class="mb-4 sub-title">{{ __('languages.upload_document.document_list') }}</h5>
										</div>
										<hr class="blue-line">
									</div>
								</div> --}}
							</div>
							<!-- table Add -->
					<form method="get">	
					<div class="row">
 						<div class="col-lg-3 col-md-4">
                            <div class="select-lng">
                                <input type="text" class="input-search-box mr-2" name="NodeId" value="{{request()->get('NodeId')}}" placeholder="{{__('languages.search_by_node_id')}}">
								@if($errors->has('NodeId'))
                                	<span class="validation_error">{{ $errors->first('NodeId') }}</span>
                            	@endif
                            </div>
                        </div>
						<div class="col-lg-3 col-md-4">
                            <div class="select-lng">
                                <input type="text" class="input-search-box mr-2" name="fileName" value="{{request()->get('fileName')}}" placeholder="{{__('languages.upload_document.search_by_file_name')}}">
								@if($errors->has('fileName'))
                                	<span class="validation_error">{{ $errors->first('fileName') }}</span>
                            	@endif
                            </div>
                        </div>
						<div class="col-lg-6 col-md-4">
                            <div class="select-lng">
								<textarea class="input-search-box mr-2" name="description" placeholder="{{__('languages.search_by_english_chinese_description')}}">{{request()->get('description')}}</textarea>
								@if($errors->has('description'))
                                	<span class="validation_error">{{ $errors->first('description') }}</span>
                            	@endif
                            </div>
                        </div>
					</div>
					<div class="row">						
						{{-- <div class="col-lg-2 col-md-4">
                            <div class="select-lng">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="FileType">
									<option value=''>{{ __('languages.search_by_file_type') }}</option>
									@if(!empty($statusList))
										@foreach($fileTypes as $file)
										<option value="{{$file['id']}}" {{ request()->get('FileType') == $file['id'] ? 'selected' : '' }}>{{ $file['name']}}</option>
										@endforeach
                                	@endif
								</select>
								@if($errors->has('FileType'))
                                	<span class="validation_error">{{ $errors->first('FileType') }}</span>
                            	@endif
                            </div>
                        </div> --}}
						<div class="col-lg-2 col-md-4">
                            <div class="select-lng">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="language" id="language">
									{{-- <option value=''>{{ __('Select Language') }}</option> --}}
									<option value=''>{{__('languages.all')}}</option>
									@if(!empty($languages))
										@foreach($languages as $language)
										<option value="{{$language->id}}" {{ request()->get('language') == $language->id ? 'selected' : '' }}>{{ $language->name}}</option>
										@endforeach
                                	@endif
								</select>
								@if($errors->has('language'))
                                	<span class="validation_error">{{ $errors->first('language') }}</span>
                            	@endif
                            </div>
                        </div>
						<div class="col-lg-2 col-md-4">
                            <div class="select-lng">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="Status" id="Status">
									<option value=''>{{ __('languages.select_status') }}</option>
									@if(!empty($statusList))
										@foreach($statusList as $status)
										<option value="{{$status['id']}}" {{ request()->get('Status') == $status['id'] ? 'selected' : '' }}>{{ $status['name']}}</option>
										@endforeach
                                	@endif
								</select>
								@if($errors->has('Status'))
                                	<span class="validation_error">{{ $errors->first('Status') }}</span>
                            	@endif
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-2">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                            </div>
                        </div>
                    </div>
					</form>
					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec upload_table">
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>#{{__('languages.sr_no')}}</span></th>
							          		<th class="first-head"><span>{{__('languages.nodes.node_id')}}</span></th>
											<th class="first-head"><span>{{__('languages.upload_document.file_name')}}</span></th>
											<th class="first-head"><span>{{__('languages.language_name')}}</span></th>
											{{-- <th class="first-head"><span>{{__('languages.upload_document.image')}}</span></th> --}}
											<th class="first-head"><span>{{__('languages.description')}}</span></th>
											{{-- <th class="first-head"><span>{{__('languages.upload_document.chinese_description')}}</span></th> --}}
											<th class="first-head"><span>{{__('languages.status')}}</span></th>
											<th class="first-head"><span>{{__('languages.action')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($uploadData))
											@foreach($uploadData as $document)
												<tr>
													<td><input type="checkbox" name="" class="checkbox"></td>
													<td>{{$loop->iteration}}</td>
													<td>{{ (isset($document->node_id) ? App\Helpers\Helper::getNodesName($document->node_id) : '----') }}</td>
													<td>
														@foreach($document->document as $key => $subFiles)
															@if($subFiles->file_type != 'url')
																@if(count($document->document) != ($key + 1))
																	{{!empty($subFiles->file_name) ? $subFiles->file_name.',' :''}}
																@else
																	{{!empty($subFiles->file_name) ? $subFiles->file_name :''}}
																@endif
															@endif
														@endforeach	
													</td>
													<td>{{ $document->language->name ?? ''}}</td>
													@if(isset($document->language->name))
														@if($document->language->name == 'English')
															<td>{{($document->description_en) ? $document->description_en : '----' }}</Td>
														@else
															<td>{{($document->description_ch) ? $document->description_ch : '----'}}</Td>
														@endif
													@else
														<td>{{'----'}}</td>
													@endif
													<td>
														@if($document->status == "active")
															<span class="badge badge-success">{{__('languages.active')}}</span>
														@else
															<span class="badge badge-danger">{{__('languages.inactive')}}</span>
														@endif
													</td>
													<td>
														@if(in_array('upload_documents_update',$permissions))
															<a href="{{ route('upload-documents.edit', $document->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
														@endif
															<a href="javascript:void(0);" class="pl-2" id="deleteDocument" data-id="{{$document->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
														{{-- @if($document->file_type != 'url')
															<a href="{{route('download-files',$document->id)}}">
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>
														@endif --}}
													</td>
												</tr>
											@endforeach
										@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($uploadData->firstItem()) ? $uploadData->firstItem() : 0}} {{__('languages.to')}} {{!empty($uploadData->lastItem()) ? $uploadData->lastItem() : 0}}
								{{__('languages.of')}}  {{$uploadData->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$uploadData->appends(request()->input())->links()}}
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$uploadData->total()}}" @if(app('request')->input('items') == $uploadData->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			
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
/*for pagination add this script added by mukesh mahanto*/ 
document.getElementById('pagination').onchange = function() {
	window.location = "{!! $uploadData->url(1) !!}&items=" + this.value;	
}; 
</script>
@endsection