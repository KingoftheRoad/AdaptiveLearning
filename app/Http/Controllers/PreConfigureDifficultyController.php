<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\GlobalConfiguration;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helper;
class PreConfigureDifficultyController extends Controller
{
    use Common;

    /**
     * USE : Listing page
     */
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('PreConfigureDifficultyList',$request);
            if(!in_array('pre_configure_difficulty_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            // $globalConfigValue = GlobalConfiguration::whereIn(cn::GLOBAL_CONFIGURATION_KEY_COL,['question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5'])->get();
            $countAicalculatedData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->count();
            $preConfigureLists = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->sortable()->orderBy(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,'DESC')->paginate($items);
            $difficultyLevels = $this->getDifficultyLevel();
            $statusList = $this->getStatusList();
            // Filteration on School code and School Name
            $Query = PreConfigurationDiffiltyLevel::select('*');
            if(isset($request->filter)){
                if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                    $Query->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficulty_lvl);
                }
                if(isset($request->difficult_value) && !empty($request->difficult_value)){
                    $Query->where(cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,'Like','%'.$request->difficult_value.'%');
                }
                if(isset($request->Status) && !empty($request->Status)){
                    $Query->where(cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL,$request->Status);
                }
                $TotalFilterData = $Query->count();
                $preConfigureLists = $Query->sortable()->paginate($items);
            }
            return view('backend.pre_configure_difficulty.list',compact('difficultyLevels','preConfigureLists','statusList','items','countAicalculatedData','TotalFilterData'));
            // return view('backend.pre_configure_difficulty.list',compact('globalConfigValue'));
        }catch(Exception $exception){
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }
    
    /**
     * USE
     */
    public function create(){
        try{
            if(!in_array('pre_configure_difficulty_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $difficultyLevels =$this->getDifficultyLevel();
            return view('backend.pre_configure_difficulty.add',compact('difficultyLevels'));
        }catch(Exception $exception){
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function store(Request $request){
        if(!in_array('pre_configure_difficulty_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
           return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        //  Check validation
        $validator = Validator::make($request->all(), PreConfigurationDiffiltyLevel::rules($request, 'create'), PreConfigurationDiffiltyLevel::rulesMessages('create'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $PostData = array(
            cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL => $this->getGlobalConfiguration('current_curriculum_year'),
            cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => $request->difficulty_level,
            cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => $request->difficulty_level_name_en,
            cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => $request->difficulty_level_name_ch,
            cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => $request->difficult_level_color,
            cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => $request->title,
            cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL => $request->status,
        );
        if(PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficulty_level)->doesntExist()){
            $PreConfigureDiffiltyLevel = PreConfigurationDiffiltyLevel::create($PostData);
            if(!empty($PreConfigureDiffiltyLevel)){
               $this->StoreAuditLogFunction($PostData,'PreConfigurationDiffiltyLevel','','','Create Pre Configure Difficulty Level',cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME,'');
               return redirect('pre-configure-difficulty')->with('success_msg', __('languages.pre_configure_difficulty_level_added_successfully'));
            }else{
               return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }else{
            return back()->with('error_msg', __('languages.difficulty_level_already_exists'));
        }
    }

    /**
     * USE : Edit record
     */
    public function edit($id){
        try{
            if(!in_array('pre_configure_difficulty_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $preConfigureData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,$id)->first();
            $difficultyLevels = $this->getDifficultyLevel();
            return view('backend.pre_configure_difficulty.edit',compact('preConfigureData','difficultyLevels'));
        }catch(Exception $exception){
            return redirect('pre-configure-difficulty')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update Record
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('pre_configure_difficulty_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), PreConfigurationDiffiltyLevel::rules($request, 'update'), PreConfigurationDiffiltyLevel::rulesMessages('update'));
            if($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => $request->difficulty_level,
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => $request->difficulty_level_name_en,
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => $request->difficulty_level_name_ch,
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => $request->difficult_level_color,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => $request->title,
                cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL  => $request->status,
            );
            if(PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficulty_level)->doesntExist()){
                $update = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,$id)->update($PostData);
            }else{
                $update = PreConfigurationDiffiltyLevel::FIND($id)->update([
                    cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => $request->title,
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => $request->difficulty_level_name_en,
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => $request->difficulty_level_name_ch,
                    cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => $request->difficult_level_color,
                    cn::PRE_CONFIGURE_DIFFICULTY_STATUS_COL  => $request->status,
                ]);
            }
            if($update){
                $this->StoreAuditLogFunction($PostData,'PreConfigurationDiffiltyLevel','','','Update Pre Configure Difficulty Level',cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,'');
                return redirect('pre-configure-difficulty')->with('success_msg', __('languages.pre_configure_difficulty_level_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * use : Delete Record
     */
    public function destroy($id){
        try{
            if(!in_array('pre_configure_difficulty_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $preConfigurationDifficultyLevel = PreConfigurationDiffiltyLevel::find($id);
            $this->StoreAuditLogFunction('','PreConfigurationDiffiltyLevel','','','Delete Pre Configure Difficulty Level ID '.$id,cn::PRE_CONFIGURE_DIFFICULTY_ID_COL,'');
            if($preConfigurationDifficultyLevel->delete()){
                return $this->sendResponse([], __('languages.pre_configure_difficulty_level_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
