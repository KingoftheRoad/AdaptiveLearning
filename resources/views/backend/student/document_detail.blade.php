@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('Document Detail')}}</h2>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
							@if(!empty($url))
										@php
											$fileName = substr(strrchr($url, '\\'), 1);
											$devideExtension = explode(".",$fileName); 
										@endphp
										@if(strtolower($devideExtension[1]) == 'csv')
												<img src="{{asset('images/document_images/csv.png')}}" height="100px" width="100px" />
										@elseif(strtolower($devideExtension[1]) == 'txt')
												<img src="{{asset('images/document_images/txt.png')}}" height="100px" width="100px" />	
										@elseif(strtolower($devideExtension[1]) == 'png')
												<img src="{{ asset($url)}}" height="300px" width="300px" />	
										@elseif(strtolower($devideExtension[1]) == 'jpg')
												<img src="$path" height="100px" width="100px" />
                                        @elseif(strtolower($devideExtension[1]) == 'pdf')
                                        <embed
                                            src="{{asset($url)}}"
                                            style="width:600px; height:800px;"
                                            frameborder="0"
                                        >
                                        @elseif(strtolower($devideExtension[1]) == 'mp4')
                                            <video width="320" height="240" controls>
                                                <source src="{{asset($url)}}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @elseif(strtolower($devideExtension[1]) == 'mp3')
                                        <audio controls>
                                            <!-- <source src="horse.ogg" type="audio/ogg"> -->
                                            <source src="{{asset($url)}}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
										@else
											<b>file not defined in list</b>
										@endif
							@endif
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		
		@include('backend.layouts.footer')
@endsection