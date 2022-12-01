<div class="sec-title">
	<h3>{{__('languages.upload_document.document_list')}}</h3>
</div>
	@if(!empty($uploadData['docVideoData']))
	<div class="video-sec">
			@foreach($uploadData['docVideoData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
				<!-- <div class="video-img-sec" > -->
				<div class="video-img-sec" data-toggle="modal" data-target="#videoModal"  data-src="{{asset($file['file_path'])}}">
						@if($file['file_type'] == 'mp4' && $file['file_type'] == 'mp4' )
							<img src="{{asset('images/document_images/YouTube-logo-hero-1.png')}}" alt="{{$fileName}}" class="video-thumbnil-img" >
						@elseif($file['file_type'] == 'url')
						
							@if(isset($file['thumbnail_file_path']) && $file['thumbnail_file_path']!="")
								<img src="{{asset($file['thumbnail_file_path'])}}" alt="{{$fileName}}" class="video-thumbnil-img"></a>
							@else
								<img src="{{asset('images/document_images/YouTube-logo-hero-1.png')}}" alt="{{$fileName}}" class="video-thumbnil-img">
							@endif
						@else
						
						<img src="{{asset('images/document_images/YouTube-logo-hero-1.png')}}" alt="image Not Found" class="video-thumbnil-img" >
						@endif
			</div>
			@if($uploadData['docVideoDataCount']>=2)
				<div class="video-btn-sec">
					<form method="post" action="{{ route('exam-documents','video') }}" id="exam-documents-video">
						@csrf
						@if(isset($nodeList) && !empty($nodeList))
							<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
			      @endif
						<a class="video-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-video').submit();">{{__('languages.upload_document.more_videos')}}</a>
					</form>
				</div>
			@endif
			@endforeach
	</div>
	@endif  
	@if(!empty($uploadData['docPdfData']))								
	<div class="document-sec">
			@foreach($uploadData['docPdfData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'], '/'), 1);
			@endphp
			<div class="document-img">
				@if($file['file_type'] == 'pdf' && $file['file_type'] == 'pdf')
					<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/pdf.png')}}" alt="{{$fileName}}" ></a>
				@else
					<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
			@if($uploadData['docPdfDataCount']>=2)

				<form method="post" action="{{ route('exam-documents','pdf') }}" id="exam-documents-pdf">
					@csrf
					@if(isset($nodeList) && !empty($nodeList))
						<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
		      @endif
						<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-pdf').submit();">{{__('languages.upload_document.more_pdf_files')}}</a>
				</form>
			@endif
			</div>
			@endforeach
	</div>
	@endif  
	@if(!empty($uploadData['docPptData']))								
	<div class="document-sec">
			@foreach($uploadData['docPptData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
			<div class="document-img">
				@if($file['file_type'] == 'ppt' && $file['file_type'] == 'ppt')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/ppt.png')}}" alt="{{$fileName}}" ></a>
				@elseif($file['file_type'] == 'pptx' && $file['file_type'] == 'pptx')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/ppt.png')}}" alt="{{$fileName}}" ></a>
				@else
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
			@if($uploadData['docPptDataCount']>=2)
				<form method="post" action="{{ route('exam-documents','ppt') }}" id="exam-documents-ppt">
					@csrf
					@if(isset($nodeList) && !empty($nodeList))
						<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
		      @endif
						<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-ppt').submit();">{{__('languages.upload_document.more_ppt_files')}}</a>
				</form>
			@endif
			</div>
			@endforeach
	</div>
	@endif  
	@if(!empty($uploadData['docDocData']))								
	<div class="document-sec">
		@foreach($uploadData['docDocData'] as $file)
		@php
		$fileName = substr(strrchr($file['file_path'],'/'), 1);
		@endphp
		<div class="document-img">
			@if($file['file_type'] == 'doc' && $file['file_type'] == 'doc')
				<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/word.png')}}" alt="{{$fileName}}" ></a>
			@elseif($file['file_type'] == 'docx' && $file['file_type'] == 'docx')
				<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/word.png')}}" alt="{{$fileName}}" ></a>
			@else
				<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
			@endif
		</div>
		<div class="document-description">
			<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
		@if($uploadData['docDocDataCount']>=2)
			<form method="post" action="{{ route('exam-documents','doc') }}" id="exam-documents-doc">
				@csrf
				@if(isset($nodeList) && !empty($nodeList))
					<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
	      @endif
					<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-doc').submit();">{{__('languages.upload_document.more_word_files')}}</a>
			</form>
		@endif
		</div>
		@endforeach
	</div>
	@endif
	@if(!empty($uploadData['docExcelData']))								
	<div class="document-sec">
			@foreach($uploadData['docExcelData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
			<div class="document-img">
				@if($file['file_type'] == 'xlsx' || $file['file_type'] == 'csv')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/excel.png')}}" alt="{{$fileName}}" ></a>
				@elseif($file['file_type'] == 'xls' && $file['file_type'] == 'xls')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/excel.png')}}" alt="{{$fileName}}" ></a>
				@else
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
			@if($uploadData['docExcelDataCount']>=2)
				<form method="post" action="{{ route('exam-documents','excel') }}" id="exam-documents-excel">
					@csrf
					@if(isset($nodeList) && !empty($nodeList))
						<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
		      @endif
						<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-excel').submit();">{{__('languages.upload_document.more_excel_files')}}</a>
				</form>
			@endif
			</div>
			@endforeach
	</div>
	@endif 
	@if(!empty($uploadData['docTxtData']))								
	<div class="document-sec">
			@foreach($uploadData['docTxtData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
			<div class="document-img">
				@if($file['file_type'] == 'txt' && $file['file_type'] == 'txt')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/txt.png')}}" alt="{{$fileName}}" ></a>
				@else
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
			@if($uploadData['docTxtDataCount']>=2)
				<form method="post" action="{{ route('exam-documents','txt') }}" id="exam-documents-txt">
					@csrf
					@if(isset($nodeList) && !empty($nodeList))
						<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
		      @endif
						<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-txt').submit();">{{__('languages.upload_document.more_txt_files')}}</a>
				</form>
			@endif
			</div>
			@endforeach
	</div>
	@endif 
	@if(!empty($uploadData['docAudioData']))								
	<div class="document-sec">
			@foreach($uploadData['docAudioData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
			<div class="document-img">
				@if($file['file_type'] == 'mp3' && $file['file_type'] == 'mp3')
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/audio.png')}}" alt="{{$fileName}}" ></a>
				@else
						<a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><img src="{{asset('images/document_images/no_image.png')}}" alt="image Not Found" ></a>
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
			@if($uploadData['docAudioDataCount']>=2)
				<form method="post" action="{{ route('exam-documents','audio') }}" id="exam-documents-audio">
					@csrf
					@if(isset($nodeList) && !empty($nodeList))
						<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
		      @endif
						<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-audio').submit();">{{__('languages.upload_document.more_audio_files')}}</a>
				</form>
			@endif
			</div>
			@endforeach
	</div>
	@endif 
	@if(!empty($uploadData['docImageData']))								
	<div class="document-sec">
			@foreach($uploadData['docImageData'] as $file)
			@php
			$fileName = substr(strrchr($file['file_path'],'/'), 1);
			@endphp
			<div class="document-img document-img-view" data-toggle="modal" data-target="#imgModal"  data-src="{{asset($file['file_path'])}}">
				@if($file['file_type'] == 'jpg' && $file['file_type'] == 'jpg')
						<img src="{{asset($file['file_path'])}}" alt="{{$fileName}}" >
				@elseif($file['file_type'] == 'jpeg' && $file['file_type'] == 'jpeg')
						<img src="{{asset($file['file_path'])}}" alt="{{$fileName}}" >
				@elseif($file['file_type'] == 'png' && $file['file_type'] == 'png')
						<img src="{{asset($file['file_path'])}}" alt="{{$fileName}}" >
				@elseif($file['file_type'] == 'gif' && $file['file_type'] == 'gif')
						<img src="{{asset($file['file_path'])}}" alt="{{$fileName}}" >
				@else
						<img src="{{asset($file['file_path'])}}" alt="image Not Found" >
				@endif
			</div>
			<div class="document-description">
				<h3 class="doc-title"><span>{{$fileName}}</span><a href="{{asset($file['file_path'])}}" target="_blank" title="{{$file['file_path']}}"><i class="fa fa-download" aria-hidden="true"></i></a></h3>
				@if($uploadData['docImageDataCount']>=2)
					<form method="post" action="{{ route('exam-documents','image') }}" id="exam-documents-image">
						@csrf
						@if(isset($nodeList) && !empty($nodeList))
							<input type="hidden" name="nodeList" value="{{ json_encode($nodeList) }}">
			      @endif
							<a class="doc-btn" href="javascript:void(0);" onclick="document.getElementById('exam-documents-image').submit();">{{__('languages.upload_document.more_images_files')}}</a>
					</form>
				@endif
			@endforeach
			</div>
	</div>
	@endif 
	@if(empty($uploadData['docVideoData']) && empty($uploadData['docPdfData']) && empty($uploadData['docPptData']) && empty($uploadData['docDocData']))
		<p class="text-center">{{__('languages.no_any_documents_are_available')}}</p>
	@endif