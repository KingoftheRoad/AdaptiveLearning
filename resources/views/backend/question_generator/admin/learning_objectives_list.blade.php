@if(isset($LearningObjectives) && !empty($LearningObjectives))
<div class="selected-learning-objectives-difficulty">
    <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> {{__('languages.question_generators_menu.select_all')}}
</div>
@foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
    <!-- Get count of total no of question per Learning Objectives -->
    @php
        $learningObjectives = $learningObjectives->toArray();
        $selectedDifficultyLevel = $requestData['learning_unit'][$learningObjectives['learning_unit_id']]['learning_objective'][$learningObjectives['id']]['learning_objectives_difficulty_level'];
        $get_no_of_question_learning_objectives = $requestData['learning_unit'][$learningObjectives['learning_unit_id']]['learning_objective'][$learningObjectives['id']]['get_no_of_question_learning_objectives'];
        $noOfQuestionPerLearningObjective = App\Helpers\Helper::CountAllQuestionPerLearningObjective($learningObjectives['learning_unit_id'],$learningObjectives['id'],$testType,$selectedDifficultyLevel,$get_no_of_question_learning_objectives);
    @endphp

    <div class="selected-learning-objectives-difficulty">
        <input type="checkbox" name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{$learningObjectives['id']}}]" value="{{$learningObjectives['id']}}" class="learning_objective_checkbox" checked>
        <label>{{$learningObjectives['foci_number']}} {{$learningObjectives['title_'.app()->getLocale()] }}</label>
        <select name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{ $learningObjectives['id'] }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>
            <option value="1" @if(in_array(1,$selectedDifficultyLevel)) selected @endif>1</option>
            <option value="2" @if(in_array(2,$selectedDifficultyLevel)) selected @endif>2</option>
            <option value="3" @if(in_array(3,$selectedDifficultyLevel)) selected @endif>3</option>
            <option value="4" @if(in_array(4,$selectedDifficultyLevel)) selected @endif>4</option>
            <option value="5" @if(in_array(5,$selectedDifficultyLevel)) selected @endif>5</option>
        </select>
        <input type="text" name="learning_unit[{{$learningObjectives['learning_unit_id']}}][learning_objective][{{ $learningObjectives['id']}}][get_no_of_question_learning_objectives]" value="{{ $noOfQuestionPerLearningObjective }}" class="get_no_of_question_learning_objectives" max="{{$noOfQuestionPerLearningObjective}}">
    </div>
@endforeach
@endif