@extends('backend.layouts.app')
    @section('content')
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
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
                        <div class="col-md-12">
                            <div class="sec-title">
                                <h2 class="mb-4 main-title">{{__('languages.sidebar.leaderboard')}}</h2>
                            </div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
                            <hr class="blue-line">
                        </div>
                    </div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
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
					<form class="leaderBoardFilterForm" id="leaderBoardFilterForm" method="get">	
						<div class="row">
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4"> 
                                <label for="leaderboard_type">{{__('languages.learning_type')}}</label>
								<select name="leaderboard_type" class="form-control select-option" id="leaderboard_type">
									<option value="credit_point" {{ request()->get('leaderboard_type') == 'credit_point' ? 'selected' : '' }} selected>{{__("languages.credit_points")}}</option>
                                    <option value="overall_ability" {{ request()->get('leaderboard_type') == 'overall_ability' ? 'selected' : '' }}>{{__("languages.overall_ability")}}</option>
								</select>
							</div>
						</div>
					</form>
					<div class="sm-add-user-sec card leader_board">
                        <div class="select-option-sec pb-5 card-body">
                            <div class="leaders">
                                @if($studentList->isNotEmpty())
                                    @foreach($studentList as $student)
                                    @php
                                        $randomColor = implode(',',\App\Helpers\Helper::RandomColorGenerator());
                                        $maxCreditPoint = $studentList->max('no_of_credit_points') ?? 0;
                                    @endphp

                                    <div class="leader">
                                        <a href="{{ route('credit-point-history',$student->user->id) }}">
                                            <div class="leader-wrap" >
                                                <div class="leader-ava"> {{-- style="background-color:rgb(<?= $randomColor; ?>);" --}}
                                                    {{-- <span class="leaderboard_rank" style="background-color:rgb(<?= $randomColor; ?>);">{{$loop->iteration}}</span> --}}
                                                    @if(!empty($student->users->profile_photo))
                                                        <img src="{{asset($student->user->profile_photo)}}"  class="credit_point_image" alt="credit Point">
                                                    @else
                                                        <img src="{{asset('images/credit.png')}}"  class="credit_point_image" alt="credit Point">
                                                    @endif
                                                </div>
                                                <div class="leader-content">
                                                    <div class="leader-name">{{$student->user->DecryptNameEn}}</div>
                                                    <div class="leader-score">
                                                        <div class="leader-score_title"><b>{{$student->no_of_credit_points}}</b></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="leader-bar" style="animation-delay: 0.4s;">
                                            <div class="bar" style="background-color:rgb(<?= $randomColor; ?>); width: <?= ( ($student->no_of_credit_points) ? (($student->no_of_credit_points * 100)/ $maxCreditPoint) : 0)  ?>%;"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <b><p align="center">{{__('languages.no_any_data')}}</p></b>                               
                                @endif
                            </div>
                        </div>
                    </div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection