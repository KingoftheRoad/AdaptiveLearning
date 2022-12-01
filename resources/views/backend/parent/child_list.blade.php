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
								<h2 class="mb-4 main-title">{{__('languages.parent.child_list')}}</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
				
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
											<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>
											<th>{{__('languages.parent.teacher_list')}}</th>
											<th>{{__('languages.parent.subject_list')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($List))
											@foreach($List as $data)
									        	<tr>
													<td>{{ ($data->name_en) ? App\Helpers\Helper::decrypt($data->name_en) : $data->name }}</td>
													<td>{{ ($data->name_ch) ? App\Helpers\Helper::decrypt($data->name_ch) : 'N/A' }}</td>
													<td><a href="{{ route('teacher-list',$data->id) }}" class="badge badge-success">View Teacher</a></td>
													<td><a href="{{ route('subject-list',$data->id) }}" class="badge badge-success">View Subject</a></td>
												</tr>
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