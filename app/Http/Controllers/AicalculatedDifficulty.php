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
use Validator;


class AiCalculatedDifficulty extends Controller
{
    use Common;

    public function index(Request $request){
        try{
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            $countAicalculatedData = AiCalculatedDiffiltyLevel::all()->count();
            $AicalculatedList = AiCalculatedDiffiltyLevel::sortable()->orderBy(cn::AI_CALCULATED_DIFFICULTY_ID_COL,'DESC')->paginate($items);
            $difficultyLevels = array(
                ['id' =>  1,"name" => '1 - Easy'],
                ['id' =>  2,"name" => '2 - Medium'],
                ['id' =>  3,"name" => '3 - Difficult'],
                ['id' =>  4,"name" => '4 - Tough']
            );
            $statusList = array(
                ['id' => 'active',"name" => 'Active'],
                ['id' => 'inactive',"name" => 'Inactive']
            );
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
            $difficultyLevels = array(
                ['id' =>  1,"name" => '1 - Easy'],
                ['id' =>  2,"name" => '2 - Medium'],
                ['id' =>  3,"name" => '3 - Difficult'],
                ['id' =>  4,"name" => '4 - Tough']
            );
            return view('backend.ai_calculated_difficulty.add',compact('difficultyLevels'));
        }catch(Exception $exception){
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

   
    public function store(Request $request){
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
               $this->AuditLogfuncation($PostData,'AiCalculatedDiffiltyLevel','','','Create Ai Calculated Difficulty Level',cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
               return redirect('ai-calculated-difficulty')->with('success_msg', 'Ai Calculated Difficulty Level added successfully.');
            }else{
               return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return back()->with('error_msg', 'Difficulty Level Already Exists');
        }
    }
    
    /**
     * USE : Edit details for AI difficulty
     */
    public function edit($id){
        try{
            $AicalculatedData = AiCalculatedDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_ID_COL,$id)->first();
            $difficultyLevels = array(
                ['id' =>  1,"name" => '1 - Easy'],
                ['id' =>  2,"name" => '2 - Medium'],
                ['id' =>  3,"name" => '3 - Difficult'],
                ['id' =>  4,"name" => '4 - Tough']
            );
            return view('backend.ai_calculated_difficulty.edit',compact('AicalculatedData','difficultyLevels'));
        }catch(Exception $exception){
            return redirect('ai-calculated-difficulty')->withError($exception->getMessage())->withInput(); 
        }
    }

   
    /**
     * USE : Update detail for AI Difficulty
     */
    public function update(Request $request, $id){
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
            $this->AuditLogfuncation($PostData,'AiCalculatedDiffiltyLevel','','','Update Ai Calculated Difficulty Level',cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
            return redirect('ai-calculated-difficulty')->with('success_msg', 'Ai Calculated Difficulty Level updated successfully.');
        }else{
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    /**
     * Delete for AI Difficulty
     */
    public function destroy($id){
        try{
            $AiCalculatedDiffiltyLevel = AiCalculatedDiffiltyLevel::find($id);
            $this->AuditLogfuncation('','AiCalculatedDiffiltyLevel','','','Delete Ai Calculated Difficulty Level ID '.$id,cn::AI_CALCULATED_DIFFICULTY_TABLE_NAME,'');
            if($AiCalculatedDiffiltyLevel->delete()){
                return $this->sendResponse([], 'Ai Calculated Difficulty Level deleted successfully');
            }else{
                return $this->sendError('Please try again...', 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
