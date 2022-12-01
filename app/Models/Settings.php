<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = cn::SETTINGS_TABLE_NAME;
    
    public $fillable = [
        cn::SETTINGS_SITE_NAME_COL,
        cn::SETTINGS_SITE_URL_COL,
        cn::SETTINGS_EMAIL_COL,
        cn::SETTINGS_CONTACT_NUMBER_COL,
        cn::SETTINGS_FAV_ICON_COL,
        cn::SETTINGS_LOGO_IMAGE_COL,
        cn::SETTINGS_SMTP_DRIVER_COL,
        cn::SETTINGS_SMTP_HOST_COL,
        cn::SETTINGS_SMTP_PORT_COL,
        cn::SETTINGS_SMTP_USERNAME_COL,
        cn::SETTINGS_SMTP_EMAIL_COL,
        cn::SETTINGS_SMTP_PASSWORD_COL,
        cn::SETTINGS_SMTP_ENCRYPTION_COL
    ];

    public $timestamps = true;
}
