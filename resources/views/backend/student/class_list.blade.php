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
								<h2 class="mb-4 main-title">{{__('languages.my_classmates')}}</h2>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					<!-- For filtration on name email and city -->
					<form class="UserFilterForm" id="UserFilterForm" method="get">	
					<div class="row">
						<div class="col-lg-4 col-md-5">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="Search" value="{{request()->get('Search')}}" placeholder="{{__('languages.search_by_name')}},{{__('languages.email')}},{{__('languages.user_management.city')}}">
								@if($errors->has('Search'))
                                	<span class="validation_error">{{ $errors->first('Search') }}</span>
                            	@endif
                            </div>
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
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>@sortablelink('name_en',__('languages.english_name'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.chinese_name'))</span></th>
											<th class="selec-head">{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(isset($studentList) && !empty($studentList))
											@foreach($studentList as $User)
											<tr>
												<td><input type="checkbox" name="" class="checkbox"></td>
												<td>{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name }}</td>
												<td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
												<td class="btn-edit">
													<a href="{{ route('student.myclass.student-profile', $User->id) }}" class=""><i class="fa fa-eye" aria-hidden="true"></i></a>
												</td>
											</tr>
											@endforeach
										@else
										<tr><td>{{__('languages.no_data_found')}}</td></tr>
										@endif
							  	</tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($studentList->firstItem()) ? $studentList->firstItem() : 0}} {{__('languages.to')}} {{!empty($studentList->lastItem()) ? $studentList->lastItem() : 0}}
									{{__('languages.of')}}  {{$studentList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$studentList->appends(request()->input())->links()}} 
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
												<option value="{{$studentList->total()}}" @if(app('request')->input('items') == $studentList->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		<script>
			/*for pagination add this script added by mukesh mahanto*/ 
			document.getElementById('pagination').onchange = function() {
				window.location = "{!! $studentList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection