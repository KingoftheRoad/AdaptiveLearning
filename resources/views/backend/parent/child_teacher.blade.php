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
								<h2 class="mb-4 main-title">{{__('languages.parent.child_teacher_list')}}</h2>
								
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
											<th>#{{__('languages.sr_no')}}</th>
											<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>  
							          		<th class="first-head"><span>{{__('languages.email')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($TeachersList))
											@foreach($TeachersList as $data)
									        	<tr>
													<td>{{ $loop->iteration }}</td>
													<td>{{ ($data->teachers->name_en) ? App\Helpers\Helper::decrypt($data->teachers->name_en) : $data->teachers->name }}</td>
													<td>{{ ($data->teachers->name_ch) ? App\Helpers\Helper::decrypt($data->teachers->name_ch) : 'N/A' }}</td>
													<td>{{ $data->teachers->email }}</td>
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