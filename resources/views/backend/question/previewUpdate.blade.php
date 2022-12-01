
	    <div class="row" id="question_data">
	    	<div class="col-md-12 col-lg-12 col-sm-12 attmp-main-timer">
                <div class="select-lng w-25">
                    <select name="language" class="form-control select-option" id="q-exam-language" @if(request()->get("language") === "ch") disabled @endif>
                        <option value="en" @if(request()->get("language") === "en") selected @endif>{{ __('languages.english') }}</option>
                        <option value="ch" @if(request()->get("language") === "ch") selected @endif>{{ __('languages.chinese') }}</option>
                    </select>
            	</div>
            </div>
	        <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
	            <div class="attmp-main-que">
	              	<h4>{{__('languages.questions.question')}}:</h4>
	                <div class="attmp-que language_en">
	                   @php echo $question->{'question_'.$examLanguage}; @endphp
	                </div>
	                <div class="attmp-que language_ch" style="display: none;">
	                   @php echo $question->{'question_ch'}; @endphp
	                </div>
	                
	            </div>
	            <div class="attmp-main-answer language_en">
	                @if(isset($question->answers->{'answer1_'.$examLanguage}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        <div class="answer-title bg-transparent mr-2">1</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer1_'.$examLanguage}; @endphp</p>                                                  
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer1_'.$examLanguage}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        <div class="answer-title bg-transparent mr-2">2</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer2_'.$examLanguage}; @endphp</p>                                                   
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer3_'.$examLanguage}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        <div class="answer-title bg-transparent mr-2">3</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>   @php echo $question->answers->{'answer3_'.$examLanguage}; @endphp</p>                                                    
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer4_'.$examLanguage}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        
	                        <div class="answer-title bg-transparent mr-2">4</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer4_'.$examLanguage}; @endphp</p>                                                    
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	            </div>
	            <div class="attmp-main-answer language_ch" style="display: none;">
	                @if(isset($question->answers->{'answer1_ch'}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        
	                        <div class="answer-title bg-transparent mr-2">1</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer1_ch'}; @endphp</p>                                                  
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer2_ch'}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        <div class="answer-title bg-transparent mr-2">2</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer2_ch'}; @endphp</p>                                                   
	                                    </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer3_ch'}))
	                    <div class="attmp-ans pl-2 pb-2">
	                       
	                        <div class="answer-title bg-transparent mr-2">3</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer3_ch'}; @endphp</p>                                                    
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	                @if(isset($question->answers->{'answer4_ch'}))
	                    <div class="attmp-ans pl-2 pb-2">
	                        
	                        <div class="answer-title bg-transparent mr-2">4</div>
	                        <div class="progress">
	                            <div  role="progressbar">
	                                <div class="anser-detail pl-2">
	                                    <p>@php echo $question->answers->{'answer4_ch'}; @endphp</p>                                                    
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                @endif
	            </div>
	        @if (isset($question->question_type) && $question->question_type == 2)
	            <div class="attmp-main-explain">
	                <div class="attmp-expln-inner questionhint" style="display: none">
	                    <h5>{{__('languages.my_studies.explain')}}</h5>
	                    <p id="questionhint"></p>
	                </div>
	            </div>
	        @endif
			<button type="button" class="btn btn-danger language_en" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
			<button type="button" class="btn btn-danger language_ch" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" style="display:none;">{{__('想要提示？')}}</button>
	    </div>
	    {{-- @if (isset($question->question_type) && ($question->question_type == 2 || ($question->question_type == 1))) --}}
        <!-- Want a hint Modal -->
        <div class="modal fade" id="WantAHintModal" tabindex="-1" aria-labelledby="WantAHintModal" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
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
            </div>
          </div>
        </div>
    {{-- @endif --}}