<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Constants\DbConstant as cn;

class ClassTableSeeder extends Seeder
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
                        cn::CLASS_CLASS_NAME_COL =>"A",
                        cn:: CLASS_ACTIVE_STATUS_COL => 1,
                        cn::CLASS_SCHOOL_ID_COL => 1,
                    ],
                    [
                        cn::CLASS_CLASS_NAME_COL =>"B",
                        cn:: CLASS_ACTIVE_STATUS_COL => 1,
                        cn::CLASS_SCHOOL_ID_COL => 1,
                    ],
                    [
                        cn::CLASS_CLASS_NAME_COL =>"C",
                        cn:: CLASS_ACTIVE_STATUS_COL => 1,
                        cn::CLASS_SCHOOL_ID_COL => 1,
                    ],
                    [
                        cn::CLASS_CLASS_NAME_COL =>"D",
                        cn:: CLASS_ACTIVE_STATUS_COL => 1,
                        cn::CLASS_SCHOOL_ID_COL => 1,
                    ]
                ];

            foreach ($data as $key => $value) {
                ClassModel::create($value);
            }
    }
}
