<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Constants\DbConstant as cn;
use App\Models\OtherRoles;
class other_role_data_seeder extends Seeder
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
                cn::OTHER_ROLE_ID_COL =>"1",
                cn:: OTHER_ROLE_NAME_COL => "Musician",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"2",
                cn:: OTHER_ROLE_NAME_COL => "Librarian",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"3",
                cn:: OTHER_ROLE_NAME_COL => "Dancer",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"4",
                cn:: OTHER_ROLE_NAME_COL => "singer",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"5",
                cn:: OTHER_ROLE_NAME_COL => "Sports",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"6",
                cn:: OTHER_ROLE_NAME_COL => "Drawing",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"7",
                cn:: OTHER_ROLE_NAME_COL => "Anchoring",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"8",
                cn:: OTHER_ROLE_NAME_COL => "Leadership",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"9",
                cn:: OTHER_ROLE_NAME_COL => "Mentor",
            ],
            [
                cn::OTHER_ROLE_ID_COL =>"10",
                cn:: OTHER_ROLE_NAME_COL => "Planner",
            ],
           
        ];

        foreach ($data as $key => $value) {
            OtherRoles::create($value);
        }
     }
 }
