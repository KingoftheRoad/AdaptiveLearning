<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Strands;
use App\Constants\DbConstant as cn;

class StrandsSeeder extends Seeder
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
                cn::STRANDS_NAME_COL => 'Number and Algebra Strand',
                cn::STRANDS_NAME_EN_COL => 'Number and Algebra Strand',
                cn::STRANDS_NAME_CH_COL => '數與代數鏈',
                cn::STRANDS_CODE_COL => 'NA'
            ],
            [
                cn::STRANDS_NAME_COL => 'Measures, Shape and Space Strand',
                cn::STRANDS_NAME_EN_COL => 'Measures, Shape and Space Strand',
                cn::STRANDS_NAME_CH_COL => '措施，形狀和空間鏈',
                cn::STRANDS_CODE_COL => 'MS'
            ],
            [
                cn::STRANDS_NAME_COL => 'Data Handling Strand',
                cn::STRANDS_NAME_EN_COL => 'Data Handling Strand',
                cn::STRANDS_NAME_CH_COL => '數據處理鏈',
                cn::STRANDS_CODE_COL => 'DH'
            ]
        ];

        if(!empty($data)){
            foreach($data as $key => $value){
                $checkExists = Strands::where([cn::STRANDS_NAME_COL => $value[cn::STRANDS_NAME_COL]])->first();
                if(!isset($checkExists) && empty($checkExists)){
                    Strands::create($value);
                }else{
                    Strands::find($checkExists->id)->update($value);
                }
            }
        }
    }
}
