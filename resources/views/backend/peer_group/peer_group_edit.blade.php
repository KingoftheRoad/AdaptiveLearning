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
                                    <h2 class="mb-4 main-title">{{__('languages.peer_group.edit_peer_group')}}</h2>
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
                                <form method="post" id="addPeerGroupForm" action="{{ route('peer-group.update',$peerGroupData->id) }}">
                                    @csrf()
                                    @method('patch')
                                    <input type="hidden" name="memberIdsList" value="" id="peer-group-member-id" readonly>

                                <input type="hidden" name="dreamschat_group_id" id="dreamschat_group_id"  value="{{$peerGroupData->dreamschat_group_id}}" >
                                    <input type="hidden" name="groupIds" value="{{$peerGroupData->id}}" id="peer-group-id" readonly>
                                    <input type="hidden" name="GroupMemberDataList" id="GroupMemberData">
                                    <div class="form-row select-data">
                                        <div class="form-group col-md-6">
                                            <label class="text-bold-600">{{ __('languages.peer_group.group_name') }}</label>
                                            <input type="text" class="form-control" name="group_name" id="group_name" placeholder="{{__('languages.peer_group.group_name')}}" value="{{$peerGroupData->group_name}}">
                                            @if($errors->has('group_name'))<span class="validation_error">{{ $errors->first('group_name') }}</span>@endif
                                        </div>
                                        {{-- <div class="form-group col-md-6">
                                            <label class="text-bold-600">{{ __('languages.peer_group.group_name_ch') }}</label>
                                            <input type="text" class="form-control" name="group_name_ch" id="group_name_ch" placeholder="{{__('languages.peer_group.group_name_ch')}}" value="{{$peerGroupData->group_name_en}}">
                                            @if($errors->has('group_name_ch'))<span class="validation_error">{{ $errors->first('group_name_ch') }}</span>@endif
                                        </div> --}}
                                        {{-- <div class="form-group col-md-6">
                                            <label>{{ __('languages.peer_group.subject') }}</label>
                                            <fieldset class="form-group">
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="peer_group_subject_id" id="peer_group_subject_id">
                                                    @if(!empty($SubjectList))
                                                    @foreach($SubjectList as $subject)
                                                    <option value="{{$subject->id}}" {{($peerGroupData->subject_id == $subject->id) ?  "selected" : ''}}>{{$subject->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </fieldset>
                                            <span id="error-status"></span>
                                            @if($errors->has('peer_group_subject_id'))<span class="validation_error">{{ $errors->first('peer_group_subject_id') }}</span>@endif
                                        </div> --}}
                                        
                                        <div class="form-group col-md-6">
                                            <label>{{ __('languages.peer_group.status') }}</label>
                                            <fieldset class="form-group">
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="status" id="status">
                                                    <option value="active" {{($peerGroupData->status == 1) ?  "selected" : ''}}>{{__('languages.active')}}</option>
                                                    <option value="inactive" {{($peerGroupData->status == 0) ? "selected" : ''}}>{{__('languages.inactive')}}</option>
                                                </select>
                                            </fieldset>
                                            <span id="error-status"></span>
                                            @if($errors->has('status'))<span class="validation_error">{{ $errors->first('status') }}</span>@endif
                                        </div>
                                    </div>
                                    <div class="form-row btn-sec">
                                        <button type="button" class="blue-btn btn btn-sm btn-primary" id="edit_group_member">{{__('languages.peer_group.edit_peer_group')}}</button>
                                    </div>
                                    <hr class="blue-line">
                                    
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div id="peer-group-member-list-section">
                                                <div class="form-row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <strong>{{__('languages.peer_group.group_members')}}</strong>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-4">
                                                        <div class="select-lng pt-2 pb-2">
                                                            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="student_grade_id" id="student_grade_id">
                                                                <option value='all'>{{ __('languages.all') }}</option>
                                                                @if(!empty($gradesList))
                                                                    @foreach($gradesList as $grade)
                                                                    <option value="{{$grade->id}}" {{ (request()->get('student_grade_id')) == $grade->id ? 'selected' : '' }}>{{ $grade->name}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3">
                                                        <div class="select-lng pt-2 pb-2">
                                                            <select name="class_type_id[]" class="form-control select-option" id="classType-select-option" multiple >
                                                                {!!$classTypeOptions!!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- For a Filtration on name,email & city -->
                                                    <div class="col-lg-4 col-md-5">
                                                        <div class="select-lng pt-2 pb-2">
                                                            <input type="text" class="input-search-box mr-2" name="searchtext" id="searchtext" value="{{request()->get('searchtext')}}" placeholder="{{__('languages.search_by_name')}},{{__('languages.email')}},{{__('languages.user_management.city')}}">
                                                            @if($errors->has('searchtext'))
                                                                <span class="validation_error">{{ $errors->first('searchtext') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-2 col-md-3">
                                                        <div class="select-lng pt-2 pb-2">
                                                            <button type="button" name="filter" value="filter" class="btn-search" id="selected-member-filter-list">{{ __('languages.search') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <table id="edit-member-peer-group" class="display" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <input type="checkbox" name="" class="all-select-member-checkbox" class="checkbox" {{($peerMembers->count() > 0) ? 'checked' : ''}}>
                                                            </th>
                                                            <th class="first-head"><span>{{__('languages.name_english')}}</span></th>
                                                            <th class="first-head"><span>{{__('languages.name_chinese')}}</span></th>
                                                            <th class="sec-head selec-opt"><span>{{__('languages.email')}}</span></th>
                                                            <th class="selec-head">{{__('languages.grade')}}</th>
                                                            <th class="selec-head">{{__('languages.class')}}</th>
                                                            <th class="selec-head">{{__('languages.profile.class_student_number')}}</th>
                                                            <th>{{__('languages.peer_group.action')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="scroll-pane">
                                                        @if(!empty($peerMembers))
                                                        @foreach($peerMembers as $member)
                                                        <tr>
                                                            <td><input type="checkbox" name="memberIds[]" class="checkbox select-member-checkbox" value="{{$member->student->id}}" checked></td>
                                                            <td>{{($member->student->name_en) ? \App\Helpers\Helper::decrypt($member->student->name_en) : ''}}</td>
                                                            <td>{{($member->student->name_ch) ? \App\Helpers\Helper::decrypt($member->student->name_ch) : ''}}</td>
                                                            <td>{{($member->student->email) ? $member->student->email : ''}}</td>
                                                            <td>{{($member->student->grade_id) ? $member->student->grade_id : ''}}</td>
                                                            <td>{{($member->student->class_id) ? \App\Helpers\Helper::getSingleClassName($member->student->class_id) : ''}}</td>
                                                            <td>{{($member->student->class_student_number) ? $member->student->class_student_number : ''}}</td>
                                                            <td class="btn-edit">
                                                                <a href="javascript:void(0);" class="pl-2 deletePeerGroupMember" data-pagetype="addMemberPage" data-id="{{$member->student->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
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
        <div class="modal" id="add_edit_member_group_popup" tabindex="-1" aria-labelledby="add_edit_member_group_popup" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="max-width: 90%;">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h4 class="modal-title w-100">{{__('languages.member_list')}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body" id="add-member-listing-sestion">
                            
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
            var GroupMemberList = [];
            var GroupMemberDataList = [];
            var GroupMemberOldList = [];
            $(function () {
                var MemberIds = [];
                $(document).ready(function(){
                    $('#peer-group-member-list-section .select-member-checkbox').each(function () {
                        GroupMemberList.push($(this).val());
                    })
                    if ($(".select-member-checkbox").is(":checked")) {
                        $("#edit-member-peer-group")
                        .DataTable()
                        .table("#edit-member-peer-group")
                        .rows()
                        .every(function (index, element) {
                            var row = $(this.node());
                            row.closest('tr').find(".select-member-checkbox").prop('checked', true);
                            var memberid = row.closest('tr').find(".select-member-checkbox").val();
                            if (MemberIds.indexOf(memberid) !== -1) {
                                // Current value is exists in array
                            } else {
                                MemberIds.push(memberid);
                                GroupMemberOldList.push(memberid);
                            }
                        });
                    }
                    $('#peer-group-member-id').val(MemberIds);
                });
                
                 /**
                 * USE : On Change Event after selecting 
                 */
                $(document).on("click", ".select-member-checkbox", function () {
                    console.log('select-member-checkbox');
                    if($('.select-member-checkbox').length === $('.select-member-checkbox:checked').length){
                        $(".all-select-member-checkbox").prop('checked',true);
                    }else{
                        $(".all-select-member-checkbox").prop('checked',false);
                    }
                    memberid = $(this).val();
                    if($(this).is(":checked")){
                        if (MemberIds.indexOf(memberid) !== -1) {
                            // Current value is exists in array
                        }else{
                            MemberIds.push(memberid);
                        }
                    }else{
                        MemberIds = $.grep(MemberIds, function(value) {
                            return value != memberid;
                        });
                    }
                    console.log(MemberIds);
                    GroupMemberList=[];
                    $('#edit-member-peer-group .select-member-checkbox:checked').each(function () {
                        GroupMemberList.push($(this).val());
                    })
                    $('#peer-group-member-id').val(MemberIds);
                });
                
                /**
                 * USE : remove existing added peer group members
                 */
                $(document).on("click", ".deletePeerGroupMember", function (){
                    var deleteMemberid = $(this).attr('data-id');
                    var tr = $(this).closest("tr");
                    if($(this).attr('data-pagetype') == 'addMemberPage'){
                        $.confirm({
                            title: "Are you sure to remove member ?",
                            content: CONFIRMATION,
                            autoClose: "Cancellation|8000",
                            buttons: {
                            deleteMember: {
                                text: "Remove Member",
                                action: function () {
                                    $("#cover-spin").show();
                                        $.ajax({
                                            url: BASE_URL + "/peer-group/remove-member",
                                            method: "GET",
                                            data: {
                                                deleteMemberid : deleteMemberid,    
                                                peerGroupId:$('#peer-group-id').val(),
                                            },
                                            success: function (response) {
                                                $("#cover-spin").hide();
                                                var data = JSON.parse(
                                                    JSON.stringify(response)
                                                );
                                                if (data.status === "success") {
                                                    if (MemberIds.indexOf(deleteMemberid) !== -1) {
                                                        MemberIds = $.grep(MemberIds, function(value) {
                                                            return value != deleteMemberid;
                                                        });
                                                        tr.fadeOut(500, function () {
                                                            $(this).remove();
                                                        });
                                                    }else{
                                                        //If not selected and remove from list member then remove tr.
                                                        tr.fadeOut(500, function () {
                                                            $(this).remove();
                                                        });
                                                    } 
                                                    toastr.success(data.message); 
                                                }
                                            },
                                            error: function (response) {
                                                ErrorHandlingMessage(response);
                                            },
                                        });
                                    },
                                },
                                Cancellation: function () {},
                            },
                        });
                    }
                });

                // On change event of select all memberlist option
                $(document).on("click", ".all-select-member-checkbox", function (){
                    if ($(this).is(":checked")) {
                        $("#edit-member-peer-group, #add-member-peer-group")
                        .DataTable()
                        .table("#edit-member-peer-group, #add-member-peer-group")
                        .rows()
                        .every(function (index, element) {
                            var row = $(this.node());
                            row.closest('tr').find(".select-member-checkbox").prop('checked', true);
                            var memberid = row.closest('tr').find(".select-member-checkbox").val();
                            if (MemberIds.indexOf(memberid) !== -1) {
                                // Current value is exists in array
                            } else {
                                MemberIds.push(memberid);
                            }
                        });
                    } else {
                        $("#edit-member-peer-group, #add-member-peer-group")
                        .DataTable()
                        .table("#edit-member-peer-group, #add-member-peer-group")
                        .rows()
                        .every(function (index, element) {
                            var row = $(this.node());
                            row.closest('tr').find(".select-member-checkbox").prop('checked', false);
                        });
                        MemberIds = [];
                    }
                    $('#peer-group-member-id').val(MemberIds);
                });

                $(document).on("click", "#edit_group_member", function () {
                    $("#cover-spin").show();
                    $('#peer-group-member-list-section').html('');
                    $.ajax({
                        url: BASE_URL + "/peer-group/memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            MemberIds : MemberIds,
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#add-member-listing-sestion').html(data.data);
                            if($('.select-member-checkbox').length === $('.select-member-checkbox:checked').length){
                                $(".all-select-member-checkbox").prop('checked',true);
                            }else{
                                $(".all-select-member-checkbox").prop('checked',false);
                            }
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $('#add_edit_member_group_popup').modal('show');
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
                            $('#add-member-listing-sestion').html(data.data);
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
                $("#edit-member-peer-group").DataTable({
                    order: [[0, "desc"]],
                });
                
                /**
                 * Display into listing page selected members
                 */
                $(document).on("click", ".close-add-member-popup,.close", function () {
                    $("#cover-spin").show();
                    $('#peer-group-member-list-section').html('');
                    $.ajax({
                        url: BASE_URL + "/peer-group/get-selected-memberlist",
                        method: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            MemberIds : MemberIds
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            $('#peer-group-member-list-section').html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            GroupMemberList=[];
                            $('#peer-group-member-list-section .select-member-checkbox:checked').each(function () {
                                GroupMemberList.push($(this).val());
                            })
                            $('#peer-group-member-list-section .select-member-checkbox:checked').each(function () {
                                var uid=$(this).val();
                                var alp_chat_user_id='';
                                if($(this).attr('data-alp-chat-id')!="")
                                {
                                    alp_chat_user_id=$(this).attr('data-alp-chat-id');
                                }
                                console.log(alp_chat_user_id);
                                var trData=$(this).closest('tr');
                                var email=trData.find('td:eq(3)').text();
                                var name=trData.find('td:eq(1)').text();
                                var memberData={
                                    id:uid,
                                    email:email,
                                    name_en:name,
                                    mobile_no:'',
                                    alp_chat_user_id:alp_chat_user_id
                                }
                                GroupMemberDataList.push(memberData);
                            })
                            $("#GroupMemberData").val(JSON.stringify(GroupMemberDataList));
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
                    var gradeId = $('#student_grade_id').val();
                    var classIds = $('#classType-select-option').val();
                    var searchText = $('#searchtext').val();
                    $('#peer-group-member-list-section').html('');
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
                            $('#peer-group-member-list-section').html(data.data);
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