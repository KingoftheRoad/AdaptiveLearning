<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableStudentGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::STUDENT_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->engine = cn::DB_ENGINE_NAME;
            $table->bigIncrements(cn::STUDENT_GROUP_ID_COL);
            $table->string(cn::STUDENT_GROUP_NAME_COL,100);
            $table->longText(cn::STUDENT_GROUP_STUDENT_ID_COL);
            $table->boolean(cn::STUDENT_GROUP_STATUS_COL)->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::STUDENT_GROUP_TABLE_NAME);
    }
}
