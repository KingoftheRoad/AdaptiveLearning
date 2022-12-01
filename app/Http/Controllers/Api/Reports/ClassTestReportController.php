<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\School;
use App\Models\Grades;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\User;
use App\Models\AttemptExams;
use App\Models\Question;
use View;
use App\Models\Answer;
use App\Models\StudentGroup;

class ClassTestReportController extends Controller
{
    use Common;

    /**
     * USE : Class test report, correct and incorrect (table version, correct/wrong)
     */
    public function ClassTestResultCorrectIncorrectAnswers(Request $request){
        try {
            if(!isset($request->exam_id)){
                return $this->sendError('Exam id field is required', 422);
            }
            $ResultList = [];
            $SchoolList = School::all();
            $GradeList = Grades::all();
            $ExamList = Exam::all();
            $QuestionSkills = [];
            $studentCount = 0;
            $totalQuestions = 0;
            $students = [];
            $answerPercentage = [];
            $CountStudentAnswer = [];

            $ExamData = Exam::find($request->exam_id);
            if(empty($ExamData)){
                return $this->sendError('Exam details not found', 422);
            }
            if(!empty($ExamData)){
                $ResultList['examDetails'] = $ExamData->toArray();
                if(!empty($ExamData->student_ids)){
                    $studentIds = explode(',',$ExamData->student_ids);
                    foreach($studentIds as $studentKey => $studentId){
                        // Get correct answer detail
                        $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id',$request->exam_id)->first();
                        if(isset($AttemptExamData) && !empty($AttemptExamData)){
                            $StudentDetail = User::find($studentId);
                            $students[$studentKey]['student_id'] = $StudentDetail->id;
                            $students[$studentKey]['student_grade'] = $StudentDetail->grade_id ?? 0;
                            $students[$studentKey]['student_number'] = $StudentDetail->id;
                            $students[$studentKey]['student_name'] = $StudentDetail->name;
                            $students[$studentKey]['student_status'] = 'Active';
                            $students[$studentKey]['countStudent'] = (++$studentCount);
                            $students[$studentKey]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                            $students[$studentKey]['exam_status'] = (($AttemptExamData->status) && $AttemptExamData->status == 1) ? 'Complete' : 'Pending';

                            
                            if(!empty($ExamData->question_ids)){
                                $questionIds = explode(',',$ExamData->question_ids);
                                $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                                if(isset($QuestionList) && !empty($QuestionList)){
                                    $totalQuestions = count($QuestionList);
                                    $students[$studentKey]['countQuestions'] = count($QuestionList);
                                    foreach($QuestionList as $questionKey => $question){
                                        $countanswer = [];
                                        if(isset($question)){
                                            if(isset($AttemptExamData['question_answers'])){
                                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                    if($var->question_id == $question['id']){
                                                        return $var ?? [];
                                                    }
                                                });
                                            }
                                        }

                                        if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                            foreach($filterattempQuestionAnswer as $fanswer){
                                                if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                    $students[$studentKey]['Q'.(++$questionKey)] = 'true';
                                                    $CountStudentAnswer[$questionKey] = (($CountStudentAnswer[$questionKey] ?? 0) + 1);
                                                }else{
                                                    $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                    $CountStudentAnswer[$questionKey] = ($CountStudentAnswer[$questionKey] ?? 0);
                                                }
                                            }
                                        }else{
                                            $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                            $CountStudentAnswer[$questionKey] = 0;
                                        }
                                        // Store exams skill array
                                        $QuestionSkills[$questionKey] = $question->e;
                                    }
                                }
                            }
                            
                        }
                        
                    }
                }
            }

            if(!empty($totalQuestions)){
                for($i=1; $i <= $totalQuestions; ++$i){
                    if(isset($CountStudentAnswer[$i]) && !empty($CountStudentAnswer[$i])){
                        $answerPercentage[$i] = round(((100 * $CountStudentAnswer[$i]) / $studentCount), 2).'%';
                    }else{
                        $answerPercentage[$i] = '0%';
                    }
                }
            }
            
            $ResultList['stundet_correct_answer'] = $CountStudentAnswer;
            $ResultList['students'] = $students;
            $ResultList['percentage_rate_correct_answer'] = $answerPercentage;
            $ResultList['skills'] = $QuestionSkills;

            return $this->sendResponse($ResultList);
        }catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function isSchoolStudent($schoolid, $studentId){
        $Student = User::where('id',$studentId)->where('school_id',$schoolid)->first();
        if(isset($Student) && !empty($Student)){
            return true;
        }
        return false;
    }

    /**
     * USE : Question Difficulty Detection API
     */
    // public function ClassDifficultyDetection(Request $request){
    //     $SchoolReports = [];
    //     $schools = School::all();
    //     if(!empty($schools)){
    //         foreach($schools as $school){
    //             // set school data
    //             $SchoolReports[$school->id]['SchoolName'] = $school->{cn::SCHOOL_SCHOOL_NAME_COL};

    //             // get Grouptest assignes to school
    //             $GroupTest = StudentGroup::whereNotNull('exam_ids')->whereRaw("find_in_set($school->id,school_ids)")->get();
    //             if(!empty($GroupTest)){
    //                 // Removed group to ansssignes exams to schools
    //                 foreach($GroupTest as $groupKey => $group){
    //                     $explodeGroup = explode(',',$group->exam_ids);                    
    //                     $examGroup = Exam::whereIn('id',$explodeGroup)
    //                                 ->whereRaw("find_in_set($school->id,school_id)")
    //                                 ->where('is_group_test',1)
    //                                 ->where('status','publish')->get();
    //                     if($examGroup->isEmpty()){
    //                         unset($GroupTest[$groupKey]);
    //                     }
    //                 }
    //             }
    //             $ExamList = Exam::where('is_group_test',0)->whereRaw("find_in_set($school->id,school_id)")->where('status','publish')->get();

    //             $studentCount = 0;
    //             $ExamData = '';
    //             $QuestionSkills = [];

    //             // IsGroup test data
    //             if(!empty($GroupTest)){
    //                 foreach($GroupTest as $groups){
    //                     $arrayOfExams = explode(',', $groups->exam_ids);
    //                     if(count($arrayOfExams) == 1){
    //                     }else{
    //                         /** Set the "School Name" ***/
    //                         $SchoolReports[$school->id]['TestName'] = $groups->name;

    //                         // Set Question codes
    //                         $ExamQuestionIds = Exam::whereIn(cn::EXAM_TABLE_ID_COLS, $arrayOfExams)->pluck(cn::EXAM_TABLE_QUESTION_IDS_COL);
    //                         $QuestionIds = [];
    //                         if(isset($ExamQuestionIds) && !empty($ExamQuestionIds)){
    //                             foreach($ExamQuestionIds as $examQuestionIds){
    //                                 if(isset($examQuestionIds) && !empty($examQuestionIds)){
    //                                     foreach(explode(',', $examQuestionIds) as $quesid){
    //                                         $QuestionIds[] = $quesid;
    //                                     }
    //                                 }
    //                             }
    //                         }

    //                        /** Set the "Question Codes" ***/
    //                         $QuestionCodes = Question::whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->pluck(cn::QUESTION_QUESTION_CODE_COL)->toArray();
    //                         $SchoolReports[$school->id]['QuestionCodes'] = isset($QuestionCodes) ? $QuestionCodes : [];

    //                         /** Set the "Number of Answers in the Questions" ***/
    //                         $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->get();
    //                         if(isset($QuestionList) && !empty($QuestionList)){
    //                             foreach($QuestionList as $Question){
    //                                 if(isset($Question['answers']) && !empty($Question['answers'])){
    //                                     if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER4_EN_COL])){
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['NoOfAnswers'] = 4;
    //                                     }elseif(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL])){
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['NoOfAnswers'] = 3;
    //                                     }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL])){
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['NoOfAnswers'] = 2;
    //                                     }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL])){
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['NoOfAnswers'] = 1;
    //                                     }else{
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                         $SchoolReports[$school->id]['NumberOfAnswersQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['NoOfAnswers'] = 0;
    //                                     }
    //                                 }

    //                                 /** Set the "Initially Assigned Difficulties of the Questions" ***/
    //                                 $SchoolReports[$school->id]['QuestionDifficulties'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                 $SchoolReports[$school->id]['QuestionDifficulties'][$Question->{cn::QUESTION_TABLE_ID_COL}]['DifficultyLevel'] = $this->getDifficultyName($Question->{cn::QUESTION_DIFFICULTY_LEVEL_COL});

    //                                 /** Student Names (IDs) who Attempted the questions" ***/
    //                                 $AttemptExamsStudentsIds = AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID, $arrayOfExams)->groupBy(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID)->pluck(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID);
    //                                 $SchoolReports[$school->id]['StudentNameIdsAttemptedQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
    //                                 $studentDetails = [];
    //                                 if(isset($AttemptExamsStudentsIds) && !empty($AttemptExamsStudentsIds)){
    //                                     foreach($AttemptExamsStudentsIds as $studentId){
    //                                         $StudentData = User::find($studentId);
    //                                         $studentDetails[$studentId]['ID'] = $StudentData->id;
    //                                         $studentDetails[$studentId]['StudentName'] = $StudentData->name;
    //                                     }
    //                                 }
    //                                 $SchoolReports[$school->id]['StudentNameIdsAttemptedQuestion'][$Question->{cn::QUESTION_TABLE_ID_COL}]['Students'] = $studentDetails;
    //                             }
    //                         }


    //                         /** Corresponding Results (Correct/Incorrect) of 1.5 ***/
    //                         if(!empty($arrayOfExams)){
    //                             foreach($arrayOfExams as $exams){
    //                                 $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$exams)->whereRaw("find_in_set($school->id,school_id)")->first();
    //                                 if(!empty($ExamData)){
    //                                     if(!empty($ExamData->student_ids)){
    //                                         $studentIds = explode(',',$ExamData->student_ids);
    //                                         foreach($studentIds as $studentKey => $studentId){
    //                                             if($this->isSchoolStudent($school->id, $studentId)){
    //                                                 $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id',$exams)->first();
    //                                                 if(isset($AttemptExamData) && !empty($AttemptExamData)){
    //                                                     if(!empty($ExamData->question_ids)){
    //                                                         $questionIds = explode(',',$ExamData->question_ids);
    //                                                         $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                                                            
    //                                                         $corosPoindingResult = [];

    //                                                         if(isset($QuestionList) && !empty($QuestionList)){
    //                                                             foreach($QuestionList as $questionKey => $question){
    //                                                                 if(isset($question)){
    //                                                                     if(isset($AttemptExamData['question_answers'])){
    //                                                                         $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
    //                                                                             if($var->question_id == $question['id']){
    //                                                                                 return $var ?? [];
    //                                                                             }
    //                                                                         });
    //                                                                     }

    //                                                                     if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
    //                                                                         foreach($filterattempQuestionAnswer as $fanswer){
    //                                                                             if($fanswer->answer == $question->answers->{'correct_answer_'.$AttemptExamData->language}){
    //                                                                                 $corosPoindingResult[$studentId][$question->id]['QuestionId'] = $question->id;
    //                                                                                 $corosPoindingResult[$studentId][$question->id]['Answer'] = 'true';
    //                                                                             }else{
    //                                                                                 $corosPoindingResult[$studentId][$question->id]['QuestionId'] = $question->id;
    //                                                                                 $corosPoindingResult[$studentId][$question->id]['Answer'] = 'false';
    //                                                                             }
    //                                                                         }
    //                                                                     }else{
    //                                                                         $corosPoindingResult[$studentId][$question->id]['QuestionId'] = $question->id;
    //                                                                         $corosPoindingResult[$studentId][$question->id]['Answer'] = 'false';
    //                                                                     }
    //                                                                 }
    //                                                             }
    //                                                         }

    //                                                         $SchoolReports[$school->id]['CorrespondingResults'][$question->{cn::QUESTION_TABLE_ID_COL}] = $corosPoindingResult;
    //                                                     }
    //                                                 }
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }

                            
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $this->sendResponse($SchoolReports);
    //     echo '<pre>';print_r($SchoolReports);die;
    // }

    /**
     * USE : Set Difficulty Level
     */
    public function getDifficultyName($level){
        $levelName = '';
        switch ($level) {
            case 1:
                $levelName = '1 - Easy';
                break;
            case 2:
                $levelName = '2 - Medium';
                break;
            case 3:
                $levelName = '3 - difficult';
                break;
            case 4:
                $levelName = '4 - Tough';
                break;
            default:
                $levelName = 'None';
        }
        return $levelName;
    }




    public function ClassDifficultyDetection(Request $request){
        $SchoolReports = [];
        $Reports = [];
        $schools = School::all();
        if(!empty($schools)){
            foreach($schools as $school){
                // set school data
                $SchoolReports['SchoolID'] = $school->{cn::SCHOOL_ID_COLS};
                $SchoolReports['SchoolName'] = $school->{cn::SCHOOL_SCHOOL_NAME_COL};

                // get Grouptest assignes to school
                $GroupTest = StudentGroup::whereNotNull('exam_ids')->whereRaw("find_in_set($school->id,school_ids)")->get();
                if(!empty($GroupTest)){
                    // Removed group to ansssignes exams to schools
                    foreach($GroupTest as $groupKey => $group){
                        $explodeGroup = explode(',',$group->exam_ids);                    
                        $examGroup = Exam::whereIn('id',$explodeGroup)
                                    ->whereRaw("find_in_set($school->id,school_id)")
                                    ->where('is_group_test',1)
                                    ->where('status','publish')->get();
                        if($examGroup->isEmpty()){
                            unset($GroupTest[$groupKey]);
                        }
                    }
                }
                
                // IsGroup test data
                $testSetData = [];
                if(!empty($GroupTest)){
                    foreach($GroupTest as $key => $groups){
                        $testSetData[] = $this->GetReportData($groups->exam_ids, $school->id, $groups->name);
                    }
                }
                $SchoolReports['TestSet'] = $testSetData;

                // If without group test result
                $ExamList = Exam::where('is_group_test',0)->whereRaw("find_in_set($school->id,school_id)")->where('status','publish')->get();
                if(isset($ExamList) && !empty($ExamList)){
                    foreach($ExamList as $key => $Exams){
                        $testSetData[] = $this->GetReportData($Exams->id, $school->id, $Exams->title);
                    }
                }
                $SchoolReports['TestSet'] = $testSetData;

                $Reports[] = $SchoolReports;
            }
        }
        return $this->sendResponse($Reports);
    }



    public function GetReportData($examIds, $schoolid, $testSetName){
        $ResultList = [];
        $QuestionSkills = [];
        $studentCount = 0;
        $ExamData = '';
        
        $arrayOfExams = explode(',',$examIds);
        if(count($arrayOfExams) == 1){ // If user can select single test
            $examId = $examIds;

            //set test group name
            $ResultList['SetName'] = $testSetName;

            $ExamQuestionIds = Exam::where(cn::EXAM_TABLE_ID_COLS, $examId)->pluck(cn::EXAM_TABLE_QUESTION_IDS_COL);
            if(isset($ExamQuestionIds) && !empty($ExamQuestionIds)){
                foreach(explode(',', $ExamQuestionIds[0]) as $quesid){
                    $QuestionIds[] = $quesid;
                }
            }
            
            /** /** 1.1 Question Codes" ***/
            $QuestionCodes = Question::whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->pluck(cn::QUESTION_NAMING_STRUCTURE_CODE_COL)->toArray();
            $ResultList['QuestionCodes'] = isset($QuestionCodes) ? $QuestionCodes : [];

            /** 1.2 Test Sets which include the questions & the corresponding Question Numbers in the Test Sets. ***/
            $QuestionSets = [];
            $ExamsSetData = Exam::find($examId);
            if(isset($ExamsSetData) && !empty($ExamsSetData)){
                $questionset = [
                    'TestTitle' => $ExamsSetData->title,
                    'Questions' => $ExamsSetData->question_ids
                ];
                $QuestionSets[] = $questionset;
            }
            $ResultList['SetOfQuestion'] = $QuestionSets ?? [];


            /** 1.3 Number of Answers in the Questions */
            $QuestionsAnswerDetails = [];
            $QuestionDifficulties = [];
            $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->get();
            if(isset($QuestionList) && !empty($QuestionList)){
                $questionOder = 1;
                foreach($QuestionList as $queKey => $Question){
                    if(isset($Question['answers']) && !empty($Question['answers'])){
                        if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER4_EN_COL])){
                            $queAnsDetail = [
                                'QuestionOderNo' => ($queKey+1),
                                'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                'NoOfAnswers' => 4
                            ];
                        }elseif(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL])){
                            $queAnsDetail = [
                                'QuestionOderNo' => ($queKey+1),
                                'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                'NoOfAnswers' => 3
                            ];
                        }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL])){
                            $queAnsDetail = [
                                'QuestionOderNo' => ($queKey+1),
                                'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                'NoOfAnswers' => 2
                            ];
                        }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL])){
                            $queAnsDetail = [
                                'QuestionOderNo' => ($queKey+1),
                                'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                'NoOfAnswers' => 1
                            ];
                        }else{
                            $queAnsDetail = [
                                'QuestionOderNo' => ($queKey+1),
                                'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                'NoOfAnswers' => 1
                            ];
                        }
                        $QuestionsAnswerDetails[] = $queAnsDetail;
                    }

                    /** 1.4 Initially Assigned Difficulties of the Questions */
                    $QuestionDifficulties[] = [
                        'QuestionOderNo' => ($queKey+1),
                        'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                        'DifficultyLevel' => $this->getDifficultyName($Question->{cn::QUESTION_DIFFICULTY_LEVEL_COL})
                    ];
                    // $QuestionDifficulties[$Question->{cn::QUESTION_TABLE_ID_COL}]['QuestionNo'] = $Question->{cn::QUESTION_TABLE_ID_COL};
                    // $QuestionDifficulties[$Question->{cn::QUESTION_TABLE_ID_COL}]['DifficultyLevel'] = $this->getDifficultyName($Question->{cn::QUESTION_DIFFICULTY_LEVEL_COL});
                }
            }
            $ResultList['NoOfAnswerDetail'] = $QuestionsAnswerDetails ?? [];
            $ResultList['QuestionDifficulties'] = $QuestionDifficulties ?? [];

            /** 1.5 Student Names (IDs) who Attempted the questions */
            $attemptedStudents = [];
            $studentCount = 0;
            $ExamData = Exam::find($examId);
            if(!empty($ExamData)){
                if(!empty($ExamData->student_ids)){
                    $studentIds = explode(',',$ExamData->student_ids);
                    foreach($studentIds as $studentKey => $studentId){
                        if($this->isSchoolStudent($schoolid, $studentId)){
                            $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id', $examId)->first();
                            if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                //$attemptedStudents[$studentKey]['exam_id'] = $ExamData->id;
                                $StudentDetail = User::find($studentId);
                                $attemptedStudents[$studentKey]['student_id'] = $StudentDetail->id;
                                $attemptedStudents[$studentKey]['student_name'] = $StudentDetail->name;
                                $attemptedStudents[$studentKey]['login_email'] = $StudentDetail->email;
                                $attemptedStudents[$studentKey]['student_grade'] = $StudentDetail->grade_id ?? 0;
                                $attemptedStudents[$studentKey]['student_number'] = $StudentDetail->id;
                                //$attemptedStudents[$studentKey]['countStudent'] = (++$studentCount);
                                $attemptedStudents[$studentKey]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                                $attemptedStudents[$studentKey]['exam_status'] = (($AttemptExamData->status) && $AttemptExamData->status == 1) ? 'Complete' : 'Pending';
                                $attemptedStudents[$studentKey]['completion_time'] = ($AttemptExamData->exam_taking_timing) ? $AttemptExamData->exam_taking_timing : '--';
                                //$attemptedStudents[$studentKey]['student_ranking'] = $this->getStudentExamRanking($ExamData->id, $studentId);

                                if(!empty($ExamData->question_ids)){
                                    $questionIds = explode(',',$ExamData->question_ids);
                                    $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                                    if(isset($QuestionList) && !empty($QuestionList)){
                                        $attemptedStudents[$studentKey]['countQuestions'] = count($QuestionList);
                                        $q = 0;
                                        foreach($QuestionList as $questionKey => $question){
                                            if(isset($question)){
                                                if(isset($AttemptExamData['question_answers'])){
                                                    $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                        if($var->question_id == $question['id']){
                                                            return $var ?? [];
                                                        }
                                                    });
                                                }
                                            }
                                            
                                            /** 1.6 Corresponding Results (Correct/Incorrect) of 1.5 **/
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                        $correctIncorrectResult = [
                                                            'QuestionOderNo' => ($questionKey + 1),
                                                            'Question_id' => $fanswer->question_id,
                                                            'answer' => 'true'
                                                        ];
                                                        $attemptedStudents[$studentKey]['correct_incorrect_result'][$questionKey] = $correctIncorrectResult;
                                                    }else{
                                                        $correctIncorrectResult = [
                                                            'QuestionOderNo' => ($questionKey + 1),
                                                            'Question_id' => $fanswer->question_id,
                                                            'answer' => 'false'
                                                        ];
                                                        $attemptedStudents[$studentKey]['correct_incorrect_result'][$questionKey] = $correctIncorrectResult;
                                                    }

                                                    /** 1.7 Students’ Answers (1st, 2nd, 3rd…etc) */
                                                    $studentSelectedAnswer = [
                                                        'QuestionOderNo' => ($questionKey + 1),
                                                        'Question_id' => $fanswer->question_id,
                                                        'selected_answer' => $fanswer->answer
                                                    ];
                                                    $attemptedStudents[$studentKey]['student_selected_answers'][$questionKey] = $studentSelectedAnswer;
                                                }
                                            }else{
                                                $correctIncorrectResult = [
                                                    'QuestionOderNo' => ($questionKey + 1),
                                                    'Question_id' => $question->id,
                                                    'answer' => 'false'
                                                ];
                                                $attemptedStudents[$studentKey]['correct_incorrect_result'][$questionKey] = $correctIncorrectResult;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $ResultList['attemptedStudents'] = $attemptedStudents ?? [];
            
        }else{
            // If group test reports
            if(!empty(explode(',',$examIds))){
                
                //set test group name
                $ResultList['SetName'] = $testSetName;

                $ExamQuestionIds = Exam::whereIn(cn::EXAM_TABLE_ID_COLS, $arrayOfExams)->pluck(cn::EXAM_TABLE_QUESTION_IDS_COL);
                $QuestionIds = [];
                if(isset($ExamQuestionIds) && !empty($ExamQuestionIds)){
                    foreach($ExamQuestionIds as $examQuestionIds){
                        if(isset($examQuestionIds) && !empty($examQuestionIds)){
                            foreach(explode(',', $examQuestionIds) as $quesid){
                                $QuestionIds[] = $quesid;
                            }
                        }
                    }
                }

                /** /** 1.1 Question Codes" ***/
                $QuestionCodes = Question::whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->pluck(cn::QUESTION_NAMING_STRUCTURE_CODE_COL)->toArray();
                $ResultList['QuestionCodes'] = isset($QuestionCodes) ? $QuestionCodes : [];
                

                /** 1.2 Test Sets which include the questions & the corresponding Question Numbers in the Test Sets. ***/
                $QuestionSets = [];
                $ExamsSetData = Exam::whereIn('id',$arrayOfExams)->get();
                if(isset($ExamsSetData) && !empty($ExamsSetData)){
                    foreach($ExamsSetData as $ExamSets){
                        $questionset = [
                            'TestTitle' => $ExamSets->title,
                            'Questions' => $ExamSets->question_ids
                        ];
                        $QuestionSets[] = $questionset;
                    }
                }
                $ResultList['SetOfQuestion'] = $QuestionSets ?? [];


                /** 1.3 Number of Answers in the Questions */
                $QuestionsAnswerDetails = [];
                $QuestionDifficulties = [];
                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL, $QuestionIds)->get();
                if(isset($QuestionList) && !empty($QuestionList)){
                    foreach($QuestionList as $queKey => $Question){
                        if(isset($Question['answers']) && !empty($Question['answers'])){
                            if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER4_EN_COL])){
                                $queAnsDetail = [
                                    'QuestionOderNo' => ($queKey+1),
                                    'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                    'NoOfAnswers' => 4
                                ];
                            }elseif(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER3_EN_COL])){
                                $queAnsDetail = [
                                    'QuestionOderNo' => ($queKey+1),
                                    'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                    'NoOfAnswers' => 3
                                ];
                            }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL]) && !empty($Question['answers'][cn::ANSWER_ANSWER2_EN_COL])){
                                $queAnsDetail = [
                                    'QuestionOderNo' => ($queKey+1),
                                    'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                    'NoOfAnswers' => 2
                                ];
                            }else if(!empty($Question['answers'][cn::ANSWER_ANSWER1_EN_COL])){
                                $queAnsDetail = [
                                    'QuestionOderNo' => ($queKey+1),
                                    'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                    'NoOfAnswers' => 1
                                ];
                            }else{
                                $queAnsDetail = [
                                    'QuestionOderNo' => ($queKey+1),
                                    'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                                    'NoOfAnswers' => 0
                                ];
                            }
                            $QuestionsAnswerDetails[] = $queAnsDetail;
                        }

                        /** 1.4 Initially Assigned Difficulties of the Questions */
                        $QuestionDifficulties[] = [
                            'QuestionOderNo' => ($queKey+1),
                            'QuestionNo' => $Question->{cn::QUESTION_TABLE_ID_COL},
                            'DifficultyLevel' => $this->getDifficultyName($Question->{cn::QUESTION_DIFFICULTY_LEVEL_COL})
                        ];
                    }
                }
                $ResultList['NoOfAnswerDetail'] = $QuestionsAnswerDetails ?? [];
                $ResultList['QuestionDifficulties'] = $QuestionDifficulties ?? [];

                // Saved current schools assigned group test sets
                $groupTestData = [];
                if(!empty(explode(',',$examIds))){
                    $studentCount = 0;
                    foreach(explode(',',$examIds) as $exams){
                        $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$exams)->whereRaw("find_in_set($schoolid,school_id)")->first();
                        if(!empty($ExamData)){
                            if(!empty($ExamData->student_ids)){
                                $studentIds = explode(',',$ExamData->student_ids);
                                foreach($studentIds as $studentKey => $studentId){
                                    if($this->isSchoolStudent($schoolid, $studentId)){
                                        $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id',$exams)->first();
                                        if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                            $StudentDetail = User::find($studentId);
                                            $groupTestData[$studentId][$exams]['exam_id'] = $ExamData->id;
                                            $groupTestData[$studentId][$exams]['student_id'] = $StudentDetail->id;
                                            $groupTestData[$studentId][$exams]['student_name'] = $StudentDetail->name;
                                            $groupTestData[$studentId][$exams]['login_email'] = $StudentDetail->email;
                                            $groupTestData[$studentId][$exams]['student_grade'] = $StudentDetail->grade_id ?? 0;
                                            $groupTestData[$studentId][$exams]['student_number'] = $StudentDetail->id;
                                            $groupTestData[$studentId][$exams]['student_status'] = 'Active';
                                            $groupTestData[$studentId][$exams]['countStudent'] = (++$studentCount);
                                            $groupTestData[$studentId][$exams]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                                            $groupTestData[$studentId][$exams]['exam_status'] = (($AttemptExamData->status) && $AttemptExamData->status == 1) ? 'Complete' : 'Pending';
                                            // $getTotalTimedata = AttemptExams::whereIn('exam_id',explode(',',$examIds))->where('student_id',$StudentDetail->id)->get()->pluck('exam_taking_timing');
                                            // $groupTestData[$studentId][$exams]['completion_time'] = $this->TotalDurationCalculation($getTotalTimedata);
                                            $groupTestData[$studentId][$exams]['completion_time'] = ($AttemptExamData->exam_taking_timing) ? $AttemptExamData->exam_taking_timing : '--';
                                            //$groupTestData[$studentId][$exams]['student_ranking'] = $this->getStudentExamRanking($ExamData->id, $studentId);
                                            if(!empty($ExamData->question_ids)){
                                                $questionIds = explode(',',$ExamData->question_ids);
                                                $groupTestData[$studentId][$exams]['questionIds'] = $questionIds;
                                                $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                                                if(isset($QuestionList) && !empty($QuestionList)){
                                                    $groupTestData[$studentId][$exams]['countQuestions'] = count($QuestionList);
                                                    foreach($QuestionList as $questionKey => $question){
                                                        if(isset($question)){
                                                            if(isset($AttemptExamData['question_answers'])){
                                                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                                    if($var->question_id == $question['id']){
                                                                        return $var ?? [];
                                                                    }
                                                                });
                                                            }
                                                        }
                                                        if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                            $q = 0;
                                                            foreach($filterattempQuestionAnswer as $fanswer){
                                                                if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                                    $groupTestData[$studentId][$exams]['correct_incorrect_result'][$questionKey] = 'true';
                                                                }else{
                                                                    $groupTestData[$studentId][$exams]['correct_incorrect_result'][$questionKey] = 'false';
                                                                }

                                                                /** 1.7 Students’ Answers (1st, 2nd, 3rd…etc) */
                                                                $studentSelectedAnswer = [                                                                    
                                                                    'Question_id' => $fanswer->question_id,
                                                                    'selected_answer' => $fanswer->answer
                                                                ];
                                                                $groupTestData[$studentId][$exams]['student_selected_answers'][$questionKey] = $studentSelectedAnswer;
                                                            }
                                                        }else{
                                                            $groupTestData[$studentId][$exams]['correct_incorrect_result'][$questionKey] = 'false';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $attemptedStudents = [];
                if(!empty($groupTestData)){
                    /** 1.5 Student Names (IDs) who Attempted the questions */
                    $totalNoOfQuestions = 0;
                    $totalQuestionsArray = Exam::whereIn('id',explode(',',$examIds))->get();
                    $totalNoOfQuestions = count(explode(',',implode(',',array_column($totalQuestionsArray->toArray(),'question_ids'))));
                    foreach($groupTestData as $studentKey => $testGroup){
                        $attemptedStudents[$studentKey]['exam_id'] = implode(',',array_column($testGroup,'exam_id'));
                        $attemptedStudents[$studentKey]['total_correct_answer'] = 0;
                        $attemptedStudents[$studentKey]['countQuestions'] = 0;

                        $QueNumber = 1;
                        
                        foreach($testGroup as $k => $test){
                            $attemptedStudents[$studentKey]['student_id'] = $test['student_id'];
                            $attemptedStudents[$studentKey]['student_name'] = $test['student_name'];
                            $attemptedStudents[$studentKey]['login_email'] = $test['login_email'];
                            $attemptedStudents[$studentKey]['student_grade'] = $test['student_grade'];
                            $attemptedStudents[$studentKey]['countStudent'] = AttemptExams::where('exam_id',explode(',',$examIds))->count();
                            $attemptedStudents[$studentKey]['total_correct_answer'] = (number_format($attemptedStudents[$studentKey]['total_correct_answer']) + number_format($test['total_correct_answer']));
                            $attemptedStudents[$studentKey]['exam_status'] = $test['exam_status'];
                            $attemptedStudents[$studentKey]['completion_time'] = $test['completion_time'];
                            //$attemptedStudents[$studentKey]['student_ranking'] = $this->getGroupTestStudentExamRanking(explode(',',$examIds), $studentKey);
                            
                            /** 1.6 Corresponding Results (Correct/Incorrect) of 1.5 */
                            $attemptedStudents[$studentKey]['countQuestions'] = $totalNoOfQuestions;
                            for($i = 0; $i < $totalNoOfQuestions; $i++){
                                if(array_key_exists($i,$test['correct_incorrect_result'])){
                                    $stAnswer = [
                                        'QuestionOderNo' => ($QueNumber++),
                                        'Question_id' => $test['questionIds'][$i],
                                        'answer' => $test['correct_incorrect_result'][$i]
                                    ];
                                    $attemptedStudents[$studentKey]['correct_incorrect_result'][] = $stAnswer;
                                }
                            }
                        }

                        /** 1.7 Students’ Answers (1st, 2nd, 3rd…etc) */
                        $student_selected_answers = array_column($testGroup,'student_selected_answers');
                        $newArrayStudentAnserDetail = [];
                        if(!empty($student_selected_answers)){
                            $QueNumber = 1;
                            foreach($student_selected_answers as $AnswerValue){
                                foreach($AnswerValue as $val){
                                    $val['QuestionOderNo'] = ($QueNumber++);
                                    $newArrayStudentAnserDetail[] = $val;
                                }
                            }
                        }
                        $attemptedStudents[$studentKey]['student_selected_answers'] = $newArrayStudentAnserDetail;
                    }
                }
                // Store Sttemptedstudents all data
                $ResultList['attemptedStudents'] = isset($attemptedStudents) ? array_values($attemptedStudents) : [];
            }
        }
        return $ResultList;
    }
}
