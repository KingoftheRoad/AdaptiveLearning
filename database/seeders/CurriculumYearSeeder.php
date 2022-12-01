<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumYear;
use App\Constants\DbConstant as cn;

class CurriculumYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Starting Year
        $startYear = 2000;

        // Ending Year
        $endYear = 5000;

        for($i = $startYear; $i <= $endYear; $i++){
            $Year = $i.'-'.date('y',strtotime('01/01/'.($i+1)));
            if(CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$Year)->exists()){
                CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$Year)->Update([cn::CURRICULUM_YEAR_YEAR_COL => $Year]);
            }else{
                CurriculumYear::Create([cn::CURRICULUM_YEAR_YEAR_COL => $Year]);
            }
        }
    }
}
