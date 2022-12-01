<?php
namespace App\Constants;

class DbConstant {

    const SUPERADMIN_ROLE_ID        = 1;
    const TEACHER_ROLE_ID           = 2;
    const STUDENT_ROLE_ID           = 3;
    const PARENT_ROLE_ID            = 4;
    const SCHOOL_ROLE_ID            = 5;
    const EXTERNAL_RESOURCE_ROLE_ID = 6;
    const PRINCIPAL_ROLE_ID = 7;

    //Default Grade 
    const DEFAULT_GRADE_NAME = '4';
    const DEFAULT_GRADE_CODE = '4';

    // Mandantory Subject
    const SUBJECTMATHEMATICS = 'Mathematics';
    const CODEMATHEMATICS    = 'MA';
    
    const DB_NAME = 'school_management';
    const DB_ENGINE_NAME = 'InnoDB';
    const DB_ENGINE_ONDELETE_NAME = 'cascade';

    const CREATED_AT_COL = 'created_at';
    const UPDATED_AT_COL = 'updated_at';
    const DELETED_AT_COL = 'deleted_at';

    // User Table
    const USERS_TABLE_NAME = 'users';
    const USERS_ID_COL = 'id';
    const USERS_ALP_CHAT_USER_ID_COL = "alp_chat_user_id";
    const USERS_NAME_COL = 'name';
    const USERS_NAME_EN_COL = 'name_en';
    const USERS_NAME_CH_COL = 'name_ch';
    const USERS_EMAIL_COL = 'email';
    const USERS_MOBILENO_COL ='mobile_no';
    const USERS_ADDRESS_COL ='address';
    const USERS_GENDER_COL ='gender';
    const USERS_CITY_COL ='city';
    const USERS_DATE_OF_BIRTH_COL ='dob';
    const USERS_PASSWORD_COL = 'password';
    const USERS_PROFILE_PHOTO_COL = 'profile_photo';
    const USERS_ROLE_ID_COL = 'role_id';
    const USERS_GRADE_ID_COL ='grade_id';
    const USERS_CLASS_ID_COL = 'class_id';
    const USERS_SCHOOL_ID_COL ='school_id';
    const USERS_PERMANENT_REFERENCE_NUMBER = 'permanent_reference_number';
    const STUDENT_NUMBER_WITHIN_CLASS = 'student_number_within_class';
    const USERS_CLASS = 'class';
    const USERS_CLASS_STUDENT_NUMBER = 'class_student_number';
    const USERS_STUDENT_NUMBER = 'student_number';
    const USERS_CLASS_COL = 'class_name';
    const USERS_CLASS_CLASS_STUDENT_NUMBER = 'class_class_student_number'; // Class + Class Student Number
    const USERS_OTHER_ROLES_COL = 'other_roles_id';
    const USERS_OVERALL_ABILITY_COL = 'overall_ability';
    const USERS_EMAIL_VERIFID_AT_COL = 'email_verified_at';
    const USERS_REMEMBER_TOKEN_COL = 'remember_token';
    const USERS_STATUS_COL = 'status';
    const USERS_CREATED_BY_COL = 'created_by';
    const USERS_CREATED_AT_COL = 'created_at';
    const USERS_UPDATED_AT_COL = 'updated_at';
    const USERS_DELETED_AT_COL = 'deleted_at';
    const USERS_IMPORT_DATE_COL = 'import_date';
    // Deleted Columns
    const USERS_ISADMIN_COL = 'is_admin';    
    const USERS_SECTION_ID_COL ='section_id';
    
    
    
    // Roles Table
    const ROLES_TABLE_NAME = 'roles';
    const ROLES_ID_COL = 'id';
    const ROLES_ROLE_NAME_COL = 'role_name';
    const ROLES_ROLE_SLUG_COL = 'role_slug';
    const ROLES_PERMISSION_COL = 'permission';
    const ROLES_STATUS_COL    = 'status';
    const ROLES_CREATED_AT_COL = 'created_at';
    const ROLES_UPDATED_AT_COL = 'updated_at';

    //Modules Table

    const MODULES_TABLE_NAME = 'modules';
    const MODULES_ID_COL     = 'id';
    const MODULES_MODULE_NAME_COL = 'module_name';
    const MODULES_MODULE_SLUG_COL = 'module_slug';
    const MODULES_STATUS_COL    = 'status';
    const MODULES_CREATED_AT_COL  = 'created_at';
    const MODULES_UPDATED_AT_COL  = 'updated_at';

    //SCHOOL TABLE

    const SCHOOL_TABLE_NAME = 'school';
    const SCHOOL_ID_COLS = 'id';
    const SCHOOL_SCHOOL_NAME_COL = 'school_name';
    const SCHOOL_SCHOOL_NAME_EN_COL = 'school_name_en';
    const SCHOOL_SCHOOL_NAME_CH_COL = 'school_name_ch';
    const SCHOOL_SCHOOL_CODE_COL = 'school_code';
    const SCHOOL_SCHOOL_EMAIL_COL = 'school_email';
    const SCHOOL_SCHOOL_ADDRESS = 'school_address';
    const SCHOOL_SCHOOL_ADDRESS_EN_COL = 'school_address_en';
    const SCHOOL_SCHOOL_ADDRESS_CH_COL = 'school_address_ch';
    const SCHOOL_SCHOOL_CITY = 'city';
    const SCHOOL_SCHOOL_STATUS = 'status';
    const SCHOOL_CREATED_AT_COL = 'created_dt';
    const SCHOOL_UPDATED_AT_COL = 'updated_dt';
    const SCHOOL_DELETED_AT_COL = 'deleted_dt';
    const SCHOOL_STARTTIME_COL = 'school_start_time';


    //CLASS TABLE
    const CLASS_TABLE_NAME = 'class';
    const CLASS_ID_COL = 'id';
    const CLASS_CLASS_NAME_COL = 'class_name';
    const CLASS_ACTIVE_STATUS_COL ='active_status';
    const CLASS_SCHOOL_ID_COL ='school_id';
    const CLASS_CREATED_AT_COL = 'created_dt';
    const CLASS_UPDATED_AT_COL = 'updated_dt';
    const CLASS_DELETED_AT_COL = 'deleted_dt';

     //SECTION
     const SECTION_TABLE_NAME ='section';
     const SECTION_ID_COL ='id';
     const SECTION_SECTION_NAME_COL ='section_name';
     const SECTION_ACTIVE_STATUS_COL ='active_status';
     const SECTION_CREATED_BY_COL ='created_by';
     const SECTION_UPDATED_BY_COL ='updated_by';
     const SECTION_SCHOOL_ID_COL='school_id';
     const SECTION_CREATED_AT_COL ='created_dt';
     const SECTION_UPDATED_AT_COL ='updated_dt';
     const SECTION_DELETED_AT_COL ='deleted_at';

    //QUESTION TABLE
    const QUESTION_TABLE_NAME = 'question';
    const QUESTION_TABLE_ID_COL = 'id';
    const QUESTION_OBJECTIVE_MAPPING_ID_COL = 'objective_mapping_id';
    const QUESTION_QUESTION_CODE_COL = 'question_code';
    const QUESTION_NAMING_STRUCTURE_CODE_COL = 'naming_structure_code';
    const QUESTION_QUESTION_UNIQUE_CODE_COL = 'question_unique_code';
    const QUESTION_MARKS_COL = 'marks';
    const QUESTION_CLASS_ID_COL ='class_id';
    const QUESTION_BANK_SECTION_ID_COL ='section_id';
    const QUESTION_BANK_UPDATED_BY_COL = 'updated_by';
    const QUESTION_BANK_SCHOOL_ID_COL = 'school_id';
    const QUESTION_QUESTION_EN_COL ='question_en';
    const QUESTION_QUESTION_CH_COL = 'question_ch';
    const QUESTION_QUESTION_TYPE_COL ='question_type';
    const QUESTION_DIFFICULTY_LEVEL_COL = 'dificulaty_level';
    const QUESTION_GENERAL_HINTS_EN = 'general_hints_en';
    const QUESTION_GENERAL_HINTS_CH = 'general_hints_ch';
    const QUESTION_GENERAL_HINTS_VIDEO_ID_EN = 'general_hints_video_id_en';
    const QUESTION_GENERAL_HINTS_VIDEO_ID_CH = 'general_hints_video_id_ch';
    const QUESTION_FULL_SOLUTION_EN = 'full_solution_en';
    const QUESTION_FULL_SOLUTION_CH = 'full_solution_ch';
    const QUESTION_E_COL = 'e';
    const QUESTION_F_COL = 'f';
    const QUESTION_G_COL = 'g';
    const QUESTION_IS_APPROVED_COL = "is_approved";
    const QUESTINON_STATUS_COL = 'status';
    const QUESTION_CREATED_AT_COL = 'created_at';
    const QUESTION_UPDATED_AT_COL = 'updated_at';
    const QUESTION_DELETED_AT_COL = 'deleted_at';

    
    //Answer Table
    const ANSWER_TABLE_NAME = 'answer';
    const ANSWER_ID_COL = 'id';
    const ANSWER_QUESTION_ID_COL = 'question_id';
    const ANSWER_ANSWER1_EN_COL = 'answer1_en';
    const ANSWER_ANSWER2_EN_COL = 'answer2_en';
    const ANSWER_ANSWER3_EN_COL = 'answer3_en';
    const ANSWER_ANSWER4_EN_COL ='answer4_en';
    const ANSWER_HINT_ANSWER1_EN_COL = 'hint_answer1_en';
    const ANSWER_HINT_ANSWER2_EN_COL = 'hint_answer2_en';
    const ANSWER_HINT_ANSWER3_EN_COL = 'hint_answer3_en';
    const ANSWER_HINT_ANSWER4_EN_COL = 'hint_answer4_en';
    const ANSWER_ANSWER1_CH_COL = 'answer1_ch';
    const ANSWER_ANSWER2_CH_COL = 'answer2_ch';
    const ANSWER_ANSWER3_CH_COL = 'answer3_ch';
    const ANSWER_ANSWER4_CH_COL = 'answer4_ch';
    const ANSWER_HINT_ANSWER1_CH_COL = 'hint_answer1_ch';
    const ANSWER_HINT_ANSWER2_CH_COL = 'hint_answer2_ch';
    const ANSWER_HINT_ANSWER3_CH_COL = 'hint_answer3_ch';
    const ANSWER_HINT_ANSWER4_CH_COL = 'hint_answer4_ch';
    const ANSWER_NODE_HINT_ANSWER1_EN_COL = 'node_hint_answer1_en';
    const ANSWER_NODE_HINT_ANSWER2_EN_COL = 'node_hint_answer2_en';
    const ANSWER_NODE_HINT_ANSWER3_EN_COL = 'node_hint_answer3_en';
    const ANSWER_NODE_HINT_ANSWER4_EN_COL = 'node_hint_answer4_en';
    const ANSWER_NODE_HINT_ANSWER1_CH_COL = 'node_hint_answer1_ch';
    const ANSWER_NODE_HINT_ANSWER2_CH_COL = 'node_hint_answer2_ch';
    const ANSWER_NODE_HINT_ANSWER3_CH_COL = 'node_hint_answer3_ch';
    const ANSWER_NODE_HINT_ANSWER4_CH_COL = 'node_hint_answer4_ch';
    const ANSWER1_NODE_RELATION_ID_EN_COL = 'answer1_node_relation_id_en';
    const ANSWER2_NODE_RELATION_ID_EN_COL = 'answer2_node_relation_id_en';
    const ANSWER3_NODE_RELATION_ID_EN_COL = 'answer3_node_relation_id_en';
    const ANSWER4_NODE_RELATION_ID_EN_COL = 'answer4_node_relation_id_en';
    const ANSWER1_NODE_RELATION_ID_CH_COL = 'answer1_node_relation_id_ch';
    const ANSWER2_NODE_RELATION_ID_CH_COL = 'answer2_node_relation_id_ch';
    const ANSWER3_NODE_RELATION_ID_CH_COL = 'answer3_node_relation_id_ch';
    const ANSWER4_NODE_RELATION_ID_CH_COL = 'answer4_node_relation_id_ch';
    const ANSWER_CORRECT_ANSWER_COL = 'correct_answer';
    const ANSWER_CORRECT_ANSWER_EN_COL = 'correct_answer_en';
    const ANSWER_CORRECT_ANSWER_CH_COL = 'correct_answer_ch';
    const ANSWER_CREATED_AT_COL = 'created_at';
    const ANSWER_UPDATED_AT_COL = 'updated_at';
    const ANSWER_DELETED_AT_COL = 'deleted_at';

    // Grades Table
    const GRADES_TABLE_NAME = 'grades';
    const GRADES_ID_COL = 'id';
    const GRADES_NAME_COL = 'name';
    const GRADES_CODE_COL = 'code';
    const GRADES_SCHOOL_ID_COL ='school_id';
    const GRADES_STATUS_COL = 'status';
    const GRADES_CREATED_AT_COL = 'created_at';
    const GRADES_UPDATED_AT_COL = 'updated_at';
    const GRADES_DELETED_AT_COL = 'deleted_at';

    // Subject Table
    const SUBJECTS_TABLE_NAME = 'subjects';
    const SUBJECTS_ID_COL = 'id';
    const SUBJECTS_NAME_COL = 'name';
    const SUBJECTS_CODE_COL = 'code';
    const SUBJECTS_CLASSIDS_COL = 'class_ids';
    const SUBJECTS_SCHOOL_ID_COL = 'school_id';
    const SUBJECTS_STATUS_COL = 'status';
    const SUBJECTS_CREATED_AT_COL = 'created_at';
    const SUBJECTS_UPDATED_AT_COL = 'updated_at';
    const SUBJECTS_DELETED_AT_COL = 'deleted_at';

    // Strands Table
    const STRANDS_TABLE_NAME = 'strands';
    const STRANDS_ID_COL = 'id';
    const STRANDS_NAME_COL = 'name';
    const STRANDS_NAME_EN_COL = 'name_en';
    const STRANDS_NAME_CH_COL = 'name_ch';
    const STRANDS_CODE_COL = 'code';
    const STRANDS_STATUS_COL = 'status';
    const STRANDS_CREATED_AT_COL = 'created_at';
    const STRANDS_UPDATED_AT_COL = 'updated_at';
    const STRANDS_DELETED_AT_COL = 'deleted_at';

    // Learning_Units Table
    const LEARNING_UNITS_TABLE_NAME = 'learning_units';
    const LEARNING_UNITS_ID_COL = 'id';
    const LEARNING_UNITS_NAME_COL = 'name';
    const LEARNING_UNITS_NAME_EN_COL = 'name_en';
    const LEARNING_UNITS_NAME_CH_COL = 'name_ch';
    const LEARNING_UNITS_STRANDID_COL = 'strand_id';
    const LEARNING_UNITS_CODE_COL = 'code';
    const LEARNING_UNITS_STATUS_COL = 'status';
    const LEARNING_UNITS_CREATED_AT_COL = 'created_at';
    const LEARNING_UNITS_UPDATED_AT_COL = 'updated_at';
    const LEARNING_UNITS_DELETED_AT_COL = 'deleted_at';

    // Learning Objectives Table
    const LEARNING_OBJECTIVES_TABLE_NAME = 'learning_objectives';
    const LEARNING_OBJECTIVES_ID_COL = 'id';
    const LEARNING_OBJECTIVES_STUDY_FOCI_COL = 'foci_number';
    const LEARNING_OBJECTIVES_TITLE_COL = 'title';
    const LEARNING_OBJECTIVES_TITLE_EN_COL ='title_en';
    const LEARNING_OBJECTIVES_TITLE_CH_COL ='title_ch';
    const LEARNING_OBJECTIVES_LEARNING_UNITID_COL = 'learning_unit_id';
    const LEARNING_OBJECTIVES_CODE_COL = 'code';
    const LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL  = 'is_available_questions';
    const LEARNING_OBJECTIVES_STATUS_COL = 'status';
    const LEARNING_OBJECTIVES_CREATED_AT_COL = 'created_at';
    const LEARNING_OBJECTIVES_UPDATED_AT_COL = 'updated_at';
    const LEARNING_OBJECTIVES_DELETED_AT_COL = 'deleted_at';

    // Learning Objectives Mappings
    const  OBJECTIVES_MAPPINGS_TABLE_NAME = 'strand_units_objectives_mappings';
    const  OBJECTIVES_MAPPINGS_ID_COL = 'id';
    const  OBJECTIVES_MAPPINGS_GRADE_ID_COL = 'grade_id';
    const  OBJECTIVES_MAPPINGS_SUBJECT_ID_COL = 'subject_id';
    const  OBJECTIVES_MAPPINGS_STRAND_ID_COL = 'strand_id';
    const  OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL = 'learning_unit_id';
    const  OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL = 'learning_objectives_id';

    //Online Exam

    const EXAM_TABLE_NAME = 'exam';
    const EXAM_TABLE_ID_COLS = 'id';
    const EXAM_TABLE_PARENT_EXAM_ID_COLS = 'parent_exam_id';
    const EXAM_TABLE_USE_OF_MODE_COLS = 'use_of_mode';
    const EXAM_TYPE_COLS = 'exam_type';
    //const EXAM_UNIQUE_ID_COLS = 'exam_unique_id';
    const EXAM_REFERENCE_NO_COL = 'reference_no';
    const EXAM_TABLE_TITLE_COLS = 'title';
    const EXAM_TABLE_SCHOOL_COLS = 'school_id';
    const EXAM_TABLE_FROM_DATE_COLS = 'from_date';
    const EXAM_TABLE_TO_DATE_COLS = 'to_date';
    const EXAM_TABLE_START_TIME_COL = 'start_time';
    const EXAM_TABLE_END_TIME_COL = 'end_time';
    const EXAM_TABLE_REPORT_TYPE_COLS = 'report_type';
    const EXAM_TABLE_RESULT_DATE_COLS = 'result_date';
    const EXAM_TABLE_PUBLISH_DATE_COL = 'publish_date';
    const EXAM_TABLE_TIME_DURATIONS_COLS = 'time_duration';
    const EXAM_TABLE_DESCRIPTION_COLS = 'description';
    const EXAM_TABLE_QUESTION_IDS_COL = 'question_ids';
    const EXAM_TABLE_STUDENT_IDS_COL = 'student_ids';
    const EXAM_TABLE_PEER_GROUP_IDS_COL = 'peer_group_ids';
    const EXAM_TABLE_GROUP_IDS_COL = 'group_ids';
    const EXAM_TABLE_IS_GROUP_TEST_COL = 'is_group_test';
    const EXAM_TABLE_STATUS_COLS = 'status';
    const EXAM_TABLE_RESULT_DECLARE_COL = 'result_declare';
    const EXAM_TABLE_IS_UNLIMITED = 'is_unlimited';
    const EXAM_TABLE_IS_TEACHING_REPORT_SYNC = 'is_teaching_report_sync';
    const EXAM_TABLE_TEMPLATE_ID = 'template_id';
    const EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL = 'self_learning_test_type';
    const EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL = 'no_of_trials_per_question';
    const EXAM_TABLE_DIFFICULTY_MODE_COL = 'difficulty_mode';
    const EXAM_TABLE_DIFFICULTY_LEVELS_COL = 'difficulty_levels';
    const EXAM_TABLE_IS_DISPLAY_HINTS_COL = 'display_hints';
    const EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL = 'display_full_solution';
    const EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL = 'display_pr_answer_hints';
    const EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL = 'randomize_answer';
    const EXAM_TABLE_IS_RANDOMIZED_ORDER_COL = 'randomize_order';
    const EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL = 'learning_objectives_configuration';
    const EXAM_TABLE_CREATED_BY_COL = 'created_by';
    const EXAM_TABLE_CREATED_BY_USER_COL = 'created_by_user';
    const EXAM_TABLE_ASSIGN_SCHOOL_STATUS = 'assign_school_status';
    const EXAM_TABLE_CREATED_AT = 'created_at';
    const EXAM_TABLE_UPDATED_AT = 'updated_at';
    const EXAM_TABLE_DELETED_AT = 'deleted_at';

    //Attempt_exam_student
    const ATTEMPT_EXAMS_TABLE_NAME = 'attempt_exams';
    const ATTEMPT_EXAMS_ID_COL = 'id';
    const ATTEMPT_EXAMS_EXAM_ID = 'exam_id';
    const ATTEMPT_EXAMS_STUDENT_STUDENT_ID = 'student_id';
    const ATTEMPT_EXAMS_STUDENT_GRADE_ID = 'grade_id';
    const ATTEMPT_EXAMS_STUDENT_CLASS_ID = 'class_id';
    const ATTEMPT_EXAMS_LANGUAGE_COL = 'language';
    const ATTEMPT_EXAMS_QUESTION_ANSWER_COL = 'question_answers';
    const ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL = 'attempt_first_trial';
    const ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL = 'attempt_second_trial';
    const ATTEMPT_EXAMS_WRONG_ANSWER_COL = 'attempt_wrong_answer';
    const ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS = 'total_correct_answers';
    const ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS = 'total_wrong_answers';
    const ATTEMPT_EXAMS_EXAM_TAKING_TIMING = 'exam_taking_timing';
    const ATTEMPT_EXAMS_STUDENT_ABILITY_COL = 'student_ability';
    const ATTEMPT_EXAMS_SERVER_DETAILS_COL = 'server_details';
    const ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL = 'before_exam_survey';
    const ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL = 'after_exam_survey';
    const ATTEMPT_EXAMS_STATUS_COL = "status";

    //Student Groups
    const STUDENT_GROUP_TABLE_NAME = 'student_group';
    const STUDENT_GROUP_ID_COL = 'id';
    const STUDENT_GROUP_NAME_COL = 'name';
    const STUDENT_GROUP_GRADE_ID_COL = 'grade_id';
    const STUDENT_GROUP_STUDENT_ID_COL = 'student_ids';
    const STUDENT_GROUP_EXAM_GROP_IDS_COL = 'exam_ids';
    const STUDENT_GROUP_SCHOOL_ID_COL = 'school_ids';
    const STUDENT_GROUP_STATUS_COL = 'status';

    // User login activities
    const LOGIN_ACTIVITIES_TABLE_NAME = 'login_activities';
    const LOGIN_ACTIVITIES_ID_COL = 'id';
    const LOGIN_ACTIVITIES_TYPE_COL = 'type';
    const LOGIN_ACTIVITIES_USER_ID_COL = 'user_id';
    const LOGIN_ACTIVITIES_USER_AGENT_ID_COL = 'user_agent';

    // Settings table
    const SETTINGS_TABLE_NAME = 'settings';
    const SETTINGS_ID_COL = 'id';
    const SETTINGS_SITE_NAME_COL = 'site_name';
    const SETTINGS_SITE_URL_COL = 'site_url';
    const SETTINGS_EMAIL_COL = 'email';
    const SETTINGS_CONTACT_NUMBER_COL = 'contact_number';
    const SETTINGS_FAV_ICON_COL = 'fav_icon';
    const SETTINGS_LOGO_IMAGE_COL = 'logo_image';
    const SETTINGS_SMTP_DRIVER_COL = 'smtp_driver';
    const SETTINGS_SMTP_HOST_COL = 'smtp_host';
    const SETTINGS_SMTP_PORT_COL = 'smtp_port';
    const SETTINGS_SMTP_USERNAME_COL = 'smtp_username';
    const SETTINGS_SMTP_EMAIL_COL = 'smtp_email';
    const SETTINGS_SMTP_PASSWORD_COL = 'smtp_passowrd';
    const SETTINGS_SMTP_ENCRYPTION_COL = 'smtp_encryption';

    // Teacher Table
    const TEACHER_TABLE_NAME = 'teacher';
    const TEACHER_ID_COL = 'id';
    const TEACHER_USERS_ID_COL = 'user_id';
    const TEACHER_NAME_COL = 'name';
    const TEACHER_EMAIL_COL = 'email';
    const TEACHER_MOBILE_NO_COL ='mobile_no';
    const TEACHER_ADDRESS_COL ='address';
    const TEACHER_DATE_OF_BIRTH_COL ='dob';
    const TEACHER_GENDER_COL ='gender';
    const TEACHER_SCHOOL_ID_COL ='school_id';
    const TEACHER_STATUS_COL = 'status';
    const TEACHER_CREATED_AT_COL = 'created_at';
    const TEACHER_UPDATED_AT_COL = 'updated_at';
    const TEACHER_DELETED_AT_COL = 'deleted_at';


    // teacher class subject management Table
    const TEACHER_CLASS_SUBJECT_TABLE_NAME = 'teachers_class_subject_assign';
    const TEACHER_CLASS_SUBJECT_ID_COL = 'id';
    const TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL ='school_id';
    const TEACHER_CLASS_SUBJECT_TEACHER_ID_COL ='teacher_id';
    const TEACHER_CLASS_SUBJECT_CLASS_ID_COL ='class_id';
    const TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL ='subject_id';
    const TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL = 'class_name_id';
    const TEACHER_CLASS_SUBJECT_STATUS_COL = 'status';
    const TEACHER_CLASS_SUBJECT_CREATED_AT_COL = 'created_at';
    const TEACHER_CLASS_SUBJECT_UPDATED_AT_COL = 'updated_at';
    const TEACHER_CLASS_SUBJECT_DELETED_AT_COL = 'deleted_at';

    // subject assign Table
    const SUBJECT_ASSIGN_TABLE_NAME = 'subject_assign';
    const SUBJECT_ASSIGN_ID_COL = 'id';
    const SUBJECT_ASSIGN_ASSIGN_ID_COL = 'assign_id';
    const SUBJECT_ASSIGN_SUBJECT_ID_COL = 'subject_id';
    const SUBJECT_ASSIGN_WEEKDAY_COL = 'week_day';
    const SUBJECT_ASSIGN_START_TIME_COL = 'start_time';
    const SUBJECT_ASSIGN_END_TIME_COL = 'end_time';


    //class_assignment_students table
    const CLASS_ASSIGNMENT_STUDENTS_TABLE_NAME = 'class_assignment_students';
    const CLASS_ASSIGNMENT_ID_COL = 'id';
    const CLASS_ASSIGNMENT_SCHOOL_ID_COL = 'school_id';
    const CLASS_ASSIGNMENT_CLASS_ID_COL = 'class_id';
    const CLASS_ASSIGNMENT_STUDENT_ID_COL = 'student_id';
    const CLASS_ASSIGNMENT_STATUS_COL = 'status';
    const CLASS_ASSIGNMENT_CREATED_AT_COL = 'created_at';
    const CLASS_ASSIGNMENT_UPDATED_AT_COL = 'updated_at';

    // Upload Documents Table
    const UPLOAD_DOCUMENTS_TABLE_NAME = 'upload_documents';
    const UPLOAD_DOCUMENTS_ID_COL = 'id';
    const UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID = 'document_mapping_id';
    const UPLOAD_DOCUMENTS_DOCUMENT_TYPE_COL = 'document_type';
    const UPLOAD_DOCUMENTS_NODE_ID = 'node_id';
    const UPLOAD_DOCUMENTS_STRAND_UNITS_MAPPING_ID_COL = 'strand_units_mapping_id';
    const UPLOAD_DOCUMENTS_TITLE_COL = 'title';
    const UPLOAD_DOCUMENTS_FILE_TYPE_COL =  'file_type';
    const UPLOAD_DOCUMENTS_FILE_NAME_COL = 'file_name';
    const UPLOAD_DOCUMENTS_FILE_PATH_COL = 'file_path';
    const UPLOAD_DOCUMENTS_THUMBNAIL_FILE_PATH_COL = 'thumbnail_file_path';
    const UPLOAD_DOCUMENTS_DESCRIPTION_EN_COL = 'description_en';
    const UPLOAD_DOCUMENTS_DESCRIPTION_CH_COL = 'description_ch';
    const UPLOAD_DOCUMENTS_UPLOAD_BY_COL = 'upload_by';
    const UPLOAD_DOCUMENTS_LANGUAGE_ID = 'language_id';
    const UPLOAD_DOCUMENTS_STATUS_COL = 'status';
    const UPLOAD_DOCUMENTS_CREATED_AT_COL = 'created_by';
    const UPLOAD_DOCUMENTS_UPDATED_BY_COL = 'updated_by';

    //Main upload Document
    const MAIN_UPLOAD_DOCUMENT_TABLE_NAME = 'main_upload_document';
    const MAIN_UPLOAD_DOCUMENT_ID_COL = 'id';
    const MAIN_UPLOAD_DOCUMENT_NODE_ID_COL = 'node_id';
    const MAIN_UPLOAD_DOCUMENT_STRAND_UNITS_MAPPING_ID_COL = 'strand_units_mapping_id';
    const MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL = 'file_name';
    const MAIN_UPLOAD_DOCUMENT_DESCRIPTION_EN_COL = 'description_en';
    const MAIN_UPLOAD_DOCUMENT_DESCRIPTION_CH_COL = 'description_ch';
    const MAIN_UPLOAD_DOCUMENT_UPLOAD_BY_COL = 'upload_by';
    const MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID = 'language_id';
    const MAIN_UPLOAD_DOCUMENT_STATUS_COL = 'status';
    
    // Class Subject Mapping Table
    const CLASS_SUBJECT_MAPPING_TABLE_NAME = 'class_subject_mapping';
    const CLASS_SUBJECT_MAPPING_ID_COL = 'id';
    const CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL = 'subject_id';
    const CLASS_SUBJECT_MAPPING_CLASS_ID_COL = 'class_id';
    const CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL = 'school_id';
    const CLASS_SUBJECT_MAPPING_STATUS_COL = 'status';

    // parent child  Mapping Table
    const PARANT_CHILD_MAPPING_TABLE_NAME = 'parent_child_mapping';
    const PARANT_CHILD_MAPPING_ID_COL = 'id';
    const PARANT_CHILD_MAPPING_PARENT_ID_COL = 'parent_id';
    const PARANT_CHILD_MAPPING_STUDENT_ID_COL = 'student_id';

    //Other Roles Table
    const OTHER_ROLE_TABLE_NAME = 'other_role';
    const OTHER_ROLE_ID_COL = 'id';
    const OTHER_ROLE_NAME_COL = 'role_name';
    const OTHER_ROLE_ACTIVE_STATUS_COL ='active_status';
    const OTHER_ROLE_CREATED_AT_COL = 'created_at';
    const OTHER_ROLE_UPDATED_AT_COL = 'updated_at';
    const OTHER_ROLE_DELETED_AT_COL = 'deleted_at';

    //Nodes Table
    const NODES_TABLE_NAME = 'nodes';
    const NODES_NODE_ID_COL = 'id';
    const NODES_MAIN_ID_COL = 'main_node_id';
    const NODES_FIRST_MAIN_ID_COL = 'first_main_node_id';
    const NODES_NODEID_COL = 'node_id';
    const NODES_SCHOOL_ID_COL = 'school_id';
    const NODES_NODE_TITLE_EN_COL = 'node_title_en';
    const NODES_NODE_TITLE_CH_COL = 'node_title_ch';
    const NODES_DESCRIPTION_EN_COL = 'node_description_en';
    const NODES_DESCRIPTION_CH_COL = 'node_description_ch';
    const NODES_WEAKNESS_NAME_EN_COL = 'weakness_name_en';
    const NODES_WEAKNESS_NAME_CH_COL = 'weakness_name_ch';
    const NODES_IS_MAIN_NODE_COL = 'is_main_node';
    const NODES_STATUS_COL = 'status';
    const NODES_CREATED_BY_COL ='created_by';
    const NODES_CREATED_AT_COL ='created_at';
    const NODES_UPDATED_AT_COL = 'updated_at';
    const NODES_DELETED_AT_COL = 'deleted_at';
    
    //Nodes relation Table
    const NODES_RELATION_TABLE_NAME = 'node_relation';
    const NODES_RELATION_ID_COL = 'id';
    const NODES_RELATION_PARENT_NODE_ID_COL = 'parent_node_id';
    const NODES_RELATION_CHILD_NODE_ID_COL = 'child_node_id';
    const NODES_RELATION_STATUS = 'status';
    const NODES_RELATION_CREATED_AT_COL ='created_at';
    const NODES_RELATION_UPDATED_AT_COL = 'updated_at';
    const NODES_RELATION_DELETED_AT_COL = 'deleted_at';

    // Test Template management
    const TEST_TEMPLATE_TABLE_NAME = 'test_templates';
    const TEST_TEMPLATE_ID_COL = 'id';
    const TEST_TEMPLATE_NAME_COL = 'name';
    const TEST_TEMPLATE_TYPE = "template_type";
    const TEST_TEMPLATE_DIFFICULTY_LEVEL_COL = 'difficulty_level';
    const TEST_TEMPLATE_QUESTION_IDS_COL = 'question_ids';
    const TEST_TEMPLATE_CREATED_BY = 'created_by';
    const TEST_TEMPLATE_STATUS = 'status';
    const TEST_TEMPLATE_CREATED_AT_COL ='created_at';
    const TEST_TEMPLATE_UPDATED_AT_COL = 'updated_at';
    const TEST_TEMPLATE_DELETED_AT_COL = 'deleted_at';
    
    
    //Audit Logs Table
    const AUDIT_LOGS_TABLE_NAME = 'audit_logs';
    const AUDIT_LOGS_ID_COL = 'id';
    const AUDIT_LOGS_ROLE_TYPE_COL = 'role_type';
    const AUDIT_LOGS_USER_ID_COL = 'logged_user_id';
    const AUDIT_LOGS_NAME_COL = 'log_name';
    const AUDIT_LOGS_PAYLOAD_COL = 'log_payload';
    const AUDIT_LOGS_TABLE_NAME_COL = 'table_name';
    const AUDIT_LOGS_CHILD_TABLE_NAME_COL = 'child_table_name';
    const AUDIT_LOGS_PAGE_NAME_COL = 'page_name';
    const AUDIT_LOGS_IP_ADDRESS_COL = 'ip_address';
    const AUDIT_LOGS_CREATED_AT_COL = 'created_at';
    const AUDIT_LOGS_UPDATED_AT_COL = 'updated_at';
    const AUDIT_LOGS_DELETED_AT_COL = 'deleted_at';

    // Grades School Mapping Table 

    const GRADES_MAPPING_TABLE_NAME = 'grades_school_mapping';
    const GRADES_MAPPING_ID_COL = 'id';
    const GRADES_MAPPING_SCHOOL_ID_COL = 'school_id';
    const GRADES_MAPPING_GRADE_ID_COL = 'grade_id';
    const GRADES_MAPPING_STATUS_COL  ='status';
    const GRADES_MAPPING_CREATED_AT_COL = 'created_at';
    const GRADES_MAPPING_UPDATED_AT_COL = 'updated_at';
    const GRADES_MAPPING_DELETED_AT_COL = 'deleted_at';


    // Subject School Mapping Table 

    const SUBJECT_MAPPING_TABLE_NAME = 'subjects_school_mapping';
    const SUBJECT_MAPPING_ID_COL = 'id';
    const SUBJECT_MAPPING_SCHOOL_ID_COL = 'school_id';
    const SUBJECT_MAPPING_SUBJECT_ID_COL = 'subject_id';
    const SUBJECT_MAPPING_STATUS_COL  ='status';
    const SUBJECT_MAPPING_CREATED_AT_COL = 'created_at';
    const SUBJECT_MAPPING_UPDATED_AT_COL = 'updated_at';
    const SUBJECT_MAPPING_DELETED_AT_COL = 'deleted_at';

    // Pre Configure Difficulty Table

    const PRE_CONFIGURE_DIFFICULTY_TABLE_NAME = 'pre_configured_difficulty';
    const PRE_CONFIGURE_DIFFICULTY_ID_COL     = 'id';
    const PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_COL = 'difficulty_level_name';
    const PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_EN_COL = 'difficulty_level_name_en';
    const PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_NAME_CH_COL = 'difficulty_level_name_ch';
    const PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL = 'difficulty_level_color';
    const PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL = 'difficulty_level';
    const PRE_CONFIGURE_DIFFICULTY_TITLE_COL = 'title';
    const PRE_CONFIGURE_DIFFICULTY_STATUS_COL = 'status';

    // Ai configure Difficulty Table

    const AI_CALCULATED_DIFFICULTY_TABLE_NAME = 'ai_calculated_difficulty';
    const AI_CALCULATED_DIFFICULTY_ID_COL     = 'id';
    const AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL = 'difficulty_level';
    const AI_CALCULATED_DIFFICULTY_TITLE_COL = 'title';
    const AI_CALCULATED_DIFFICULTY_STATUS_COL = 'status';

    // Grade Class Mapping Table
    const GRADE_CLASS_MAPPING_TABLE_NAME = 'grade_class_mapping';
    const GRADE_CLASS_MAPPING_ID_COL = 'id';
    const GRADE_CLASS_MAPPING_SCHOOL_ID_COL = 'school_id';
    const GRADE_CLASS_MAPPING_GRADE_ID_COL = 'grade_id';
    const GRADE_CLASS_MAPPING_NAME_COL = 'name';
    const GRADE_CLASS_MAPPING_STATUS_COL = 'status';

    // Global Configurations
    const GLOBAL_CONFIGURATION_TABLE_NAME = 'global_configuration';
    const GLOBAL_CONFIGURATION_ID_COL = 'id';
    const GLOBAL_CONFIGURATION_KEY_COL = 'key';
    const GLOBAL_CONFIGURATION_VALUE_COL = 'value';

    //Languages
    const LANGUAGES_TABLE_NAME = 'languages';
    const LANGUAGES_ID_COL ='id';
    const LANGUAGES_NAME_COL = 'name';
    const LANGUAGES_CODE_COL = 'code';

    // CLass Promotion History
    const CLASS_PROMOTION_HISTORY_TABLE_NAME = 'class_promotion_history';
    const CLASS_PROMOTION_HISTORY_ID_COL = 'id';
    const CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL = 'school_id';
    const CLASS_PROMOTION_HISTORY_STUDENT_ID_COL = 'student_id';
    const CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL = 'current_grade_id';
    const CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL = 'current_class_id';
    const CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL = 'promoted_grade_id';
    const CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL = 'promoted_class_id';
    const CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL = 'promoted_by_userid'; 

    // Exam Configurations Details
    const EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME = 'exam_configurations_details';
    const EXAM_CONFIGURATIONS_DETAILS_ID_COL = 'id';
    const EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL = 'exam_id';
    const EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL = 'created_by_user_id';
    const EXAM_CONFIGURATIONS_DETAILS_STRAND_IDS_COL = 'strand_ids';
    const EXAM_CONFIGURATIONS_DETAILS_LEARNING_UNIT_IDS_COL = 'learning_unit_ids';
    const EXAM_CONFIGURATIONS_DETAILS_LEARNING_OBJECTIVES_IDS_COL = 'learning_objectives_ids';
    const EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_MODE_COL = 'difficulty_mode';
    const EXAM_CONFIGURATIONS_DETAILS_DIFFICULTY_LEVELS_COL = 'difficulty_levels';
    const EXAM_CONFIGURATIONS_DETAILS_NO_OF_QUESTIONS_COL = 'no_of_questions';
    const EXAM_CONFIGURATIONS_DETAILS_TIME_DURATION_COL = 'time_duration';
    const EXAM_CONFIGURATIONS_DETAILS_CREATED_AT_COL = 'created_at';
    const EXAM_CONFIGURATIONS_DETAILS_UPDATED_AT_COL = 'updated_at';
    const EXAM_CONFIGURATIONS_DETAILS_DELETED_AT_COL = 'deleted_at';

    // password_resets Table
    const PASSWORD_RESETS_TABLE_NAME = 'password_resets';
    const PASSWORD_RESETS_EMAIL_COL = 'email';
    const PASSWORD_RESETS_TOKEN_COL = 'token';
    const PASSWORD_RESETS_CREATED_AT_COL = 'created_at';

    // peer_groups table
    const PEER_GROUP_TABLE_NAME = 'peer_group';
    const PEER_GROUP_ID_COL = 'id';
    const PEER_GROUP_DREAMSCHAT_GROUP_ID = 'dreamschat_group_id';
    const PEER_GROUP_SCHOOL_ID_COL = 'school_id';
    const PEER_GROUP_GROUP_NAME_COL = 'group_name';
    const PEER_GROUP_GROUP_PREFIX_COL = 'group_prefix';
    const PEER_GROUP_GROUP_NAME_EN_COL = 'group_name_en';
    const PEER_GROUP_GROUP_NAME_CH_COL = 'group_name_ch';
    const PEER_GROUP_CREATED_BY_TEACHER_ID_COL = 'created_by_teacher_id';
    const PEER_GROUP_CREATED_BY_USER_ID_COL = 'created_by_user_id';
    const PEER_GROUP_SUBJECT_ID_COL = "subject_id";
    const PEER_GROUP_CREATED_TYPE_COL = "created_type";
    const PEER_GROUP_AUTO_GROUP_BY_COL = "auto_group_by";
    const PEER_GROUP_STATUS_COL = 'status';
    const PEER_GROUP_CREATED_AT_COL = 'created_at';
    const PEER_GROUP_UPDATED_AT_COL = 'updated_at';
    const PEER_GROUP_DELETED_AT_COL = 'deleted_at';

    // peer_group_student_mapping
    const PEER_GROUP_MEMBERS_TABLE = 'peer_group_members';
    const PEER_GROUP_MEMBERS_ID_COL = 'id';
    const PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL = 'peer_group_id';
    const PEER_GROUP_MEMBERS_MEMBER_ID_COL = 'member_id';
    const PEER_GROUP_MEMBERS_STATUS_COL = 'status';
    const PEER_GROUP_MEMBERS_CREATED_AT_COL = 'created_at';
    const PEER_GROUP_MEMBERS_UPDATED_AT_COL = 'updated_at';
    const PEER_GROUP_MEMBERS_DELETED_AT_COL = 'deleted_at';

    // Curriculum Year Student Mapping
    const CURRICULUM_YEAR_STUDENT_MAPPING_TABLE = 'curriculum_year_student_mapping';
    const CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL = 'id';
    const CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL = 'user_id';
    //const CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULM_YEAR_COL = 'curriculm_year';
    const CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL = 'curriculum_year_id';
    const CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL = 'school_id';
    const CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL = 'grade_id';
    const CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL = 'class_id';
    const CURRICULUM_YEAR_STUDENT_MAPPING_STATUS_COL = 'status';
    const CURRICULUM_YEAR_STUDENT_MAPPING_CREATED_AT_COL = 'created_at';
    const CURRICULUM_YEAR_STUDENT_MAPPING_UPDATED_AT_COL = 'updated_at';
    const CURRICULUM_YEAR_STUDENT_MAPPING_DELETED_AT_COL = 'deleted_at';

    //Teaching Report Table
    const TEACHING_REPORT_TABLE = 'teaching_report';
    const TEACHING_REPORT_ID_COL = 'id';
    const TEACHING_REPORT_REPORT_TYPE_COL = 'report_type';
    const TEACHING_REPORT_STUDY_TYPE_COL = 'study_type';
    const TEACHING_REPORT_SCHOOL_ID_COL = 'school_id';
    const TEACHING_REPORT_EXAM_ID_COL = 'exam_id';
    const TEACHING_REPORT_GRADE_ID_COL = 'grade_id';
    const TEACHING_REPORT_CLASS_ID_COL = 'class_id';
    const TEACHING_REPORT_PEER_GROUP_ID = 'peer_group_id';
    const TEACHING_REPORT_GRADE_WITH_CLASS_COL = 'grade_with_class';
    const TEACHING_REPORT_STUDENT_IDS_COL = 'student_ids';
    const TEACHING_TABLE_NO_OF_STUDENTS_COL = 'no_of_students';
    const TEACHING_REPORT_STUDENT_PROGRESS_COL = 'student_progress';
    const TEACHING_REPORT_AVERAGE_ACCURACY_COL = 'average_accuracy';
    const TEACHING_REPORT_STUDY_STATUS_COL = 'study_status';
    const TEACHING_REPORT_QUESTIONS_DIFFICULTIES_COL = 'questions_difficulties';
    const TEACHING_REPORT_DATE_AND_TIME_COL = 'date_time';
    const TEACHING_REPORT_CREATED_AT_COL = 'created_at';
    const TEACHING_REPORT_UPDATED_AT_COL = 'updated_at';
    const TEACHING_REPORT_DELETED_AT_COL = 'deleted_at';

    //Study Report 
    const STUDY_REPORT_TABLE = 'study_report';
    const STUDY_REPORT_ID_COL = 'id';
    const STUDY_REPORT_REPORT_TYPE_COL = 'report_type';
    const STUDY_REPORT_STUDY_TYPE_COL ='study_type';
    const STUDY_REPORT_SCHOOL_ID_COL = 'school_id';
    const STUDY_REPORT_EXAM_ID_COL = 'exam_id';
    const STUDY_REPORT_STUDENT_ID_COL = 'student_id';
    const STUDY_REPORT_GRADE_ID_COL = 'grade_id';
    const STUDY_REPORT_CLASS_ID_COL = 'class_id';
    const STUDY_REPORT_AVERAGE_ACCURACY_COL = 'average_accuracy';
    const STUDY_REPORT_STUDY_STATUS_COL = 'study_status';
    const STUDY_REPORT_QUESTIONS_DIFFICULTIES_COL = 'questions_difficulties';
    const STUDY_REPORT_DATE_TIME_COL = 'date_time';

    //Exam Grade Class Mapping Table
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE = 'exam_school_grade_class_mapping';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL = 'id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL = 'school_id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL = 'grade_id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL = 'class_id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL = 'peer_group_id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL = 'exam_id';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL = 'student_ids';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL = 'start_time';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL = 'end_time';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL = 'start_date';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL = 'end_date';
    const EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL = 'status';

    // Exam School Mapping Table

    const EXAM_SCHOOL_MAPPING_TABLE = 'exam_school_mapping';
    const EXAM_SCHOOL_MAPPING_ID_COL = 'id';
    const EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL = 'school_id';
    const EXAM_SCHOOL_MAPPING_EXAM_ID_COL = 'exam_id';
    const EXAM_SCHOOL_MAPPING_STATUS_COL = 'status';

    //User Credit Points

    const USER_CREDIT_POINTS_TABLE      = 'user_credit_points';
    const USER_CREDIT_POINTS_ID_COL     = 'id';
    const USER_CREDIT_USER_ID_COL       = 'user_id';
    const USER_NO_OF_CREDIT_POINTS_COL  = 'no_of_credit_points';

    //User Credit Point History

    const USER_CREDIT_POINT_HISTORY     = 'user_credit_point_history';
    const USER_CREDIT_POINT_HISTORY_ID_COL = 'id';
    const USER_CREDIT_POINT_HISTORY_EXAM_ID_COL = 'exam_id';
    const USER_CREDIT_POINT_HISTORY_USER_ID_COL = 'user_id';
    const USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL = 'test_type';
    const USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL = 'self_learning_type';
    const USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL = 'credit_point_type';
    const USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL = 'no_of_credit_point';
    const USER_CREDIT_POINT_HISTORY_CREDIT_POINT_HISTORY_COL = 'credit_point_history';
    
    //Exam Credit Point Rules Mapping

    const EXAM_CREDIT_POINT_RULES_MAPPING_TABLE = 'exam_credit_point_rules_mapping';
    const EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL = 'id';
    const EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL = 'exam_id';
    const EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL = 'school_id';
    const EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL = 'credit_point_rules';
    const EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL = 'rules_value';
    const EXAM_CREDIT_POINT_RULES_MAPPING_STATUS_COL = 'status';

    // Intelligent Tutor Video Table
    const INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME = 'intelligent_tutor_videos';
    const INTELLIGENT_TUTOR_VIDEOS_ID_COL = 'id';
    const INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_MAPPING_ID = 'document_mapping_id';
    const INTELLIGENT_TUTOR_VIDEOS_DOCUMENT_TYPE_COL = 'document_type';
    const INTELLIGENT_TUTOR_VIDEOS_NODE_ID = 'node_id';
    const INTELLIGENT_TUTOR_VIDEOS_STRAND_UNITS_MAPPING_ID_COL = 'strand_units_mapping_id';
    const INTELLIGENT_TUTOR_VIDEOS_TITLE_COL = 'title';
    const INTELLIGENT_TUTOR_VIDEOS_FILE_TYPE_COL =  'file_type';
    const INTELLIGENT_TUTOR_VIDEOS_FILE_NAME_COL = 'file_name';
    const INTELLIGENT_TUTOR_VIDEOS_FILE_PATH_COL = 'file_path';
    const INTELLIGENT_TUTOR_VIDEOS_THUMBNAIL_FILE_PATH_COL = 'thumbnail_file_path';
    const INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_EN_COL = 'description_en';
    const INTELLIGENT_TUTOR_VIDEOS_DESCRIPTION_CH_COL = 'description_ch';
    const INTELLIGENT_TUTOR_VIDEOS_UPLOAD_BY_COL = 'upload_by';
    const INTELLIGENT_TUTOR_VIDEOS_LANGUAGE_ID = 'language_id';
    const INTELLIGENT_TUTOR_VIDEOS_STATUS_COL = 'status';
    const INTELLIGENT_TUTOR_VIDEOS_CREATED_AT_COL = 'created_by';
    const INTELLIGENT_TUTOR_VIDEOS_UPDATED_BY_COL = 'updated_by';

    // Attempt Exam Student Mapping
    const ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME = 'attempt_exam_student_mapping';
    const ATTEMPT_EXAM_STUDENT_MAPPING_ID_COL = 'id';
    const ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL = 'exam_id';
    const ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL = 'student_id';
    const ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL = 'status';

    // curriculum_year
    const CURRICULUM_YEAR_TABLE_NAME = "curriculum_years";
    const CURRICULUM_YEAR_ID_COL = 'id';
    const CURRICULUM_YEAR_YEAR_COL = 'year';
    const CURRICULUM_YEAR_STATUS_COL = 'status';
} 