<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
class OtherRoles extends Model
{
    use SoftDeletes, HasFactory,Sortable;
    protected $table = cn::OTHER_ROLE_TABLE_NAME;

    public $fillable = [
        cn::OTHER_ROLE_ID_COL,
        cn::OTHER_ROLE_NAME_COL,
        cn::OTHER_ROLE_ACTIVE_STATUS_COL
     ];
     public $timestamps = true;

}
