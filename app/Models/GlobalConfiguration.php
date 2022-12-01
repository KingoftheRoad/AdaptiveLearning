<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;

class GlobalConfiguration extends Model
{
    use HasFactory;

    protected $table = cn::GLOBAL_CONFIGURATION_TABLE_NAME;
    
    public $fillable = [
        cn::GLOBAL_CONFIGURATION_KEY_COL,
        cn::GLOBAL_CONFIGURATION_VALUE_COL
    ];

    public $timestamps = false;
}
