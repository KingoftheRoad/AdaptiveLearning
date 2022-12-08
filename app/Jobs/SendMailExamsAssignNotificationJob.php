<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Traits\ResponseFormat;
use Log;
use App\Models\User;

class SendMailExamsAssignNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseFormat;

    protected $details;
    public $timeout = 7200; // 2 Hours
    

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
    */
    public function handle(){
        ini_set('memory_limit', '1024M');
        // if(isset($this->details['emails']) && !empty($this->details['emails'])){
        //     foreach($this->details['emails'] as $ReceiverEmail){
        //         $userData = User::where('email',$ReceiverEmail)->first();
        //         $MailData = array(
        //             'login_url' => env('APP_URL'),
        //             'userdata' => $userData
        //         );
        //         Log::info('Job start - Send-Email-Notification-Assigned-Exams-Students');
        //         $this->sendMails("email.send_email_notification_assigned_exams_student", $MailData, $ReceiverEmail, 'ALP-School-Management: Assigned new exams.');
        //         Log::info('Job End - Send-Email-Notification-Assigned-Exams-Students');
        //     }
        // }
    }
}
