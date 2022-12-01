<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Constants\DbConstant as cn;
Use App\Models\Languages;


class LanguagesTableSeeder extends Seeder
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
                        cn:: LANGUAGES_NAME_COL => 'English',
                        cn::LANGUAGES_CODE_COL => 'en',
                    ],
                    [
                        cn:: LANGUAGES_NAME_COL => '中文',
                        cn::LANGUAGES_CODE_COL => 'ch',
                    ],
                ];
        foreach($data as $key => $value){
            $language = Languages::where(cn::LANGUAGES_CODE_COL, $value[cn::LANGUAGES_CODE_COL])->first();
            if($language){
                $language->update($value); 
            }else{
                Languages::create($value);
            }
        }
    }
}
