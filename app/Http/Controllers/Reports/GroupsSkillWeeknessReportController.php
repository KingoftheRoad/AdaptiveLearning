<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;
use View;
use App\Models\School;
use App\Models\Grades;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\User;
use App\Models\AttemptExams;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Session;

class GroupsSkillWeeknessReportController extends Controller
{
    /**
     * USE : Get skill weekness reports
     */
    public function getSkillWeeknessReport(Request $request){
        $Exams = Exam::whereNotNull(cn::EXAM_TABLE_GROUP_IDS_COL)->get();
        if(!isset($request->exam_id)){
            if(isset($request->filter) && !isset($request->exam_id)){
                return back()->withError(__('languages.please_select_test'));
            }
            return view('backend/reports/skills_weekness',compact('Exams'));
        }
        Session::forget('error_msg');
        $ExamList = Exam::whereIn('id',$request->exam_id)->get();
        if(isset($ExamList) && !empty($ExamList)){
            $arrayOfStudents = array_column($ExamList->toArray(),'student_ids');
            if(!empty($arrayOfStudents)){
                $studentArray = [];
                foreach($arrayOfStudents as $studentIds){
                    foreach(explode(',',$studentIds) as $sid){
                        if(!empty($sid) && !in_array($sid,$studentArray)){
                            $studentArray[] = $sid;
                        }
                    }
                }
            }
            $reports = [];
            if(!empty($studentArray)){
                foreach($studentArray as $student){
                    $studentData = User::find($student);
                    foreach($ExamList as $Exam){
                        $AttemptExamDetails = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$student)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$Exam->id)->first();
                        if(isset($AttemptExamDetails) && !empty($AttemptExamDetails)){
                            $totalCorrectAnswer = $AttemptExamDetails->total_correct_answers;
                            $totalNoOfQuestions = count(explode(',',$Exam->question_ids));
                            $reports[$student][$Exam->id]['exam_id'] = $Exam->id;
                            $reports[$student][$Exam->id]['level'] = $Exam->title;
                            $reports[$student][$Exam->id]['percentage'] = round((($totalCorrectAnswer * 100)/$totalNoOfQuestions),2);
                        }else{
                            $reports[$student][$Exam->id]['exam_id'] = $Exam->id;
                            $reports[$student][$Exam->id]['level'] = $Exam->title;
                            $reports[$student][$Exam->id]['percentage'] = 0;
                        }
                    }
                    $reports[$student]['levelname'] = ($this->getNodeLevelName($reports[$student])) ? $this->getNodeLevelName($reports[$student])['levelName'] : '';
                    $reports[$student]['weekness_exam_id'] = ($this->getNodeLevelName($reports[$student])) ? $this->getNodeLevelName($reports[$student])['examId'] : '';
                    $reports[$student]['studentname'] = $studentData->name ?? '';
                }
            }
        }
        return view('backend/reports/skills_weekness',compact('Exams','ExamList','reports'));
    }

    public function getNodeLevelName($data){
        $array = [];
        foreach($data as $value){
            if(isset($value['percentage']) && $value['percentage'] <= 75){
                $array['levelName'] = $value['level'];
                $array['examId'] = $value['exam_id'];
                break;
            }
        }
        return $array ?? '';
    }
}