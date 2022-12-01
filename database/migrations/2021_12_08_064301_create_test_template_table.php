<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTestTemplateTable extends Migration
{
    public function up()
    {
        Schema::create(cn::TEST_TEMPLATE_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::TEST_TEMPLATE_ID_COL);
            $table->enum(cn::TEST_TEMPLATE_TYPE,[1,2])->comment('1 = Self-Learning, 2 = Excercise & Test')->nullable();
            $table->enum(cn::TEST_TEMPLATE_DIFFICULTY_LEVEL_COL,[1,2,3,4])->nullable();
            $table->longText(cn::TEST_TEMPLATE_QUESTION_IDS_COL)->nullable();
            $table->unsignedBigInteger(cn::TEST_TEMPLATE_CREATED_BY)->nullable();
            $table->enum(cn::TEST_TEMPLATE_STATUS,['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Set foreign key "user table"
            $table->foreign(cn::TEST_TEMPLATE_CREATED_BY)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    public function down()
    {
        Schema::dropIfExists(cn::TEST_TEMPLATE_TABLE_NAME);
    }
}
