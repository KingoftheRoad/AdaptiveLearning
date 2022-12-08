<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use App\Models\AiCalculatedDiffiltyLevel;
use Exception;
use DB;
use Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
class AiCalculatedDifficulty extends Controller
{
    use Common;

    public function index(Request $request){
        try{
            if(!in_array('ai_calculate_difficulty_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            $countAicalculatedData = AiCalculatedDiffiltyLevel::all()->count();
            $AicalculatedList = AiCalculatedDiffiltyLevel::sortable()->orderBy(cn::AI_CALCULATED_DIFFICULTY_ID_COL,'DESC')->paginate($items);
            $difficultyLevels = $this->getDifficultyLevels();
            $statusList = $this->getStatusList();
            //Filteration on School code and School Name
            $Query = AiCalculatedDiffiltyLevel::select('*');
            if(isset($request->filter)){
                if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                    $Query->where(cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficulty_lvl);
                }
                if(isset($request->difficult_value) && !empty($request->difficult_value)){
                    $Query->where(cn::AI_CALCULATED_DIFFICULTY_TITLE_COL,'Like','%'.$request->difficult_value.'%');
                }
                if(isset($request->Status) && !empty($request->Status)){
                    $Query->where(cn::AI_CALCULATED_DIFFICULTY_STATUS_COL,$request->Status);
                }
                $TotalFilterData = $Query->count();
                $AicalculatedList = $Query->sortable()->paginate($items);
            }
            return view('backend.ai_calculated_difficulty.list',compact('difficultyLevels','AicalculatedList','statusList','items','countAicalculatedData','TotalFilterData'));
        }catch(Exception $exception){
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('ai_calculate_difficulty_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $difficultyLevels = $this->getDifficultyLevels();
            return view('backend.ai_calculated_difficulty.add',compact('difficultyLevels'));
        }catch(Exception $exception){
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function store(Request $request){
        if(!in_array('ai_calculate_difficulty_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        //  Check validation
        $validator = Validator::make($request->all(), AiCalculatedDiffiltyLevel::rules($request, 'create'), AiCalculatedDiffiltyLevel::rulesMessages('create'));
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $PostData = array(
            cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL => $request->difficultyLevel,
            cn::AI_CALCULATED_DIFFICULTY_TITLE_COL => $request->difficult_value,
            cn::AI_CALCULATED_DIFFICULTY_STATUS_COL  => $request->status,
        );
        if(AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficultyLevel)->doesntExist()){
            $AiCalculatedDiffiltyLevel = AiCalculatedDiffiltyLevel::create($PostData);
            if(!empty($AiCalculatedDiffiltyLevel)){
               $this->StoreAuditLogFunction($PostData,'AiCalculatedDiffiltyLevel','','','Create Ai Calculated Difficulty Level',cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
               return redirect('ai-calculated-difficulty')->with('success_msg', __('languages.ai_calculated_difficulty_level_added_successfully'));
            }else{
               return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }else{
            return back()->with('error_msg', __('languages.difficulty_level_already_exists'));
        }
    }
    
    /**
     * USE : Edit details for AI difficulty
     */
    public function edit($id){
        try{
            if(!in_array('ai_calculate_difficulty_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $AicalculatedData = AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_ID_COL,$id)->first();
            $difficultyLevels = $this->getDifficultyLevels();
            return view('backend.ai_calculated_difficulty.edit',compact('AicalculatedData','difficultyLevels'));
        }catch(Exception $exception){
            return redirect('ai-calculated-difficulty')->withError($exception->getMessage())->withInput(); 
        }
    }

   
    /**
     * USE : Update detail for AI Difficulty
     */
    public function update(Request $request, $id){
        if(!in_array('ai_calculate_difficulty_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
       //  Check validation
       $validator = Validator::make($request->all(), AiCalculatedDiffiltyLevel::rules($request, 'create'), AiCalculatedDiffiltyLevel::rulesMessages('create'));
        if ($validator->fails()) {
           return back()->withErrors($validator)->withInput();
        }
        $PostData = array(
            cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL => $request->difficultyLevel,
            cn::AI_CALCULATED_DIFFICULTY_TITLE_COL => $request->difficult_value,
            cn::AI_CALCULATED_DIFFICULTY_STATUS_COL  => $request->status
        );
        if(AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,$request->difficultyLevel)->doesntExist()){
            $update = AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_ID_COL,$id)->update($PostData);
        }else{
            $update = AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_ID_COL,$id)->update([
                cn::AI_CALCULATED_DIFFICULTY_TITLE_COL => $request->difficult_value,
                cn::AI_CALCULATED_DIFFICULTY_STATUS_COL  => $request->status
            ]);
        }
        // After successfully updated
        if($update){
            $this->StoreAuditLogFunction($PostData,'AiCalculatedDiffiltyLevel','','','Update Ai Calculated Difficulty Level',cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
            return redirect('ai-calculated-difficulty')->with('success_msg', __('languages.ai_calculated_difficulty_level_updated_successfully'));
        }else{
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * Delete for AI Difficulty
     */
    public function destroy($id){
        try{
            if(!in_array('ai_calculate_difficulty_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $AiCalculatedDiffiltyLevel = AiCalculatedDiffiltyLevel::find($id);
            $this->StoreAuditLogFunction('','AiCalculatedDiffiltyLevel','','','Delete Ai Calculated Difficulty Level ID '.$id,cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
            if($AiCalculatedDiffiltyLevel->delete()){
                return $this->sendResponse([], __('languages.ai_calculated_difficulty_level_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}