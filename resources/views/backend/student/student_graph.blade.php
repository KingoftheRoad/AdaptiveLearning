@if(isset($progressQuestions) && !empty($progressQuestions))
@php
  $easy=$progressQuestions['Easy'];
  $medium=$progressQuestions['Medium'];
  $hard=$progressQuestions['Hard'];
@endphp
<div class="card shadow mb-4">
  <div class="card-body m-0">
    <h3>Speed</h3>
    {{$per_question_time}} Min/Hr
  </div>
</div>
<div class="card shadow mb-4">
  <div class="card-body m-0">
        <div class="font-weight-bold mb-4"><h4>Questions by difficulty</h4></div>
      <h4 class="small font-weight-bold">Easy <span class="float-right">{{$easy}}%</span></h4>
      <div class="progress mb-4">
          <div class="progress-bar  cm-progress-bar bg-success" role="progressbar" style="width: {{$easy}}%" aria-valuenow="{{$easy}}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <h4 class="small font-weight-bold">Medium <span class="float-right">{{$medium}}%</span></h4>
      <div class="progress mb-4">
          <div class="progress-bar  cm-progress-bar bg-success" role="progressbar" style="width: {{$medium}}%" aria-valuenow="{{$medium}}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <h4 class="small font-weight-bold">Hard <span class="float-right">{{$hard}}%</span></h4>
      <div class="progress mb-4">
          <div class="progress-bar cm-progress-bar bg-success" role="progressbar" style="width: {{$hard}}%" aria-valuenow="{{$hard}}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
  </div>
</div>
@endif