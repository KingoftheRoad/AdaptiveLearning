@if(isset($UploadDocumentsData) && empty($UploadDocumentsData))
    <div class="modal-header">
        <button type="button" class="close" onclick="$('#WantAHintModal').modal('hide');">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
    <div class="modal-body  embed-responsive embed-responsive-16by9">
        <button type="button" class="close" onclick="$('#WantAHintModal').modal('hide');" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
            <span aria-hidden="true">&times;</span>
        </button>
        <iframe class="embed-responsive-item " id="videoDis" frameborder="0" allowtransparency="true" allowfullscreen width="100%" height="400" ></iframe>
    </div>
    <script type="text/javascript">
    	function getYoutubeId(url) {
		    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
		    const match = url.match(regExp);
		    return (match && match[2].length === 11) ? match[2] : null;
		}
		setTimeout(function() {
		    var videoSrc = '{{ $UploadDocumentsData->file_path }}';
		    var domain = videoSrc.replace('http://','').replace('https://','').split(/[/?#]/)[0];
		    if (videoSrc.indexOf("youtube") != -1) {
		        const videoId = getYoutubeId(videoSrc);
		        $("#videoDis").attr('src','//www.youtube.com/embed/'+videoId);
		    }else if (videoSrc.indexOf("vimeo") != -1) {
		        const videoId = getYoutubeId(videoSrc);
		        var matches = videoSrc.match(/vimeo.com\/(\d+)/);
		        $("#videoDis").attr('src','https://player.vimeo.com/video/'+matches[1]);
		    }else if (videoSrc.indexOf("dailymotion") != -1) {
		        var m = videoSrc.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
		        if (m !== null) {
		            if(m[4] !== undefined) {
		                $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
		            }
		            $("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
		        }
		    }else{
		        $("#videoDis").attr('src','/'+videoSrc);
		    }
		},1000);
    </script>
@else
    <div class="modal-body">
        <div class="language_ch" style="display:none;">
            @if(trim($question->general_hints_ch)!="")
            {!! $question->general_hints_ch !!}
            @else
            {{__('languages.hint_not_available')}}
            @endif
        </div>
        <div class="language_en">
            @if(trim($question->general_hints_en)!="")
            {!! $question->general_hints_en !!}
            @else
            {{__('languages.hint_not_available')}}
            @endif
        </div>
     </div>
@endif