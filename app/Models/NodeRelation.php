<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Nodes;

class NodeRelation extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = cn::NODES_RELATION_TABLE_NAME;

    protected $html = '';
    protected $childOption = '';
    
    public $fillable = [
        cn::NODES_RELATION_PARENT_NODE_ID_COL,
        cn::NODES_RELATION_CHILD_NODE_ID_COL,
        cn::NODES_RELATION_STATUS
    ];

    public $timestamps = true;


    /**
     * USE : get nodes tree view
     */
    public function getNodeTreeView(){
        $NodeModel = new Nodes;
        $Nodedata = $NodeModel->all();
        if(isset($Nodedata) && !empty($Nodedata)){
            foreach($Nodedata as $node){
                if(NodeRelation::where(cn::NODES_RELATION_CHILD_NODE_ID_COL,$node->{cn::NODES_NODE_ID_COL})->doesntExist()){
                    $this->html .= '<ul>';
                    $this->html .= "<li dataid='".$node->{cn::NODES_NODE_ID_COL}."'>".$node->{cn::NODES_NODEID_COL};

                    $result = NodeRelation::with('nodes')->where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$node->{cn::NODES_NODE_ID_COL})->get();
                    if(isset($result) && !empty($result)){
                        $this->html .= '<ul>';
                        foreach($result as $row){
                            $this->html .= "<li dataid='".$row->nodes->{cn::NODES_NODE_ID_COL}."'>".$row->nodes->{cn::NODES_NODEID_COL};
                            $this->html .= $this->getChildNodeOption($row->{cn::NODES_RELATION_CHILD_NODE_ID_COL});
                            $this->html .= "</li>";
                        }
                        $this->html .= "</li>";
                        $this->html .= '</ul>';
                    }
                    $this->html .= '</ul>';
                }
            }
        }
        return $this->html;
    }

    public function getChildNodeOption($parentid){
        $childOption = '';
        $result = NodeRelation::with('nodes')->where(cn::NODES_RELATION_PARENT_NODE_ID_COL,$parentid)->get();
        if(isset($result) && !empty($result)){
            $childOption .= '<ul>';
            foreach($result as $row){
                $childOption .= "<li dataid='".$row->nodes->{cn::NODES_NODE_ID_COL}."'>".$row->nodes->{cn::NODES_NODEID_COL};
                $childOption .= "</li>";
            }
            $childOption .= '</ul>';
        }
        return $childOption;
    }

    public function nodes(){
        return $this->hasOne(Nodes::Class, cn::NODES_NODE_ID_COL,cn::NODES_RELATION_CHILD_NODE_ID_COL);
    }
}
