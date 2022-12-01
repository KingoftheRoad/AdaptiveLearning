<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Models\Question;

class StudentPerformanceReports extends Controller
{
    use Common;
    
    public function getStudentPerformanceResults(Request $request){
        try {
            if($this->isSchoolLogin()){
                $currentLoggedSchoolId = $this->isSchoolLogin();
                $ExamList = Exam::where('is_group_test',0)->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->where('status','publish')->get();
            }else{
                $ExamList = Exam::where('is_group_test',0)->where('status','publish')->get();
            }
            
            if(isset($request->filter)){
                if(isset($request->school_id)){
                    $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->details_report_exam_id)->whereRaw("find_in_set($request->school_id,school_id)")->first();
                }else if($this->isSchoolLogin()){
                    $currentLoggedSchoolId = $this->isSchoolLogin();
                    $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->details_report_exam_id)->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->first();
                }else{
                    $ExamData = Exam::find($request->details_report_exam_id);
                }
            }
            if(isset($request->details_report_exam_id) && !empty($request->details_report_exam_id)){
                $examId = $request->details_report_exam_id;
                if(isset($request->school_id)){
                    $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->details_report_exam_id)->whereRaw("find_in_set($request->school_id,school_id)")->first();
                }else if($this->isSchoolLogin()){
                    $currentLoggedSchoolId = $this->isSchoolLogin();
                    $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->details_report_exam_id)->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->first();
                }else{
                    $ExamData = Exam::find($request->details_report_exam_id);
                }
                if(isset($ExamData)){
                    $Questions = Question::with('answers')->whereIn('id',explode(',',$ExamData->question_ids))->get();
                }
                $percentageOfAnswer = $this->getPercentageOfSelectedAnswer($request, $examId);
                return view('backend.reports.exam_student_performance',compact('Questions','percentageOfAnswer','ExamData','ExamList','GroupTest'));
            }else{
                return back()->withError('Please Select Exam');
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getPercentageOfSelectedAnswer($request, $examId){
        $data = [];
        if(isset($request->school_id)){
            $ExamDetails = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->whereRaw("find_in_set($request->school_id,school_id)")->first();
        }else if($this->isSchoolLogin()){
            $currentLoggedSchoolId = $this->isSchoolLogin();
            $ExamDetails = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->first();
        }else{
            $ExamDetails = Exam::find($examId);
        }
        //$ExamDetails = Exam::find($examId);
        if(isset($ExamDetails)){
            $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamDetails->question_ids))->get();
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->get();
            $noOfStudentAttemptExam = count($AttemptExamData) ?? 0;
            if(!empty($Questions)){
                foreach($Questions as $queKey => $question){
                    $data[$question->id][1] = 0;
                    $data[$question->id][2] = 0;
                    $data[$question->id][3] = 0;
                    $data[$question->id][4] = 0;
                    if(!empty($question->answers)){
                        for($i=1; $i <= 4; $i++){
                            if(!empty($AttemptExamData)){
                                foreach($AttemptExamData as $attempExamKey => $attemptValue){
                                    foreach(json_decode($attemptValue['question_answers']) as $answersData){
                                        if(($answersData->question_id ==  $question->id) && ($answersData->answer == $i)){
                                            $data[$question->id][$answersData->answer] = ($data[$question->id][$answersData->answer] + 1);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $percentage = [];
        if(!empty($data)){
            foreach($data as $quesKey => $questionArray){
                foreach($questionArray as $answerKey => $answerData){
                    if($noOfStudentAttemptExam){
                        $percentage[$quesKey][$answerKey] = round(((100 * $answerData) / $noOfStudentAttemptExam), 2);
                    }else{
                        $percentage[$quesKey][$answerKey] = 0;
                    }
                }
            }
        }
        return $percentage;
    }
}
