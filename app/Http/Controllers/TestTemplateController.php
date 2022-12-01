<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use APP\Constants\DbConstant As cn;
use App\Helpers\Helper;
use App\Models\TestTemplates;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
class TestTemplateController extends Controller
{
    use Common, ResponseFormat;

    public function __construct(){
        
    }

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('TestTemplateList',$request);
            if(!in_array('test_template_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10; //For Pagination
            $TotalFilterData ='';
            $TotalTestTemplatesData = TestTemplates::all()->count();
            if (Auth::user()->{cn::USERS_ROLE_ID_COL} == 1) {
                $TestTemplatesList = TestTemplates::paginate($items);
            }else if (Auth::user()->{cn::USERS_ROLE_ID_COL} == 2){
                $TestTemplatesList = TestTemplates::where(cn::TEST_TEMPLATE_STATUS,'=','active')->paginate($items);
            }else if (Auth::user()->{cn::USERS_ROLE_ID_COL} == 3){
                $TestTemplatesList = TestTemplates::where(cn::TEST_TEMPLATE_TYPE,'=',1)->where(cn::TEST_TEMPLATE_STATUS,'=','active')->paginate($items);
            }
            $difficultyLevels = array(
                ['id' =>  1,"name" => '1 - Easy'],
                ['id' =>  2,"name" => '2 - Medium'],
                ['id' =>  3,"name" => '3 - Difficult'],
                ['id' =>  4,"name" => '4 - Tough']
            );

            $QuestionTypes = array(
                ['id' => '0', "name" => 'All'],
                ['id' => '1', "name" => 'Self-Learning'],
                ['id' => '2', "name" => 'Exercise/Assignment'],
                ['id' => '3', "name" => 'Testing']
            );
            $QuestionTypesList=array_column($QuestionTypes,'name','id');
            // for filteration code here
            $SearchTestTemplatesQuery = TestTemplates::select('*');

            if(isset($request->filter) || !empty(Session::get('TestTemplatesListFilter'))){
                $this->saveAndGetFilterData('TestTemplatesListFilter',$request);

                // Search by difficulty level
                if(isset($request->difficulty_level) && !empty($request->difficulty_level)){
                    $SearchTestTemplatesQuery->where(cn::TEST_TEMPLATE_DIFFICULTY_LEVEL_COL,$request->difficulty_level);
                }

                // Serch By Question Type
                if(isset($request->question_type) && !empty($request->question_type)){
                    $SearchTestTemplatesQuery->where(cn::TEST_TEMPLATE_TYPE,$request->question_type);
                }
                
                //Search By Status
                if(isset($request->status)){
                    $SearchTestTemplatesQuery->where(cn::TEST_TEMPLATE_STATUS,$request->status);
                }
                $TotalFilterData = $SearchTestTemplatesQuery->count();
                $TestTemplatesList = $SearchTestTemplatesQuery->paginate($items);
            }
            // Return all compact parameter
            $CompactArray = ['TestTemplatesList','difficultyLevels','QuestionTypes','items','TotalTestTemplatesData','TotalFilterData','QuestionTypesList'];
            return view('backend.test_template.list',compact($CompactArray));
        } catch (\Exception $exception) {
            return redirect('test-template')->withError($exception->getMessage());
        }
    }
    
    public function create(){
        if(!in_array('test_template_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
           return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $data = [];
        $data['TemplateTypes'] = $this->getTemplateTypes();
        $data['DifficultyLevels'] = $this->getDifficultyLevels();
        return view('backend.test_template.add',compact('data'));
    }

    public function store(Request $request){
        try{
            if(!in_array('test_template_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), TestTemplates::rules($request, 'create'), TestTemplates::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData =array(
                cn::TEST_TEMPLATE_NAME_COL => $request->name,
                cn::TEST_TEMPLATE_TYPE => $request->template_type,
                cn::TEST_TEMPLATE_DIFFICULTY_LEVEL_COL => $request->difficulty_level,
                cn::TEST_TEMPLATE_QUESTION_IDS_COL =>implode(',',$request->question_ids),
                cn::TEST_TEMPLATE_CREATED_BY =>$this->LoggedUserId()
            );
            $TestTemplates = TestTemplates::create($postData);
            if(!empty($TestTemplates)){
                $this->StoreAuditLogFunction($postData,'TestTemplates','','','Create Test Templates',cn::TEST_TEMPLATE_TABLE_NAME,'');
                return redirect('test-template')->with('success_msg', 'Test Template added successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }catch(Exception $exception){
           return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    public function edit($id){
        try{
            if(!in_array('test_template_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $data = [];
            $data['TemplateTypes'] = $this->getTemplateTypes();
            $data['DifficultyLevels'] = $this->getDifficultyLevels();
            $data['TestTemplatesData']=TestTemplates::find($id);
            $data['Question'] = '' ;
            if($data['TestTemplatesData']->question_ids != ''){
                $QuestionIdArray = explode(',', $data['TestTemplatesData']->question_ids);
                $data['Question']=Question::with('SunjectNameFromQuestion')->whereIn(cn::QUESTION_TABLE_ID_COL,$QuestionIdArray)->get();
                $data['QuestionOtherCount']=Question::with('SunjectNameFromQuestion')->whereNotIn(cn::QUESTION_TABLE_ID_COL,$QuestionIdArray)->get()->count();
            }
            return view('backend.test_template.edit',compact('data'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('test_template_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), TestTemplates::rules($request, 'update', $id), TestTemplates::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData =array(
                cn::TEST_TEMPLATE_NAME_COL => $request->name,
                cn::TEST_TEMPLATE_TYPE => $request->template_type,
                cn::TEST_TEMPLATE_DIFFICULTY_LEVEL_COL => $request->difficulty_level,
                cn::TEST_TEMPLATE_QUESTION_IDS_COL =>implode(',',$request->question_ids)
            );
            $this->StoreAuditLogFunction($postData,'TestTemplates',cn::TEST_TEMPLATE_ID_COL,$id,'Update Test Templates',cn::TEST_TEMPLATE_TABLE_NAME,'');
            $update = TestTemplates::where(cn::TEST_TEMPLATE_ID_COL,$id)->update($postData);
            if(!empty($update)){
                return redirect('test-template')->with('success_msg', 'Test Template updated successfully.');
            }
            else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again...');
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('test_template_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $TestTemplates = TestTemplates::find($id);
            $this->StoreAuditLogFunction('','TestTemplates','','','Delete Test Templates ID '.$id,cn::TEST_TEMPLATE_TABLE_NAME,'');
            if($TestTemplates->delete()){
                return $this->sendResponse([], 'Test Templates deleted successfully');
            }else{
                return $this->sendError('Please try again...', 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    public function selecttesttemplateData(Request $request){
        if (isset($request->templateTypeId) && isset($request->difficultyLevel)){
            $skip =0;
            if (isset($request->skip) && $request->skip!=""){
                $skip = $request->skip;
            }
            $take = 20;
            $question='';
            $question_recode_count=0;
            $question_recode_count_list = Question::with('SunjectNameFromQuestion')
                                            ->whereRaw("find_in_set('".$request->templateTypeId."',question_type)")
                                            ->where(cn::QUESTION_DIFFICULTY_LEVEL_COL,$request->difficultyLevel);
            $question_data = Question::with('SunjectNameFromQuestion')
                                ->whereRaw("find_in_set('".$request->templateTypeId."',question_type)")
                                ->where(cn::QUESTION_DIFFICULTY_LEVEL_COL,$request->difficultyLevel);
            if (isset($request->DataOldId) && $request->DataOldId!=""){
                $oldids = TestTemplates::find($request->DataOldId)->question_ids;
                $question_recode_count_list->whereNotIn(cn::QUESTION_TABLE_ID_COL,explode(',', $oldids));
                $question_data->whereNotIn(cn::QUESTION_TABLE_ID_COL,explode(',', $oldids));
            }
            $question_recode_count = $question_recode_count_list->skip($skip+20)->take($take)->get()->count();
            $question = $question_data->skip($skip)->take($take)->get()->toArray();
            $question['recode_count'] = $question_recode_count;
            return $question;
        }
        return '';
    }
}
