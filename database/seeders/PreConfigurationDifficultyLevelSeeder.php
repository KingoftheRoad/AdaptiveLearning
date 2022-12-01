<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Constants\DbConstant As cn;

class PreConfigurationDifficultyLevelSeeder extends Seeder
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
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => 'Level 1',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => '難度一',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => '#bef8ca',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => 1,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => -2.197224577336219,
            ],
            [
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => 'Level 2',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => '難度二',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => '#f7fda5',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => 2,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => -0.8472978603872037,
            ],
            [
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => 'Level 3',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => '難度三',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => '#ffb433',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => 3,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => 0.0,
            ],
            [
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => 'Level 4',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => '難度四',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => '#fd6dde',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => 4,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => 0.8472978603872034,
            ],
            [
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL => 'Level 5',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL => '難度五',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL => '#f77959',
                cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL => 5,
                cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL => 2.1972245773362196,
            ],
        ];

        // if(!empty($data)){
        //     foreach($data as $key => $value){
        //         PreConfigurationDiffiltyLevel::updateOrCreate($value);
        //     }
        // }

        foreach($data as $key => $value){
            $PreConfigurationDiffiltyLevel = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL, $value[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL])->first();
            if($PreConfigurationDiffiltyLevel){
                $PreConfigurationDiffiltyLevel->update($value); 
            }else{
                PreConfigurationDiffiltyLevel::create($value);
            }
        }

    }
}
