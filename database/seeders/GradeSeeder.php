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
            ],
            [
                cn::GRADES_NAME_COL => '5',
                cn::GRADES_CODE_COL => 5,
            ],
            [
                cn::GRADES_NAME_COL => '6',
                cn::GRADES_CODE_COL => 6,
            ],
            [
                cn::GRADES_NAME_COL => '7',
                cn::GRADES_CODE_COL => 7
            ],
            [
                cn::GRADES_NAME_COL => '8',
                cn::GRADES_CODE_COL => 8
            ],
            [
                cn::GRADES_NAME_COL => '9',
                cn::GRADES_CODE_COL => 9,
            ],
            [
                cn::GRADES_NAME_COL => '10',
                cn::GRADES_CODE_COL => 10,
            ],
            [
                cn::GRADES_NAME_COL => '11',
                cn::GRADES_CODE_COL => 11
            ],
            [
                cn::GRADES_NAME_COL => '12',
                cn::GRADES_CODE_COL => 12
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
