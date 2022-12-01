<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\NodeRelation;

class Nodes extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::NODES_TABLE_NAME;
    protected $mainNodesOptionHtml = '';
    protected $nodesOptionHtml = '';
    protected $parentNodeId = '';
    
    public $fillable = [
        cn::NODES_NODEID_COL,
        cn::NODES_MAIN_ID_COL,
        cn::NODES_FIRST_MAIN_ID_COL,
        cn::NODES_SCHOOL_ID_COL,
        cn::NODES_NODE_TITLE_EN_COL,
        cn::NODES_NODE_TITLE_CH_COL,
        cn::NODES_DESCRIPTION_EN_COL,
        cn::NODES_DESCRIPTION_CH_COL,
        cn::NODES_WEAKNESS_NAME_EN_COL,
        cn::NODES_WEAKNESS_NAME_CH_COL,
        cn::NODES_IS_MAIN_NODE_COL,
        cn::NODES_STATUS_COL,
        cn::NODES_CREATED_BY_COL
    ];

    public $sortable = [
        cn::NODES_NODEID_COL,
        cn::NODES_NODE_TITLE_EN_COL,
        cn::NODES_NODE_TITLE_CH_COL,
        cn::NODES_DESCRIPTION_EN_COL,
        cn::NODES_DESCRIPTION_CH_COL,
        cn::NODES_WEAKNESS_NAME_EN_COL,
        cn::NODES_WEAKNESS_NAME_CH_COL,
        cn::NODES_STATUS_COL,
    ];

    public $timestamps = true;

    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::NODES_NODEID_COL => ['required'],
                    cn::NODES_NODE_TITLE_EN_COL => ['required'],
                    cn::NODES_NODE_TITLE_CH_COL => ['required'],
                    cn::NODES_WEAKNESS_NAME_EN_COL => ['required'],
                    cn::NODES_WEAKNESS_NAME_CH_COL => ['required']
                ];
                break;
            case 'update':
                $rules = [
                    cn::NODES_NODEID_COL => ['required'],
                    cn::NODES_NODE_TITLE_EN_COL => ['required'],
                    cn::NODES_NODE_TITLE_CH_COL => ['required'],
                    cn::NODES_WEAKNESS_NAME_EN_COL => ['required'],
                    cn::NODES_WEAKNESS_NAME_CH_COL => ['required']
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

     /**
    ** Additional Validation Massages for School
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::NODES_NODEID_COL.'.required' => __('validation.please_enter_node_id'),
                    cn::NODES_NODE_TITLE_EN_COL.'.required' => __('validation.please_enter_english_title'),
                    cn::NODES_NODE_TITLE_CH_COL.'.required' => __('validation.please_enter_chinese_title'),
                    cn::NODES_WEAKNESS_NAME_EN_COL.'.required' => __('validation.please_enter_english_weakness_name'),
                    cn::NODES_WEAKNESS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_weakness_name'),
                ];
                break;
            case 'update':
                $messages = [
                    cn::NODES_NODEID_COL.'.required' => __('validation.please_enter_node_id'),
                    cn::NODES_NODE_TITLE_EN_COL.'.required' => __('validation.please_enter_english_title'),
                    cn::NODES_NODE_TITLE_CH_COL.'.required' => __('validation.please_enter_chinese_title'),
                    cn::NODES_WEAKNESS_NAME_EN_COL.'.required' => __('validation.please_enter_english_weakness_name'),
                    cn::NODES_WEAKNESS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_weakness_name'),
                ];
                break;
        }
        return $messages;
    }

    /**
     * USE : Get nodelist based on selected school
     */
    public function get_nodelist($selectedNodeId = '', $catid = 0, $space = '', $repeat = 0, $skip = 0){
        $this->mainNodesOptionHtml='';
        $Model = new static;
        $Nodedata = $Model->where(cn::NODES_IS_MAIN_NODE_COL,1)->get();
        if(isset($Nodedata) && !empty($Nodedata)){
            foreach($Nodedata as $node){
                $selectedNodeOption='';
                if(!empty($selectedNodeId) && in_array($node->id,$selectedNodeId)){
                    $selectedNodeOption = 'selected="selected"';
                }
                $this->mainNodesOptionHtml .= "<option  ".$selectedNodeOption." value='".$node->id."' >".$node->node_id."</option>";
                $this->mainNodesOptionHtml .=$this->getChildNodeList($selectedNodeId,$node->id,'','',$skip);
                $this->nodesOptionHtml='';
            }
        }
        return $this->mainNodesOptionHtml;
    }

     /**
     * USE : Get parent id using node id
     */
    public function findTopParentNodeId($id) {
        if($id != "" || $id != 0){
            $data = NodeRelation::where('child_node_id',$id)->get()->toArray();
            if(!empty($data) ){
                $this->findTopParentNodeId($data[0]['parent_node_id']);
            }else{
                $this->parentNodeId=$id;
            }
        }
        else
        {
            $this->parentNodeId=$id;
        }
        return $this->parentNodeId;
    }

    /**
     * USE : Get nodelist based on selected Parent
     */
    public function getChildNodeList($selectedNodeId = '', $catid = 0, $space = '', $repeat = 0, $skip = 0){
        $Model = new static;
        if($repeat == 0){
            //$this->nodesOptionHtml .= "<option value='' selected>Select Child Node</option>";
            ++$repeat;
        }
        if (is_array($catid)) {
            $result = NodeRelation::select('*')->whereIn(cn::NODES_RELATION_PARENT_NODE_ID_COL,$catid)->get()->toArray();
        }else{
            $result = NodeRelation::select('*')->where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$catid)->get()->toArray();
        }
        
        if(isset($result) && !empty($result)){
            $result_data = array_column($result,cn::NODES_RELATION_CHILD_NODE_ID_COL);
            if($skip != 0){
                $result = $Model->select('*')->whereIn(cn::NODES_NODE_ID_COL,$result_data)->where(cn::NODES_NODE_ID_COL,'!=',$skip)->get();
            }else{
                $result = $Model->select('*')->whereIn(cn::NODES_NODE_ID_COL,$result_data)->get();
            }
            $countRows = $result->count();
        }

        if($catid === 0){
            $space = '';
        }else{
            $space .= "----";
        }
        
        if(isset($result) && !empty($result)){
            foreach($result as $row){
                if(isset($selectedNodeId) && !empty($selectedNodeId) && is_array($selectedNodeId) && in_array($row['id'],$selectedNodeId)){
                    $this->nodesOptionHtml .= "<option value='".$row['id']."' selected>".$space.$row[cn::NODES_NODEID_COL]."</option>";
                }else{
                    $this->nodesOptionHtml .= "<option value='".$row['id']."'>".$space.$row[cn::NODES_NODEID_COL]."</option>";
                }
                
                $this->getChildNodeList($selectedNodeId,$row['id'], $space, $repeat, $skip);
            }
        }
        return $this->nodesOptionHtml;
    }

    /**
     * USE : Get parent node name list based on node id
     */
    public function getParentNameById()
    {
        $subkey=cn::NODES_NODE_ID_COL;
        if($this->$subkey!="")
        {
            $data=NodeRelation::where(cn::NODES_RELATION_CHILD_NODE_ID_COL,explode(',',$this->$subkey))->select(cn::NODES_RELATION_PARENT_NODE_ID_COL)->get()->toArray();
            if(isset($data) && !empty($data))
            {
                $dataParentId=array_column($data,cn::NODES_RELATION_PARENT_NODE_ID_COL);
                $dataParent=Nodes::select('*')->whereIn(cn::NODES_NODE_ID_COL,$dataParentId)->get()->toArray();
                if(isset($dataParent) && !empty($dataParent))
                {
                    $dataParent=array_column($dataParent,cn::NODES_NODEID_COL);
                }
               return implode(',',$dataParent);
            }
        }
        return '';
    }
}
