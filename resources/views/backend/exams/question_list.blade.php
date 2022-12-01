@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
                    @if(session()->has('success_msg'))
                    <div class="alert alert-success">
                        {{ session()->get('success_msg') }}
                    </div>
                    @endif
                    @if(session()->has('error_msg'))
                    <div class="alert alert-danger">
                        {{ session()->get('error_msg') }}
                    </div>
                    @endif
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.test.test_name')}} : {{(!empty($ExamData) ? $ExamData->title : '')}}</h4>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.question_list')}}</h4>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                            <a href="{{route('exams.index')}}" class="btn-back">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>

                    <!-- Start Question list search -->
                    <form class="addQuestionFilterForm" id="addQuestionFilterForm" method="get">
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <select name="grade_id" class="form-control select-search select-option" id="grade-id" >
                                    @if(!empty($Grades))
                                        <option value="">{{ __('languages.grade') }}</option>
                                        @foreach($Grades as $grade)
                                        <option value={{ $grade->id }} {{ request()->get('grade_id') == $grade->id ? 'selected' : '' }} >{{ ucfirst($grade->name) }}</option> 
                                        @endforeach
                                    @else
                                        <option value="">{{ __('languages.no_grade_available') }}</option>
                                    @endif
                                </select>
                                @if($errors->has('grade_id'))
                                    <span class="validation_error">{{ $errors->first('grade_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <select name="subject_id" class="form-control select-search select-option" id="subject-id" @if(isset($subjects) && !empty($subjects)) "style=display:block;" @else "style=display:none;" @endif>
                                    <option value="">{{ __('languages.subject') }}</option>
                                    @if(!empty($subjects))
                                        @foreach($subjects as $subject)
                                            <option value="{{$subject->id}}" {{ request()->get('subject_id') == $subject->id ? 'selected' : '' }}>{{$subject->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if($errors->has('subject_id'))
                                    <span class="validation_error">{{ $errors->first('subject_id') }}</span>
                                @endif
                            </div>
                        </div> -->
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <select name="strand_id" class="form-control select-search select-option" id="strand-id">
                                    <option value="">{{ __('languages.strands') }}</option>
                                    @if(!empty($strands))
                                    @foreach($strands as $strand)
                                    <option value="{{$strand->id}}" {{ request()->get('strand_id') == $strand->id ? 'selected' : '' }}>{{$strand->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if($errors->has('strand_id'))
                                    <span class="validation_error">{{ $errors->first('strand_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">                            
                                <select name="learning_unit_id"  class="form-control select-search select-option" id="learning-unit">
                                    <option value="">{{ __('languages.learning_units') }}</option>
                                    @if(!empty($LearningUnits))
                                    @foreach($LearningUnits as $LearningUnit)
                                    <option value="{{$LearningUnit->id}}" {{ request()->get('learning_unit_id') == $LearningUnit->id ? 'selected' : '' }}>{{$LearningUnit->name}}</option>
                                    @endforeach
                                    @endif  
                                </select>
                                @if($errors->has('learning_unit_id'))
                                    <span class="validation_error">{{ $errors->first('learning_unit_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <select name="learning_objective_id"  class="form-control select-search select-option" id="learning-objectives">
                                    <option value="">{{ __('languages.learning_objectives') }}</option>
                                    @if(!empty($LearningObjectives))
                                    @foreach($LearningObjectives as $LearningObjective)
                                    <option value="{{$LearningObjective->id}}" {{ request()->get('learning_objective_id') == $LearningObjective->id ? 'selected' : '' }}>{{$LearningObjective->foci_number}} {{$LearningObjective->title}}</option>
                                    @endforeach
                                    @endif  
                                </select>
                                @if($errors->has('learning_objective_id'))
                                    <span class="validation_error">{{ $errors->first('learning_objective_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="question_code" value="{{request()->get('question_code')}}" placeholder="{{__('languages.question_code')}}">
                            </div>
                        </div>
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="difficulty_level"  class="form-control select-search select-option">
                                <option value="">{{ __('languages.difficulty_level') }}</option>
                                @if(!empty($difficultyLevels))
                                    @foreach($difficultyLevels as $difficultyLevel)
                                    <option value="{{$difficultyLevel['id']}}" {{ request()->get('difficulty_level') == $difficultyLevel['id'] ? 'selected' : '' }}>{{ $difficultyLevel['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('difficulty_level'))
                                <span class="validation_error">{{ $errors->first('difficulty_level') }}</span>
                            @endif
                        </div>
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">
                            <select name="question_types"  class="form-control select-search select-option">
                                <option value="">{{ __('languages.question_type') }}</option>
                                @if(!empty($QuestionTypes))
                                    @foreach($QuestionTypes as $QuestionType)
                                    <option value="{{$QuestionType['id']}}" 
                                    <?php
                                    if(isset($_GET['question_types'])){
                                        if(request()->get('question_types') === $QuestionType['id']){
                                            echo 'selected';
                                        }
                                    }else{
                                        if($ExamData->exam_type == $QuestionType['id']){
                                            echo 'selected';
                                        }
                                    }
                                    ?>
                                    >{{ $QuestionType['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('learning_unit_id'))
                                <span class="validation_error">{{ $errors->first('learning_unit_id') }}</span>
                            @endif
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <!-- End Search form Question -->

                    <!-- Question form Listinf Start -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if($errors->has('question_ids'))<span class="validation_error">{{ $errors->first('question_ids') }}</span>@endif
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="checkbox" name="select-all-questions" data-examid="{{request()->id}}" id="select-all-questions" class="checkbox" {{$checked}}/>
                                        <span class="font-weight-bold pl-2"> {{__('languages.check_all')}}</span><br>
                                    </div>
                                </div>
                            </div>
                            <hr>
							@csrf()
                            @if(!empty($questionList))
                            @php 
                                $iteration = $questionList->perPage() * ($questionList->currentPage() - 1);
                                
                            @endphp
                            @foreach($questionList as $question)
                            @php 
                                $assignedQuestion = [];
                                if(!empty($assignQuestion)){
                                    $assignedQuestion = explode(',', $assignQuestion->question_ids);
                                }
                            @endphp
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="checkbox" name="question_ids" class="checkbox question-ids" value="{{$question->id}}"  data-examid="{{request()->id}}" @if(in_array($question->id,$assignedQuestion)) checked @endif/>
                                        <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                        <span class="font-weight-bold pl-2">{{__('languages.test_template_management.q_id')}} : {{++$iteration}}</span>
                                        <span class="pl-2"><b>{{__('languages.question_code')}} : </b> {{$question->naming_structure_code ?? ''}}</span>
                                    </div>
                                    <div class="sm-answer pl-4 pt-2">
                                        <?php echo $question->{'question_'.app()->getLocale()}; ?>
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.question_type')}} : {{$question->question_type}}</label>   
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">
                                                    {{__('languages.difficulty_level')}} :
                                                    @for($i=1; $i <= $question->dificulaty_level; $i++)
													<span style="font-size:100%;color:red;">&starf;</span>
													@endfor
                                                </label>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.subject')}} : {{$question->objectiveMapping->subjectName ?? ''}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            @endif
                            <div>{{__('languages.showing')}} {{($questionList->firstItem())}} {{__('languages.to')}} {{$questionList->lastItem()}}
								{{__('languages.of')}}  {{$questionList->total()}} {{__('languages.entries')}}
							</div>
                            <div class="row">
                                <div class="col-lg-10 col-md-10 ">
                                    @if((app('request')->input('items'))=== null)
                                        {{$questionList->appends(request()->input())->links()}}
                                    @else
                                        {{$questionList->appends(compact('items'))->links()}}
                                    @endif 
                                </div>
                                <div calss="col-lg-2 col-md-2">
                                    <form>
                                        <label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
                                        <select id="pagination" >
                                            <option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
                                            <option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
                                            <option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
                                            <option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
                                            <option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
                                            <option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
                                            <option value="{{$questionList->total()}}" @if(app('request')->input('items') == $questionList->total()) selected @endif >{{__('languages.all')}}</option>
                                        </select>
                                    </form>
                                <div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
   //for per Page on filteration hidden 
   var TotalFilterData = "{!! $TotalFilterData !!}";
	if((TotalFilterData > 0 && TotalFilterData <= 10)){
	    document.getElementById("pagination").style.visibility = "hidden";
        document.getElementById("per_page").style.visibility = "hidden";
    }
        /*for pagination add this script added by mukesh mahanto*/ 
        document.getElementById('pagination').onchange = function() {
            window.location = "{!! $questionList->url(1) !!}&items=" + this.value;			
        }; 
</script>
@include('backend.layouts.footer') 
@endsection