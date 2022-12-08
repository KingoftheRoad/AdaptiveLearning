@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.class_promotion_history')}}</h2>
							</div>
							<div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								@php $studentName = App\Helpers\Helper::decrypt($promotionHistory->name_en); @endphp
								<h6 class="mb-4 sub-title">{{ __('languages.profile.name')}} : {{$studentName ?? 'N/A'}}</h6>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div  class="question-bank-sec">
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
											<th class="first-head"><span>{{__('languages.from_class')}}</span></th>
											<th class="first-head"><span>{{__('languages.to_class')}}</span></th>
											<th class="first-head"><span>{{__('languages.promoted_date')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @if(!empty($arrayPromotionHistory))
                                            @foreach($arrayPromotionHistory as $history)
                                                <tr>
                                                    <td> {{ (!empty($history->current_grade_id)) ? App\Helpers\Helper::getGradeName($history->current_grade_id).'-'.App\Helpers\Helper::getSingleClassName($history->current_class_id,true) : 'N/A' }}</td>
													<td> {{ (!empty($history->promoted_grade_id)) ? App\Helpers\Helper::getGradeName($history->promoted_grade_id).'-'.App\Helpers\Helper::getSingleClassName($history->promoted_class_id,true) : 'N/A' }}</td>
													<td> {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y h:m:s') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
							        </tbody>
							</table>
							 <div>{{__('languages.showing')}} {{!empty($arrayPromotionHistory->firstItem()) ? $arrayPromotionHistory->firstItem() : 0}} {{__('languages.to')}} {{!empty($arrayPromotionHistory->lastItem()) ? $arrayPromotionHistory->lastItem() : 0}}
								{{__('languages.of')}}  {{$arrayPromotionHistory->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$arrayPromotionHistory->appends(request()->input())->links()}}
										@else
											{{$arrayPromotionHistory->appends(compact('items'))->links()}}
										@endif 
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
												<option value="{{$arrayPromotionHistory->total()}}" @if(app('request')->input('items') == $arrayPromotionHistory->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
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
        <script>
            /*for pagination add this script added by mukesh mahanto*/ 
            document.getElementById('pagination').onchange = function() {
                window.location = "{!! $arrayPromotionHistory->url(1) !!}&items=" + this.value;	
            }; 
        </script>
        @include('backend.layouts.footer')  
@endsection