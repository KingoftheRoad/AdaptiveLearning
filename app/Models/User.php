<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Role;
use App\Models\Grades;
use App\Models\Section;
use App\Models\School;
use App\Models\GradeClassMapping;
use App\Models\ClassPromotionHistory;
use App\Models\CurriculumYearStudentMappings;
use App\Constants\DbConstant as cn;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Http\Services\CreditPointService;

class User extends Authenticatable
{
    use Common, HasFactory, Notifiable, Sortable,SoftDeletes;

    public $CreditPointService;
    
    public function __construct(){
        $this->CreditPointService = new CreditPointService;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        cn::USERS_CURRICULUM_YEAR_ID_COL,
        cn::USERS_ALP_CHAT_USER_ID_COL,
        cn::USERS_ROLE_ID_COL,
        cn::USERS_GRADE_ID_COL,
        cn::USERS_CLASS_ID_COL,
        cn::USERS_SCHOOL_ID_COL,
        cn::USERS_STUDENT_NUMBER,
        cn::USERS_CLASS_COL,
        cn::USERS_CLASS_CLASS_STUDENT_NUMBER,
        cn::USERS_NAME_COL,
        cn::USERS_NAME_EN_COL,
        cn::USERS_NAME_CH_COL,
        cn::USERS_EMAIL_COL,
        cn::USERS_MOBILENO_COL,
        cn::USERS_ADDRESS_COL,
        cn::USERS_GENDER_COL,
        cn::USERS_CITY_COL,
        cn::USERS_DATE_OF_BIRTH_COL,
        cn::USERS_OTHER_ROLES_COL,
        cn::USERS_OVERALL_ABILITY_COL,
        cn::USERS_PASSWORD_COL,
        cn::USERS_STATUS_COL,
        cn::USERS_CREATED_BY_COL,
        cn::USERS_PERMANENT_REFERENCE_NUMBER,
        cn::STUDENT_NUMBER_WITHIN_CLASS,
        cn::USERS_CLASS,
        cn::USERS_IMPORT_DATE_COL,
        cn::USERS_CLASS_STUDENT_NUMBER,
    ];

    // Enable sortable columns name
    public $sortable = [
        cn::USERS_ROLE_ID_COL,
        cn::USERS_NAME_COL,
        cn::USERS_NAME_EN_COL,
        cn::USERS_NAME_CH_COL,
        cn::USERS_EMAIL_COL,
        cn::USERS_MOBILENO_COL, 
        cn::USERS_STUDENT_NUMBER,
        cn::USERS_CITY_COL,
        cn::USERS_DATE_OF_BIRTH_COL, 
        cn::USERS_GENDER_COL,
        cn::USERS_GRADE_ID_COL,
        cn::USERS_STATUS_COL,
        cn::USERS_DELETED_AT_COL,
        cn::USERS_PERMANENT_REFERENCE_NUMBER,
        cn::STUDENT_NUMBER_WITHIN_CLASS,
        cn::USERS_CLASS,
        cn::USERS_IMPORT_DATE_COL,
        cn::USERS_CLASS_STUDENT_NUMBER,
        cn::USERS_OVERALL_ABILITY_COL,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        cn::USERS_PASSWORD_COL,
        cn::USERS_REMEMBER_TOKEN_COL,
    ];

    protected $appends = [
        'DecryptNameEn','DecryptNameCh',
        'NormalizedOverAllAbility',
        'CurriculumYearData','CurriculumYearGradeId','CurriculumYearClassId',
        'CreditPoints'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        cn::USERS_EMAIL_VERIFID_AT_COL => 'datetime',
    ];

    /**
     * USE : Get the student available credit points
     */
    public function getCreditPointsAttribute(){
        $CreditPoints = [];
        $UserData = Self::find($this->id);
        if(!empty($this->id) && $UserData->role_id == cn::STUDENT_ROLE_ID){
            $CreditPoints = $this->CreditPointService->GetStudentCreditPoints($this->id);
        }
        return $CreditPoints;
    }

    /**
     * USE : Get the curriculum year data from selected year
     */
    public function getCurriculumYearDataAttribute(){
        $CurriculumYearData = [];
        $UserData = Self::find($this->id);
        if(!empty($this->id) && $UserData->role_id == cn::STUDENT_ROLE_ID){
            $CurriculumYearData = $this->GetStudentDataByCurriculumYear($this->GetCurriculumYear(),$this->id);
        }
        return $CurriculumYearData;
    }

    /**
     * USE : Get the curriculum year grade_id from selected year
     */
    public function getCurriculumYearGradeIdAttribute(){
        $CurriculumYearGradeId = 0;
        $UserData = Self::find($this->id);
        if(!empty($this->id) && $UserData->role_id == cn::STUDENT_ROLE_ID){
            $Data = $this->GetStudentDataByCurriculumYear($this->GetCurriculumYear(),$this->id);
            if(isset($Data) && !empty($Data)){
                $CurriculumYearGradeId = $Data['grade_id'];
            }
        }
        return $CurriculumYearGradeId;
    }

    /**
     * USE : Get the curriculum year class_id from selected year
     */
    public function getCurriculumYearClassIdAttribute(){
        $CurriculumYearClassId = 0;
        $UserData = Self::find($this->id);
        if(!empty($this->id) && $UserData->role_id == cn::STUDENT_ROLE_ID){
            $Data = $this->GetStudentDataByCurriculumYear($this->GetCurriculumYear(),$this->id);
            if(isset($Data) && !empty($Data)){
                $CurriculumYearClassId = $Data['class_id'];
            }
        }
        return $CurriculumYearClassId;
    }

    public function getNormalizedOverAllAbilityAttribute(){
        $overall_ability = null;
        if(!empty($this->overall_ability) && $this->role_id == cn::STUDENT_ROLE_ID){
            $overall_ability = Helper::getNormalizedAbility($this->overall_ability);
        }  
        return $overall_ability;
    }

    /**
     * USE : Get the decrypted user english name
     */
    public function getDecryptNameEnAttribute(){
        $name_en = null;
        if(!empty($this->name_en)){
            $name_en = $this->decrypt($this->name_en);
        }
        return $name_en;
    }

    /**
     * USE : Get the decrypted user chinese name
     */
    public function getDecryptNameChAttribute(){
        $name_ch = null;
        if(!empty($this->name_ch)){
            $name_ch = $this->decrypt($this->name_ch);
        }  
        return $name_ch;
    }

    /**
     ** Validation Rules for users
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::USERS_NAME_EN_COL => ['required'],
                    cn::USERS_NAME_CH_COL => ['required'],
                    cn::USERS_EMAIL_COL   => ['required', Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                    cn::USERS_PASSWORD_COL => ['required']
                ];
                if(auth()->user()->role_id == 1){
                    if($request->role == ''){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 2){
                        $rules = [
                            'role' => ['required'],
                            cn::USERS_EMAIL_COL   => [Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 3){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            'student_number' => ['required'],
                            'class_number' => ['required'],
                            cn::USERS_EMAIL_COL   => [Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 4){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            cn::USERS_EMAIL_COL   => [Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 5){
                        $rules = [
                            'role' => ['required'],
                            cn::USERS_EMAIL_COL   => [Rule::unique(cn::USERS_TABLE_NAME)->whereNull(cn::USERS_DELETED_AT_COL)],
                        ];
                    }
                }
                break;
            case 'update':
                $rules = [
                    cn::USERS_NAME_EN_COL => ['required'],
                    cn::USERS_NAME_CH_COL => ['required'],
                    cn::USERS_EMAIL_COL => ['required', Rule::unique(cn::USERS_TABLE_NAME)->ignore($id)->whereNull(cn::USERS_DELETED_AT_COL)],
                ];
                if(auth()->user()->role_id == 1){
                    if($request->role == ''){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 2){
                        $rules = [
                            'role' => ['required'],
                            'school' => ['required'],
                            cn::USERS_EMAIL_COL => ['required', Rule::unique(cn::USERS_TABLE_NAME)->ignore($id)->whereNull(cn::USERS_DELETED_AT_COL)],
                        ];
                    }
                    if($request->role == 3){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            'student_number' => ['required'],
                            'class_number' => ['required'],
                            cn::USERS_EMAIL_COL => [Rule::unique(cn::USERS_TABLE_NAME)->ignore($id)->whereNull(cn::USERS_DELETED_AT_COL)],
                            'school' => ['required']
                        ];
                    }
                    if($request->role == 4){
                        $rules = [
                            'grade_id' => ['required'],
                            'role' => ['required'],
                            'school' => ['required'],
                            cn::USERS_EMAIL_COL => ['required', Rule::unique(cn::USERS_TABLE_NAME)->ignore($id)->whereNull(cn::USERS_DELETED_AT_COL)],
                        ];
                    }
                    if($request->role == 5){
                        $rules = [
                            'role' => ['required'],
                            cn::USERS_EMAIL_COL => ['required', Rule::unique(cn::USERS_TABLE_NAME)->ignore($id)->whereNull(cn::USERS_DELETED_AT_COL)],
                        ];
                    }
                }
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for users
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::USERS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::USERS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::USERS_EMAIL_COL.'.required' => __('validation.please_enter_email'),
                    cn::USERS_EMAIL_COL.'.unique' => __('validation.email_already_exists'),
                    cn::USERS_PASSWORD_COL.'.required' => __('validation.please_enter_password')
                ];
                break;
            case 'update':
                $messages = [
                    cn::USERS_NAME_EN_COL.'.required' => __('validation.please_enter_english_name'),
                    cn::USERS_NAME_CH_COL.'.required' => __('validation.please_enter_chinese_name'),
                    cn::USERS_EMAIL_COL.'.required' => __('validation.please_enter_email'),
                    cn::USERS_EMAIL_COL.'.unique' => __('validation.email_already_exists'),
                    cn::USERS_PASSWORD_COL.'.required' => __('validation.please_enter_password')
                ];
                break;
        }
        return $messages;
    }
    public function promotionhistory(){
        return $this->hasMany(ClassPromotionHistory::class,cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL,cn::USERS_ID_COL)->orderBy(cn::CLASS_PROMOTION_HISTORY_ID_COL,'DESC');
    }

    public function roles(){
        return $this->belongsTo(Role::Class,cn::USERS_ROLE_ID_COL);
    }

    public function curriculum_year_mapping(){
        return $this->hasOne(CurriculumYearStudentMappings::Class,cn::CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL,'curriculum_year_mapping_id');
    }

    public function grades(){
        //return $this->hasOne(Grades::Class, cn::GRADES_ID_COL, cn::USERS_GRADE_ID_COL);
        return $this->hasOne(Grades::Class, cn::GRADES_ID_COL, 'CurriculumYearGradeId');
    }

    public function class(){
        //return $this->hasOne(GradeClassMapping::Class, cn::GRADE_CLASS_MAPPING_ID_COL, cn::USERS_CLASS_ID_COL);
        return $this->hasOne(GradeClassMapping::Class, cn::GRADE_CLASS_MAPPING_ID_COL, 'CurriculumYearClassId');
    }

    public function schools(){
        return $this->hasOne(School::Class, cn::SCHOOL_ID_COLS, cn::USERS_SCHOOL_ID_COL);
    }
    public function classes(){
        return $this->hasOne(GradeClassMapping::Class,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,cn::USERS_CLASS_ID_COL);
    }
    public function parentchild()
    {
        return $this->belongsToMany(User::class,ParentChildMapping::class,cn::PARANT_CHILD_MAPPING_PARENT_ID_COL,cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL);
    }
    

    public function getClassname($userid = null){
        $classNames = '';
        if(!empty($userid)){
            $userdata = User::find($userid);
            if(isset($userdata) && !empty($userdata)){
                //$classId = $userdata->class_id ?? null;
                $classId = $userdata->CurriculumYearClassId ?? null;
                if($classId){
                    $Result = GradeClassMapping::find($classId);
                    if(isset($Result) && !empty($Result)){
                        $classNames = $Result->name;
                    }
                }
            }
        }
        return $classNames;
    }

    public function getUserCreditPointHistory(){
        return $this->hasMany(UserCreditPointHistory::class,cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,cn::USERS_ID_COL)->orderBy(cn::USER_CREDIT_POINT_HISTORY_ID_COL,'DESC');
    }

    public function getUserCreditPoints(){
        return $this->hasOne(UserCreditPoints::class,cn::USER_CREDIT_USER_ID_COL,cn::USERS_ID_COL);
    }
}