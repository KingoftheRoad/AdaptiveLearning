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
								<h2 class="mb-4">{{__('languages.import_student')}}</h2>
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
						<form class="user-form" method="post" id="importStudents"  action="{{route('ImportStudentsData')}}" enctype="multipart/form-data">
                        @csrf()
                        <div class="form-row select-data">
                            <input type="hidden" name="old_file" id="old_file">
                            <input type="hidden" name="action" id="action">
                            <div class="form-group col-md-3">
                                <label for="users-list-role">{{ __('languages.select_mode') }}</label>
                                <fieldset class="form-group">
                                    <select class="form-control" name="mode" id="mode">
                                        <!-- <option value="1">{{__('languages.insert_update')}}</option> -->
                                        <option value="1" @if ($request->mode == 1) ? selected @endif>{{__('languages.new_students_import')}}</option>
                                        <option value="2" @if ($request->mode == 2) ? selected  @endif>{{__('languages.promote_students_import')}}</option>
                                    </select>
                                </fieldset>
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="users-list-role">{{ __('languages.select_curriculum') }}</label>                                
                                <?php
                                    // $CurriculumYearList = \App\Traits\Common::GetCurriculumCurrentYear();
                                    $CurriculumYearList = \App\Traits\Common::GetCurriculumCurrentFutureYear(\App\Traits\Common::getGlobalConfiguration('current_curriculum_year'));
                                ?>
                                <fieldset class="form-group">
                                    <select class="form-control" name="curriculum_year_id" id="curriculum">
                                        @if(isset($CurriculumYearList) && !empty($CurriculumYearList))
                                            @foreach($CurriculumYearList as $CurriculumYearListKey => $curriculumYear)
                                                <option value="{{$curriculumYear->id}}" @if($request->curriculum_year_id == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </fieldset>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="users-list-role">{{ __('languages.upload_csv_file') }}</label>
                                <fieldset class="form-group">
                                    <input type="file" name="user_file" value="">
                                </fieldset>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-6 mb-50 btn-sec">
                                    <button class="blue-btn btn btn-primary mt-4">{{ __('languages.commit') }}</button>
                                </div>
                            </div>
                        </div>
                        </form>
                        <hr class="blue-line">
                        <div class="form-row p-3">
                            <div id="csv-instructions">
                                <h2 class="wv-heading--subtitle">{{__('languages.student_csv_template_file')}}</h2>
                                <p class="wv-text--body imp-info">
                                    <a class="wv-text--link" href="{{asset('uploads/School-Student-Import.csv')}}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                        {{ __('languages.download_and_view_student_csv')}}
                                    </a>
                                    {{ __('languages.sample_student_template')}}
                                </p>            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Modal -->
 <div class="modal fade template-modal" id="importStudentModal" tabindex="-1" role="dialog" aria-labelledby="importStudentModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('languages.import_student')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick=" document.getElementById('importStudents').reset();location.reload();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger error_msg" style="display: none;">
                    </div>
                    <div class="form-group data_tbl">
                       
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button class="btn btn-secondary data_action button-hide"  data-dismiss="modal" type="button" value="1">Skip</button> --}}
                    <!-- <button class="btn btn-secondary data_action button-hide"  data-dismiss="modal" type="button" value="2">{{__('languages.insert_update')}}</button> -->
                    <button class="btn btn-danger"  data-dismiss="modal" type="button" onclick=" document.getElementById('importStudents').reset();location.reload();">{{__('languages.cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal -->
 <div class="modal fade template-modal" id="importCsvStudentModal" tabindex="-1" role="dialog" aria-labelledby="importCsvStudentModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('languages.duplicate_csv_file_records')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger error_msg" style="display: none;">
                </div>
                <div class="form-group data_tbl">
                   
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger"  data-dismiss="modal" type="button" onclick=" document.getElementById('importStudents').reset();location.reload();">{{__('languages.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
@include('backend.layouts.footer')
@endsection
