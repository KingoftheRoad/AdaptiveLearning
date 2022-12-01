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
								<h2 class="mb-4 main-title">{{__('languages.peer_group.add_peer_group')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                            </div>
							<hr class="blue-line">
						</div>
					</div>
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
							<form method="post" id="addPeerGroupForm" action="{{ route('peer-group.store') }}">
							    @csrf()
                                <input type="hidden" name="dreamschat_group_id" id="dreamschat_group_id">
                                <input type="hidden" name="memberIdsList" value="" id="peer-group-member-id" readonly>
                                <div class="form-row select-data">
                                    <div class="form-group col-md-6">
                                        <label class="text-bold-600">{{ __('languages.peer_group.group_name') }}</label>
                                        <input type="text" class="form-control" name="group_name" id="group_name" placeholder="{{__('languages.peer_group.group_name')}}" value="{{old('group_name')}}">
                                        @if($errors->has('group_name'))<span class="validation_error">{{ $errors->first('group_name') }}</span>@endif
                                    </div>                                    
                                    <div class="form-group col-md-6">
                                        <label>{{ __('languages.peer_group.status') }}</label>
                                        <fieldset class="form-group">
                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                <option value="active" selected>{{__('languages.active')}}</option>
                                                <option value="inactive">{{__('languages.inactive')}}</option>
                                            </select>
                                        </fieldset>
                                        <span id="error-status"></span>
                                        @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                    </div>
                                </div>
                                <div class="form-row btn-sec">
                                    <button type="button" class="blue-btn btn btn-sm btn-primary" id="add_group_member">{{__('languages.peer_group.add_member')}}</button>
                                </div>
                                <hr class="blue-line">

                                <div class="form-row" id="group-member-list">
                                    <div class="form-row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <strong>{{__('languages.peer_group.group_members')}}</strong>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <h5 align="center">{{__('languages.peer_group.no_any_peer_members')}}</h5>
                                    </div>
                                </div>
                                
                                <hr class="blue-line">
                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary add-submit-btn">{{ __('languages.submit') }}</button>
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

        <!-- Start Add group member Popup -->
        <div class="modal" id="add_member_group_popup" tabindex="-1" aria-labelledby="add_member_group_popup" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="max-width: 90%;">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h4 class="modal-title w-100">{{__('Member List')}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body" id="add-member-listing-section">
                            
                        </div>
                        <div class="modal-footer btn-sec">
                            <button type="button" class="btn btn-default close-add-member-popup" data-dismiss="modal">{{__('languages.close')}}</button>
                            <button type="button" class="blue-btn btn btn-primary close-add-member-popup" data-dismiss="modal">{{__('languages.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Add group member Popup -->

        @include('backend.layouts.footer')
        <script>
            var GroupAdminId='{{ Auth::user()->id }}';
            var GroupMemberList=[];
            $(function () {
                /**
                 * USE : On Change Event after selecting 
                 */
                var MemberIds = [];
                $(document).on("click", ".select-member-checkbox", function () {
                    if($('.select-member-checkbox').length === $('.select-member-checkbox:checked').length){
                        $(".all-select-member-checkbox").prop('checked',true);
                    }else{
                        $(".all-select-member-checkbox").prop('checked',false);
                    }
                    memberid = $(this).val();
                    if ($(this).is(":checked")) {
                        if (MemberIds.indexOf(memberid) !== -1) {
                            // Current value is exists in array
                        }else{
                            MemberIds.push(memberid);
                            GroupMemberList.push(memberid);
                        }
                    }else{
                        MemberIds = $.grep(MemberIds, function(value) {
                            return value != memberid;
                        });
                    }
                    $('#peer-group-member-id').val(MemberIds);
                });
                
                /**
                 * USE : remove existing added peer group members
                 */
                $(document).on("click", ".deletePeerGroupMember", function (){
                    if($(this).attr('data-pagetype') == 'addMemberPage'){
                        var tr = $(this).closest("tr");
                        var memberid = $(this).attr('data-id');
                        if(MemberIds.indexOf(memberid) !== -1){
                            MemberIds = $.grep(MemberIds, function(value){
                                return value != memberid;
                            });
                            tr.fadeOut(500, function(){
                                $(this).remove();
                            });
                        }
                        $('#peer-group-member-id').val(MemberIds);
                    }
                });

                // On change event of select all memberlist option
                $(document).on("click", ".all-select-member-checkbox", function (){
                    if($(this).is(":checked")){
                        $("#add-member-peer-group")
                        .DataTable()
                        .table("#add-member-peer-group")
                        .rows()
                        .every(function (index, element){
                            var row = $(this.node());
                            row.closest('tr').find(".select-member-checkbox").prop('checked', true);
                            var memberid = row.closest('tr').find(".select-member-checkbox").val();
                            if(MemberIds.indexOf(memberid) !== -1){
                                // Current value is exists in array
                            }else{
                                MemberIds.push(memberid);
                                GroupMemberList.push(memberid);
                            }
                        });
                    }else{
                        $("#add-member-peer-group")
                        .DataTable()
                        .table("#add-member-peer-group")
                        .rows()
                        .every(function(index, element){
                            var row = $(this.node());
                            row.closest('tr').find(".select-member-checkbox").prop('checked', false);
                        });
                        MemberIds = [];
                        GroupMemberList = [];
                    }
                    $('#peer-group-member-id').val(MemberIds);
                });

                $(document).on("click", "#add_group_member", function () {
                    $("#cover-spin").show();
                    $('#group-member-list').html('');
                    $.ajax({
                        url: BASE_URL + "/peer-group/memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            MemberIds : MemberIds,
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#add-member-listing-section').html(data.data);
                            if($('.select-member-checkbox').length === $('.select-member-checkbox:checked').length){
                                $(".all-select-member-checkbox").prop('checked',true);
                            }else{
                                $(".all-select-member-checkbox").prop('checked',false);
                            }
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $('#add_member_group_popup').modal('show');
                            $("#add-member-peer-group").DataTable({
                                order: [[0, "desc"]],
                            });
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                });

                $(document).on("click", "#btn-filter-member-list", function () {
                    $("#cover-spin").show();
                    var gradeId = $('#student_grade_id').val();
                    var classIds = $('#classType-select-option').val();
                    var searchText = $('#searchtext').val();
                    $.ajax({
                        url: BASE_URL + "/peer-group/memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            student_grade_id : gradeId,
                            class_type_id : classIds,
                            searchtext : searchText,
                            MemberIds : MemberIds,
                            filter : 'filter'
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#add-member-listing-section').html(data.data);
                            if($('.select-member-checkbox').length === $('.select-member-checkbox:checked').length){
                                $(".all-select-member-checkbox").prop('checked',true);
                            }else{
                                $(".all-select-member-checkbox").prop('checked',false);
                            }
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $("#add-member-peer-group").DataTable({
                                order: [[0, "desc"]],
                            });
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                });

                $("#add-member-peer-group").DataTable({
                    order: [[0, "desc"]],
                });

                
                /**
                 * Display into listing page selected members
                 */
                $(document).on("click", ".close-add-member-popup,.close", function () {
                    $("#cover-spin").show();
                    $('#add-member-listing-section').html();
                    $.ajax({
                        url: BASE_URL + "/peer-group/get-selected-memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            MemberIds : MemberIds
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#group-member-list').html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            /*$('#add-member-peer-group .select-member-checkbox:checked').each(function () {
                                GroupMemberList.push($(this).val());
                            })*/
                            console.log(GroupMemberList);
                            $("#selected-memberlist-peer-group").DataTable({
                                order: [[0, "desc"]],
                            });
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                });

                $(document).on("click", "#selected-member-filter-list", function () {
                    $("#cover-spin").show();
                    $('#add-member-listing-section').html();
                    var gradeId = $('#student_grade_id').val();
                    var classIds = $('#classType-select-option').val();
                    var searchText = $('#searchtext').val();
                    $.ajax({
                        url: BASE_URL + "/peer-group/get-selected-memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            MemberIds : MemberIds,
                            student_grade_id : gradeId,
                            class_type_id : classIds,
                            searchtext : searchText,
                            filter : 'filter'
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#group-member-list').html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $("#selected-memberlist-peer-group").DataTable({
                                order: [[0, "desc"]],
                            });
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                });
            });
        </script>
@endsection