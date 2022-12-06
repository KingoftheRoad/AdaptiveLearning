@php
  $permissions = [];
  $user_id = auth()->user()->id;
  if($user_id){
    $module_permission = App\Helpers\Helper::getPermissions($user_id);
    if($module_permission && !empty($module_permission)){
      $permissions = $module_permission;
    }
  }else{
    $permissions = [];
  }
@endphp
@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
  @include('backend.layouts.sidebar')
  <div id="content" class="pl-2 pb-5">
    @include('backend.layouts.header')
     <div class="sm-right-detail-sec student-detail-sec pl-5 pr-5">
      <div class="coltainer">
        <div class="row">
          <div class="col-md-12">
            <div class="sec-title">
              <h2 class="mb-0 main-title"><img src="{{ asset('images/student-dash.png')}}" alt="1">{{__('languages.student_dashboard.student_dashboard')}}</h2>
            </div>
          </div>
        </div>
        <div class="dashboard-sec-1 mb-5">
          <!-- <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="card dashboard-rant">
                <div class="card-detail d-flex">
                  <div class="card-icon">
                    <img src="{{ asset('images/list.png')}}" alt="icon" class="icon-img">
                  </div>
                  <div class="card-text">
                    <h3 class="number">{{__('62')}}</h3>
                    <p class="text mb-0">{{__('languages.student_dashboard.no_of_activities')}} </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="card dashboard-rant">
                <div class="card-detail d-flex">
                  <div class="card-icon">
                    <img src="{{ asset('images/reward.png')}}" alt="icon" class="icon-img">
                  </div>
                  <div class="card-text">
                    <h3 class="number">{{__('68')}}</h3>
                    <p class="text mb-0">{{__('languages.student_dashboard.reward_points')}}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="card dashboard-rant">
                <div class="card-detail d-flex">
                  <div class="card-icon">
                    <img src="{{ asset('images/unread-msg.png')}}" alt="icon" class="icon-img">
                  </div>
                  <div class="card-text">
                    <h3 class="number">{{__('1')}}</h3>
                    <p class="text mb-0">{{__('languages.student_dashboard.unread_messages')}} </p>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <hr class="blue-line">
        </div>
        <!-- <div class="student-activity-list">
          <div class="row">
            <div class="col-md-12">
              <strong>{{__('languages.student_dashboard.test_list')}}</strong>
              <div id="DataTable" class="question-bank-sec">
                <table>
                  <thead>
                    <tr>
                      <th>
                        <input type="checkbox" name="" class="checkbox">
                      </th>
                      <th class="first-head"><span>{{__('languages.title')}}</span></th>
                      <th class="sec-head selec-opt"><span>{{__('languages.test.from_date')}}</span></th>
                      <th class="selec-opt"><span>{{__('languages.test.to_date')}}</span></th>
                      <th>{{__('languages.test.result_date')}}</th>
                      <th>{{__('languages.status')}}</th>
                      <th>{{__('languages.action')}}</th>
                    </tr>
                  </thead>
                  <tbody class="scroll-pane">
                    @if(!empty($ExamList))
                    @foreach($ExamList as $exam)
                    @php $examArray = $exam->toArray(); @endphp
                    <tr>
                      <td><input type="checkbox" name="" class="checkbox"></td>
                      <td>{{ $exam->title }}</td>
                      <td>{{date('d/m/Y',strtotime($exam->from_date))}}</td>
                      <td>{{ date('d/m/Y',strtotime($exam->to_date)) }}</td>
                      <td>{{date('d/m/Y',strtotime($exam->result_date))}}</td>
                      <td>
                        @if((isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))))
                        <span class="badge badge-warning">{{__('languages.pending')}}</span>
                        @else
                        <span class="badge badge-success">{{__('languages.complete')}}</span>
                        @endif
                      </td>
                      <td class="btn-edit">
                        @if(in_array('exam_management_create', $permissions))
                        @if(!isset($examArray['attempt_exams']) || (isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && $exam->status == 'publish')
                        <a href="{{ route('studentAttemptExam', $exam->id) }}" class="" title="{{__('languages.attempt_exam')}}">
                          <i class="fa fa-book" aria-hidden="true"></i>
                        </a>
                        @endif
                        @endif
                        
                        @if (in_array('result_management_read', $permissions))
                        @if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && $examArray['result_date'] <= date('Y-m-d h:m:s', time()))
                        <a href="{{route('exams.result',['examid' => $exam->id, 'studentid' => Auth::user()->id])}}" class="view-result-btn" title="{{__('languages.performance_report')}}">
                          <i class="fa fa-eye" aria-hidden="true" ></i>
                        </a>
                        @endif
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </div> 
  </div>
</div>
@include('backend.layouts.footer')
@endsection