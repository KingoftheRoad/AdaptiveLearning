<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use App\Helpers\Helper;
use App\Models\School;
use App\Models\Exam;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Illuminate\Support\Facades\View;
use Log;
use Auth;
use App\Http\Services\TeacherGradesClassService;

class MyTeachingController extends Controller
{
    use Common;

    protected $AlpAiGraphController, $Exam, $GradeSchoolMappings, $GradeClassMapping, $User, $AttemptExams, $MyTeachingReport, $PeerGroup,$TeacherGradesClassService;
    public function __construct(){
        $this->AlpAiGraphController = new AlpAiGraphController();
        $this->Exam = new Exam;
        $this->GradeSchoolMappings = new GradeSchoolMappings;
        $this->GradeClassMapping = new GradeClassMapping;
        $this->User = new User;
        $this->AttemptExams = new AttemptExams;
        $this->MyTeachingReport = new MyTeachingReport;
        $this->PeerGroup = new PeerGroup;
        $this->TeacherGradesClassService = new TeacherGradesClassService;
    }

    public function getAssignmentTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $class_type_id = array();
        $GradeClassListData = array();
        $Query = '';
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
        $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
        $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
        $TeacherClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
        $TeacherAssignedClassIds = [];
        $TeacherAssignedClass = [];
        if(!empty($TeacherClass)){
            foreach($TeacherClass as $teacherClass){
                $TeacherAssignedClass[] = explode(',',$teacherClass);
            }
        }
        $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
        $classListIdArray =  GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesListIdArr)
                            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                            ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();

        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});
        $AssignmentTestList = MyTeachingReport::where(function($query) use($gradesListIdArr, $TeacherAssignedClass, $TeachersPeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                            ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                    })
                                    ->where([
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->with('exams','peerGroup')
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $AssignmentTestList = MyTeachingReport::Select('*')
                                    ->with('exams','peerGroup')
                                    ->where(function($query) use($gradeId, $classTypeId, $TeachersPeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                                    })
                                    //->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                                    //->whereIn('class_id' ,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  => 2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   => $schoolId,
                                    ])
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/assignment_test',compact('AssignmentTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }
    
    /***
     * USE : For Exam ExerciseList Display  
     */
    public function getAssignmentExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
        $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
        $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
        $TeacherClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
        $TeacherAssignedClassIds = [];
        $TeacherAssignedClass = [];
        if(!empty($TeacherClass)){
            foreach($TeacherClass as $teacherClass){
                $TeacherAssignedClass[] = explode(',',$teacherClass);
            }
        }
        $TeacherAssignedClass =$this->array_flatten($TeacherAssignedClass);
        
        // Find Teacher Peer Group Ids
        $TeachersPeerGroupIds = [];
        $TeachersPeerGroupIds = $this-> TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});

        $AssignmentExerciseList = MyTeachingReport::where(function($query) use($gradesListIdArr, $TeacherAssignedClass,$TeachersPeerGroupIds){
                                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL,$gradesListIdArr)
                                                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$TeachersPeerGroupIds);
                                                        // ->orWhere(function($SubQuery){
                                                        //     $SubQuery->whereNull(cn::TEACHING_REPORT_GRADE_ID_COL)
                                                        //     ->orWhereNull(cn::TEACHING_REPORT_CLASS_ID_COL)
                                                        //     ->orWhereNull(cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL);
                                                        // });
                                                    })
                                                    ->where(cn::TEACHING_REPORT_REPORT_TYPE_COL,'assignment_test')
                                                    ->where(cn::TEACHING_REPORT_STUDY_TYPE_COL,1)
                                                    ->where(cn::TEACHING_REPORT_SCHOOL_ID_COL,$schoolId)
                                                    ->with(['exams','peerGroup'])
                                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $Query = MyTeachingReport::with('exams','peerGroup')->Select('*');
            $Query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                ->where([
                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                ]);
            $AssignmentExerciseList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/assignment_exercise',compact('AssignmentExerciseList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }

    /***
     * USE : For Exam SelfLearning Exercise List Display  
     */
    public function getSelfLearningExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
        $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
        $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
        $TeacherClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
        $TeacherAssignedClassIds = [];
        $TeacherAssignedClass = [];
        if(!empty($TeacherClass)){
            foreach($TeacherClass as $teacherClass){
                $TeacherAssignedClass[] = explode(',',$teacherClass);
            }
        }
        $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                                            ->where([
                                                                cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                                                cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                                                cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                                            ])
                                                            ->with('exams','user','attempt_exams')
                                                            ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

        // Get Current student grade id wise strand list
        $strandsList = StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1])->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if($strandsList->isNotEmpty()){
            $strandsIds = array_unique($strandsList->toArray());
            $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

            // Get The learning units based on first Strands
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                // Get the Learning objectives based on first learning units
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                        ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck(cn::LEARNING_UNITS_ID_COL))
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
        }

        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            // Create filter query object
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/self_learning_exercise',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData','strandsList','LearningUnits','LearningObjectives'));
    }

     /***
     * USE : For Exam SelfLearning Test List Display  
     */
    public function getSelfLearningTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $strandsList = array();
        $LearningUnits = array();
        $LearningObjectives = array();
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $gradesList = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->with('getClass')->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
        $gradesListId = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])
                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                        ->toArray();
        $gradesListIdArr = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
        $TeacherClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
        $TeacherAssignedClassIds = [];
        $TeacherAssignedClass = [];
        if(!empty($TeacherClass)){
            foreach($TeacherClass as $teacherClass){
                $TeacherAssignedClass[] = explode(',',$teacherClass);
            }
        }
        $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                                ->where([
                                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                                ])
                                                ->with('exams','user','attempt_exams')
                                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

        // Get Current student grade id wise strand list
        $strandsList = StrandUnitsObjectivesMappings::where([cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => 1])->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if($strandsList->isNotEmpty()){
            $strandsIds = array_unique($strandsList->toArray());
            $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

            // Get The learning units based on first Strands
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                // Get the Learning objectives based on first learning units
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,Auth::user()->{cn::USERS_GRADE_ID_COL})
                        ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                        ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                        ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck(cn::LEARNING_UNITS_ID_COL))
                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
        }
        

        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')
                        ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
                        ->where([
                            cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                            cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                            cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                        ])
                        ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
                        
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/self_learning_test',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData','strandsList','LearningUnits','LearningObjectives'));
    }

    public function StudentResultSummaryReport(Request $request){
        $records = $this->getStudentResultSummary($request);
        $result = array();
        if(!empty( $records)){
            $result['html'] = (string)View::make('backend/teacher/student_result_summary',compact('records'));
            return $this->sendResponse($result);
        }else{
            return $this->SendError(__('languages.no_any_students_are_attempt_exams_please_wait_until_students_can_attempt_this_exams'),422);
        }
        
    }
}
