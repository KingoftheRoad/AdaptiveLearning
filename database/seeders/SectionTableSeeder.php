<?php

namespace Database\Seeders;
use App\Constants\DbConstant as cn;
use App\Models\Section;


use Illuminate\Database\Seeder;

class SectionTableSeeder extends Seeder
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
                cn::SECTION_SECTION_NAME_COL =>"A",
                cn:: SECTION_ACTIVE_STATUS_COL => 1,
                cn::SECTION_SCHOOL_ID_COL => 1,
                cn::SECTION_CREATED_BY_COL =>1,
                cn::SECTION_UPDATED_BY_COL=>1

            ],
            [
                cn::SECTION_SECTION_NAME_COL =>"B",
                cn:: SECTION_ACTIVE_STATUS_COL => 1,
                cn::SECTION_SCHOOL_ID_COL => 1,
                cn::SECTION_CREATED_BY_COL =>1,
                cn::SECTION_UPDATED_BY_COL=>1

            ],
            [
                cn::SECTION_SECTION_NAME_COL =>"C",
                cn:: SECTION_ACTIVE_STATUS_COL => 1,
                cn::SECTION_SCHOOL_ID_COL => 1,
                cn::SECTION_CREATED_BY_COL =>1,
                cn::SECTION_UPDATED_BY_COL=>1

            ],
            [
                cn::SECTION_SECTION_NAME_COL =>"D",
                cn:: SECTION_ACTIVE_STATUS_COL => 1,
                cn::SECTION_SCHOOL_ID_COL => 1,
                cn::SECTION_CREATED_BY_COL =>1,
                cn::SECTION_UPDATED_BY_COL=>1

            ],
        ];
        foreach ($data as $key => $value) {
            Section::create($value);
        }
    }
}
