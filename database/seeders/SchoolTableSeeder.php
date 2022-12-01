<?php

namespace Database\Seeders;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolTableSeeder extends Seeder
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
                cn::SCHOOL_SCHOOL_NAME_COL =>"Ganpat University",
                cn::SCHOOL_SCHOOL_CODE_COL =>"1",
                cn:: SCHOOL_SCHOOL_ADDRESS => "Thaltej",
                cn::SCHOOL_SCHOOL_CITY => "Ahemdabad",
                cn::SCHOOL_SCHOOL_STATUS => "active",
            ],
            [
                cn::SCHOOL_SCHOOL_NAME_COL =>"Parul University",
                cn::SCHOOL_SCHOOL_CODE_COL =>"2",
                cn:: SCHOOL_SCHOOL_ADDRESS => "Gota",
                cn::SCHOOL_SCHOOL_CITY => "Ahemdabad",
                cn::SCHOOL_SCHOOL_STATUS => "active",

            ],
            [
                cn::SCHOOL_SCHOOL_NAME_COL =>"RK University",
                cn::SCHOOL_SCHOOL_CODE_COL =>"3",
                cn:: SCHOOL_SCHOOL_ADDRESS => "Prahlad Nagar",
                cn::SCHOOL_SCHOOL_CITY => "Ahemdabad",
                cn::SCHOOL_SCHOOL_STATUS => "active",

            ],
            [
                cn::SCHOOL_SCHOOL_NAME_COL =>"Bhavnagar University",
                cn::SCHOOL_SCHOOL_CODE_COL =>"4",
                cn:: SCHOOL_SCHOOL_ADDRESS => "Vidhya Nagar",
                cn::SCHOOL_SCHOOL_CITY => "Bhavnagar",
                cn::SCHOOL_SCHOOL_STATUS => "active",

            ],
        ];
        foreach ($data as $key => $value) {
            School::create($value);
        }
    }
}
