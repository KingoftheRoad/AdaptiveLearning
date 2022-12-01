<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Exceptions\CustomException;
use App\Models\GlobalConfiguration;
use App\Models\CurriculumYear;
use App\Constants\DbConstant as cn;

class GlobalConfigurationController extends Controller
{
    use Common;

    /**
     * USE : Get & Update Global Configurations
     */
    public function ConfigurationUpdate(Request $request){
        $postdata ='';
        $arrayFromTo = [];
        if ($request->isMethod('GET')){
            $GlobalConfiguration = GlobalConfiguration::all();
            $getCurriculumYear = CurriculumYear::all();
            $ConfigurationArray = [];
            if(isset($GlobalConfiguration) && !empty($GlobalConfiguration)){
                foreach($GlobalConfiguration as $configuration){
                    $ConfigurationArray[$configuration->key] = $configuration->value;
                }
            }
            return view('backend.settings.global_configuration',compact('ConfigurationArray','getCurriculumYear'));
        }

        // If Request Type is Patch and update the key bay config value
        if ($request->isMethod('PATCH')){
            foreach(config()->get('GlobalConfigurationKey') as $configKey){
                if(GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->exists()){
                    if(in_array($configKey,array('question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5'))){
                        $postdata = [
                            'from' => $request->{$configKey.'_from'},
                            'to'   => $request->{$configKey.'_to'},
                            'color' => $request->{$configKey.'_color'},
                        ];
                    }else{
                        $postdata = [
                            'from' => $request->{$configKey.'_from'},
                            'to'   => $request->{$configKey.'_to'},
                        ];
                    }
                    if(in_array($configKey,array('question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5','question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','struggling','beginning','approaching','proficient','advanced'))){
                        GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->Update([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($postdata)
                        ]);
                    }else{
                        GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->Update([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => $request->{$configKey}
                        ]);
                    }
                }else{
                    if(in_array($configKey,array('question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','struggling','beginning','approaching','proficient','advanced'))){
                        if(in_array($configKey,array('question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5','question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5'))){
                            $postdata = [
                                'from' => $request->{$configKey.'_from'},
                                'to'   => $request->{$configKey.'_to'},
                                'color' => $request->{$configKey.'_color'},
                            ];
                        }else{
                            $postdata = [
                                'from' => $request->{$configKey.'_from'},
                                'to'   => $request->{$configKey.'_to'},
                            ];
                        }

                        GlobalConfiguration::Create([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($postdata)
                        ]);
                    }else{
                        if(is_array($request->{$configKey})){
                            GlobalConfiguration::Create([
                                cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                                cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($request->{$configKey})
                            ]);
                        }else{
                            GlobalConfiguration::Create([
                                cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                                cn::GLOBAL_CONFIGURATION_VALUE_COL => $request->{$configKey}
                            ]);
                        }
                    }
                }
            }
        }
        return redirect('global-configuration')->with('success_msg', __('languages.global_configuration_updated_successfully'));
    }
}
