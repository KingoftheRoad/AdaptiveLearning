<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\Languages;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\UploadDocuments;
use App\Models\IntelligentTutorVideos;
use App\Models\Nodes;
use App\Models\Exam;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\TeachersClassSubjectAssign;
use App\Models\GradeSchoolMappings;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\View;
use App\Helpers\Helper;
use Exception;
use Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseFormat;
use Cookie;

class IntelligentTutorController extends Controller
{
    use Common,ResponseFormat;

    public function index(Request $request){
        try{
            //Get Intelligent-tutor video from Question Structure Code
            if($request->has('StructureCode')){
                $questionStructureCode = explode('-',$request->StructureCode);
                $grade = $questionStructureCode[0];
                $strand = $questionStructureCode[1];
                $learning_unit = substr($questionStructureCode[2],0,2);
                $learning_objective = substr($questionStructureCode[2],2,2);
                
                $gradeID =  Grades::select(cn::GRADES_ID_COL)->where(cn::GRADES_CODE_COL,$grade)->first();
                $StrandID = Strands::select(cn::STRANDS_ID_COL)->where(cn::STRANDS_CODE_COL,$strand)->first();
                $LearningUnitID = LearningsUnits::select(cn::LEARNING_UNITS_ID_COL)->where(cn::LEARNING_UNITS_CODE_COL,$learning_unit)->first();
                $LearningObjectivesID = LearningsObjectives::select(cn::LEARNING_OBJECTIVES_ID_COL)->where(cn::LEARNING_OBJECTIVES_CODE_COL,$learning_objective)->first();

                $request->request->add([
                    'learning_tutor_grade_id' => $gradeID->toArray(),
                    'learning_tutor_strand_id' => $StrandID->toArray(),
                    'learning_tutor_learning_unit' => $LearningUnitID->toArray(),
                    'learning_tutor_learning_objectives' =>$LearningObjectivesID->toArray()
                ]);
            }
            
            //  Laravel Pagination set in Cookie
            $this->LearningTutorCookie('IntelligentTutorList',$request);
            if(!in_array('intelligent_tutor_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 3;
            $mainUploadDataQuery = collect();
            $statusList = $this->getStatusList();
            $fileTypes = $this->getListOfDocumentType();
            
            $GradeIdsArray = $this->GetPluckIds('Grades');
            $StrandIdsArray = $this->GetPluckIds('Strands');
            $LearningsUnitsIdsArray = $this->GetPluckIds('LearningsUnits');
            $LearningsObjectivesIdsArray  = $this->GetPluckIds('LearningsObjectives');
            $LanguagesIdsArray = $this->GetPluckIds('Languages');            

            $languages = Languages::all();
            $Grades = Grades::whereIn('id',$this->GetRoleBasedGrades(Auth::user()->role_id))->get();//Grades::all();
            $StrandList = Strands::all();
            $LearningUnit = LearningsUnits::all();
            $GradeID = [];
            if(!$this->isStudentLogin()){
                $GradeID = (!empty($request->learning_tutor_grade_id)) ? $request->learning_tutor_grade_id : $GradeIdsArray;
            }else{
                $GradeID = Helper::GetCurriculumDataById($this->LoggedUserId(),$this->GetCurriculumYear(),'grade_id'); ;
            }
            $GradeID = (!empty($request->learning_tutor_grade_id)) ? $request->learning_tutor_grade_id : $GradeIdsArray;
            $StrandID = (!empty($request->learning_tutor_strand_id)) ? $request->learning_tutor_strand_id : $StrandIdsArray;
            $LearningUnitID = (!empty($request->learning_tutor_learning_unit)) ? $request->learning_tutor_learning_unit : $LearningsUnitsIdsArray;
            $LearningObjectivesId = (!empty($request->learning_tutor_learning_objectives)) ? $request->learning_tutor_learning_objectives : $LearningsObjectivesIdsArray;
            $LanguageId = (!empty($request->learning_tutor_language_id)) ? $request->learning_tutor_language_id : $LanguagesIdsArray;
            $Status = (!empty($request->learning_tutor_status)) ? $request->learning_tutor_status : 'active';

            $LearningObjective = LearningsObjectives::IsAvailableQuestion()->get();

            $strandObjectivesMappingId = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$GradeID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$StrandID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnitID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectivesId)
                                                                      ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
            if(!empty($strandObjectivesMappingId)){
                $mainUploadDataQuery =  IntelligentTutorVideos::whereIn(cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID,$LanguageId)
                                                                ->whereIntegerInRaw(cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL,$strandObjectivesMappingId->toArray())
                                                                ->where(cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL,$Status)
                                                                ->where(cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                                ->orderBy(cn::INTELLIGENT_TUTOR_VIDEOS_ID_COL,'DESC');
            }
            
            $countUploadData = $mainUploadDataQuery->count();
            $uploadData = $mainUploadDataQuery->take(12)->get();
            $requestData = $request->all();
            return view('backend.intelligent_tutor.intelligent_tutor_list',compact('Grades','StrandList','LearningUnit','LearningObjective','uploadData','items','statusList','fileTypes','languages','countUploadData','requestData'));
        }catch(Exception $exception){
            return redirect('intelligent-tutor')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('intelligent_tutor_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $type= DB::select(DB::raw("SHOW COLUMNS FROM upload_documents WHERE Field = 'file_type'"))[0]->Type ;
            preg_match('/^enum((.*))$/',$type,$file_type_matches);
            $file_type="";
            if(isset($file_type_matches[1]) && !empty($file_type_matches[1])){
                $file_type=str_replace("(","",$file_type_matches[1]);
                $file_type=str_replace(")","",$file_type);
                $file_type=str_replace("'","",$file_type);
                $file_type=str_replace(",","|",$file_type);
            }
            $languages = Languages::all();
            $Grades = Grades::all();
            $StrandList = Strands::all();
            $LearningUnit = LearningsUnits::all();
            // $LearningObjective = LearningsObjectives::all();
            $LearningObjective = LearningsObjectives::IsAvailableQuestion()->get();
            $Nodes = new Nodes;
            $NodesList = $Nodes->get_nodelist();
            return view('backend.intelligent_tutor.add',compact('Grades','NodesList','languages','file_type','StrandList','LearningUnit','LearningObjective'));
        }catch(Exception $exception){
            return redirect('intelligent-tutor')->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('intelligent_tutor_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            
            $type= DB::select(DB::raw("SHOW COLUMNS FROM upload_documents WHERE Field = 'file_type'"))[0]->Type ;
            preg_match('/^enum((.*))$/',$type,$file_type_matches);
            $file_type="";
            $filename = "";
            if(isset($file_type_matches[1]) && !empty($file_type_matches[1])){
                $file_type = str_replace("(","",$file_type_matches[1]);
                $file_type = str_replace(")","",$file_type);
                $file_type = str_replace("'","",$file_type);
                $file_type = str_replace(",url","",$file_type);
                $file_type = explode(',',$file_type);
            }
            $questionType = 0;
            $uploadData ='';
            $mainUploadDocument = '';
            $objectivesMapping = '';
            $UploadedFileName = '';
            $Insert_status = false;
            $destinationPath = public_path('uploads/intelligent_tutor');
            $file_path = 'uploads/intelligent_tutor';

            // Add video urls
            if(!empty($request->learning_tutor_grade_id)){
                foreach($request->learning_tutor_grade_id as $GradeID){
                    if(!empty($request->learning_tutor_strand_id)){
                        foreach($request->learning_tutor_strand_id as $StrandID){
                            if(!empty($request->learning_tutor_learning_unit)){
                                foreach($request->learning_tutor_learning_unit as $LearningUnitID){
                                    if(!empty($request->learning_tutor_learning_objectives)){
                                         foreach($request->learning_tutor_learning_objectives as $LearningsObjectivesID){
                                            $StrandUnitsObjectivesMappings = StrandUnitsObjectivesMappings::where([
                                                cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeID,
                                                cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1,
                                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL =>$StrandID,
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL =>$LearningUnitID,
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $LearningsObjectivesID
                                            ])->first();
                                           
                                            if(!empty($StrandUnitsObjectivesMappings)){
                                                $grade = str_replace(' ','_',$this->getGradeName($GradeID));
                                                $subject = str_replace(' ','_',$this->getSubjectName(1));
                                                $strand = str_replace(' ','_',$this->getStrandName($StrandID));
                                                $learning_unit = str_replace(' ','_',$this->getLearningUnitName($LearningUnitID));
                                                $learning_objective = str_replace(' ','_',$this->getLearningObjectiveName($LearningsObjectivesID));
                                                
                                                if(!empty($request->document_urls)){
                                                    if(!is_array($request->document_urls)){
                                                        $request->merge([
                                                            'document_urls' => explode(',',$request->document_urls)
                                                        ]);
                                                    }
                                                    foreach($request->document_urls as $documentUrl){
                                                        if(!empty($documentUrl)){
                                                            $filePath='';
                                                            $thumbnailUrl=$this->urlThumbnailGenerator($documentUrl);
                                                            if($thumbnailUrl!=""){
                                                                $filename = rand(10,100000).time().'.jpg';
                                                                $filePathDir=$destinationPath.'/'.$filename;
                                                                Image::make($thumbnailUrl)->save($filePathDir);
                                                                $filePath=$file_path.'/'.$filename;
                                                            }
                                                            $PostData =[
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL      => $this->GetCurriculumYear(),
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_TITLE_COL                   => $request->document_title,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL => $StrandUnitsObjectivesMappings->id,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_TYPE_COL           => $questionType,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_FILE_TYPE_COL               => 'url',
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_FILE_PATH_COL               => $documentUrl,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_THUMBNAIL_FILE_PATH_COL     => $filePath,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID                 => $request->learning_tutor_language_id,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_EN_COL          => $request->file_description_en ?? NULL,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_CH_COL          => $request->file_description_ch ?? NULL,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL                  => $request->status
                                                            ];
                                                            $uploadData = IntelligentTutorVideos::create($PostData);
                                                        }
                                                    }
                                                    if($uploadData){
                                                        $Insert_status = true;
                                                    }else{
                                                        $Insert_status =false;
                                                    }
                                                }

                                                $supported_image = array('gif','jpg','jpeg','png');
                                                $file_name = '';
                                                
                                                if(!empty($request->file)){
                                                    $upload_file = array_unique($request->file);
                                                    foreach($upload_file as $file){
                                                        $document = $file;
                                                        $UploadedFileName = pathinfo($document->getClientOriginalName(),PATHINFO_FILENAME);
                                                        $file_name = $UploadedFileName.'_'.time().'_'.rand(1,1000).'.'.$document->extension();
                                                        $file_extension = $document->extension();
                                                        if(in_array(strtolower($document->extension()), $file_type))
                                                        {
                                                            if(in_array(strtolower($document->extension()), $supported_image)){
                                                                $img = Image::make($document->path());
                                                                $img->resize(100, 100, function ($constraint) {
                                                                    $constraint->aspectRatio();
                                                                })->save($destinationPath.'/'.$file_name);
                                                                $filePath=$destinationPath.'/'.$file_name;
                                                                copy($document->path(),$filePath);
                                                            }else{
                                                                $filePath=$destinationPath.'/'.$file_name;
                                                                copy($document->path(),$filePath);
                                                            }                                                            
                                                            // Create Post Array
                                                            $postData = array(
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL      => $this->GetCurriculumYear(),
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL => (!empty($StrandUnitsObjectivesMappings->id)) ? $StrandUnitsObjectivesMappings->id : null,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_FILE_NAME_COL               => $file_name,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_TITLE_COL                   => $request->document_title,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_TYPE_COL           => $questionType,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_FILE_TYPE_COL               => $file_extension,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_FILE_PATH_COL               => $file_path.'/'.$file_name,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID                 => $request->learning_tutor_language_id,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_EN_COL          => $request->file_description_en ?? NULL,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_CH_COL          => $request->file_description_ch ?? NULL,
                                                                cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL                  => $request->status
                                                            );
                                                            $uploadData=IntelligentTutorVideos::create($postData);
                                                            if($uploadData){
                                                                $Insert_status = true;
                                                            }else{
                                                                $Insert_status =false;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                }
            }
            if($Insert_status == true){
                if($request->SubmitType == "saveAndContinue"){
                    return $this->sendResponse(['redirect'=> ''], __('languages.files_uploaded'));
                }else{
                    return $this->sendResponse(['redirect'=> 'intelligent-tutor'], __('languages.files_uploaded'));
                }
                
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(Exception $exception){
            return redirect('intelligent-tutor')->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            $File = IntelligentTutorVideos::find($id);
            if(!empty($File)){
                return $this->sendResponse($File->toArray());
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch(Exception $exception){
            return redirect('intelligent-tutor')->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('intelligent_tutor_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $postData = [
                cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID        => $request->languageId,
                cn::UPLOAD_DOCUMENTS_TITLE_COL          => $request->title,
                cn::UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL => ($request->languageId == 1) ? $request->description_en : '',
                cn::UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL => ($request->languageId == 2) ? $request->description_ch : '',
                cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL => $request->status
            ];
            $updatedFile = IntelligentTutorVideos::find($id)->update($postData);
            if(!empty($updatedFile)){
                return $this->sendResponse([], __('languages.file_uploaded_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch(Exception $exception){
            return redirect('intelligent-tutor')->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('intelligent_tutor_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $removeFiles = IntelligentTutorVideos::find($id);
            if(!empty($removeFiles)){
                if($removeFiles->delete()){
                    return $this->sendResponse([], __('languages.file_deleted_successfully'));
                }else{
                    return $this->sendError(__('languages.please_try_again'), 422);
                }
            }
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function AddMoreVideoFiles(Request $request){
        $html = '';
        $countUploadData = $request->count;
        $AllFileData ="";
        $params = array();
        parse_str($request->formData, $params);
        $GradeIdsArray = $this->GetPluckIds('Grades');
        $StrandIdsArray = $this->GetPluckIds('Strands');
        $LearningsUnitsIdsArray = $this->GetPluckIds('LearningsUnits');
        $LearningsObjectivesIdsArray  = $this->GetPluckIds('LearningsObjectives');
        $LanguagesIdsArray = $this->GetPluckIds('Languages');
        $GradeID = (isset($params['learning_tutor_grade_id'])) ? $params['learning_tutor_grade_id'] : $GradeIdsArray;
        $StrandID = (isset($params['learning_tutor_strand_id'])) ? $params['learning_tutor_strand_id'] : $StrandIdsArray;
        $LearningUnitID = (isset($params['learning_tutor_learning_unit'])) ? $params['learning_tutor_learning_unit'] : $LearningsUnitsIdsArray;
        $LearningObjectivesId = (isset($params['learning_tutor_learning_objectives'])) ? $params['learning_tutor_learning_objectives'] : $LearningsObjectivesIdsArray;
        $Language = (isset($params['learning_tutor_language_id'])) ? $params['learning_tutor_language_id'] : $LanguagesIdsArray;
        $Status = (isset($params['learning_tutor_status'])) ? $params['learning_tutor_status'] : 'active';
        $strandObjectivesMappingId = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$GradeID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$StrandID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnitID)
                                                                      ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectivesId)
                                                                      ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
                                                                     
        if(!empty($strandObjectivesMappingId)){
            $AllFileData =  IntelligentTutorVideos::whereIn(cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL,$strandObjectivesMappingId)->orderBy('id','DESC');
        }
        $uploadData = IntelligentTutorVideos::whereIntegerInRaw(cn::INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL,$strandObjectivesMappingId->toArray())
                                            ->where(cn::INTELLIGENT_TUTOR_VIDEOS_STATUS_COL,$Status)
                                            ->where(cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                            ->whereIn(cn::INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID,$LanguagesIdsArray)
                                            ->orderBy(cn::INTELLIGENT_TUTOR_VIDEOS_ID_COL,'DESC')->skip($countUploadData)
                                            ->take(12)
                                            ->get();
        if(!empty($uploadData)){
            $html =  (string)View::make('backend.intelligent_tutor.more_files',compact('uploadData','countUploadData'));
            $countUploadData += 12;  
            return $this->sendResponse([$html,$countUploadData,$AllFileData->count()]);
        }else{
            return $this->SendError(__('languages.no_any_documents'),422);
        }
    }
}