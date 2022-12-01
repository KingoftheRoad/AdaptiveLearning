<table class="table">
  <tr>
    <th scope="col">#Q.No</th>
    <th scope="col">{{__('Questions')}}</th>
    <th scope="col">{{__('Suggested Answers')}}</th>
    <th scope="col">{{__('Student Answer')}}</th>
    <th scope="col">{{__('Correct / Incorrect Answer')}}</th>
    <th scope="col">{{__('Skill')}}</th>
  </tr>
  @if(!empty($resultArray))
  @foreach($resultArray as $result)
  <tr>
    <td>{{$loop->iteration}}</td>
    <td><?php echo $result['question']; ?></td>
    <td><?php echo $result['correct_answer']; ?></td>
    <td><?php echo $result['student_answer'] ?? '';?></td>
    @if($result['answer_status'] == 'true')
    <td class="reports-result correct-icon">
      <span style="visibility: hidden;">{{__("Correct")}}</span>
      <i class="fa fa-check" aria-hidden="true"></i>
    </td>
    @else
    <td class="reports-result incorrect-icon">	
      <span style="visibility: hidden;">{{__("Incorrect")}}</span>
      <i class="fa fa-times" aria-hidden="true"></i>
    </td>
    @endif
    <td>{{$result['skill'] ?? 0}}</td>
  </tr>
  @endforeach
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>{{__('languages.report.total_no_of_correct_answer')}}</strong></td>
    <td style="text-align:center;"><strong>{{$result['total_correct_answer']}} / {{$result['countQuestions']}}</strong></td>
    <td></td>
  </tr>
  @endif
</table>

<!-- Start section Student weekness questions -->
@if(!empty($QuestionAnswerWeeknessSkills))
  @php
    $KeyImprovementData='';
    $KeyWeaknessData='';
  @endphp
  @for ($i = 0; $i < sizeof($QuestionAnswerWeeknessSkills) ; $i++)
    @if($i <= 1)
      @php
        $KeyImprovementData.='<li style="list-style:disc;">'.$QuestionAnswerWeeknessSkills[$i].'</li>';
      @endphp
    @else
      @php
        $KeyWeaknessData.='<li style="list-style:disc;">'.$QuestionAnswerWeeknessSkills[$i].'</li>';
      @endphp
    @endif
  @endfor
<div id="accordionImprovement" class="weakness_result_list">
  <div class="card1">
    <div class="card-header1" id="heading{{$student_id}}">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseImprovement{{$student_id}}" aria-expanded="false" aria-controls="collapse{{$student_id}}">
          <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.key_improvement_points')}}</b></h6 >
        </button>
      </h5>
    </div>
    <div id="collapseImprovement{{$student_id}}" class="collapse" aria-labelledby="heading{{$student_id}}" data-parent="#accordionImprovement">
      <ul class="list-unstyled ml-5">
        @if($KeyImprovementData!="")
          {!! $KeyImprovementData !!}
        @else
          <li>{{__('languages.report.no_key_improvement_point_available')}}</li>
        @endif
      </ul>
    </div>
  </div>
</div>
<div id="accordion" class="weakness_result_list">
  <div class="card1">
    <div class="card-header1" id="heading{{$student_id}}">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{$student_id}}" aria-expanded="false" aria-controls="collapse{{$student_id}}">
          <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.weakness')}}</b></h6 >
        </button>
      </h5>
    </div>
    <div id="collapse{{$student_id}}" class="collapse" aria-labelledby="heading{{$student_id}}" data-parent="#accordion">
      <ul class="list-unstyled ml-5">
        @if($KeyWeaknessData!="")
          {!! $KeyWeaknessData !!}
        @else
          <li>{{__('languages.report.no_weakness_available')}}</li>
        @endif
      </ul>
    </div>
  </div>
</div>
@endif

<script>
  $(document).ready(function(){
    //Add a minus icon to the collapse element that is open by default
      $('.weakness_result_list .collapse.show').each(function(){
          $(this).parent().find(".fa").removeClass("fa-plus").addClass("fa-minus");
      });
        
    //Toggle plus/minus icon on show/hide of collapse element
      $('.weakness_result_list .collapse').on('shown.bs.collapse', function(){
          $(this).parent().find(".fa").removeClass("fa-plus").addClass("fa-minus");
      }).on('hidden.bs.collapse', function(){
          $(this).parent().find(".fa").removeClass("fa-minus").addClass("fa-plus");
      });       
  });
  </script>

<!-- Start section Student weekness questions -->