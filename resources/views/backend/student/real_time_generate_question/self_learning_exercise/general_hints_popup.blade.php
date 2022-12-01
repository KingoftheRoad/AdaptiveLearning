<div class="modal fade" id="WantAHintModal" tabindex="-1" aria-labelledby="WantAHintModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            @if(isset($UploadDocumentsData) && empty($UploadDocumentsData))
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
            <div class="modal-body  embed-responsive embed-responsive-16by9">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <iframe class="embed-responsive-item " id="videoDis" frameborder="0" allowtransparency="true" allowfullscreen width="100%" height="400" ></iframe>
            </div>
            @else
            <div class="modal-body">
                @if($examLanguage=='ch')
                    @if(trim($Question['general_hints_ch'])!="")
                    {!! $Question['general_hints_ch'] !!}
                    @else
                    {{__('languages.hint_not_available')}}
                    @endif
                @else
                    @if(trim($Question['general_hints_en'])!="")
                    {!! $Question['general_hints_en'] !!}
                    @else
                    {{__('languages.hint_not_available')}}
                    @endif
                @endif
            </div>
            @endif
        </div>
    </div>
</div>