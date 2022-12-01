@extends('backend.layouts.app')
    @section('content')
	<style>
        ul, li{
            margin:0;
            padding:0;
            list-style:none;
        }
        label{
            color:#000;
            font-size:16px;
        }
        .ms-selectall{
            color: #6767e7 !important;
            font-size: 16px !important;
            font-weight: 500;
        }
        </style>
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
		@include('backend.layouts.sidebar')
		<div id="content" class="pl-2 pb-5">
			@include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h5 class="mb-4">{{__('languages.report.student_weakness')}}</h5>
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
					<div class="row">
						<div class="col-md-6 col-lg-6 class-report-form">
						<form class="class-test-skill-weekness-report" id="class-test-skill-weekness-report" method="get" action="{{route('report.skill-weekness')}}">
							<div class="select-lng pt-2 pb-2 col-lg-6 col-md-6">
								<select name="exam_id[]"  id="exams-select-option" class="form-control select-option" multiple>
									@if(!empty($Exams))
										@foreach($Exams as $exam)
										<option value="{{$exam->id}}" @if(request()->get('exam_id') != '' && in_array($exam->id,request()->get('exam_id'))) selected @endif>{{ $exam->title}}</option>
										@endforeach
									@endif
								</select>
								@if($errors->has('exam_id'))
									<span class="validation_error">{{ $errors->first('exam_id') }}</span>
								@endif
							</div>
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search" id="">{{ __('languages.search') }}</button>
								</div>
							</div>
							</form>
						</div>
					</div>
				
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec class-test-report-scroll @if(empty($ResultList)) remove-overflow-scroll @endif">
								<table id="group-skill-report-datatable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
											<th class="first-head"><span>{{__('languages.report.student_name')}}</span></th>
											@if(!empty($ExamList))
											@foreach($ExamList as $Exam)
											<th class="first-head"><span class="report-title">{{__('languages.report.correct_percentage')}}</span><span class="report-value">{{$Exam->title}}</span></th>
											@endforeach
											@endif
                                            <th class="selec-opt"><span>{{__('languages.report.consider_hints_of_which_nodes')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
									@if(!empty($reports))
										@foreach($reports as $data)
										@if(!empty($data))
										@php $percentageArray = array_column($data,'percentage'); @endphp
										<tr class="report-header-tr">
											<td>{{$data['studentname']}}</td>
											@foreach($percentageArray as $percentage)
											<td>{{$percentage}}%</td>
											@endforeach
											<td>{{$data['levelname']}}</td>
										</tr>
										@endif
										@endforeach
									@endif
							  		</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection
