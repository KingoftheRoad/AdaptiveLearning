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
								<h2 class="mb-4 main-title">{{ __('languages.profile.profile') }}
                                <!-- <a href="#" class="setting-button float-right" id="my-study-config-btn"><i class="fa fa-cogs"></i></a> -->
                            </h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
                    @include('backend.student.student_profile_menus')
                    {{-- <div class="row pb-4">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                                <a href="{{ route('student.student-profiles',auth()->user()->id) }}" class="btn-search white-font active-btn">{{ __('languages.my_class.personal_details') }}</a>
                                <a href="{{ route('credit-point-history',auth()->user()->id) }}" class="btn-search white-font inactive-btn">{{ __('languages.credit_point_history') }}</a>
                                <a href="{{ route('student.progress-report.learning-units',auth()->user()->id) }}" class="btn-search white-font inactive-btn">{{ __('languages.learning_units') }}</a>
                                <a href="{{ route('student.progress-report.learning-objective') }}" class="btn-search white-font inactive-btn">{{ __('languages.learning_objectives') }} {{__('languages.report')}}</a>
                        </div>
                    </div> --}}
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
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
                       @php 
                       
                       @endphp
                        <form class="user-form" method="post" id="updateProfileForm"  action="{{ route('student.profile.update',$user->id) }}" enctype="multipart/form-data">
							@csrf()
                            @method('patch')
                            @if(Auth::user()->role_id != 4) 
                                <div class="form-row select-data">
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.main_role') }} : {{ucfirst($user->roles->role_name)}}</label>
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.grade') }} : {{($user->grade_id) ? $user->grade_id : 'N/A'}}</label>
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.class_name') }} : {{(App\Helpers\Helper::getSingleClassName($user->class_id) ? App\Helpers\Helper::getSingleClassName($user->class_id) : 'N/A') }}</label>
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.class_student_number') }} : {{($user->class_student_number) ? ucfirst($user->class_student_number) : 'N/A'}}</label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_en">{{ __('languages.profile.english_name') }}</label>
                                    <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{ __('languages.profile.english_name') }}" value="{{App\Helpers\Helper::decrypt($user->name_en)}}">
                                    @if($errors->has('name_en'))<span class="validation_error">{{ $errors->first('name_en') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="name_ch">{{ __('languages.profile.chinese_name') }}</label>
                                    <input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{ __('languages.profile.chinese_name') }}" value="{{App\Helpers\Helper::decrypt($user->name_ch)}}">
                                    @if($errors->has('name_ch'))<span class="validation_error">{{ $errors->first('name_Ch') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row select-data">
                                <!-- <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Name" value="{{$user->name}}">
                                    @if($errors->has('user_name'))<span class="validation_error">{{ $errors->first('user_name') }}</span>@endif
                                </div> -->
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('languages.profile.email') }}" value="{{$user->email}}" readonly>
                                    @if($errors->has('email'))<span class="validation_error">{{ $errors->first('email') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.mobile_number') }}</label>
                                    <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="{{__('languages.user_management.enter_the_number')}}" value="{{App\Helpers\Helper::decrypt($user->mobile_no)}}" maxLength="8">
                                    @if($errors->has('mobile_no'))<span class="validation_error">{{ $errors->first('mobile_no') }}</span>@endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label for="id_end_time">{{ __('languages.profile.date_of_birth') }}</label>
                                    <div class="input-group date" id="id_4">
                                    <input type="text" class="form-control birthdate-date-picker" name="date_of_birth" placeholder="{{__('languages.select_date')}}" value="{{ date('d/m/Y', strtotime($user->dob)) }}" >
                                        @if($errors->has('date_of_birth'))<span class="validation_error">{{ $errors->first('date_of_birth') }}</span>@endif
                                        <div class="input-group-addon input-group-append">
                                            <div class="input-group-text">
                                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="error-dateof-birth"></span>
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.gender') }}</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mt-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="male" value="male" @if($user->gender == 'male') checked @endif >
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="male">{{ __('languages.profile.male') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="female" value="female" @if($user->gender == 'female') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="female">{{ __('languages.profile.female') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block my-1 mr-1 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="gender" id="other" value="other" @if($user->gender == 'other') checked @endif>
                                                    @if($errors->has('gender'))<span class="validation_error">{{ $errors->first('gender') }}</span>@endif
                                                    <label class="custom-control-label" for="other">{{ __('languages.profile.other') }}</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                    <span class="gender-select-err"></span>
                                </div>
                            </div>
                            <div class="form-row">
                               
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.city') }}</label>
                                    <input type="text" class="form-control" name="city" id="city" placeholder="{{__('languages.user_management.enter_the_city')}}" value="{{App\Helpers\Helper::decrypt($user->city)}}">
                                    @if($errors->has('city'))<span class="validation_error">{{ $errors->first('city') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                        <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.profile_photo') }}</label>
                                    <input type="file" class="form-control" name="profile_photo" id="profile_photo"  >
                                    @if($errors->has('profile_photo'))<span class="validation_error">{{ $errors->first('profile_photo') }}</span>@endif
                                </div>
                            </div>
                            <?php
                                if(isset($user->profile_photo)){
                                    $previewProfileImagePath = asset($user->profile_photo);
                                }else{
                                    $previewProfileImagePath = asset('uploads/settings/image_not_found.gif');
                                } 
                            ?>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-50">
                                    <label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.profile.address') }}</label>
                                    <textarea class="form-control" name="address" id="address" placeholder="{{__('languages.user_management.enter_the_address')}}" value="" rows=5>{{App\Helpers\Helper::decrypt($user->address)}}</textarea>
                                    @if($errors->has('address'))<span class="validation_error">{{ $errors->first('address') }}</span>@endif
                                </div>
                                <div class="form-group col-md-6 mb-50">
                                    <img id="preview-profile-image" src="{{ $previewProfileImagePath }}" alt="preview image" style="max-height: 250px;">
                                    @if($errors->has('profile_picture'))<span class="validation_error">{{ $errors->first('profile_picture') }}</span>@endif
                                </div>
                            </div>
                           
                            <div class="form-row select-data">
                                <div class="sm-btn-sec form-row">
                                    <div class="form-group col-md-6 mb-50 btn-sec">
                                        @if (in_array('profile_management_create', $permissions))
                                                <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
							</form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        <!-- My study Configuration Popup -->
        <!-- <div class="modal fade" id="my-study-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h4 class="modal-title w-100">{{__('languages.my_study_configuration')}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row m-0">
                                <div class="col-md-12 categories-main-list">
                                    @if(!empty($studyFocusTreeOption))
                                    {!! $studyFocusTreeOption !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->
        <!-- My study Configuration Popup -->
        @include('backend.layouts.footer')  
        <!-- <style type="text/css">
            .categories-main-list .categories-list input[type=checkbox]{
                display: none;
            }
        </style> -->
        <!-- <script type="text/javascript">
            $(function() {
                $.each($(".categories-main-list input[type=checkbox][name='strands[]']"), function() {
                var listConfigIdList= new Array();
                listConfigIdList.push($(this).val());
                var maindata=$(this);
                $.ajax({
                    url: BASE_URL + '/estimate_student_competence_web',
                    type: 'POST',
                    data : {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'list_strands_id' : listConfigIdList,
                    },
                    success: function(response) {
                        if(response.data.length!=0){
                            var mainDataVal=maindata.val();
                            var mainDataName=maindata.attr('name');
                            if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
                                var classAdd='up-50';
                                if(response<=49){
                                    classAdd='down-50';
                                }
                                var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
                                labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
                                labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
                            }
                            $("#cover-spin").hide();
                        }else{
                            var mainDataVal=maindata.val();
                            var mainDataName=maindata.attr('name');
                            if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
                                var responseData=0;
                                var classAdd='up-50';
                                if(responseData<=49){
                                    classAdd='down-50';
                                }
                                var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
                                labelData.find('.label-percentage:eq(0)').text('N/A').show();
                                labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
                            }
                        }
                    }
                });
            });
            
            $(document).on('click',".categories-main-list a.collapse-category", function() {
                if($(this).hasClass('open')){
                    $(this).parent().find(' > ul > li > input[type=checkbox]').each(function(){
                        $("#cover-spin").show();
                        var var_data=new Array($(this).val());
                        var var_name=$(this).attr('name').replace('[]','');
                        var maindata=$(this);
                        $.ajax({
                            url: BASE_URL + '/estimate_student_competence_web',
                            type: 'POST',
                            data : {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                [var_name]: var_data,
                            },
                            success: function(response) {
                                if(response.data.length!=0){
                                    var mainDataVal=maindata.val();
                                    var mainDataName=maindata.attr('name');
                                    if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
                                        var classAdd='up-50';
                                        if(response<=49){
                                            classAdd='down-50';
                                        }
                                        var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
                                        labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
                                        labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
                                    }
                                }else{
                                    var mainDataVal=maindata.val();
                                    var mainDataName=maindata.attr('name');
                                    if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
                                        var responseData=0;
                                        var classAdd='up-50';
                                        if(responseData<=49){
                                            classAdd='down-50';
                                        }
                                        var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
                                        labelData.find('.label-percentage:eq(0)').text('N/A').show();
                                        labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
                                    }
                                }
                                $("#cover-spin").hide();
                            }
                        });
                    });
                }
            });
        });
        </script> -->
        <!-- <script type="text/javascript">
            $.fn.cascadeCheckboxes = function() {
                $.fn.checkboxParent = function() {
                    var checkboxParent = $(this).parent("li").parent("ul").parent("li").find('> input[type="checkbox"]');
                    return checkboxParent;
                };
                $.fn.checkboxChildren = function() {
                    var checkboxChildren = $(this).parent("li").find('> .subcategories > li > input[type="checkbox"]');
                    return checkboxChildren;
                };
                $.fn.cascadeUp = function() {
                    var checkboxParent = $(this).checkboxParent();
                    if ($(this).prop("checked")) {
                        if (checkboxParent.length) {
                            var children = $(checkboxParent).checkboxChildren();
                            var booleanChildren = $.map(children, function(child, i) {
                                return $(child).prop("checked");
                            });
                            var allChecked = booleanChildren.filter(function(x) {return !x})
                            if (!allChecked.length) {
                                $(checkboxParent).prop("checked", true);
                                $(checkboxParent).cascadeUp();
                            }
                        }
                    } else {
                        if (checkboxParent.length) {
                            $(checkboxParent).prop("checked", false);
                            $(checkboxParent).cascadeUp();
                        }
                    }
                };
                $.fn.cascadeDown = function() {
                    var checkboxChildren = $(this).checkboxChildren();
                    if (checkboxChildren.length) {
                        checkboxChildren.prop("checked", $(this).prop("checked"));
                        checkboxChildren.each(function(index) {
                            $(this).cascadeDown();
                        });
                    }
                }
                $(this).cascadeUp();
                $(this).cascadeDown();
            };

            $("input[type=checkbox]:not(:disabled)").on("change", function() {
                $(this).cascadeCheckboxes();
            });
            $(".category a").on("click", function(e) {
                e.preventDefault();
                $(this).parent().find("> .subcategories").slideToggle(function() {
                    if ($(this).is(":visible")) $(this).css("display", "flex");
                });
            });
            $('.collapse-category').on("click", function(){
                if($(this).hasClass('close')){
                    $(this).removeClass('close');
                    $(this).addClass('open');
                }else{
                    $(this).removeClass('open');
                    $(this).addClass('close');
                }
            });
        </script> -->
@endsection