<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestion extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    
        return [
           'question_code' => 'required',
           'grade_id' => 'required',
           
           'question_en' => 'required',
           'question_ch' => 'required',
           'answer1_en' => 'required',
           'answer2_en' => 'required',
           'answer3_en' => 'required',
           'answer4_en' => 'required',
        //    'hint_answer1_en' => 'required',
        //    'hint_answer2_en' => 'required',
        //    'hint_answer3_en' => 'required',
        //    'hint_answer4_en' => 'required',
           'answer1_ch' => 'required',
           'answer2_ch' => 'required',
           'answer3_ch' => 'required',
           'answer4_ch' => 'required',
        //    'hint_answer1_ch' => 'required',
        //    'hint_answer2_ch' => 'required',
        //    'hint_answer3_ch' => 'required',
        //    'hint_answer4_ch' => 'required',
           'question_type' => 'required',
           'dificulaty_level' => 'required',
           'correct_answer_en' => 'required',
           'correct_answer_ch' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'question_code.required' => "Enter Question Code",
            'grade_id.required' => "Enter Grade",
            'question_en.required' => "Enter English Question",
            'question_ch.required' => "Enter Chinese Question",
            'answer1_en.required' => "Enter First Option of English",
            'answer2_en.required' => "Enter Second Option of English",
            'answer3_en.required' => "Enter Third Option of English",
            'answer4_en.required' => "Enter Forth Option of English",
            'hint_answer1_en.required' => "Enter Hints Of First English Option",
            'hint_answer2_en.required' => "Enter Hints Of Second English Option",
            'hint_answer3_en.required' => "Enter Hints Of Third English Option",
            'hint_answer4_en.required' => "Enter Hints Of Fourth English Option",
            'answer1_ch.required' => "Enter First Option of Chinese",
            'answer2_ch.required' => "Enter Second Option of Chinese",
            'answer3_ch.required' => "Enter Third Option of Chinese",
            'answer4_ch.required' => "Enter Forth Option of Chinese",
            'hint_answer1_ch.required' => "Enter Hints Of First Chinese Option",
            'hint_answer2_ch.required' => "Enter Hints Of Second Chinese Option",
            'hint_answer3_ch.required' => "Enter Hints Of Third Chinese Option",
            'hint_answer4_ch.required' => "Enter Hints Of Fourth Chinese Option",
            'question_type.required' => "Enter Question Type",
            'dificulaty_level.required' => "Enter Difficult Level",
            'correct_answer_en.required' => "Please select Correct Answer",
            'correct_answer_ch.required' => "Please select Correct Answer"
        ];
    }
}
