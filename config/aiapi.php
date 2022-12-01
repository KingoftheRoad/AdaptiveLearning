<?php

return [
    //'host' => env('AIAPI_HOST', 'http://alp4.hkahss.edu.hk/'),
    'host' => env('AIAPI_HOST', 'http://alpapi.hkahss.edu.hk/'),
    'api' => [
        'estimate_student_competence' => [
            'uri' => 'estimate_student_competence',
            'method' => 'POST'
        ],
        'estimate_question_difficulty' => [
            'uri' => 'estimate_question_difficulty',
            'method' => 'POST'
        ],
        'PlotGraph' => [
            'uri' => 'PlotGraph',
            'method' => 'POST'
        ],
        'Plot_Analyze_Question' => [
            'uri' => 'Plot_Analyze_Question',
            'method' => 'POST'
        ],
        'Plot_Analyze_Student' => [
            'uri' => 'Plot_Analyze_Student',
            'method' => 'POST'
        ],
        'Plot_Analyze_Data' => [
            'uri' => 'Plot_Analyze_Data',
            'method' => 'POST'
        ],
        'SkewNorm_Fit' => [
            'uri' => 'SkewNorm_Fit',
            'method' => 'POST'
        ],
        'Plot_Analyze_Test_Difficulty' => [
            'uri' => 'Plot_Analyze_Test_Difficulty',
            'method' => 'POST'
        ],
        'Plot_Analyze_My_Class_Ability' => [
            'uri' => 'Plot_Analyze_My_Class_Ability',
            'method' => 'POST'
        ],
        'Plot_Analyze_My_School_Ability' => [
            'uri' => 'Plot_Analyze_My_School_Ability',
            'method' => 'POST'
        ],
        'Plot_Analyze_All_Schools_Ability' => [
            'uri' => 'Plot_Analyze_All_Schools_Ability',
            'method' => 'POST'
        ],
        'Assign_Questions_Manually' =>[
            'uri' => 'Assign_Questions_Manually',
            'method' => 'POST'
        ],
        'Assign_Questions_AutoMode' => [
            'uri' => 'Assign_Questions',
            'method' => 'POST'
        ],
        'Create_Peer_Groups' => [
            'uri' => 'Create_Peer_Groups',
            'method' => 'POST'
        ],
        'Assign_Questions_Manually_To_Learning_Units' => [
            'uri' => 'Assign_Questions_Manually_To_Learning_Units',
            'method' => 'POST'
        ],
        'Assign_Questions_To_Learning_Units' => [
            'uri' => 'Assign_Questions_To_Learning_Units',
            'method' => 'POST'
        ],
        'Real_Time_Assign_Question_N_Estimate_Ability' => [
            'uri' => 'Real_Time_Assign_Question_N_Estimate_Ability',
            'method' => 'POST'
        ]
    ]
];
