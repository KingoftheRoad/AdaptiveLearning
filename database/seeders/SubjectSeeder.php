<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subjects;
use App\Constants\DbConstant as cn;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [[cn::SUBJECTS_NAME_COL => 'Mathematics',cn::SUBJECTS_CODE_COL => 'MA']];
        if(!empty($data)){
            foreach($data as $key => $value){
                $checkExists = Subjects::where([cn::SUBJECTS_NAME_COL => $value[cn::SUBJECTS_NAME_COL]])->first();
                if(!isset($checkExists) && empty($checkExists)){
                    Subjects::create($value);
                }else{
                    Subjects::where([cn::SUBJECTS_ID_COL => $checkExists[cn::SUBJECTS_ID_COL]])->update($value);
                }
            }
        }
    }
}
