<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class Answer extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = cn::ANSWER_TABLE_NAME;

    public $fillable = [
        cn::ANSWER_ID_COL,
        cn::ANSWER_QUESTION_ID_COL,
        cn::ANSWER_ANSWER1_EN_COL,
        cn::ANSWER_ANSWER2_EN_COL,
        cn::ANSWER_ANSWER3_EN_COL,
        cn::ANSWER_ANSWER4_EN_COL,
        cn::ANSWER_HINT_ANSWER1_EN_COL,
        cn::ANSWER_HINT_ANSWER2_EN_COL,
        cn::ANSWER_HINT_ANSWER3_EN_COL,
        cn::ANSWER_HINT_ANSWER4_EN_COL,
        cn::ANSWER_ANSWER1_CH_COL,
        cn::ANSWER_ANSWER2_CH_COL,
        cn::ANSWER_ANSWER3_CH_COL,
        cn::ANSWER_ANSWER4_CH_COL,
        cn::ANSWER_HINT_ANSWER1_CH_COL,
        cn::ANSWER_HINT_ANSWER2_CH_COL,
        cn::ANSWER_HINT_ANSWER3_CH_COL,
        cn::ANSWER_HINT_ANSWER4_CH_COL,
        cn::ANSWER_CORRECT_ANSWER_EN_COL,
        cn::ANSWER_CORRECT_ANSWER_CH_COL,
        cn::ANSWER_CREATED_AT_COL,
        cn::ANSWER_UPDATED_AT_COL,
        cn::ANSWER_DELETED_AT_COL,
        cn::ANSWER_NODE_HINT_ANSWER1_EN_COL,
        cn::ANSWER_NODE_HINT_ANSWER2_EN_COL,
        cn::ANSWER_NODE_HINT_ANSWER3_EN_COL,
        cn::ANSWER_NODE_HINT_ANSWER4_EN_COL,
        cn::ANSWER_NODE_HINT_ANSWER1_CH_COL,
        cn::ANSWER_NODE_HINT_ANSWER2_CH_COL,
        cn::ANSWER_NODE_HINT_ANSWER3_CH_COL,
        cn::ANSWER_NODE_HINT_ANSWER4_CH_COL,
        cn::ANSWER1_NODE_RELATION_ID_EN_COL,
        cn::ANSWER2_NODE_RELATION_ID_EN_COL,
        cn::ANSWER3_NODE_RELATION_ID_EN_COL,
        cn::ANSWER4_NODE_RELATION_ID_EN_COL,
        cn::ANSWER1_NODE_RELATION_ID_CH_COL,
        cn::ANSWER2_NODE_RELATION_ID_CH_COL,
        cn::ANSWER3_NODE_RELATION_ID_CH_COL,
        cn::ANSWER4_NODE_RELATION_ID_CH_COL,
    ];

    public $timestamps = true;
}
