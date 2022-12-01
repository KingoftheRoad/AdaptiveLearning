<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Languages extends Model
{
    use SoftDeletes, HasFactory, Sortable;

    protected $table = cn::LANGUAGES_TABLE_NAME;

    public $fillable = [
        cn::LANGUAGES_NAME_COL,
        cn::LANGUAGES_CODE_COL
    ];

    public $sortable = [
        cn::LANGUAGES_NAME_COL,
        cn::LANGUAGES_CODE_COL
    ];

    public $timestamps = true;
}
