<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Subjects;
use App\Constants\DbConstant as cn;
use Exception;
use App\Traits\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Models\User;
use App\Models\ClassSubjectMapping;
use App\Models\UploadDocuments;
use App\Helpers\Helper;

class StudentDashboardController extends Controller
{
    use Common;
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }
    
    public function index(){
        try{
            $AttemptedExamsIds = collect();
            $ExamList = collect();
            // $AttemptedExamsIds = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,Auth::user()->id)->pluck(cn::ATTEMPT_EXAMS_EXAM_ID);
            // if(isset($AttemptedExamsIds) && !empty($AttemptedExamsIds)){
            //     $ExamList = Exam::with('attempt_exams')->whereIn('id',$AttemptedExamsIds)->get();
            // }
            return view('backend.student_dashboard',compact('ExamList'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Get the students current class subjects
     */
    public function mySubjects(Request $request){
        try{
            if(!in_array('my_subjects_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return redirect('/');
            }
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('StudentMySubjectsList',$request);

            $subjectList = [];
            $items = $request->items ?? 10;
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            // $studentCurrectClassId = Auth::user()->grade_id;
            $studentCurrectClassId = Auth::user()->CurriculumYearGradeId;
            $SubjectIdsArray = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId,
                                cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL  => $studentCurrectClassId
                            ])->get()->pluck(cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL);
            if(isset($SubjectIdsArray) && !empty($SubjectIdsArray)){
                $subjectId = [];
                foreach($SubjectIdsArray as $subjectIds){
                    $subjectId[] = explode(',',$subjectIds);
                }
                $subjectId = call_user_func_array('array_merge', $subjectId);
                if(isset($subjectId) && !empty($subjectId)){
                    $subjectList = Subjects::whereIn(cn::SUBJECTS_ID_COL,$subjectId)->sortable()->paginate($items);
                    $Query = Subjects::select('*');
                    if(isset($request->filter)){
                        $Query->whereIn(cn::SUBJECTS_ID_COL,$subjectId);
                        if(isset($request->searchText) && !empty($request->searchText)){
                            $Query->where(cn::SUBJECTS_NAME_COL,'like','%'.$request->searchText.'%')->orWhere(cn::SUBJECTS_CODE_COL,'like','%'.$request->searchText.'%');
                        }
                        if(isset($request->status) && $request->status!=""){
                            $Query->where(cn::SUBJECTS_STATUS_COL,$request->status);
                        }
                        $subjectList = $Query->sortable()->paginate($items);
                    }
                    if(isset($request->status) && $request->status!=""){
                        $Query->where(cn::SUBJECTS_STATUS_COL,$request->status);
                    }
                    $subjectList = $Query->whereIn(cn::SUBJECTS_ID_COL,$subjectId)->sortable()->paginate($items);
                }
            }
            return view('backend.student.my_subjects',compact('subjectList','items'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get the Teachers list
     */
    public function myTeachers(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('StudentMyTeachersList',$request);
            $items = $request->items ?? 10;
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            // $GradeId = Auth::user()->grade_id;
            // $ClassId = Auth::user()->class_id;
            $GradeId = Auth::user()->CurriculumYearGradeId;
            $ClassId = Auth::user()->CurriculumYearClassId;
            $TeachersList = TeachersClassSubjectAssign::with('teachers')->where([
                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId,
                                cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $GradeId,
                                ])
                                ->whereRaw("find_in_set($ClassId,class_name_id)")
                                ->sortable()->paginate($items);
            if(isset($request->filter)){
                $Query = TeachersClassSubjectAssign::select('*');
                $Query->whereHas('teachers', function ($query) use($request, $Query) {
                        if(isset($request->searchText) && !empty($request->searchText)){
                            $query->where(cn::USERS_NAME_COL, 'like', '%'.$request->searchText.'%')->orWhere(cn::USERS_EMAIL_COL,'like','%'.$request->searchText.'%');
                        }
                        if(isset($request->status) && $request->status!=""){
                            if($request->status == 1){
                                $status = 'active';
                            }
                            if($request->status == 0){
                                $status = 'inactive';
                            }
                            $Query->where(cn::USERS_STATUS_COL,$status);
                        }
                    });
                $TeachersList = $Query->where([cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId, cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $GradeId])->sortable()->paginate($items);
            }
            return view('backend.student.my_teachers',compact('TeachersList','items'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function myclass(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('StudentMyClassesList',$request);
            $items = $request->items ?? 10;
            // $studentList = User::where([
            //                         cn::USERS_SCHOOL_ID_COL => Auth()->user()->{CN::USERS_SCHOOL_ID_COL},
            //                         cn::USERS_GRADE_ID_COL => Auth::user()->{CN::USERS_GRADE_ID_COL},
            //                         cn::USERS_CLASS_ID_COL => Auth::user()->{CN::USERS_CLASS_ID_COL},
            //                         cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
            //                         ])
            //                     ->where(cn::USERS_ID_COL,'<>',Auth()->user()->{CN::USERS_ID_COL})
            //                     ->sortable()
            //                     ->orderBy(cn::USERS_ID_COL,'DESC')
            //                     ->paginate($items);
            $studentList = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids(Auth::user()->{CN::USERS_GRADE_ID_COL},Auth::user()->{CN::USERS_CLASS_ID_COL},Auth::user()->{CN::USERS_SCHOOL_ID_COL}))
                                    ->where(cn::USERS_ID_COL,'<>',Auth()->user()->{CN::USERS_ID_COL})
                                    ->sortable()
                                    ->orderBy(cn::USERS_ID_COL,'DESC')
                                    ->paginate($items);
            $countSchoolData = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids(Auth::user()->{CN::USERS_GRADE_ID_COL},Auth::user()->{CN::USERS_CLASS_ID_COL},Auth::user()->{CN::USERS_SCHOOL_ID_COL}))
                                    ->where(cn::USERS_ID_COL,'<>',Auth()->user()->{CN::USERS_ID_COL})
                                    ->count();
            if(isset($request->filter)){
                // $Query = User::select('*')->where([
                //                 cn::USERS_SCHOOL_ID_COL => Auth()->user()->{CN::USERS_SCHOOL_ID_COL},
                //                 cn::USERS_GRADE_ID_COL => Auth::user()->{CN::USERS_GRADE_ID_COL},
                //                 cn::USERS_CLASS_ID_COL => Auth::user()->{CN::USERS_CLASS_ID_COL},
                //                 cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID]);
                $Query = User::select('*')->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                ->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids(Auth::user()->{CN::USERS_GRADE_ID_COL},Auth::user()->{CN::USERS_CLASS_ID_COL},Auth::user()->{CN::USERS_SCHOOL_ID_COL}));
                if(isset($request->Search)){
                    $Query->where(function($query) use ($request){
                        $query->Where(cn::USERS_EMAIL_COL,'like','%'.$request->Search.'%')
                        ->orWhere(cn::USERS_NAME_EN_COL,'like','%'.$request->Search.'%')
                        ->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$request->Search.'%');
                    });
                }
                $Query->where(cn::USERS_ID_COL,'<>',Auth()->user()->id);
                $studentList = $Query->sortable()->orderBy(cn::USERS_ID_COL,'DESC')->paginate($items);
            }
            return view('backend.student.class_list',compact('studentList','countSchoolData','items'));
            
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function studentProfile($id) {
        try{
            $profile = User::find($id);
            return view('backend.student.other_student_profile',compact('profile'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function getDocuments(Request $request){
        try{
            $uploadData = UploadDocuments::orderBy(cn::UPLOAD_DOCUMENTS_ID_COL,'DESC')->get();
            return view('backend.student.document_list',compact('uploadData'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function viewDoument(Request $request){
        $uploadDocumentID = substr(strrchr($request->fullUrl(), '//'), 1);
        $getData = UploadDocuments::find($uploadDocumentID);
        $url = $getData->file_path;
        $getFileName = explode("\\", $getData->file_path);
        $fileName = end($getFileName);
        $path = public_path($url);
        $contentType = mime_content_type($path);
        return response(File::get($path), 200)->header('Cache-Control', 'public')->header('Content-Description', 'File Transfer')
        ->header('Content-disposition', "attachment; filename=$fileName")->header('Content-Type', $contentType)
        ->header('Content-Transfer-Encoding', 'BINARY');
    }
}

