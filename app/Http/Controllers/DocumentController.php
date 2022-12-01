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
use App\Models\Nodes;
use App\Models\Exam;
use App\Models\MainUploadDocument;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\TeachersClassSubjectAssign;
use App\Models\GradeSchoolMappings;
use App\Constants\DbConstant As cn;
use App\Helpers\Helper;
use Exception;
use Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DocumentController extends Controller
{
    use Common;
    
    public function getNodeIdByNodeName($nodeName){
        if(!empty($nodeName)){
            $node = Nodes::where(cn::NODES_NODEID_COL,$nodeName)->first();
            return $node->id;
        }
    }
    
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('DocumentsList',$request);
            if(!in_array('upload_documents_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Nodes = new Nodes;
            $items = $request->items ?? 10;
            $statusList = $this->getStatusList();
            $fileTypes = $this->getListOfDocumentType();
            $NodesList = $Nodes->get_nodelist();
            $languages = Languages::all();
            $uploadData = MainUploadDocument::with('document','language')->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->paginate($items);
            $countuploadData = MainUploadDocument::get()->count();
            $Query = MainUploadDocument::select('*')->with('document');
            if($request->filter){
                if(!empty($request->NodeId) && isset($request->NodeId)){
                    $nodeId = $this->getNodeIdByNodeName($request->NodeId);
                    if(!empty($nodeId)){
                        $Query->where(cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL,$nodeId);
                    }
                }
                if(!empty($request->fileName && isset($request->fileName))){
                    $Query->whereHas('document', function ($query) use($request) {
                        $query->where(cn::UPLOAD_DOCUMENTS_FILE_NAME_COL,'LIKE','%'.$request->fileName .'%');
                    });
                }
                if(!empty($request->description && isset($request->description))){
                    $Query->where(cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL,'like','%'.$request->description.'%')
                    ->orWhere(cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL,'like','%'.$request->description.'%');
                }
                if($request->language){
                    $Query->where(cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID,$request->language);
                }
                if($request->Status){
                    $Query->where(cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL,$request->Status);
                }
                $uploadData = $Query->orderBy(cn::MAIN_UPLOAD_DOCUMENT_ID_COL,'DESC')->paginate($items);
               
                $countuploadData = $Query->count();
            }
            return view('backend.document.admin_document_list',compact('uploadData','NodesList','items','countuploadData','statusList','fileTypes','languages'));
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }
    
    public function create(){
        try{
            if(!in_array('upload_documents_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
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
            $Nodes = new Nodes;
            $NodesList = $Nodes->get_nodelist();
            $Grades = Grades::all();
            return view('backend.document.add',compact('Grades','NodesList','languages','file_type'));
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store Uploaded documents
     */
    public function store(Request $request){
        try{
            if(!in_array('upload_documents_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $type= DB::select(DB::raw("SHOW COLUMNS FROM upload_documents WHERE Field = 'file_type'"))[0]->Type ;
            preg_match('/^enum((.*))$/',$type,$file_type_matches);
            $file_type="";
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
            // check validations
            if(empty($request->node_id)){
                return back()->withInput('error_msg', __('languages.please_select_nodes'));
            }
            if(empty($request->file('upload')) && empty($request->document_urls)){
                return back()->with('error_msg', __('languages.please_upload_documents_or_enter_document_urls'));
            }
            if(isset($request->node_id) && !empty($request->node_id)){
                $Insert_status = true;
                $node_id = array_unique($request->node_id);
                foreach ($node_id as $key => $value) {
                    $objectivesMapping = $this->StrandUnitsObjectivesMappingsIdByNodes($value);
                    if(($objectivesMapping['StrandUnitsObjectivesMappingsId'] == 0)){
                        return back()->withInput()->with('error_msg', __('languages.invalid_question_code_please_try_using_valid_question_code'));
                    }
                }
                $destinationPath = public_path('uploads/upload_documents');
                $file_path = 'uploads/upload_documents';
                $grade = str_replace(' ','_',$this->getGradeName($objectivesMapping['grade_id']));
                $subject = str_replace(' ','_',$this->getSubjectName($objectivesMapping['subject_id']));
                $strand = str_replace(' ','_',$this->getStrandName($objectivesMapping[cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL]));
                $learning_unit = str_replace(' ','_',$this->getLearningUnitName($objectivesMapping['learning_unit_id']));
                $learning_objective = str_replace(' ','_',$this->getLearningObjectiveName($objectivesMapping['learning_objectives_id']));
                if (isset($objectivesMapping['question_type'])){
                    $questionType = $objectivesMapping['question_type'];
                }
                //check folder is exits or not if not then create
                if(!File::exists($destinationPath.''.$grade)) {
                    File::makeDirectory($destinationPath.'/'.$grade, $mode = 0777, true, true);
                }
                if(!File::exists($destinationPath.'/'.$grade.'/'.$subject)){
                    File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject, $mode = 0777, true, true);
                }
                if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand)){
                    File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand, $mode = 0777, true, true);
                }
                if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit)){
                    File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit, $mode = 0777, true, true);
                }
                if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective)){
                    File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective, $mode = 0777, true, true);
                }

                $mainUploadDocument = MainUploadDocument::create([
                    cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL        => implode(',',$node_id),
                    // cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL      => $request->FileName,
                    cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL => $request->file_description_en,
                    cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL => $request->file_description_ch,
                    cn::MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL      => $this->LoggedUserId(),
                    cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID        => $request->language_id,
                    cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL         => $request->status
                ]);

                // Add video urls
                if(!empty($request->document_urls)){
                    foreach($request->document_urls as $documentUrl){
                        if(!empty($documentUrl)){
                            $filePath='';
                            $thumbnailUrl=$this->urlThumbnailGenerator($documentUrl);
                            if($thumbnailUrl!="")
                            {
                                $filename = rand(10,100000).time().'.jpg';
                                $filePathDir=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$filename;
                                Image::make($thumbnailUrl)->save($filePathDir);
                                $filePath=$file_path.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$filename;
                            }
                            
                            $uploadData = UploadDocuments::create([
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID         => (!empty($mainUploadDocument)) ? $mainUploadDocument->id : null,//$value,
                                cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? null,
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL           => $questionType,
                                cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL               => 'url',
                                cn::UPLOAD_DOCUMENTS_FILE_PATH_COL               => $documentUrl,
                                cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL     => $filePath,
                                cn::UPLOAD_DOCUMENTS_LANGUAGE_ID                 => $request->language_id 
                            ]);
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
                
                if($upload_file = $request->file('upload')){
                    foreach($upload_file as $file){
                        $document = $file;
                        $UploadedFileName = pathinfo($document->getClientOriginalName(),PATHINFO_FILENAME);
                        $file_name = $UploadedFileName.'_'.time().'_'.rand(1,1000).'.'.$document->extension();
                        $file_extension = $document->extension();
                        if(in_array(strtolower($document->extension()), $file_type))
                        {
                            //check folder is exits or not if not then create
                            if(!File::exists($destinationPath.''.$grade)) {
                                File::makeDirectory($destinationPath.'/'.$grade, $mode = 0777, true, true);
                            }
                            if(!File::exists($destinationPath.'/'.$grade.'/'.$subject)){
                                File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject, $mode = 0777, true, true);
                            }
                            if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand)){
                                File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand, $mode = 0777, true, true);
                            }
                            if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit)){
                                File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit, $mode = 0777, true, true);
                            }
                            if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective)){
                                File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective, $mode = 0777, true, true);
                            }
                            if(in_array(strtolower($document->extension()), $supported_image)){
                                $img = Image::make($document->path());
                                $img->resize(100, 100, function ($constraint) {
                                    $constraint->aspectRatio();
                                })->save($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name);
                                $filePath=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name;
                                copy($document->path(),$filePath);
                            }else{
                                $filePath=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name;
                                copy($document->path(),$filePath);
                            }
                            // Create Post Array
                            $postData = array(
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID         => (!empty($mainUploadDocument)) ? $mainUploadDocument->id : null,//$value,
                                cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? null,
                                cn::UPLOAD_DOCUMENTS_FILE_NAME_COL               => $file_name,
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL           => $questionType,
                                cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL               => $file_extension,
                                cn::UPLOAD_DOCUMENTS_FILE_PATH_COL               => $file_path.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name,
                                cn::UPLOAD_DOCUMENTS_LANGUAGE_ID                 => $request->language_id
                            );
                            $uploadData=UploadDocuments::create($postData);
                            if($uploadData){
                                $Insert_status = true;
                            }else{
                                $Insert_status =false;
                            }
                        }
                    }
                }
            }
            if($Insert_status == true){
                return redirect('upload-documents')->with('success_msg', __('languages.document_uploaded_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('upload_documents_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
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
            $selectedNode = '';
            $Document = MainUploadDocument::with('document')->find($id);
            if(!empty($Document)){
                $selectedNode = explode(',',$Document->{cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL});
            }
            $Nodes = new Nodes;
            $NodesList = $Nodes->get_nodelist($selectedNode);
            return view('backend.document.edit',compact('Document','NodesList','languages','file_type'));
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request,$id){
        try{
            if(!in_array('upload_documents_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $type= DB::select(DB::raw("SHOW COLUMNS FROM upload_documents WHERE Field = 'file_type'"))[0]->Type ;
            preg_match('/^enum((.*))$/',$type,$file_type_matches);
            $file_type="";
            if(isset($file_type_matches[1]) && !empty($file_type_matches[1])){
                $file_type=str_replace("(","",$file_type_matches[1]);
                $file_type=str_replace(")","",$file_type);
                $file_type=str_replace("'","",$file_type);
                $file_type=str_replace(",url","",$file_type);
                $file_type=explode(',',$file_type);
            }
            $objectivesMapping = '';
            $questionType = 0;
            $Insert_status = false;

           // check validations
            if(empty($request->node_id)){
                return back()->withInput('error_msg', __('languages.please_select_nodes'));
            }
            if(empty($request->file('upload')) && empty($request->document_urls)){
                return back()->with('error_msg', __('languages.please_upload_documents_or_enter_document_urls'));
            }
            if(isset($request->node_id) && !empty($request->node_id)){
                $Insert_status = true;
                $node_id = array_unique($request->node_id);
                foreach ($node_id as $key => $value) {
                    $objectivesMapping = $this->StrandUnitsObjectivesMappingsIdByNodes($value);
                    if(($objectivesMapping['StrandUnitsObjectivesMappingsId'] == 0)){
                        return back()->withInput()->with('error_msg', __('languages.invalid_question_code_please_try_using_valid_question_code'));
                    }
                }
            }

            //update Language in Upload Document
            if(isset($request->language_id)){
                MainUploadDocument::where(cn::MAIN_UPLOAD_DOCUMENT_ID_COL,$id)->update([cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID => $request->language_id]);
                UploadDocuments::where(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$id)->update([cn::UPLOAD_DOCUMENTS_LANGUAGE_ID => $request->language_id]);
            }

            $destinationPath = public_path('uploads/upload_documents');
            $file_path = 'uploads/upload_documents';
            $grade = str_replace(' ','_',$this->getGradeName($objectivesMapping['grade_id']));
            $subject = str_replace(' ','_',$this->getSubjectName($objectivesMapping['subject_id']));
            $strand = str_replace(' ','_',$this->getStrandName($objectivesMapping['strand_id']));
            $learning_unit = str_replace(' ','_',$this->getLearningUnitName($objectivesMapping['learning_unit_id']));
            $learning_objective = str_replace(' ','_',$this->getLearningObjectiveName($objectivesMapping['learning_objectives_id']));

            if (isset($objectivesMapping['question_type'])){
                $questionType = $objectivesMapping['question_type'];
            }
            $mainUploadData = MainUploadDocument::find($id)->update([
                cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL        => implode(',',$node_id),
                // cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL      => $request->FileName,
                cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL => $request->file_description_en,
                cn::MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL => $request->file_description_ch,
                cn::MAIN_UPLOAD_DOCUMENT_STATUS_COL         => $request->status
            ]);

            //For URLs
            if(!empty($request->document_urls)){
                foreach($request->document_urls as $documentUrl){
                    if(!empty($documentUrl)){
                        $filePath='';
                        if(UploadDocuments::where([cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID=>$id,cn::UPLOAD_DOCUMENTS_FILE_PATH_COL=> $documentUrl])->doesntExist()){

                            $thumbnailUrl=$this->urlThumbnailGenerator($documentUrl);
                            if($thumbnailUrl!=""){
                                //$filename = basename($thumbnailUrl).'.jpg';
                                $filename = rand(10,100000).time().'.jpg';
                                $filePathDir=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$filename;
                                Image::make($thumbnailUrl)->save($filePathDir);
                                $filePath=$file_path.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$filename;
                            }
                        }
                        //Check Url Alredy Exists or Not if Exists then update otherwise create.
                        if(UploadDocuments::where([cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID=>$id,cn::UPLOAD_DOCUMENTS_FILE_PATH_COL=> $documentUrl])->exists()){
                            $uploadData = UploadDocuments::where([cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID=>$id,cn::UPLOAD_DOCUMENTS_FILE_PATH_COL=> $documentUrl])->update([
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID         => $id,
                                cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? null,
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL           => $questionType,
                                cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL               => 'url',
                                cn::UPLOAD_DOCUMENTS_FILE_PATH_COL               => $documentUrl,
                                cn::UPLOAD_DOCUMENTS_LANGUAGE_ID                 => $request->language_id
                            ]);
                        }else{
                            $uploadData = UploadDocuments::where([cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID=>$id,cn::UPLOAD_DOCUMENTS_FILE_PATH_COL=> $documentUrl])->create([
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID         => $id,
                                cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? null,
                                cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL           => $questionType,
                                cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL               => 'url',
                                cn::UPLOAD_DOCUMENTS_FILE_PATH_COL               => $documentUrl,
                                cn::UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL     => $filePath,
                                cn::UPLOAD_DOCUMENTS_LANGUAGE_ID                 => $request->language_id
                            ]);
                        }
                    }
                }
            }
           
            //For Files 
            $supported_image = array('gif','jpg','jpeg','png');
            $file_name = '';
            
            if($upload_file = $request->file('upload')){
                foreach($upload_file as $file){
                    $document = $file;
                    $UploadedFileName = pathinfo($document->getClientOriginalName(),PATHINFO_FILENAME);
                    $file_name = $UploadedFileName.'_'.time().'_'.rand(1,1000).'.'.$document->extension();
                    $file_extension = $document->extension();
                    if(in_array(strtolower($document->extension()), $file_type)){
                        //check folder is exits or not if not then create
                        if(!File::exists($destinationPath.''.$grade)) {
                            File::makeDirectory($destinationPath.'/'.$grade, $mode = 0777, true, true);
                        }
                        if(!File::exists($destinationPath.'/'.$grade.'/'.$subject)){
                            File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject, $mode = 0777, true, true);
                        }
                        if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand)){
                            File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand, $mode = 0777, true, true);
                        }
                        if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit)){
                            File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit, $mode = 0777, true, true);
                        }
                        if(!File::exists($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective)){
                            File::makeDirectory($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective, $mode = 0777, true, true);
                        }
                        if(in_array(strtolower($document->extension()), $supported_image)){
                            $img = Image::make($document->path());
                            $img->resize(100, 100, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name);
                            $filePath=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name;
                            copy($document->path(),$filePath);
                        }else{
                            $filePath=$destinationPath.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name;
                            copy($document->path(),$filePath);
                        }
                       
                        // Create Post Array
                        $postData = array(
                            cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID         =>$id,
                            cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? null,
                            cn::UPLOAD_DOCUMENTS_FILE_NAME_COL               => $file_name,
                            cn::UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL           => $questionType,
                            cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL               => $file_extension,
                            cn::UPLOAD_DOCUMENTS_FILE_PATH_COL               => $file_path.'/'.$grade.'/'.$subject.'/'.$strand.'/'.$learning_unit.'/'.$learning_objective.'/'.$file_name,
                            cn::UPLOAD_DOCUMENTS_LANGUAGE_ID                 => $request->language_id
                        );
                        $uploadData=UploadDocuments::where(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$id)->updateOrCreate($postData);
                    }
                }
            }   
            if($mainUploadData){
                return redirect('upload-documents')->with('success_msg', __('languages.document_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('upload_documents_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $getDocumentData = MainUploadDocument::with('document')->find($id);
            $childDocumentData = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,[$id]);
            if(!empty($getDocumentData->document)){
                foreach($getDocumentData->document as $document){
                    if($document->file_type != 'url'){
                        $file_path = public_path($document->file_path);
                        unlink($file_path);
                    }
                }
            }
            if($getDocumentData->delete() && $childDocumentData->delete()){
                return $this->sendResponse([], __('languages.file_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function removeSingleFileFromDatabase($id){
        try{
            $getDocumentData = UploadDocuments::find($id);
            if(!empty($getDocumentData)){
                if($getDocumentData->file_type != 'url'){
                    $file_path = public_path($getDocumentData->{cn::UPLOAD_DOCUMENTS_FILE_PATH_COL});
                    unlink($file_path);
                }
            }
            if($getDocumentData->delete()){
                return $this->sendResponse([],  __('languages.file_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Get Documents using student exam 
     */
    public function getExamDocument(Request $request,$type){
        try{
            $ExamList = array();
            $strands_id = array();
            $learning_units_id = array();
            $learning_objectives_id = array();
            $isFilter = 0;
            $active_tab="";
            $student_id="";
            $userId = Auth::id();
            $nodeList=array();
            if(isset($request->nodeList) && !empty($request->nodeList)){
                $nodeList = json_decode($request->nodeList,true);
                $nodeList = array_values($nodeList);
                $nodeList = array_filter($nodeList);
                $nodeList = array_unique($nodeList);
            }else{
                if(isset($request->student_id) && !empty($request->student_id)){
                    $userId = $request->student_id;
                    $student_id = $request->student_id;
                }
                if(isset($request->active_tab) && !empty($request->active_tab)){
                    $active_tab = $request->active_tab;
                }
                if(isset($request->strands) && !empty($request->strands)){
                    $strands_id = json_decode($request->strands);
                    $isFilter = 1;
                }
                if(isset($request->learning_units) && !empty($request->learning_units)){
                    $learning_units_id = json_decode($request->learning_units);
                    $isFilter = 1;
                }
                if(isset($request->learning_objectives_id) && !empty($request->learning_objectives_id)){
                    $learning_objectives_id = json_decode($request->learning_objectives_id);
                    $isFilter = 1;
                }

                // Get Mapping Ids By filtering selected options
                $StrandUnitsObjectivesMappings = StrandUnitsObjectivesMappings::where(function ($query) use ($strands_id,$learning_units_id,$learning_objectives_id) {
                    if(!empty($strands_id)){
                        $query->whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strands_id);
                    }
                    if(!empty($learning_units_id)){
                        $query->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learning_units_id);
                    }
                    if(!empty($learning_objectives_id)){
                        $query->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learning_objectives_id);
                    }
                })->get()->toArray();
                if(isset($StrandUnitsObjectivesMappings) && !empty($StrandUnitsObjectivesMappings) && $isFilter==1){
                    $StrandUnitsObjectivesMappingsId=array_column($StrandUnitsObjectivesMappings,'id');
                    $QuestionsList = Question::with('answers')->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)->orderBy('id')->get()->toArray();
                    if(isset($QuestionsList) && !empty($QuestionsList)){
                        $QuestionsDataList = array_column($QuestionsList,'id');
                        $ExamList = Exam::with('attempt_exams')->whereIn(cn::EXAM_TABLE_QUESTION_IDS_COL,$QuestionsDataList)->get()->toArray();
                        if(isset($ExamList) && !empty($ExamList)){
                            $ExamList = array_column($ExamList,'id');
                        }
                    }
                }
                $dataExamQuestion_ids = '';
                $dataExam = Exam::with('attempt_exams')->whereRaw("find_in_set($userId,student_ids)")->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)->where(function ($query) use ($learning_objectives_id,$ExamList){
                    if(!empty($learning_objectives_id)){
                        $query->whereIn(cn::EXAM_TABLE_ID_COLS,$ExamList);
                    }
                })->get()->toArray();
                if(isset($dataExam) && !empty($dataExam)){
                    foreach ($dataExam as $Examkey => $Examvalue) {
                        if($dataExamQuestion_ids != ""){
                            $dataExamQuestion_ids .= ','.$Examvalue['question_ids'];
                        }else{
                            $dataExamQuestion_ids = $Examvalue['question_ids'];
                        }
                    }
                }

                $nodeList = '';
                if($dataExamQuestion_ids != ""){
                    $dataExamQuestion_ids = explode(',', $dataExamQuestion_ids);
                    $dataExamQuestion_ids = array_unique($dataExamQuestion_ids);
                    $QuestionsList = Answer::whereIn(cn::ANSWER_QUESTION_ID_COL,$dataExamQuestion_ids)->get()->toArray();
                    foreach ($QuestionsList as $Questionskey => $Questionsvalue) {                    
                        if($nodeList!=""){
                            $nodeList.=','.$Questionsvalue['answer1_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer2_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer3_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer4_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer1_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer2_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer3_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer4_node_relation_id_ch'];
                        }else{
                            $nodeList=$Questionsvalue['answer1_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer2_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer3_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer4_node_relation_id_en'];
                            $nodeList.=','.$Questionsvalue['answer1_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer2_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer3_node_relation_id_ch'];
                            $nodeList.=','.$Questionsvalue['answer4_node_relation_id_ch'];
                        }
                    }
                }
                $nodeList=explode(',', $nodeList);
                $nodeList=array_unique($nodeList);
            }

            if(!empty($nodeList)){
                $mainDocumentId = [];
                foreach($nodeList as $node){
                    $mainUploadIds = MainUploadDocument::whereRaw("find_in_set($node,node_id)")->get()->pluck(cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
                    if(!$mainUploadIds->isEmpty()){
                        $mainDocumentId[] = $mainUploadIds[0];
                    }
                }
            }
           
            $uploadData=array();
            if($type){
                $typeList='';
                if($type!=""){
                    $typeList=$this->getDocumentType($type);
                }
                $uploadData = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$mainDocumentId)->whereIn(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,$typeList)->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->get()->toArray();
            }
            return view('backend.document.exam_documents',compact('uploadData','strands_id','learning_units_id','learning_objectives_id','active_tab'));
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get all documents using student exam id on right side on the screen document list 
     */
    // public function getExamAllDocument(Request $request){        
    //     $ExamList = array();
    //     $strands_id = array();
    //     $learning_units_id = array();
    //     $learning_objectives_id = array();
    //     $isFilter = 0;
    //     $active_tab="";
    //     $student_id="";
    //     $userId = Auth::id();
    //     $list_exam_id=array();
    //     if(isset($request->student_id) && !empty($request->student_id)){
    //         $userId=$request->student_id;
    //         $student_id=$request->student_id;
    //     }
    //     if(isset($request->active_tab) && !empty($request->active_tab)){
    //         $active_tab=$request->active_tab;
    //     }
    //     if(isset($request->list_exam_id) && !empty($request->list_exam_id)){
    //         $list_exam_id_data = implode(',',$request->list_exam_id);
    //         $list_exam_id_data = explode(',',$list_exam_id_data);
    //         $list_exam_id = $list_exam_id_data;
    //     }
    //     $dataExamQuestion_ids = '';
    //     $dataExam = Exam::with('attempt_exams')->whereRaw("find_in_set($userId,student_ids)")->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)->where(function ($query) use ($list_exam_id){
    //         if(!empty($list_exam_id)){
    //             $query->whereIn(cn::EXAM_TABLE_ID_COLS,$list_exam_id);
    //         }
    //     })->get()->toArray();
    //     if(isset($dataExam) && !empty($dataExam)){
    //         foreach ($dataExam as $Examkey => $Examvalue) {
    //             if($dataExamQuestion_ids != ""){
    //                 $dataExamQuestion_ids .= ','.$Examvalue['question_ids'];
    //             }else{
    //                 $dataExamQuestion_ids = $Examvalue['question_ids'];
    //             }
    //         }
    //     }
        
    //     // Find the group Test Documents
    //     $nodeList='';
    //     if($dataExamQuestion_ids != ""){
    //         $dataExamQuestion_ids = explode(',', $dataExamQuestion_ids);
    //         $dataExamQuestion_ids = array_unique($dataExamQuestion_ids);
    //         $QuestionsList = Answer::whereIn(cn::ANSWER_QUESTION_ID_COL,$dataExamQuestion_ids)->get()->toArray();
    //         foreach ($QuestionsList as $Questionskey => $Questionsvalue) {                    
    //             if($nodeList!=""){
    //                 $nodeList.=','.$Questionsvalue['answer1_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer2_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer3_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer4_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer1_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer2_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer3_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer4_node_relation_id_ch'];
    //             }else{
    //                 $nodeList=$Questionsvalue['answer1_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer2_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer3_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer4_node_relation_id_en'];
    //                 $nodeList.=','.$Questionsvalue['answer1_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer2_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer3_node_relation_id_ch'];
    //                 $nodeList.=','.$Questionsvalue['answer4_node_relation_id_ch'];
    //             }
    //         }
    //     }
    //     $nodeList=explode(',', $nodeList);
    //     $nodeList=array_unique(array_filter($nodeList));
    //     $uploadData=array();
    //     if(!empty($nodeList)){
    //         $mainDocumentId = [];
    //         foreach($nodeList as $node){
    //             $mainUploadIds = MainUploadDocument::whereRaw("find_in_set($node,node_id)")->get()->pluck(cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
    //             if(!$mainUploadIds->isEmpty()){
    //                 $mainDocumentId[] = $mainUploadIds[0];
    //             }
    //         }

    //         $fileType = ['pdf','video','ppt','doc','txt','excel','audio','image'];
    //         foreach($fileType as $filetype){
    //             $typeVideoList=$this->getDocumentType($filetype);
    //             $mainDocumentId = array_unique($mainDocumentId);
    //             if(!empty($mainDocumentId)){
    //                 $data = $this->getDocument($mainDocumentId, $typeVideoList);
    //                 $uploadData['doc'.ucfirst($filetype).'Data'] = $data['document'];
    //                 $uploadData['doc'.ucfirst($filetype).'DataCount'] = $data['documentCount'];
    //             }
    //         }
    //     }
    //     return view('backend.document.exam_document_list',compact('uploadData','nodeList'));
    // }

    public function getDocument($mainDocumentId, $typeVideoList){
        $data = [];
        $data['document'] = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$mainDocumentId)
                                            ->whereIn(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,$typeVideoList)
                                            ->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')
                                            ->limit(1)->get()->toArray();
        $data['documentCount'] = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$mainDocumentId)
        ->whereIn(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,$typeVideoList)->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->count();
        return $data;
    }

    public function getAllDocuments(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('DocumentsList',$request);
            if(!in_array('documents_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Grades ='';
            $GradesId ='';
            $strandsUnitMappingIds = '';
            if($this->isTeacherLogin()){
                $GradesId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}])->pluck('class_id');
            }
            if($this->isStudentLogin()){
                $GradesId = [Auth::user()->grade_id];
            }
            if($this->isPrincipalLogin()){
                $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                $GradesId = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
            }

            if(!empty($GradesId)){
                $Grades = Grades::whereIn(cn::GRADES_ID_COL,$GradesId)->get();
                $strandsUnitMappingIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$GradesId)->get()->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
            }
            $strands = Strands::all(); 
            $LearningUnits = LearningsUnits::all();
            // $LearningObjectives = LearningsObjectives::all();
            $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->get();

            $uploadData = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL,$strandsUnitMappingIds)->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->get();
            if($request->filter){
                $Query = StrandUnitsObjectivesMappings::select('*');
                if($request->grade_id){
                    $Query->whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,[$request->grade_id]);
                }
                if($request->strand_id){
                    $Query->whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,[$request->grade_id])->where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1,cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $request->strand_id]);
                }
                if($request->learning_unit_id){
                    $Query->whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,[$request->grade_id])->where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1,cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $request->strand_id,cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL =>$request->learning_unit_id]);
                } 
                if($request->learning_objectives_id){
                    $Query->whereIn(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,[$request->grade_id])->where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1,cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $request->strand_id,cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL =>$request->learning_unit_id,cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $request->learning_objective_id]);
                }
                $getStrandsUnitsMappingIds = $Query->get()->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
                if(!empty($getStrandsUnitsMappingIds)){
                    $uploadData = UploadDocuments::whereIn('strand_units_mapping_id',$getStrandsUnitsMappingIds)->orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->get();
                }
            }
            return view('backend.student.document_list',compact('uploadData','Grades','strands','LearningUnits','LearningObjectives'));
        }catch(Exception $exception){
            return redirect('upload-documents')->withError($exception->getMessage())->withInput();
        }
    }
}
