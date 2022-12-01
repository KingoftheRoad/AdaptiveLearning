<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Models\Strands;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helper;
class StrandsController extends Controller
{
    use Common;

    /**
     * USE : Listing page on strands
     */
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('StrandList',$request);
            if(!in_array('strands_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $strandsList = Strands::sortable()->orderBy(cn::STRANDS_ID_COL,'DESC')->paginate($items);
            $statusList = array(
                ['id' => '1',"name" => 'Active'],
                ['id' => '0',"name" => 'Inactive']
            );
            // Filteration on School code and School Name
            $Query = Strands::select('*');
            if(isset($request->filter)){
                if(isset($request->StrandsName) && !empty($request->StrandsName)){
                    $Query->where(cn::STRANDS_NAME_EN_COL,$request->StrandsName);
                }
                if(isset($request->StrandsCode) && !empty($request->StrandsCode)){
                    $Query->where(cn::STRANDS_CODE_COL,$request->StrandsCode);
                }
                if(isset($request->Status)){
                    $Query->where(cn::STRANDS_STATUS_COL,$request->Status);
                }
                $strandsList = $Query->sortable()->paginate($items);
            }
            return view('backend.strands.list',compact('strandsList','statusList','items'));
        }catch(Exception $exception){
            return redirect('strands')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Create page open on add new strands
     */
    public function create(){
        try{
            if(!in_array('strands_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            return view('backend.strands.add');
        }catch(Exception $exception){
           return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * USE : Add new strands detail
     */
    public function store(Request $request){
        try{
            if(!in_array('strands_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), Strands::rules($request, 'create'), Strands::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $postData = array(
                cn::STRANDS_NAME_COL    => $request->name_en,
                cn::STRANDS_NAME_EN_COL => $request->name_en,
                cn::STRANDS_NAME_CH_COL => $request->name_ch,
                cn::STRANDS_CODE_COL    => $request->code,
                cn::STRANDS_STATUS_COL  => $request->status
            );
            $this->StoreAuditLogFunction($postData,'Strands','','','Create Strands',cn::STRANDS_TABLE_NAME,'');
            $Strands = Strands::create($postData);
            if($Strands){
                return redirect('strands')->with('success_msg', __('languages.strands_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return redirect('strands')->withError($exception->getMessage())->withInput(); 
        }
    }

    /**
     * USE : Edit record for strands
     */
    public function edit($id){
        try{
            if(!in_array('strands_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $StrandsData = Strands::where(cn::STRANDS_ID_COL,$id)->first();
            return view('backend.strands.edit',compact('StrandsData'));
        }catch(Exception $exception){
            return redirect('strands')->withError($exception->getMessage())->withInput(); 
        }
    }

    /**
     * USE : Update record for strands
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('strands_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), Strands::rules($request, 'create'), Strands::rulesMessages('create'));
            if($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }
            $postData = array(
                cn::STRANDS_NAME_COL    => $request->name_en,
                cn::STRANDS_NAME_EN_COL => $request->name_en,
                cn::STRANDS_NAME_CH_COL => $request->name_ch,
                cn::STRANDS_CODE_COL    => $request->code,
                cn::STRANDS_STATUS_COL  => $request->status
            );
            $this->StoreAuditLogFunction($postData,'Strands',cn::STRANDS_ID_COL,$id,'Update Strands',cn::STRANDS_TABLE_NAME,'');
            $Strands = Strands::find($id)->update($postData);
            if($Strands){
                return redirect('strands')->with('success_msg', __('languages.strands_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * USE : Delete strands
     */
    public function destroy($id){
        try{
            if(!in_array('strands_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $strand = Strands::find($id);
            $this->StoreAuditLogFunction('','Strands','','','Delete Strand ID '.$id,cn::STRANDS_TABLE_NAME,'');
            if($strand->delete()){
                return $this->sendResponse([], __('languages.strands_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
