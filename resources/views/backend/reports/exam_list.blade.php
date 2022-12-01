@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('Test List')}}</h2>
								<!-- <div class="btn-sec">
									<a href="{{ route('exams.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('Add Test')}}</a>
								</div> -->
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
						<div class="col-md-12 pb-2">
							<div class="select-lng pt-2 pb-2" style="float:left;">
								<a href="{{route('report.class-test-reports.correct-incorrect-answer')}}" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('Correct/Incorrect Test Report') }}</a>
							</div>
							<div class="select-lng pt-2 pb-2">
								<a href="{{ route('reports.exam-list')}}" class="btn-search remove-radius {{ (request()->is('reports/exam-list')) ? 'active': ''  }}" id="">{{ __('Student Result') }}</a>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="example" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th class="first-head"><span>{{__('Title')}}</span>
							      	  		</th>
											<th class="sec-head selec-opt">
												<span>{{__('From Date')}}</span>
											</th>
											<th>{{__('To Date')}}</th>
											<th>{{__('Rsesult Date')}}</th>
                                            <th>{{__('Time Duration (Minute)')}}</th>
											<th>{{__('Status')}}</th>
											<th>{{__('Action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @if(!empty($exams))
										@foreach($exams as $exam)
										<tr>
                                            <td>{{$exam->title}}</td>
                                            <td>{{date('d/m/Y',strtotime($exam->from_date))}}</td>
                                            <td>{{date('d/m/Y',strtotime($exam->to_date))}}</td>
                                            <td>{{date('d/m/Y',strtotime($exam->result_date))}}</td>
                                            <td>{{$exam->time_duration}}</td>
                                            <td>
												@if($exam->status == 'active')
													<span class="badge badge-success">Active</span>
												@elseif($exam->status == 'pending')
													<span class="badge badge-warning">Pending</span>
												@elseif($exam->status == 'complete')
													<span class="badge badge-info">Complete</span>
												@elseif($exam->status == 'publish')
													<span class="badge badge-success">Publish</span>
												@else
													<span class="badge badge-danger">InActive</span>
												@endif
											</td>
                                            <td class="edit-class">
												<a href="{{ route('reports.attempt-exams.student-list', $exam->id) }}" data-toggle="tooltip" title="{{__('Check Student Exam Result')}}" class="pl-2">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</a>
                                            </td>
										</tr>
										@endforeach
										@endif
									</tbody>
								</table>
								<div id="table_box_bootstrap">
									<div class="table-export-table">
										<div class="export-table setting-table">
											<i class="fa fa-download"></i>
											<p>Exported Selected</p>
										</div>
										<div class="configure-table setting-table">
											<i class="fa fa-cog"></i>
											<p>Exported Selected</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection