<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateGradeMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GRADES_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::GRADES_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::GRADES_MAPPING_SCHOOL_ID_COL);
            $table->unsignedBigInteger(cn::GRADES_MAPPING_GRADE_ID_COL);
            $table->enum(cn::GRADES_MAPPING_STATUS_COL,['active','inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

              // foreign key of school and grade table
            $table->foreign(cn::GRADES_MAPPING_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::GRADES_MAPPING_GRADE_ID_COL)->references(cn::GRADES_ID_COL)->on(cn::GRADES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GRADES_MAPPING_TABLE_NAME);
    }
}
