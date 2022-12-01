@if(!empty($QuestionsList))
@foreach($QuestionsList as $questionList)
<div class="sm-que-list pl-4">
    <div class="sm-que">
        <span class="font-weight-bold pl-2">{{__('languages.test_template_management.q_id')}}: {{$questionList->id}}</span>
        <span class="pl-2"><b>{{__('languages.test_template_management.question_code')}} :</b> {{$questionList->question_code}}</span>
    </div>
    <div class="sm-answer pl-4 pt-2">
    @php echo $questionList->question_en; @endphp
    </div>
    <div class="pt5 pl-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <label for="email">{{__('languages.test_template_management.question_type')}}: {{$questionList->question_type}}</label>   
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <label for="email">{{__('languages.test_template_management.difficulty_level')}}:{{$questionList->dificulaty_level}}</label>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <label for="email">{{__('languages.test_template_management.subject')}}: {{$questionList->SunjectNameFromQuestion->subjectName}}</label>
            </div>
        </div>
    </div><hr>
</div>
@endforeach
@endif