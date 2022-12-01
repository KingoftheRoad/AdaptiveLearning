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
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{ __('languages.credit_point_history') }}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
                    @include('backend.student.student_profile_menus')
                    
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">   
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="question-bank-sec">
                                        <table  class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#{{__('languages.no')}}</th>
                                                    <th class="first-head"><span>@sortablelink('exam_id',__('languages.exam_name'))</span></th>
                                                    <th class="first-head"><span>@sortablelink('test_type',__('languages.exam_type'))</span></th>
                                                    <th class="first-head"><span>@sortablelink('self_learning_type',__('languages.self_learning_test_type'))</span></th>
                                                    <th class="first-head"><span>{{ __('languages.credit_point_history') }}</span></th>
                                                    <th class="first-head"><span>{{ __('languages.achieve_no_of_credit_point') }}</span></th>
                                                    <th class="first-head"><span>{{ __('languages.achieve_date') }}</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="scroll-pane">
                                            @if(!empty($CreditPointHistoryList))
                                                @foreach($CreditPointHistoryList as $data)
                                                    @php
                                                        $CreditPointDetail= \App\Helpers\Helper::getUserCreditPointType($data->id,$data->user_id,$data->exam_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $data->getExam->title ?? '--' }}</td>
                                                        <td>{{ ($data->test_type!="" ? __('languages.'.$data->test_type) : '--' ) }}</td>
                                                        <td>{{ ($data->self_learning_type!="" ? __('languages.my_studies.'.$data->self_learning_type) : '--' ) }}</td>
                                                        <td>{!! $CreditPointDetail['examCreditPointHtml'] !!}</td>
                                                        <td>{{ $CreditPointDetail['examAchieveCreditPoint'] }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($data->created_at)) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        <div>{{__('languages.showing')}} {{($CreditPointHistoryList->firstItem()) ? $CreditPointHistoryList->firstItem() : 0}} {{__('languages.to')}} {{!empty($CreditPointHistoryList->lastItem()) ? $CreditPointHistoryList->lastItem() : 0}}
                                            {{__('languages.of')}}  {{$CreditPointHistoryList->total()}} {{__('languages.entries')}}
                                        </div>
                                        <div class="pagination-data">
                                            <div class="col-lg-9 col-md-9 pagintn">
                                                @if((app('request')->input('items'))=== null)
                                                    {{$CreditPointHistoryList->appends(request()->input())->links()}}
                                                @else
                                                    {{$CreditPointHistoryList->appends(compact('items'))->links()}}
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
                                                        <option value="{{$CreditPointHistoryList->total()}}" @if(app('request')->input('items') == $CreditPointHistoryList->total()) selected @endif >{{__('languages.all')}}</option>
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
	      </div>
		</div>
        <script>
            /*for pagination add this script added by mukesh mahanto*/ 
            document.getElementById('pagination').onchange = function() {
                // window.location = window.location.href + "&items=" + this.value;			
                window.location = "{!! $CreditPointHistoryList->url(1) !!}&items=" + this.value;
            }; 
    </script>
        @include('backend.layouts.footer') 
@endsection