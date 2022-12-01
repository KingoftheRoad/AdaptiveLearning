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
								<h2 class="mb-4 main-title">{{__('languages.subjects.my_subjects_list')}}</h2>
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
					<form class="mySubjects" id="mySubjects" method="get">	
						<div class="row">
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="searchText" value="{{request()->get('searchText')}}" placeholder="{{__('languages.search_by_subject_name')}} {{__('languages.or')}} {{__('languages.code')}}">
									@if($errors->has('searchText'))
										<span class="validation_error">{{ $errors->first('searchText') }}</span>
									@endif
								</div>
							</div>
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="status" class="form-control select-option" id="status">
									<option value="">{{__("languages.select_status")}}</option>
									<option value="1" {{ request()->get('status') == 1 ? 'selected' : '' }}>{{__("languages.active")}}</option>
									<option value="0" @php if(isset($_GET['status']) && $_GET['status'] == 0) echo 'selected'; @endphp>{{__("languages.inactive")}}</option>
								</select>
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>#{{__('languages.sr_no')}}</th>
							          		<th class="first-head"><span>@sortablelink('name',__('languages.name'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('code',__('languages.code'))</span></th>
											<th class="selec-head">@sortablelink('status',__('languages.status'))</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($subjectList))
										@foreach($subjectList as $data)
							        	<tr>
											<td>{{$loop->iteration}}</td>
                                            <td>{{ $data->name }}</td>
											<td>{{ $data->code }}</td>
                                            <td>
												@if($data->status == '1')
													<span class="badge badge-success">Active</span> 
												@else
												<span class="badge badge-primary">InActive</span> 
												@endif
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
							@if(!empty($subjectList))
							<div>{{__('languages.showing')}} {{!empty($subjectList->firstItem()) ? $subjectList->firstItem() : 0}} {{__('languages.to')}} {{!empty($subjectList->lastItem()) ? $subjectList->lastItem() : 0 }}
								{{__('languages.of')}}  {{$subjectList->total()}} {{__('languages.entries')}}
							</div>
							<div class="pagination-data">
								<div class="col-lg-9 col-md-9 pagintn">
									{{$subjectList->appends(request()->input())->links()}}
								</div>
								<div class="col-lg-3 col-md-3 pagintns">
									<form>
										<label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
										<select id="pagination" >
											<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
											<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
											<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
											<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
											<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
											<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
											<option value="{{$subjectList->total()}}" @if(app('request')->input('items') == $subjectList->total()) selected @endif >{{__('languages.all')}}</option>
										</select>
									</form>
								</div>
							</div>
							@endif
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@if(!empty($subjectList))
		<script>
            /*for pagination add this script added by mukesh mahanto*/
            document.getElementById('pagination').onchange = function() {
                window.location = "{!! $subjectList->url(1) !!}&items=" + this.value;
            };
        </script>
		@endif
        @include('backend.layouts.footer')
@endsection