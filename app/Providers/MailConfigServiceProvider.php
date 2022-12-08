<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Settings;
use Artisan;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // if(Schema::hasTable(cn::SETTINGS_TABLE_NAME)){
        //     $emailServices = Settings::latest()->first();
        //     if ($emailServices && !empty($emailServices->smtp_driver) && !empty($emailServices->smtp_host) && !empty($emailServices->smtp_port) && !empty($emailServices->smtp_username)
        //     && !empty($emailServices->smtp_passowrd) && $emailServices->smtp_encryption) {
        //         $config = array(
        //             'driver'     => $emailServices->smtp_driver,
        //             'host'       => $emailServices->smtp_host,
        //             'port'       => $emailServices->smtp_port,
        //             'username'   => $emailServices->smtp_email,
        //             'password'   => $emailServices->smtp_passowrd,
        //             'encryption' => $emailServices->smtp_encryption,
        //             'from'       => array('address' => $emailServices->smtp_email, 'name' => $emailServices->smtp_username),
        //             'sendmail'   => '/usr/sbin/sendmail -bs',
        //             'pretend'    => false,
        //         );
        //         \Config::set('mail', $config);
        //     }
        // }
    }
}
