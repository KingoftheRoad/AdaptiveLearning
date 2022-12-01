<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Artisan;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::TEACHER_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::TEACHER_ID_COL);
            $table->string(cn::TEACHER_NAME_COL)->nullable();
            $table->string(cn::TEACHER_EMAIL_COL)->unique()->nullable();
            $table->bigInteger(cn::TEACHER_MOBILE_NO_COL)->nullable();
            $table->String(cn::TEACHER_ADDRESS_COL)->nullable();
            $table->Text(cn::TEACHER_GENDER_COL)->nullable();
            $table->date(cn::TEACHER_DATE_OF_BIRTH_COL)->nullable();
            $table->bigInteger(cn::TEACHER_SCHOOL_ID_COL)->nullable();
            $table->enum(cn::TEACHER_STATUS_COL,['active','inactive'])->default('active'); 
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
        Schema::dropIfExists('teacher');
    }
}
