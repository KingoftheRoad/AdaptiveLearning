<input type="hidden" data-examid="{{$examId}}" id="examid" value={{$examId}} />

<input type="hidden" data-examid="{{$examId}}" id="oldStudentList" value="{{ (!empty($oldStudentList) ? implode(',',$oldStudentList) : '') }}" />
<form id="add-exma-student-in-grade-class">
    <input type="hidden" name="save_and_publish" value="publish">
    <input type="hidden" data-examid="{{$examId}}" name="examid" id="examid" value={{$examId}} />
    <input type="hidden" name="start_date" value="{{ date('d/m/Y', strtotime($examData->from_date)) }}">
    <input type="hidden" name="end_date" value="{{ date('d/m/Y', strtotime($examData->to_date)) }}">
    <select name="start_time" class="form-control select-option" id="test_start_time" style="display: none;">
        <option value="">{{__('languages.question_generators_menu.select_test_start_time')}}</option>
        @if(isset($timeSlots) && !empty($timeSlots))
            @foreach($timeSlots as $timeSlotKey => $time)
                <option @if($examData->start_time==$time) selected @endif value="{{$time}}">{{$time}}</option>
            @endforeach
        @endif
    </select>
    <select name="end_time" class="form-control select-option" id="test_end_time"  style="display: none;">
        <option value="">{{__('languages.question_generators_menu.select_test_end_time')}}</option>
        @if(isset($timeSlots) && !empty($timeSlots))
            @foreach($timeSlots as $timeSlotKey => $time)
                <option @if($examData->end_time==$time) selected @endif value="{{$time}}">{{$time}}</option>
            @endforeach
        @endif
    </select>
    @csrf
    <section class="form-steps step2">
        <div class="form-row">
            @if($type!="peergroup")
            <div class="form-grade-section w-100 m-0">
                <div class="student-grade-class-section row">
                    <div class="form-grade-heading col-lg-12">
                        <label>{{__('languages.question_generators_menu.grade-classes')}}</label>
                    </div>
                    <div class="form-grade-select-section col-lg-12">
                        @if(!empty($GradeClassData))
                        @foreach($GradeClassData as $grade)
                        <div class="form-grade-select">
                            <div class="form-grade-option">
                                <div class="form-grade-single-option">
                                    <input type="checkbox"  name="grades[]" value="{{$grade->id}}" class="question-generator-grade-chkbox">{{$grade->name}}
                                </div>
                            </div>
                            @if(!empty($grade->classes))
                            <div class="form-grade-sub-option">
                                <div class="form-grade-sub-single-option">
                                    @foreach($grade->classes as $classes)
                                    <input type="checkbox" name="classes[{{$grade->id}}][]" value="{{$classes->id}}"   data-label="{{$grade->name}}{{$classes->name}}" class="question-generator-class-chkbox" >
                                    <label>{{$grade->name}}{{$classes->name}}</label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="grade-class-date-time-list clearfix clearfix float-left"></div>
                <div class="form-group student_list_section mt-3 row">
                    <div class="student_list_heading col-lg-12">
                        <label>{{__('languages.question_generators_menu.grade-classes')}}</label>
                    </div>
                    <div class="student_list_option col-lg-12">
                        @if(isset($StudentList) && !empty($StudentList))
                        <select name="studentIds[]" class="form-control select-option w-100" id="question-generator-student-id" multiple disabled>
                        @foreach($StudentList as $student)
                            
                            <option  value="{{$student->id}}" @if(isset($oldStudentList) && !empty($oldStudentList) && in_array($student->id,$oldStudentList)) selected="selected" disabled="disabled" @endif >
                                @if(app()->getLocale() == 'en') {{$student->DecryptNameEn}}  @else {{$student->DecryptNameCh}}  @endif
                                @if($student->class_student_number) ({{$student->CurriculumYearData['class_student_number']}}) @endif
                            </option>
                        @endforeach
                        </select>
                        @endif
                    </div>
                </div> 
                

                <script type="text/javascript">
                    $("#question-generator-student-id").multiselect({
                        enableHTML: true,
                        templates: {
                            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                            filterClearBtn:
                                '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                        },
                        columns: 1,
                        placeholder: SELECT_CLASS,
                        search: true,
                        selectAll: true,
                        includeSelectAllOption: true,
                        enableFiltering: true,
                    });
                </script>
                @endif
                @if($type=="peergroup")
                <div class="form-group student_peer_group_section mt-3 row">
                    <div class="student_peer_group_heading col-lg-12">
                        <label>{{__('languages.question_generators_menu.student_peer_groups')}}</label>
                    </div>
                    <div class="student_peer_group_option col-lg-12">
                        <select class="select-option form-control assign-new-group" data-show-subtext="true" data-live-search="true" name="peerGroupIds[]" id="question-generator-peer-group-options" multiple>
                            <option value="">{{__('languages.question_generators_menu.select_peer_groups')}}</option>
                            @if($getGroupData)
                                @foreach($getGroupData as $peerGroup)
                                    <option value="{{$peerGroup->id}}" data-label="{{$peerGroup->PeerGroupName}}">{{$peerGroup->PeerGroupName}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-12 group-date-time-list mt-3"></div>
                </div>
                @endif
            </div>
        </div>
    </section>
</form>