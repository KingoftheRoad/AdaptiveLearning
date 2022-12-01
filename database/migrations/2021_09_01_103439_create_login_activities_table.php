<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateLoginActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists(cn::LOGIN_ACTIVITIES_TABLE_NAME);
        
        Schema::create(cn::LOGIN_ACTIVITIES_TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(cn::LOGIN_ACTIVITIES_ID_COL);
            $table->enum(cn::LOGIN_ACTIVITIES_TYPE_COL,['login','logout']);
            $table->integer(cn::LOGIN_ACTIVITIES_USER_ID_COL)->unsigned();            
            $table->longtext(cn::LOGIN_ACTIVITIES_USER_AGENT_ID_COL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::LOGIN_ACTIVITIES_TABLE_NAME);
    }
}
