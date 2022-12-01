<div class="student-grade-class-section row">
<input type="hidden" name="examId" id="ExamId" value="{{$ExamId}}">
    @if(!$GradeClassData->isEmpty())
    <div class="form-grade-heading col-lg-3">
        <label>{{__('languages.question_generators_menu.grade-classes')}}</label>
    </div>
    <div class="form-grade-select-section col-lg-9">
        @foreach($GradeClassData as $grade)
        <div class="form-grade-select">
            <div class="form-grade-option">
                <div class="form-grade-single-option">
                    <input type="checkbox"  name="grades[]" value="{{$grade->id}}" class="question-generator-grade-chkbox">{{$grade->name}}
                </div>
            </div>
            @if(!$grade->classes->isEmpty())
            <div class="form-grade-sub-option">
                <div class="form-grade-sub-single-option">
                    @foreach($grade->classes as $classes)
                    <input type="checkbox" name="classes[{{$grade->id}}][]" value="{{$classes->id}}"   data-label="{{$grade->name}}{{$classes->name}}" class="question-generator-class-chkbox">
                    <label>{{$grade->name}}{{$classes->name}}</label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <hr class="blue-line">
    @if(!empty($PeerGroupData))
    <div class="form-grade-heading col-lg-3">
        <label>{{__('languages.question_generators_menu.peer_groups')}}</label>
    </div>
    <div class="form-grade-select-section col-lg-9">
        <div class="form-grade-select">
            <div class="form-grade-sub-option">
                <div class="form-grade-sub-single-option">
                    @foreach($PeerGroupData as $PeerGroup)
                    <input type="checkbox" name="PeerGroup[{{$PeerGroup->id}}][]" value="{{$PeerGroup->id}}" data-label="{{$PeerGroup->group_name}}" class="question-generator-peer-group-chkbox">
                    <label>{{$PeerGroup->group_name}}</label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>