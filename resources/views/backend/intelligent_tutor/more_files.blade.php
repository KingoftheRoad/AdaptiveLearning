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
@if(!empty($uploadData))
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
                        <video class="playVideo responsive-thumb-image" id='myImg' data-filepath="{{$content->file_path}}">
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
@endif
