
@if(isset($VideoList) && !empty($VideoList))
    @foreach($VideoList as $key => $video)
    <div class="video-hint-main">
        <input type="radio" name="questionVideoHintID" id="questionVideoHintID" value="{{$video->id}}" @if($selectedVideoId == $video->id) checked @endif class="radio questionVideoHint" data-language="{{ $language_code }}">
        <div class="video-hint-image">
            @if(isset($video->thumbnail_file_path) && !empty($video->thumbnail_file_path))
                <img src="{{ asset($video->thumbnail_file_path) }}" class="video-image">
            @else
                <img src="{{ asset('images/document_images/no_image.png') }}" class="video-image" style="height: 60px;width: 60px;">
            @endif
        </div>
        @if(isset($video->documentData))
        <p>
            @if(!empty($video->documentData->{'description_'.app()->getLocale()}))
                {{ $video->documentData->{'description_'.app()->getLocale()} }}
            @else
                {{ __('languages.no_available_description') }}
            @endif
        </p>
        @endif
    </div>
    @endforeach
@else
    <p>{{ __('languages.no_video_available') }}</p>
@endif
