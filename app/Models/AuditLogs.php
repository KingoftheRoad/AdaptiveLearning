<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLogs extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = cn::AUDIT_LOGS_TABLE_NAME;
    
    protected $fillable = [
        cn::AUDIT_LOGS_ROLE_TYPE_COL,
        cn::AUDIT_LOGS_USER_ID_COL,
        cn::AUDIT_LOGS_NAME_COL,
        cn::AUDIT_LOGS_PAYLOAD_COL,
        cn::AUDIT_LOGS_TABLE_NAME_COL,
        cn::AUDIT_LOGS_CHILD_TABLE_NAME_COL,
        cn::AUDIT_LOGS_PAGE_NAME_COL,
        cn::AUDIT_LOGS_IP_ADDRESS_COL,
        cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL
    ];
}