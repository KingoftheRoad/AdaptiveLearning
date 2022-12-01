
                    @if(!empty($Questions))
                    @foreach($Questions as $question)
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
                                <div class="attmp-main-que">
                                    <h4>Q-{{ $questionNo }}</h4>
                                  
                                    <div class="attmp-que">
                                       @php echo $question->{'question_'.$examLanguage}; @endphp
                                    </div>

                                </div>

                                <div class="attmp-main-answer">
                                    @php
                                        $random_number_array = range(1,4);
                                        shuffle($random_number_array );
                                        $random_number_array = array_slice($random_number_array ,0,4);
                                    @endphp
                                    @if(isset($question_position) && !empty($question_position) && isset($question_position[$question->id]))
                                        @php
                                            if($question_position[$question->id]!="")
                                            {
                                                $random_number_array=json_decode($question_position[$question->id]);
                                            }
                                        @endphp
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[0]}}" class="radio mr-2 checkanswer" @if(!empty($SelectedAnswersArray) && isset($SelectedAnswersArray['ans_que_'.$question->id]) && $SelectedAnswersArray['ans_que_'.$question->id] == $random_number_array[0]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
                                            <div class="answer-title mr-2">A</div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}; @endphp</p>                                                  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[1]}}" class="radio mr-2 checkanswer" @if(!empty($SelectedAnswersArray) && isset($SelectedAnswersArray['ans_que_'.$question->id]) && $SelectedAnswersArray['ans_que_'.$question->id] == $random_number_array[1]) checked @endif question-id="{{$question->id}}"  @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
                                            <div class="answer-title mr-2">B</div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}; @endphp</p>                                                   
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[2]}}" class="radio mr-2 checkanswer" @if(!empty($SelectedAnswersArray) && isset($SelectedAnswersArray['ans_que_'.$question->id]) && $SelectedAnswersArray['ans_que_'.$question->id] == $random_number_array[2]) checked @endif question-id="{{$question->id}}"  @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
                                            <div class="answer-title mr-2">C</div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                    <div class="anser-detail pl-2">
                                                        <p>   @php echo $question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}; @endphp</p>                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}))
                                        <div class="attmp-ans pl-2 pb-2">
                                            <input type="radio" name="ans_que_{{$question->id}}" value="{{$random_number_array[3]}}" class="radio mr-2 checkanswer" @if(!empty($SelectedAnswersArray) && isset($SelectedAnswersArray['ans_que_'.$question->id]) && $SelectedAnswersArray['ans_que_'.$question->id] == $random_number_array[3]) checked @endif question-id="{{$question->id}}" @if(isset($examDetail[0]->exam_type)) question-type="{{$examDetail[0]->exam_type}}" @endif @if(isset($examDetail->exam_type)) question-type="{{$examDetail->exam_type}}" @endif>
                                            <div class="answer-title mr-2">D</div>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                    <div class="anser-detail pl-2">
                                                        <p>@php echo $question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}; @endphp</p>                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @if (isset($examDetail[0]->exam_type) && $examDetail[0]->exam_type == 2)
                                <div class="attmp-main-explain">
                                    <div class="attmp-expln-inner questionhint" style="display: none">
                                        <h5>{{__('languages.my_studies.explain')}}</h5>
                                        <p id="questionhint"></p>
                                    </div>
                                </div>
                            @endif
                            @if (isset($examDetail->exam_type) && $examDetail->exam_type == 2)
                                <div class="attmp-main-explain">
                                    <div class="attmp-expln-inner questionhint" style="display: none">
                                        <h5>{{__('languages.my_studies.explain')}}</h5>
                                        <p id="questionhint"></p>
                                    </div>
                                </div>
                            @endif
                            </div>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 attmp-all-button">
                                @if ($Questionsfirstid != $Questions[0]['id'])     
                                <div class="attmp-prev-btn attmp-butns">
                                    @if($examLanguage == 'en')
                                        <button type="button" class="btn btn-info mr-2" id="prevquestion" question-id-prev="{{$Questions[0]['id']}}" data-text="Previous" >{{__('languages.my_studies.previous')}}</button>
                                    @else
                                        <button type="button" class="btn btn-info mr-2" id="prevquestion" question-id-prev="{{$Questions[0]['id']}}" data-text="Previous" >{{__('以前的')}}</button>
                                    @endif
                                </div>
                                @endif
                                @if ($Questionslastid != $Questions[0]['id'])
                                    <div class="attmp-next-btn attmp-butns">
                                        @if($examLanguage == 'en')
                                            <button type="button" class="btn btn-warning mr-2" id="nextquestion" question-id-next="{{$Questions[0]['id']}}" disabled data-text="Next" >{{__('languages.my_studies.next')}}</button>    
                                        @else
                                            <button type="button" class="btn btn-warning mr-2" id="nextquestion" question-id-next="{{$Questions[0]['id']}}" disabled data-text="Next" >{{__('下一個')}}</button>   
                                        @endif
                                    </div>
                                @endif
                                <div class="attmp-submit-btn attmp-butns">
                                    @if($examLanguage == 'en')
                                        <button type="submit" class="btn btn-success mr-2" style="display:none;" id="submitquestion" disabled @if ($Questionslastid == $Questions[0]['id']) submit-id="1" @endif>{{__('languages.submit')}}</button>
                                    @else
                                        <button type="submit" class="btn btn-success mr-2" style="display:none;" id="submitquestion" disabled @if ($Questionslastid == $Questions[0]['id']) submit-id="1" @endif>{{__('提交')}}</button>
                                    @endif
                                </div>

                                @if(isset($examDetail[0]->exam_type) && ($examDetail[0]->exam_type == 2 || ($examDetail[0]->exam_type == 1 && $examDetail[0]->self_learning_test_type == 1)))
                                    <div class="">
                                        @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                                            {{-- <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button> --}}
                                            @if($examLanguage == 'en')
                                                <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button><button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                            @else
                                                <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('想要提示？')}}</button>
                                            @endif
                                        @else
                                            @if($examLanguage == 'en')
                                                <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                            @else
                                                <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('想要提示？')}}</button>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                                
                                @if(isset($examDetail->exam_type) && ($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)))
                                    <div class="">
                                        @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                                            {{-- <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button> --}}
                                            @if($examLanguage == 'en')
                                                <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                            @else
                                                <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData->file_path }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('想要提示？')}}</button>
                                            @endif
                                        @else
                                            {{-- <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button> --}}
                                            @if($examLanguage == 'en')
                                            <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                                            @else
                                            <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('想要提示？')}}</button>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(isset($examDetail->exam_type) && ($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)))
                        <!-- Want a hint Modal -->
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
                                            @if(trim($question->general_hints_ch)!="")
                                            {!! $question->general_hints_ch !!}
                                            @else
                                            {{__('languages.hint_not_available')}}
                                            @endif
                                        @else
                                            @if(trim($question->general_hints_en)!="")
                                            {!! $question->general_hints_en !!}
                                            @else
                                            {{__('languages.hint_not_available')}}
                                            @endif
                                        @endif
                                     </div>
                                @endif
                            </div>
                          </div>
                        </div>
                    @endif

                    @if(isset($examDetail[0]->exam_type) && ($examDetail[0]->exam_type == 2 || ($examDetail[0]->exam_type == 1 && $examDetail[0]->self_learning_test_type == 1)))
                        <!-- Want a hint Modal -->
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
                                        @if(trim($question->general_hints_ch)!="")
                                        {!! $question->general_hints_ch !!}
                                        @else
                                        {{__('languages.hint_not_available')}}
                                        @endif
                                    @else
                                        @if(trim($question->general_hints_en)!="")
                                        {!! $question->general_hints_en !!}
                                        @else
                                        {{__('languages.hint_not_available')}}
                                        @endif
                                    @endif
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                    @endif