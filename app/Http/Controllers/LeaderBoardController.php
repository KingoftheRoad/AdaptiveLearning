<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserCreditPoints;
use Illuminate\Support\Facades\View;
use App\Http\Services\TeacherGradesClassService;
use App\Constants\DbConstant as cn;
use Auth;

class LeaderBoardController extends Controller 
{
    use Common;

    protected $TeacherGradesClassService;

    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->TeacherGradesClassService = new TeacherGradesClassService;
    }

    /* Leaderboard on load credit point data display   */
    public function getLeaderBoardDetail(){
        $studentList = collect();
        switch(Auth::user()->role_id){
            case 2 :
                $GetAssignedGradeClassIds = [];
                $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                if(isset($TeacherGradeClass['grades']) && !empty($TeacherGradeClass['grades']) && isset($TeacherGradeClass['class']) && !empty($TeacherGradeClass['class'])){
                    $studentList = UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->whereHas('user',function($query) use($TeacherGradeClass){
                                    // return $query->where([
                                    //                         cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                    //                         cn::USERS_SCHOOL_ID_COL => Auth::user()->school_id,
                                    //                     ])
                                    //                     ->get()
                                    //                     ->whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                    //                     ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClass['class']);
                                    return $query->where([
                                                        cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->school_id,
                                                    ])
                                                    ->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($TeacherGradeClass['grades'],$TeacherGradeClass['class'],Auth::user()->school_id));
                                }) 
                                ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                                ->get();
                }
                return view('backend.leaderboard.student_leaderboard',compact('studentList'));
                break;
            case 3 :
                $studentList =UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                ->whereHas('user',function($query){
                    return $query->where([
                                            cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                            cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            // cn::USERS_GRADE_ID_COL  => Auth::user()->{cn::USERS_GRADE_ID_COL},
                                            // cn::USERS_CLASS_ID_COL  => Auth::user()->{cn::USERS_CLASS_ID_COL}
                                            // cn::USERS_GRADE_ID_COL  => Helper::GetCurriculumDataById($this->LoggedUserId(),$this->GetCurriculumYear(),'grade_id') ,
                                            // cn::USERS_CLASS_ID_COL  => Helper::GetCurriculumDataById($this->LoggedUserId(),$this->GetCurriculumYear(),'class_id') 
                                            cn::USERS_GRADE_ID_COL  => Auth::user()->CurriculumYearGradeId,
                                            cn::USERS_CLASS_ID_COL  => Auth::user()->CurriculumYearClassId
                                        ]);
                }) 
                ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                ->get();
                return view('backend.leaderboard.student_leaderboard',compact('studentList'));
                break;
            case 5 :
            case 7 :
                $studentList =UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                ->whereHas('user',function($query){
                    return $query->where([
                                            cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                            cn::USERS_SCHOOL_ID_COL => Auth::user()->school_id,
                                        ]);
                }) 
                ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                ->get();
                return view('backend.leaderboard.student_leaderboard',compact('studentList'));
                break;
        }
        // return view('backend.leaderboard.student_leaderboard',compact('studentList'));
    }

    /* Filtration on LeaderBoard type Dropdown   */
    public function filterLeaderBoardDetail(Request $request){
        $studentList = collect();
        $html = '';
        switch($request->LeaderBoardType){
            case 'credit_point':
                switch(Auth::user()->role_id){
                    case cn::TEACHER_ROLE_ID :
                        $GetAssignedGradeClassIds = [];
                        $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                        if(isset($TeacherGradeClass['grades']) && !empty($TeacherGradeClass['grades']) && isset($TeacherGradeClass['class']) && !empty($TeacherGradeClass['class'])){
                            $studentList = UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereHas('user',function($query) use($TeacherGradeClass){
                                            // return $query->where([
                                            //                         cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                            //                         cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            //                     ])
                                            //                     ->whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                            //                     ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClass['class']);
                                            return $query->where([
                                                        cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->school_id,
                                                    ])
                                                    ->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($TeacherGradeClass['grades'],$TeacherGradeClass['class'],Auth::user()->school_id));
                                        }) 
                                        ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                                        ->get();
                        }
                        $html = (string)View::make('backend.leaderboard.student_credit_point_leaderboard',compact('studentList'));
                        break;
                    case cn::STUDENT_ROLE_ID :
                        $studentList = UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->whereHas('user',function($query){
                            return $query->where([
                                                    cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                    // cn::USERS_GRADE_ID_COL  => Auth::user()->{cn::USERS_GRADE_ID_COL},
                                                    // cn::USERS_CLASS_ID_COL     => Auth::user()->{cn::USERS_CLASS_ID_COL}
                                                    // cn::USERS_GRADE_ID_COL  =>  Helper::GetCurriculumDataById($this->LoggedUserId(),$this->GetCurriculumYear(),'grade_id'),
                                                    // cn::USERS_CLASS_ID_COL  => Helper::GetCurriculumDataById($this->LoggedUserId(),$this->GetCurriculumYear(),'class_id') 
                                                    cn::USERS_GRADE_ID_COL  => Auth::user()->CurriculumYearGradeId,
                                                    cn::USERS_CLASS_ID_COL  => Auth::user()->CurriculumYearClassId 
                                                ]);
                        }) 
                        ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                        ->get();
                        $html = (string)View::make('backend.leaderboard.student_credit_point_leaderboard',compact('studentList'));
                        break;
                        
                    case cn::SCHOOL_ROLE_ID :
                    case cn::PRINCIPAL_ROLE_ID :
                        $studentList =UserCreditPoints::with('user')->where(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                        ->whereHas('user',function($query){
                            return $query->where([
                                                    cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                ]);
                        }) 
                        ->orderBy(cn::USER_NO_OF_CREDIT_POINTS_COL,'desc')
                        ->get();
                        $html = (string)View::make('backend.leaderboard.student_credit_point_leaderboard',compact('studentList'));
                        break;  
                }
                break;
            case 'overall_ability':
                switch(Auth::user()->role_id){
                    case cn::TEACHER_ROLE_ID :
                        $GetAssignedGradeClassIds = [];
                        $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                        if(isset($TeacherGradeClass['grades']) && !empty($TeacherGradeClass['grades']) && isset($TeacherGradeClass['class']) && !empty($TeacherGradeClass['class'])){
                            // $studentList = User::where([
                            //                     cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                            //                     cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            //                 ])
                            //                 ->whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClass['grades'])
                            //                 ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClass['class'])
                            //                 ->get();
                            $studentList = User::where([
                                                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                            cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                        ])
                                                        ->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($TeacherGradeClass['grades'],$TeacherGradeClass['class'],Auth::user()->{cn::USERS_SCHOOL_ID_COL}))
                                                        ->get();

                            $studentList = $studentList->sortByDesc(function($studentList){
                                return $studentList->NormalizedOverAllAbility;
                            });      
                        }
                        $html = (string)View::make('backend.leaderboard.student_overall_ability_leaderboard',compact('studentList'));
                        break;
                    case cn::STUDENT_ROLE_ID :
                        $studentList = User::where([
                                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                        // cn::USERS_GRADE_ID_COL  => Auth::user()->{cn::USERS_GRADE_ID_COL},
                                                        // cn::USERS_CLASS_ID_COL     => Auth::user()->{cn::USERS_CLASS_ID_COL}
                                                        cn::USERS_GRADE_ID_COL  => Auth::user()->CurriculumYearGradeId,
                                                        cn::USERS_CLASS_ID_COL     => Auth::user()->CurriculumYearClassId
                                                    ])
                                                    ->get();
                                                   
                                                    $studentList = $studentList->sortByDesc(function($studentList){
                                                        return $studentList->NormalizedOverAllAbility;
                                                    });
                            $html = (string)View::make('backend.leaderboard.student_overall_ability_leaderboard',compact('studentList'));
                            break;
                    case cn::SCHOOL_ROLE_ID :
                    case cn::PRINCIPAL_ROLE_ID :
                        $studentList = User::where([
                                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                    ])
                                                    ->get();
                        $studentList = $studentList->sortByDesc(function($studentList){
                            return $studentList->NormalizedOverAllAbility;
                        });
                        $html = (string)View::make('backend.leaderboard.student_overall_ability_leaderboard',compact('studentList'));
                        break;
                }
                break;
        }
        return $this->sendResponse($html);
    }
}
