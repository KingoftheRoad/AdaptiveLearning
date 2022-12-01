<div class="row">
	<div class="col-md-12">
		<div id="DataTable" class="question-bank-sec">
			<table>
				<thead>
					<tr>
						<th>
							<input type="checkbox" name="" class="checkbox">
						</th>
						<th class="first-head"><span>{{__('Title')}}</span></th>
						<th class="sec-head selec-opt"><span>{{__('From Date')}}</span></th>
						<th class="selec-opt"><span>{{__('To Date')}}</span></th>
						<th>{{__('Result Date')}}</th>
						<th>{{__('Status')}}</th>
						<th>{{__('Action')}}</th>
					</tr>
				</thead>
				<tbody class="scroll-pane">
					@if(!empty($examList))
						@foreach($examList as $exam)
							@php $examArray = $exam->toArray(); @endphp
							<tr>
								<td><input type="checkbox" name="" class="checkbox"></td>
								<td>{{ $exam->title }}</td>
								<td>{{date('d/m/Y',strtotime($exam->from_date))}}</td>
								<td>{{ date('d/m/Y',strtotime($exam->to_date)) }}</td>	
								<td>{{date('d/m/Y',strtotime($exam->result_date))}}</td>
								<td>
									@if((isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))))
									<span class="badge badge-warning">Pending</span>
									@else
									<span class="badge badge-success">Complete</span>
									@endif
								</td>
								<td class="btn-edit">
								@if(in_array('attempt_exam_update', $permissions))
									@if(!isset($examArray['attempt_exams']) || (isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && $exam->status == 'publish')
										<a href="{{ route('studentAttemptExam', $exam->id) }}" class="" title="Test">
											<i class="fa fa-book" aria-hidden="true"></i>
										</a>
									@endif
								@endif

								@if (in_array('result_management_read', $permissions))	
									@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && $examArray['result_date'] <= date('Y-m-d h:m:s', time()))
									<a href="{{route('exams.result',['examid' => $exam->id, 'studentid' => Auth::user()->id])}}" class="view-result-btn">
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
                   