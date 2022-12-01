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
								<h4 class="mb-4">{{__('languages.user_activity.user_name')}}: {{ ($UsesDetail->name_en) ? App\Helpers\Helper::decrypt($UsesDetail->name_en) : $UsesDetail->name   }} </h4>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h6 class="mb-4">{{__('languages.user_activity.user_activities_report')}}</h6>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>

                    <!-- Question form Listinf Start -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if(!empty($UserActivities))
                            @foreach($UserActivities as $activities)

                            @if(!empty($activities->user_agent))
                                @php
                                    $serverDetail = json_decode($activities->user_agent);
                                @endphp
                            @endif
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                    </div>
                                    <div class="sm-answer pl-4 pt-2">
                                        @if($activities->type == 'login')
                                        <span class="badge badge-success">{{$activities->type}}</span>
                                        @endif
                                        @if($activities->type == 'logout')
                                        <span class="badge badge-warning">{{$activities->type}}</span>
                                        @endif
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.user_activity.ip_address')}}:
                                                    @if(!empty($serverDetail))
                                                        {{ $serverDetail->IP ?? 'Not Found'}}
                                                    @else
                                                        {{__('languages.not_found')}}
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">
                                                    <?php if(!empty($serverDetail)){ 
                                                        if($serverDetail->Browser == 'Chrome'){
                                                            echo '<img src="'.asset('images/browser_icon/chrome.png').'">';
                                                        }else if($serverDetail->Browser == 'Firefox'){
                                                            echo '<img src="'.asset('images/browser_icon/firefox.png').'">';
                                                        }else if($serverDetail->Browser == 'Opera'){
                                                            echo '<img src="'.asset('images/browser_icon/opera.png').'">';
                                                        }else if($serverDetail->Browser == 'InternetExplorer'){
                                                            echo '<img src="'.asset('images/browser_icon/internet-explorer.png').'">';
                                                        }else if($serverDetail->Browser == 'MicrosoftEdge'){
                                                            echo '<img src="'.asset('images/browser_icon/edge.png').'">';
                                                        }else{
                                                            echo $serverDetail->Browser;
                                                        }
                                                    } ?>
                                                </label>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.user_activity.request_date_time')}} : @php echo date('d-m-Y h:i:s',strtotime($activities->created_at)); @endphp</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            @endif
                            <div class="row">
                                <div class="col-lg-10 col-md-10 ">
                                            @if((app('request')->input('items')) === null)
                                                {{$UserActivities->links()}}
                                            @else
                                                {{$UserActivities->appends(compact('items'))->links()}}
                                            @endif
                                        </div>
                                        <div calss="col-lg-2 col-md-2">
                                            <form>
                                                <label for="pagination">{{__('languages.per_page')}}</label>
                                                <select id="pagination" >
                                                    <option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
                                                    <option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
                                                    <option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
                                                    <option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
                                                    <option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
                                                    <option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
                                                    <option value="{{$UserActivities->total()}}" @if(app('request')->input('items') == $UserActivities->total()) selected @endif >{{__('languages.all')}}</option>
                                                </select>
                                            </form>
                                        <div>
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
            window.location = "{!! $UserActivities->url(1) !!}&items=" + this.value;      
    }; 
</script>
@include('backend.layouts.footer') 
@endsection