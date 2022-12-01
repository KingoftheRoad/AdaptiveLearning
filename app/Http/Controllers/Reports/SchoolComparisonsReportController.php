<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use App\Models\School;
use App\Models\Grades;
use App\Models\Exam;
use App\Models\User;
use App\Models\AttemptExams;
use App\Models\GradeClassMapping;
use App\Models\TeachersClassSubjectAssign;

class SchoolComparisonsReportController extends Controller
{
    use Common;
    /**
     * USE : Get reports for school comparison
     */
    public function getSchoolComparisonsReport(Request $request){
        $GradeList = Grades::all();
        if($this->isTeacherLogin()){
            $GradeList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL=>Auth()->user()->id])->with('getClass')->pluck('class_id')->toArray();
            $GradeList = Grades::where(cn::GRADES_ID_COL,$GradeList)->get();
        }
        $grade_id='';
        $class_type_id = array();
        $studentList = array();
        $filter = 0;
        $GradeClassListData = array();
        $GradeClassMapping = array();
        if(isset($request->grade_id) && !empty($request->grade_id)){
            $grade_id = $request->grade_id;
            $filter = 1;
            if($this->isSchoolLogin()){
                $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
            if($this->isTeacherLogin()){
                $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isTeacherLogin())->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
            if($this->isAdmin()){
                $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
        }

        if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
            $filter = 1;
            $class_type_id = $request->class_type_id;
            if($this->isSchoolLogin()){
                $GradeClassMapping = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$request->class_type_id)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
            if($this->isTeacherLogin()){
                $GradeClassMapping = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$request->class_type_id)->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$this->isTeacherLogin())->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
            if($this->isAdmin()){
                $GradeClassMappingName = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$request->class_type_id)->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL)->toArray();
                $GradeClassMapping = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->grade_id)->whereIn(cn::GRADE_CLASS_MAPPING_NAME_COL,$GradeClassMappingName)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            }
        }
        if($this->isSchoolLogin()){
            $currentLoggedSchoolId = $this->isSchoolLogin();
            $ExamList = Exam::whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->where('status','publish')->get();
        }else{
            $ExamList = Exam::where('status','publish')->get();
        }

        $SchoolReportsData = [];
        $QuestionIdArray = [];
        $totalNoOfStudents = 0;
        $ExamData = [];
        if(isset($request->exam_id) && !empty($request->exam_id)){
            $arrayOfExams = explode(',',$request->exam_id);
            // If count ==1 Means Is single test reports
            $reportType = 'singleTest';
            $ExamData = Exam::find($request->exam_id);
            if(!isset($ExamData) && !empty($ExamData)){
                return back()->withError('Invalid exam id');
            }
            if(isset($ExamData->school_id) && !empty($ExamData->school_id)){
                $schoolIdArray = [];
                $schoolIdArray = explode(',',$ExamData->school_id);
                $totalNoOfQuestions = count(explode(',',$ExamData->question_ids));
                if(empty($schoolIdArray)){
                    return back()->withError('No any one assign test to schools');
                }
                
                if(!empty($schoolIdArray)){
                    foreach($schoolIdArray as $key => $SchoolId){
                        $schoolData = School::find($SchoolId);
                        if(!empty($schoolData)){
                            $SchoolReportsData[$key]['school_id'] = $schoolData->id;
                            if(app()->getLocale() == 'en'){
                                $SchoolReportsData[$key]['school_name'] = $schoolData->DecryptSchoolNameEn ?? $schoolData->school_name;
                            }else{
                                $SchoolReportsData[$key]['school_name'] = $schoolData->DecryptSchoolNameCh ?? $schoolData->school_name;
                            }

                            // Get Number of total student particular schools
                            $totalNoOfStudents = User::where('role_id',3)->where('school_id',$SchoolId)->count();
                            if(isset($request->grade_id) && !empty($request->grade_id)){
                                $totalNoOfStudents = User::select('id')->where('role_id',3)->where('school_id',$SchoolId)->where(cn::USERS_GRADE_ID_COL,$request->grade_id)->count();
                            }
                            if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
                                $totalNoOfStudents = User::select('id')->where('role_id',3)->where('school_id',$SchoolId)->where(cn::USERS_GRADE_ID_COL,$request->grade_id)->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id)->count();
                            }
                            $SchoolReportsData[$key]['total_students'] = $totalNoOfStudents;
                            $averageOfCorrectAnswers = 0;
                            $total_attempted_exams_students = 0;
                            $studentsData = [];
                            // If students available this schools
                            $SchoolsStudents = User::select('id')->where('role_id',cn::STUDENT_ROLE_ID)->where('school_id',$SchoolId)->get();
                            if(isset($request->grade_id) && !empty($request->grade_id)){
                                $SchoolsStudents = User::select('id')->where('role_id',cn::STUDENT_ROLE_ID)->where('school_id',$SchoolId)->where(cn::USERS_GRADE_ID_COL,$request->grade_id)->get();
                            }
                            if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
                                $SchoolsStudents = User::select('id')->where('role_id',cn::STUDENT_ROLE_ID)->where('school_id',$SchoolId)->where(cn::USERS_GRADE_ID_COL,$request->grade_id)->whereIn(cn::USERS_CLASS_ID_COL,$request->class_type_id)->get();
                            }
                            $AttemptedStudents = [];
                            if(isset($SchoolsStudents) && !empty($SchoolsStudents)){
                                foreach($SchoolsStudents as $studentKey => $student){
                                    // Get correct answer detail
                                    $AttemptExamData = AttemptExams::where('student_id',$student->id)->where('exam_id',$request->exam_id)->first();
                                    if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                        if(!in_array($student->id,$AttemptedStudents)){
                                            $AttemptedStudents[] = $student->id;
                                        }
                                        $total_attempted_exams_students++;
                                        $averageOfCorrectAnswers += $AttemptExamData->total_correct_answers;
                                    }
                                }
                            }
                            $SchoolReportsData[$key]['no_of_correct_answers'] = ($averageOfCorrectAnswers && $total_attempted_exams_students) ? number_format(($averageOfCorrectAnswers / $total_attempted_exams_students),2) : 0;
                            $SchoolReportsData[$key]['no_of_total_questions'] = ($ExamData->question_ids) ? (count(explode(',',$ExamData['question_ids'])) * count($AttemptedStudents)) : 0;
                            $SchoolReportsData[$key]['total_attempted_exams_students'] = count($AttemptedStudents);
                            if($total_attempted_exams_students){
                                $avg = ((($averageOfCorrectAnswers) / ($totalNoOfQuestions * count($AttemptedStudents))) * 100);
                                $SchoolReportsData[$key]['average_of_correct_answers'] = (number_format($avg,2) + 0).'%';
                            }else{
                                $SchoolReportsData[$key]['average_of_correct_answers'] = '0%';
                            }
                        }
                    }
                }
            }
            return view('backend.reports.school_comparisons_report',compact('reportType','SchoolReportsData','ExamData','ExamList','GradeList','grade_id','class_type_id','GradeClassListData'));
        }else{
            return back()->withError('Please Select Exam');
        }
    }
}