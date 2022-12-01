<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Models\LearningsUnits;
use App\Models\Strands;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Auth;
class LearningUnitsController extends Controller
{
    use Common;
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('LearningUnitsList',$request);
            if(!in_array('learning_units_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $statusList = $this->getStatusOptions();
            $strands = Strands::where(cn::STRANDS_STATUS_COL, 1)->get();
            $items = $request->items ?? 10;
            $LearningsUnitsList = LearningsUnits::with('Strands')->orderBy(cn::LEARNING_UNITS_ID_COL,'DESC')->paginate($items);
            //Filteration on Node ID and Node Title
            $Query = LearningsUnits::with('Strands')->select('*');
            if(isset($request->filter)){
                if(isset($request->Search) && !empty($request->Search)){
                    $Query->orWhere(cn::LEARNING_UNITS_NAME_COL,'Like','%'.$request->Search.'%')
                    ->orWhere(cn::LEARNING_UNITS_NAME_EN_COL,'Like','%'.$request->Search.'%')
                    ->orWhere(cn::LEARNING_UNITS_NAME_CH_COL,'Like','%'.$request->Search.'%');
                }
                if(isset($request->Status)){
                    $Query->where(cn::LEARNING_UNITS_STATUS_COL,$request->Status);
                }
                if(isset($request->strand_id)){
                    $Query->where(cn::LEARNING_UNITS_STRANDID_COL,$request->strand_id);
                }
                $LearningsUnitsList = $Query->paginate($items);
            }
            return view('backend.learning_units.list',compact('LearningsUnitsList','items','statusList','strands'));
        }catch(Exception $exception){
            return redirect('learning_units')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Create new strands
     */
    public function create(){
        try{
            if(!in_array('learning_units_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $strands = Strands::where(cn::STRANDS_STATUS_COL, 1)->get();
            return view('backend.learning_units.add',compact('strands'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Save new strands details
     */
    public function store(Request $request){
        try{
            if(!in_array('learning_units_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), LearningsUnits::rules($request, 'create'), LearningsUnits::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData =array(
                cn::LEARNING_UNITS_NAME_COL => $request->name_en,
                cn::LEARNING_UNITS_NAME_EN_COL => $request->name_en,
                cn::LEARNING_UNITS_NAME_CH_COL => $request->name_ch,
                cn::LEARNING_UNITS_STRANDID_COL => $request->strand_id,
                cn::LEARNING_UNITS_CODE_COL => $request->code,
                cn::MODULES_STATUS_COL    => $request->status,
            );
            $this->StoreAuditLogFunction($PostData,'LearningsUnits','','','Create LearningsUnits',cn::LEARNING_UNITS_TABLE_NAME,'');
            $LearningsUnits = LearningsUnits::create($PostData);
            if(!empty($LearningsUnits)){
                return redirect('learning_units')->with('success_msg', __('languages.learning_units_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get the selected strands details
     */
    public function edit($id){
        try{
            if(!in_array('learning_units_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $learning_units = LearningsUnits::find($id);
            $strands = Strands::where(cn::STRANDS_STATUS_COL, 1)->get();
            return view('backend.learning_units.edit',compact('learning_units','strands'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update details for strands details
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('learning_units_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), LearningsUnits::rules($request, 'update'), LearningsUnits::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::LEARNING_UNITS_NAME_COL => $request->name_en,
                cn::LEARNING_UNITS_NAME_EN_COL => $request->name_en,
                cn::LEARNING_UNITS_NAME_CH_COL => $request->name_ch,
                cn::LEARNING_UNITS_STRANDID_COL => $request->strand_id,
                cn::LEARNING_UNITS_CODE_COL => $request->code,
                cn::MODULES_STATUS_COL    => $request->status,
            );
            $this->StoreAuditLogFunction($PostData,'LearningsUnits',cn::LEARNING_UNITS_ID_COL,$id,'Update LearningsUnits',cn::LEARNING_UNITS_TABLE_NAME,'');
            $update = LearningsUnits::where(cn::LEARNING_UNITS_ID_COL,$id)->update($PostData);
            if(!empty($update)){
                return redirect('learning_units')->with('success_msg', __('languages.learning_units_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('learning_units_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $LearningsUnits = LearningsUnits::find($id);
            $this->StoreAuditLogFunction('','LearningsUnits','','','Delete LearningsUnits ID '.$id,cn::LEARNING_UNITS_TABLE_NAME,'');
            if($LearningsUnits->delete()){
                return $this->sendResponse([], __('languages.learning_units_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}