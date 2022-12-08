@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="coltainer">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.my_class.student_details')}}</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@include('backend.student.student_profile_menus')
					<div class="sm-profile-sec">
						<div class="row student_profile_layout">
							<div class="col-md-2">
								<div class="profile-img">
									<img src="{{ asset('images/profile_image.jpeg') }}" class="profile-pic">
								</div>
							</div>
							<div class="col-md-10">
								<div class="profile-detail">
									<div class="personal_details_main create_line">
										<h5>{{__('languages.my_class.personal_details') }} <a href="{{route('profile.index')}}">
											@if(Auth::user()->role_id==3)
												<span class="fa fa-pencil"></a></span>
											@endif
										</h5>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.user_activity.english_name') }} :</strong></p>
											<p class="detail-p"> {{($profile->name_en) ? App\Helpers\Helper::decrypt($profile->name_en) :'N/A'}} </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.user_activity.chinese_name') }} :</strong></p>
											<p class="detail-p"> {{ ($profile->name_ch) ? App\Helpers\Helper::decrypt($profile->name_ch) :'N/A'}} </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.name') }} :</strong></p>
											<p class="detail-p"> {{($profile->name) ? $profile->name :'N/A' }} </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.email') }} :</strong> </p>
											<p class="detail-p">{{$profile->email}} </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.grade') }} :</strong>  </p>
											<p class="detail-p"> {{(!empty($profile->CurriculumYearData)) ? App\Helpers\Helper::getGradeName($profile->CurriculumYearData->grade_id) : 'N/A'}}  </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.class') }} :</strong>  </p>
											<p class="detail-p"> {{(!empty($profile->CurriculumYearData)) ? App\Helpers\Helper::getSingleClassName($profile->CurriculumYearData->class_id) : 'N/A'}}  </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.profile.class_student_number') }} :</strong>  </p>
											<p class="detail-p"> {{ ($profile->CurriculumYearData->class_student_number) ? $profile->CurriculumYearData->class_student_number : ''}}  </p>
										</div>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.user_management.city') }} :</strong>  </p>
											<p class="detail-p"> {{($profile->city) ?  App\Helpers\Helper::decrypt($profile->city) : 'N/A' }}  </p>
										</div>
										
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.user_management.gender') }} :</strong>  </p>
											<p class="detail-p"> {{$profile->gender}}  </p>
										</div>
									</div>
									<div class="contact_detail_main create_line">
										<h5>{{__('languages.my_class.contact_information') }}</h5>
										<div class="detail-sec">
											<p class="detail-s"><strong>{{ __('languages.contact_no') }} :</strong>  </p>
											<p class="detail-p"> {{($profile->mobile_no) ?  App\Helpers\Helper::decrypt($profile->mobile_no) : 'N/A'}}  </p>
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