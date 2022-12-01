<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningsUnits;
use App\Constants\DbConstant as cn;

class LearningUnitsSeeder extends Seeder
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
                cn::LEARNING_UNITS_NAME_COL => 'Quadratic equations in one unknown',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Quadratic equations in one unknown',
                cn::LEARNING_UNITS_NAME_CH_COL => '一 元 二 次 方 程',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '01'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Functions and graphs',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Functions and graphs',
                cn::LEARNING_UNITS_NAME_CH_COL => '函 數 及 其 圖 像', 
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '02'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Exponential and logarithmic functions',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Exponential and logarithmic functions',
                cn::LEARNING_UNITS_NAME_CH_COL => '指 數 函 數 與 對 數 函 數',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '03'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'More about polynomials',
                cn::LEARNING_UNITS_NAME_EN_COL =>'More about polynomials',
                cn::LEARNING_UNITS_NAME_CH_COL => '更多關於多項式',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '04'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'More about equations',
                cn::LEARNING_UNITS_NAME_EN_COL =>'More about equations',
                cn::LEARNING_UNITS_NAME_CH_COL => '續多項式',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '05'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Variations',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Variations',
                cn::LEARNING_UNITS_NAME_CH_COL => '變 分',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '06'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Arithmetic and geometric sequences and their summations',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Arithmetic and geometric sequences and their summations',
                cn::LEARNING_UNITS_NAME_CH_COL => '等 差 數 列 與 等 比 數 列 及 其 求 和 法',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '07'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Inequalities and linear programming',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Inequalities and linear programming',
                cn::LEARNING_UNITS_NAME_CH_COL => '不 等 式 與 線 性 規 畫',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '08'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'More about graphs of functions',
                cn::LEARNING_UNITS_NAME_EN_COL =>'More about graphs of functions',
                cn::LEARNING_UNITS_NAME_CH_COL => '續 函 數 圖 像',
                cn::LEARNING_UNITS_STRANDID_COL => 1,
                cn::LEARNING_UNITS_CODE_COL => '09'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Basic properties of circles',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Basic properties of circles',
                cn::LEARNING_UNITS_NAME_CH_COL => '圓 的 基 本 性 質',
                cn::LEARNING_UNITS_STRANDID_COL => 2,
                cn::LEARNING_UNITS_CODE_COL => '10'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Loci',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Loci',
                cn::LEARNING_UNITS_NAME_CH_COL => '軌 跡',
                cn::LEARNING_UNITS_STRANDID_COL => 2,
                cn::LEARNING_UNITS_CODE_COL => '11'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Equations of straight lines and Equations of circles',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Equations of straight lines and Equations of circles',
                cn::LEARNING_UNITS_NAME_CH_COL => '直線方程與續 三 角 學',
                cn::LEARNING_UNITS_STRANDID_COL => 2,
                cn::LEARNING_UNITS_CODE_COL => '12'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'More about trigonometry',
                cn::LEARNING_UNITS_NAME_EN_COL =>'More about trigonometry',
                cn::LEARNING_UNITS_NAME_CH_COL => '續 三 角 學',
                cn::LEARNING_UNITS_STRANDID_COL => 2,
                cn::LEARNING_UNITS_CODE_COL => '13'],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Permutations and combinations',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Permutations and combinations',
                cn::LEARNING_UNITS_NAME_CH_COL => '排列與組合',
                cn::LEARNING_UNITS_STRANDID_COL => 3,
                cn::LEARNING_UNITS_CODE_COL => '14'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'More about probability',
                cn::LEARNING_UNITS_NAME_EN_COL =>'More about probability',
                cn::LEARNING_UNITS_NAME_CH_COL => '續概率',
                cn::LEARNING_UNITS_STRANDID_COL => 3,
                cn::LEARNING_UNITS_CODE_COL => '15'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Measures of dispersion',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Measures of dispersion',
                cn::LEARNING_UNITS_NAME_CH_COL => '離差的度量',
                cn::LEARNING_UNITS_STRANDID_COL => 3,
                cn::LEARNING_UNITS_CODE_COL => '16'
            ],
            [
                cn::LEARNING_UNITS_NAME_COL => 'Uses and abuses of statistics',
                cn::LEARNING_UNITS_NAME_EN_COL =>'Uses and abuses of statistics',
                cn::LEARNING_UNITS_NAME_CH_COL => '統計的應用及誤用',
                cn::LEARNING_UNITS_STRANDID_COL => 3,
                cn::LEARNING_UNITS_CODE_COL => '17'
            ],
        ];

        if(!empty($data)){
            foreach($data as $key => $value){
                $checkExists = LearningsUnits::where([cn::LEARNING_UNITS_NAME_COL => $value[cn::LEARNING_UNITS_NAME_COL]])->first();
                if(!isset($checkExists) && empty($checkExists)){
                    LearningsUnits::create($value);
                }else{
                    LearningsUnits::find($checkExists->id)->update($value);
                }
            }
        }
    }
}
