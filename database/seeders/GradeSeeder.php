<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grades;
use App\Constants\DbConstant as cn;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                cn::GRADES_NAME_COL => '1',
                cn::GRADES_CODE_COL => 1,
            ],
            [
                cn::GRADES_NAME_COL => '2',
                cn::GRADES_CODE_COL => 2,
            ],
            [
                cn::GRADES_NAME_COL => '3',
                cn::GRADES_CODE_COL => 3
            ],
            [
                cn::GRADES_NAME_COL => '4',
                cn::GRADES_CODE_COL => 4
            ]
        ];

        if(!empty($data)){
            foreach($data as $key => $value){
                $checkExists = Grades::where([cn::GRADES_NAME_COL => $value[cn::GRADES_NAME_COL]])->first();
                if(!isset($checkExists) && empty($checkExists)){
                    Grades::create($value);
                }
            }
        }
    }
}
