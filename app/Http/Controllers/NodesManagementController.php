<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nodes;
use App\Models\NodeRelation;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helper;

class NodesManagementController extends Controller
{
    use common;
    protected $FirstMainNodeId = null;

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('NodeList',$request);
            if(!in_array('node_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $nodeList = Nodes::sortable()->orderBy(cn::NODES_NODE_ID_COL,'DESC')->paginate($items);
            $statusList = $this->getStatusList();
            //Filteration on Node ID and Node Title
            $Query = Nodes::select('*');
            if(isset($request->filter)){
                if(isset($request->Search) && !empty($request->Search)){
                    $Query->orWhere(cn::NODES_NODEID_COL,'Like','%'.$request->Search.'%')
                    ->orWhere(cn::NODES_NODE_TITLE_EN_COL,'Like','%'.$request->Search.'%')
                    ->orWhere(cn::NODES_NODE_TITLE_CH_COL,'Like','%'.$request->Search.'%');
                }
                if(isset($request->SearchParentNode) && !empty($request->SearchParentNode)){
                    $childNodes = [];
                    //Search Node is parent node
                    $parentNode = Nodes::where(cn::NODES_NODEID_COL,$request->SearchParentNode)->first();
                    if(!empty($parentNode)){
                        $childNodes = NodeRelation::where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$parentNode->{cn::NODES_NODE_ID_COL})->pluck(cn::NODES_RELATION_CHILD_NODE_ID_COL);
                    }
                    if(!empty($childNodes)){
                        $Query->whereIn(cn::NODES_NODE_ID_COL,$childNodes);
                    }
                }
                if(isset($request->Status)){
                    $Query->where(cn::NODES_STATUS_COL,$request->Status);
                }
                $nodeList = $Query->sortable()->paginate($items);
            }
            return view('backend.nodes.list',compact('nodeList','items','statusList'));
        }catch(Exception $exception){
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }

    public function create(){
       try{
            if(!in_array('node_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $NodesList = Nodes::where(cn::NODES_IS_MAIN_NODE_COL,1)->get();
            $Nodes = new Nodes;
            $NodesList = $Nodes->get_nodelist();
            return view('backend.nodes.add',compact('NodesList'));
        }catch(Exception $exception){
            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function store(Request $request){
        try{
            if(!in_array('node_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), Nodes::rules($request, 'create'), Nodes::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $IsMainNode = 0 ;
            if(!isset($request->main_node_id)){
                $IsMainNode = 1 ;
            }
            $postData =array(
                cn::NODES_NODEID_COL            => $request->node_id,
                cn::NODES_NODE_TITLE_EN_COL     => $request->node_title_en,
                cn::NODES_NODE_TITLE_CH_COL     => $request->node_title_ch,
                cn::NODES_WEAKNESS_NAME_EN_COL  => $request->weakness_name_en,
                cn::NODES_WEAKNESS_NAME_CH_COL  => $request->weakness_name_ch,
                cn::NODES_DESCRIPTION_EN_COL    => $request->description_en,
                cn::NODES_DESCRIPTION_CH_COL    => $request->description_ch,
                cn::NODES_IS_MAIN_NODE_COL      => $IsMainNode,
                cn::NODES_STATUS_COL            => $request->status,
                cn::NODES_CREATED_BY_COL        => $this->LoggedUserId(),
            );
            $Nodes = Nodes::create($postData);
            if(isset($request->main_node_id) && !empty($request->main_node_id)){
                $record = array_unique($request->main_node_id);
                foreach ($record as $key => $value) {
                    $Data = array(
                        cn::NODES_RELATION_PARENT_NODE_ID_COL => $value,
                        cn::NODES_RELATION_CHILD_NODE_ID_COL => $Nodes->{cn::NODES_NODE_ID_COL}
                    );
                    $NodeRelation = NodeRelation::create($Data);
                }
            }
            if(!empty($Nodes)){
                $this->StoreAuditLogFunction($postData,'Nodes','','','Create Node',cn::NODES_TABLE_NAME,'');
                return redirect('nodes')->with('success_msg', __('languages.node_added_successfully'));
            }else{
                return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function edit(Request $request, $id){
        try{
            if(!in_array('node_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }  
            $nodeData = Nodes::find($id);
            $mainNodeId=array();
            $NodeRelation = NodeRelation::where(cn::NODES_RELATION_CHILD_NODE_ID_COL,$nodeData->{cn::NODES_NODE_ID_COL})->get()->toArray();
            $mainNodeId=array_column($NodeRelation, 'parent_node_id');
            $Nodes = new Nodes;
            $MainNodesList = $Nodes->get_nodelist($mainNodeId,'','','',$nodeData->{cn::NODES_NODE_ID_COL});
            $displaynodeData = $Nodes->whereIn(cn::NODES_NODE_ID_COL,$mainNodeId)->get();
            return view('backend.nodes.edit',compact('nodeData','MainNodesList','displaynodeData'));
        }catch(Exception $exception){
            return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function update(Request $request, $id){
        try{
            if(!in_array('node_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            //  Check validation
            $validator = Validator::make($request->all(), Nodes::rules($request, 'create'), Nodes::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $IsMainNode = 0 ;
            if(!isset($request->main_node_id) && !isset($request->sub_node_id) && $request->sub_node_id==null){
                $IsMainNode = 1 ;
                $NodeRelation = NodeRelation::where(cn::NODES_RELATION_CHILD_NODE_ID_COL,$id)->delete();
            }
            $postData = array(
                cn::NODES_IS_MAIN_NODE_COL  => $IsMainNode,
                cn::NODES_NODEID_COL => $request->node_id,
                cn::NODES_NODE_TITLE_EN_COL     => $request->node_title_en,
                cn::NODES_NODE_TITLE_CH_COL     => $request->node_title_ch,
                cn::NODES_WEAKNESS_NAME_EN_COL  => $request->weakness_name_en,
                cn::NODES_WEAKNESS_NAME_CH_COL  => $request->weakness_name_ch,
                cn::NODES_DESCRIPTION_EN_COL    => $request->description_en,
                cn::NODES_DESCRIPTION_CH_COL    => $request->description_ch,
                cn::NODES_STATUS_COL   => $request->status,
            );
            $this->StoreAuditLogFunction($postData,'Nodes',cn::NODES_NODE_ID_COL,$id,'Update Node',cn::NODES_TABLE_NAME,'');
            $update = Nodes::where(cn::NODES_NODE_ID_COL,$id)->update($postData);
            if(isset($request->main_node_id) && !empty($request->main_node_id) && $request->sub_node_id==null){
                foreach ($request->main_node_id as $key => $value) {
                    $selectNodeRelation = NodeRelation::where(cn::NODES_RELATION_CHILD_NODE_ID_COL,$id)->where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$value)->get()->toArray();
                    if (empty($selectNodeRelation)) {
                        $Data = array(
                            cn::NODES_RELATION_PARENT_NODE_ID_COL => $value,
                            cn::NODES_RELATION_CHILD_NODE_ID_COL => $id,
                        );
                        $NodeRelation = NodeRelation::create($Data);
                    }
                }
            }
            if(!empty($update)){
                return redirect('nodes')->with('success_msg', __('languages.node_updated_successfully'));
            }else{
                return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
        }
    }

    public function destroy($id){
        try{
            if(!in_array('node_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check node id sub node exists or not
            if(NodeRelation::where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$id)->exists()){
                return $this->sendError(__('languages.do_not_remove_direct_main_node_please_remove_child_nodes_first'), 422);
            }else{
                $NodeRelationIds = NodeRelation::where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$id)->orWhere(cn::NODES_RELATION_CHILD_NODE_ID_COL,$id)->pluck(cn::NODES_RELATION_ID_COL);
                $Nodes = Nodes::find($id);
                // If no any assign sub nodes
                if($Nodes->delete()){
                    if(isset($NodeRelationIds) && !empty($NodeRelationIds)){
                        NodeRelation::whereIn(cn::NODES_RELATION_ID_COL,$NodeRelationIds)->delete();
                    }
                    $this->StoreAuditLogFunction('','Nodes','','','Delete Node ID '.$id,cn::NODES_TABLE_NAME,'');
                    return $this->sendResponse([], __('languages.node_deleted_successfully'));
                }else{
                    return $this->sendError(__('languages.please_try_again'), 422);
                }
            }
        }catch(Exception $exception){
            return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
        }
    }

    /**
     * USE : Get all nodes list based on selected school
     */
    public function getNodelistBySchool(Request $request){
        $Nodes = new Nodes;
        $NodesList = $Nodes->get_nodelist($request->school_id);
        return $this->sendResponse($NodesList);
    }

    /**
     * USE : Get all nodes list based on selected Parent Node
     */
    public function getChildNodelistByParent(Request $request){
        $childNodesList = '';
        if(!empty($request->main_node_id)){
            $Nodes = new Nodes;
            if(isset($request->skip_id)){
                $childNodesList = $Nodes->getChildNodeList('',$request->main_node_id,'','',$request->skip_id);
            }else{
                $childNodesList = $Nodes->getChildNodeList('',$request->main_node_id);
            }
            return $this->sendResponse($childNodesList);
        }
    }

    /**
     * USE : Get the list of categories
     */
    public function getTreeViewListNodes(Request $request){
        $NodeRelation = new NodeRelation;
        $nodelist = $NodeRelation->getNodeTreeView();
        return view('backend.nodes.tree_view_node_list',compact('nodelist'));
    }

    // From Edit Page display node list to remove node
    public function removeParentNode(Request $request,$id){
        $removeNodeRelation = NodeRelation::where([cn::NODES_RELATION_CHILD_NODE_ID_COL => $request->currentNodeId,cn::NODES_RELATION_PARENT_NODE_ID_COL =>$id])->delete();
        if($removeNodeRelation){
            return $this->sendResponse([], __('languages.node_deleted_successfully'));
        }else{
            return back()->with('error_msg',  __('languages.problem_was_occur_please_try_again'));
        }
    }
}
