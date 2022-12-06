<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CloneSchoolDataNextCurriculumYear;
use Log;

class CloneSchoolData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone:school-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command are used to after next year automatically copy school 
                            data like (Grade, Class, Teacher Class Assignment) and create next year via this task schedule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $CloneSchoolDataNextCurriculumYear;

    public function __construct()
    {
        parent::__construct();

        $this->CloneSchoolDataNextCurriculumYear = new CloneSchoolDataNextCurriculumYear;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$this->info('Hourly Update has been send successfully');
        Log::info('Schedule Run Start: Copy and Clone School Data');
        dispatch($this->CloneSchoolDataNextCurriculumYear)->delay(now()->addSeconds(1));
        Log::info('Schedule Run Successfully: Copy and Clone School Data');
    }
}