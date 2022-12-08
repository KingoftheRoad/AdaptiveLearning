<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use Exception;
use App\Helpers\Helper;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Auth;
use App\Models\CurriculumYear;
use App\Models\Grades;
use App\Models\User;
use App\Models\ClassPromotionHistory;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\CurriculumYearStudentMappings;
use App\Models\RemainderUpdateSchoolYearData;
use Carbon\Carbon;

class ImportController extends Controller
{
    use common, ResponseFormat;

    public function __construct(){

    }

    /**
     * USE : Import CSV via upgrade student school year
     */
    public function StudentUpgradeSchoolYear(Request $request){
        try{
            if($request->isMethod('get')){
                $CurrentCurriculumYearId = Helper::getGlobalConfiguration('current_curriculum_year');
                $CurriculumYears = CurriculumYear::IsActiveYear()->get()->toArray();
                return view('backend.import.upgrade_student_school_year',compact('CurriculumYears','CurrentCurriculumYearId'));
            }

            // If After Importing CSV file
            if($request->isMethod('post')){
                $file = $request->file('csv_file');
                // File Details 
                $filename = $file->getClientOriginalName();
                $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
                $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
                $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

                $extension = $file->getClientOriginalExtension();
                $tempPath = $file->getRealPath();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Valid File Extensions
                $valid_extension = array("csv");

                // 2MB in Bytes
                $maxFileSize = 2097152;

                // Check file extension
                if(in_array(strtolower($extension),$valid_extension)){
                    // Check file size
                    if($fileSize <= $maxFileSize){
                        // File upload location
                        $location = 'uploads/student_upgrade_school_year';
                            
                        // Upload file
                        $file->move(public_path($location), $filename);

                        // Import CSV to Database
                        $filepath = public_path($location."/".$filename);
                                                                    
                        // Reading file
                        $file = fopen($filepath,"r");
                        $importData_arr = array();
                        $i = 0;

                        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                            $num = count($filedata );
                            // Skip first row (Remove below comment if you want to skip the first row)
                            if($i != 0){
                                for ($c=0; $c < $num; $c++) {
                                    $importData_arr[$i][] = $filedata [$c];
                                }   
                            }
                            $i++;
                        }
                        fclose($file);

                        // Default variable
                        $classId = null;

                        $PostRefrenceNumbers = array_column($importData_arr,'4');

                        if(isset($importData_arr) && !empty($importData_arr)){
                            // Insert to MySQL database
                            foreach($importData_arr as $importData){
                                // Find classId by classs name
                                if(isset($importData[5]) && !empty($importData[5])){
                                    // Check grade is already available or not
                                    $Grade = Grades::where(cn::GRADES_NAME_COL,$importData[5])->first();
                                    if(isset($Grade) && !empty($Grade)){
                                        $GradeClassMapping = GradeSchoolMappings::where([
                                                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                                                cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                                cn::GRADES_MAPPING_GRADE_ID_COL => $Grade->id
                                                            ])->first();
                                        if(isset($GradeClassMapping) && !empty($GradeClassMapping)){
                                            $gradeId = $Grade->id;
                                        }else{
                                            $GradeSchoolMappings =  GradeSchoolMappings::create([
                                                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                                        cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                                                        cn::GRADES_MAPPING_STATUS_COL    => 'active'
                                                                    ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }else{
                                        // If in the syaytem grade is not available then create new grade first
                                        $Grade = Grades::create([
                                                    cn::GRADES_NAME_COL => $importData[5],
                                                    cn::GRADES_CODE_COL => $importData[5],
                                                    cn::GRADES_STATUS_COL => 1
                                                ]);
                                        if($Grade){
                                            // Create grade and school mapping
                                            $GradeSchoolMappings =  GradeSchoolMappings::create([
                                                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                                        cn::GRADES_MAPPING_GRADE_ID_COL  => $Grade->id,
                                                                        cn::GRADES_MAPPING_STATUS_COL    => 'active'
                                                                    ]);
                                            if($GradeSchoolMappings){
                                                $gradeId = $Grade->id;
                                            }
                                        }
                                    }

                                    // Check class is already available in this school
                                    $ClassData = GradeClassMapping::where([
                                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->school_id,
                                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                                    cn::GRADE_CLASS_MAPPING_NAME_COL => strtoupper($importData[6])
                                                ])->first();
                                    if(isset($ClassData) && !empty($ClassData)){
                                        $classId = $ClassData->id;
                                        $className = $ClassData->name;
                                    }else{
                                        // If the class is not available into this school then create new class
                                        $ClassData =    GradeClassMapping::create([
                                                            cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL  => $gradeId,
                                                            cn::GRADE_CLASS_MAPPING_NAME_COL      => strtoupper($importData[6]),
                                                            cn::GRADE_CLASS_MAPPING_STATUS_COL    => 'active'
                                                        ]);
                                        if($ClassData){
                                            $classId = $ClassData->id;
                                            $className = $ClassData->name;
                                        }
                                    }
                                }

                                // Stire one variable into studentNumberWithInClass
                                $studentNumberWithInClass = '';
                                if(isset($importData[7]) && !empty($importData[7])){
                                    if(strlen($importData[7]) == 1){
                                        $studentNumberWithInClass = '0'.$importData[7];
                                    }else{
                                        $studentNumberWithInClass = $importData[7];
                                    }
                                }

                                // check user is already exists or not
                                $checkUserExists =  User::where([
                                                        cn::USERS_EMAIL_COL => $importData[0],
                                                        cn::USERS_SCHOOL_ID_COL => Auth()->user()->school_id,
                                                    ])->first();
                                if(!empty($checkUserExists)){                                
                                    // Store variable into student current grade-id
                                    $StudentCurrentGradeId = $checkUserExists->grade_id;

                                    // Store variable into student current class-id
                                    $StudentCurrentClassId = $checkUserExists->class_id;

                                    User::where(cn::USERS_ID_COL,$checkUserExists->id)->update([
                                        //cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                        cn::USERS_GRADE_ID_COL => $gradeId,
                                        cn::USERS_CLASS_ID_COL => $classId,
                                        cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                        cn::USERS_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_STATUS_COL => 'active',
                                        cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                        cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);

                                    // Create one record into School Year
                                    if(CurriculumYearStudentMappings::where([
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $checkUserExists->id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                        // cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        // cn::CURRICULUM_YEAR_STUDENT_CLASS => $Grade->name.''.$ClassData->name,
                                        // cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass
                                    ])->exists()
                                    ){
                                        // Update existing record
                                        CurriculumYearStudentMappings::where([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $checkUserExists->id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                        ])->Update([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $checkUserExists->id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId,
                                            cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                            cn::CURRICULUM_YEAR_STUDENT_CLASS => $Grade->name.''.$ClassData->name,
                                            cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass
                                        ]);
                                    }else{
                                        // Create new record
                                        CurriculumYearStudentMappings::Create([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $checkUserExists->id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId,
                                            cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                            cn::CURRICULUM_YEAR_STUDENT_CLASS => $Grade->name.''.$ClassData->name,
                                            cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass
                                        ]);
                                    }

                                    // Create Class Promotion History
                                    ClassPromotionHistory::updateOrCreate([
                                        cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL => $checkUserExists->id,
                                        cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL => (!empty($StudentCurrentGradeId)) ? $StudentCurrentGradeId : null,
                                        cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL => (!empty($StudentCurrentClassId)) ? $StudentCurrentClassId : null,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => $gradeId,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => $classId,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);
                                }else{
                                    // If user is not exists then create new student
                                    $LastCreatedUser = User::create([
                                        cn::USERS_EMAIL_COL =>   ($importData[0]) ? trim($importData[0]) :null,
                                        cn::USERS_PASSWORD_COL => ($importData[1]) ? Hash::make($this->setPassword(trim($importData[1]))) : null,
                                        cn::USERS_NAME_EN_COL => ($importData[2]) ? $this->encrypt(trim($importData[2])) : null,
                                        cn::USERS_NAME_CH_COL => ($importData[3]) ? $this->encrypt(trim($importData[3])) : null,
                                        cn::USERS_PERMANENT_REFERENCE_NUMBER => ($importData[4]) ? trim($importData[4]) : null,
                                        cn::USERS_GRADE_ID_COL => $gradeId,
                                        cn::USERS_CLASS_ID_COL => $classId,
                                        cn::STUDENT_NUMBER_WITHIN_CLASS => ($studentNumberWithInClass) ? $studentNumberWithInClass : null,
                                        cn::USERS_CLASS => $Grade->name.''.$ClassData->name,
                                        cn::USERS_CLASS_STUDENT_NUMBER => $Grade->name.$ClassData->name.$studentNumberWithInClass,
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::USERS_STATUS_COL => 'active',
                                        cn::USERS_IMPORT_DATE_COL => Carbon::now(),
                                        cn::USERS_CREATED_BY_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);

                                    // Create one record into School Year
                                    if(CurriculumYearStudentMappings::where([
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $LastCreatedUser->id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                    ])->exists()
                                    ){
                                        // Update existing record
                                        CurriculumYearStudentMappings::where([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $LastCreatedUser->id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                        ])->Update([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId
                                        ]);
                                    }else{
                                        // Create new record
                                        CurriculumYearStudentMappings::Create([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $request->curriculum_year_id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $LastCreatedUser->id,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => $gradeId,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => $classId
                                        ]);
                                    }

                                    // Create Class Promotion History
                                    ClassPromotionHistory::updateOrCreate([
                                        cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL => $LastCreatedUser->id,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => $gradeId,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => $classId,
                                        cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                    ]);
                                }
                            }
                        }
                        $this->StoreAuditLogFunction('','User','','','Student Imported successfully. file name '.$filepath,cn::USERS_TABLE_NAME,'');

                        // After Upgrade new student reminder table update for the school
                        RemainderUpdateSchoolYearData::where([
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $this->GetNextCurriculumYearId(),
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL => 'pending'
                        ])->Update([
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $this->GetNextCurriculumYearId(),
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_IMPORTED_DATE_COL => $this->CurrentDate(),
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_UPLOADED_BY_COL => Auth::user()->{cn::USERS_ID_COL},
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL => 'complete'
                        ]);

                        return redirect('Student')->with('success_msg', __('languages.student_upgraded_successfully'));
                    }
                }
            }
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}
