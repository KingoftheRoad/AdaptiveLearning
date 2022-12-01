<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;


class CreateTableClassPromotionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CLASS_PROMOTION_HISTORY_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::CLASS_PROMOTION_HISTORY_ID_COL);
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL)->references(cn::GRADE_CLASS_MAPPING_ID_COL)->on(cn::GRADE_CLASS_MAPPING_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::CLASS_PROMOTION_HISTORY_TABLE_NAME);
    }
}
