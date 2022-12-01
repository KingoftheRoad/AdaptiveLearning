<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modules;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Traits\Common;
use Auth;
use App\Helpers\Helper;
class ModulesManagementController extends Controller
{
   use Common;
   
    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('ModulesList',$request);
            if(!in_array('modules_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $TotalModuleData = Modules::all()->count(); 
            $ModuleList = Modules::sortable()->orderBy(cn::MODULES_ID_COL,'DESC')->paginate($items);
            return view('backend.modulesmanagement.list',compact('ModuleList','TotalModuleData','items'));
        }
        catch(Exception $exception){
            return redirect('modulesmanagement')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
        try{
            if(!in_array('modules_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            return view('backend.modulesmanagement.add');
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('modules_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
             //  Check validation
            $validator = Validator::make($request->all(), Modules::rules($request, 'create'), Modules::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::MODULES_MODULE_NAME_COL => ucwords($request->module_name),
                cn::MODULES_MODULE_SLUG_COL => strtolower(str_replace(' ','_',trim($request->module_name,' '))),
                cn::MODULES_STATUS_COL    => $request->status,
             );
            $this->StoreAuditLogFunction($PostData,'Modules','','','Create Modules',cn::MODULES_TABLE_NAME,'');
            $Module = Modules::create($PostData);
            if(!empty($Module)){
                return redirect('modulesmanagement')->with('success_msg', __('languages.module_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function edit($id){
        try{
            if(!in_array('modules_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $module = Modules::find($id);
            return view('backend.modulesmanagement.edit',compact('module'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('modules_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), Modules::rules($request, 'update'), Modules::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $PostData = array(
                cn::MODULES_MODULE_NAME_COL => ucwords($request->module_name),
                cn::MODULES_MODULE_SLUG_COL => strtolower(str_replace(' ','_',trim($request->module_name,' '))),
                cn::MODULES_STATUS_COL    => $request->status,
            );
            $this->StoreAuditLogFunction($PostData,'Modules',cn::MODULES_ID_COL,$id,'Update Modules',cn::MODULES_TABLE_NAME,'');
            $update = Modules::where(cn::MODULES_ID_COL,$id)->update($PostData);
            if(!empty($update)){
                return redirect('modulesmanagement')->with('success_msg', __('languages.module_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function destroy($id){
        try{
            if(!in_array('modules_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Module = Modules::find($id);
            $this->StoreAuditLogFunction('','Modules','','','Delete Modules ID '.$id,cn::MODULES_TABLE_NAME,'');
            if($Module->delete()){
                return $this->sendResponse([], __('languages.module_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}
