<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant as cn;
use Illuminate\Support\Facades\Mail;

class WelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseFormat;

    protected $details;
    protected $send_mail;

    public $timeout = 7200; // 2 Hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public function __construct($details)
    // {
    //     $this->details = $details;
    // }

    public function __construct($send_mail)
    {
        $this->send_mail = $send_mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // public function handle()
    // {
    //     Log::info('Job start - Welcome Mail Job');
    //     $dataSet = [
    //         cn::USERS_EMAIL_COL => $this->details[cn::USERS_EMAIL_COL],
    //         'userdata' => $this->details,
    //         'login_url' => config()->get('app.url').'login'
    //     ];
    //     $this->sendMails("email.welcome_mail", $dataSet, $dataSet[cn::USERS_EMAIL_COL], 'Welcome to Adaptive Learning');
    //     Log::info('Job end - Welcome Mail Job');
    // }

    public function handle()
    {
        $email = new WelcomeEmailJob('developer@gmail.com');        
        Mail::to($this->send_mail)->send($email);
    }
}
