<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableExamCreditPointRulesMapping extends Migration{
    
    public function up()
    {
        Schema::create(cn::EXAM_CREDIT_POINT_RULES_MAPPING_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL)->nullable();
            $table->enum(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL,['submission_on_time','credit_points_of_accuracy','credit_points_of_normalized_ability'])->nullable();
            $table->enum(cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL,['yes','no']);
            $table->enum(cn::EXAM_CREDIT_POINT_RULES_MAPPING_STATUS_COL,['active','inactive'])->default('active');
            $table->timestamps();
            $table->SoftDeletes();

            $table->foreign(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL)->references(cn::EXAM_TABLE_ID_COLS)->on(cn::EXAM_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down(){
        Schema::dropIfExists(cn::EXAM_CREDIT_POINT_RULES_MAPPING_TABLE);
    }
}
