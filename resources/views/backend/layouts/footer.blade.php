@php
if(Auth::user()->role_id == 1){
        $color = '#A5A6F6';
    }else if(Auth::user()->role_id==2){
        $color = '#f7bfbf';
    }else if(Auth::user()->role_id==3){
        $color = '#d8dc41';
    }else if(Auth::user()->role_id == 7){
        $color = '#BDE5E1';
    }else{
        $color = '#a8e4b0';
    }
@endphp
<footer class="sm-admin-footer p-2" style="background-color:{{$color}};">
    <div class="container">
        <div class="row">
            <div class="copyrights-line text-center">
                <p class="p2">{{__('languages.footer.grow_your_mind_with_better_learning')}}</p>
            </div>
            @if(!App\Helpers\Helper::isAdmin())
            <div class="footer_chat_main" id="alp_chat_btn">
                <a href="javascript:void(0);"><img src="{{asset('images/alp_chat.png')}}"/></a>
            </div>
            @endif
        </div>
    </div>
</footer>


<!-- Start Change password Popup -->
<div class="modal" id="changeUserPwd" tabindex="-1" aria-labelledby="changeUserPwd" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="max-width: 50%;">
        <div class="modal-content">
            <form id="changepasswordUserFrom">	
                @csrf()
                <input type="hidden" value="" name="userId" id="changePasswordUserId">
                <div class="modal-header">
                    <h4 class="modal-title w-100">{{__('languages.change_password')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <label class="text-bold-600" for="newPassword">{{__('languages.new_password')}}</label>
                            <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="{{__('languages.new_password')}}" value="">
                            @if($errors->has('newPassword'))<span class="validation_error">{{ $errors->first('newPassword') }}</span>@endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-lg-12 col-md-12">
                            <label class="text-bold-600" for="confirmPassword">{{__('languages.confirm_password')}}</label>
                            <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="{{__('languages.confirm_password')}}" value="">
                            @if($errors->has('confirmPassword'))<span class="validation_error">{{ $errors->first('confirmPassword') }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-sec">
                    <button type="button" class="btn btn-default close-userChangePassword-popup" data-dismiss="modal">{{__('languages.close')}}</button>
                    <button type="submit" class="blue-btn btn btn-primary submit-change-password-form">{{__('languages.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Change password Popup -->

<!-- Full Solution Start Popup in Report -->
<div class="modal" id="SolutionImageModal" tabindex="-1" aria-labelledby="SolutionImageModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title w-100">{{__('languages.full_question_solution')}}</h4>
                    <button type="button" class="close closePop" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <img src="" id="fullSolution-image" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="closePop btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Full Solution End Popup in Report -->

<!-- Start list of questions list preview  Popup -->
<div class="modal" id="teacher-question-list-preview" tabindex="-1" aria-labelledby="teacher-question-list-preview" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog question-list-modal-lg">
		<div class="modal-content">
			<form method="get">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.question_list_preview')}}</h4>	
					<button type="button" class="close closeQuestionPop" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body teacher-question-list-preview-data modal-lg">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default closeQuestionPop" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of list of questions list preview  Popup -->

<!-- USE: Change End Date of Exam Model -->
    <div class="modal fade" id="ChangeEndDateModal" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('languages.change_exam_date')}}</h5>
                    <button type="button" class="close changeExamResultOrEndDate" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{route('ChangeExamEndDate')}}" id="changeExamEndDateForm">
                @CSRF
                @method("POST")
                <div class="modal-body ChangeEndDateModal-modal-body">
                    <input type="hidden" name="ExamId" id="ExamId" value =""/>
                    {{-- <input type="hidden" name="ExamType" id="ExamType" value =""/> --}}
                    <input type="hidden" name="dateType" id="dateType" value =""/>
                    <div>
                        <p><strong>{{__('languages.title')}}</strong> : <span class="test_title">ABC</span></p>
                    </div>
                    <div>
                        <p><strong>{{__('languages.reference_number')}}</strong> : <span class="test_reference_number">54545</span></p>
                    </div>
                    <div>
                        <label class="SetLabelOfChangeDate"><strong>{{__('languages.question_generators_menu.end_date')}}</strong></label>
                        <div class="test-list-clandr">
                            <input type="text" class="form-control changeExamDate" id="examToDate" name="to_date" value="" placeholder="{{__('languages.select_date')}}" autocomplete="off">
                            <div class="input-group-addon input-group-append">
                                <div class="input-group-text">
                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <span id="toDate-error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <div calss="col-lg-3 col-md-3 col-sm-3">
                        <button type="submit" class="btn btn-search">{{__('languages.submit')}}</button>
                    </div>
                    <button type="button" class="btn btn-secondary changeExamResultOrEndDate" data-dismiss="modal">{{__('languages.test.close')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
<!-- End: Change End Date of Exam Model -->

<!-- Student Result Summary Report -->
<div class="modal" id="StudentSummaryReportModal" tabindex="-1" aria-labelledby="StudentSummaryReportModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header embed-responsive">
					<h4 class="modal-title w-100 ml-3">{{__('languages.student_summary_report')}}</h4>
				</div>
				<div class="modal-body student-report-summary-data">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default close-student-report-summary-popup" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Result Summary Popup -->

<!-- USE: Remainder upgrade school year data popup -->
<div class="modal" id="remainder-upgrade-school-data-popup" tabindex="-1" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="get">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.remainder')}}</h4>
					<button type="button" class="close closeRemainderPopup" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body modal-xl">
                    <p>Please be reminded to upload the new student css file for the new curriculum year {{(((int)date('Y')+1).'-'.((int)(date('y'))+2))}}. <a href="{{route('student.import.upgrade-school-year')}}">Click here</a> to upload it otherwise, the users of your school will not be able to access the data of {{(((int)date('Y')+1).'-'.((int)(date('y'))+2))}} after 1st Sept {{((int)date('Y')+1)}}.</p>
                    <p>You will also need to re-assign teachers to classes for the new curriculum year. <a href="{{url('teacher-class-subject-assign')}}">Click here.</a></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default closeRemainderPopup" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End: Remainder upgrade school year data popup -->

<script>
/**
 * * USE : On click chat icon then will be open chat page
 * */
$(function (){
    $(document).on('click', '#alp_chat_btn,.alp_chat_icon', function(e) {
        var UserId = "{{Auth::user()->id}}";
        var username = "{{Auth::user()->email}}";
        var password = "{{Auth::user()->email}}";
        var language = "English-en";
        var SelectedAlpChatGroup = $(this).attr('data-AlpChatGroupId');
        var ALP_CHAT_USER_ID = "{{Auth::user()->alp_chat_user_id}}";
        if(ALP_CHAT_USER_ID==""){
            //If current user is not exist in firebase then we will create new user into firebase
            $.ajax({
                url: BASE_URL + "/get-user-info",
                type: "GET",
                async: true,
                data: {
                    uid: UserId
                },
                success: function(response){
                    var userData = response.data;
                    AddUserFirebase(userData);
                    AutoLoginAlpChat(username, password, language, SelectedAlpChatGroup);
                }
            });
        }else{
            // Call to default login function for alp-chat
            AutoLoginAlpChat(username, password, language, SelectedAlpChatGroup);
        }
    });
});
</script>