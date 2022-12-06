<?php
    
use Illuminate\Support\Facades\Route;

/******************************************************************************************************************************
 *  Start Cron Job Urls **
 * ****************************************************************************************************************************/
Route::get('update-students-overall-ability', 'CommonController@UpdateStudentAbility')->name('update-students-overall-ability');
Route::get('generate-math-formula-image', 'CommonController@GenerateMathFormulaImage')->name('generate-math-formula-image');
Route::get('updateMyTeachingReports', 'CronJobController@updateMyTeachingReports')->name('updateMyTeachingReports');
Route::get('updateMyTeachingTable', 'CronJobController@updateMyTeachingTable')->name('updateMyTeachingTable');
Route::get('remove-duplicate-student', 'CronJobController@RemoveDuplicateStudent')->name('remove-duplicate-student');
Route::get('updateStudyReports', 'MyStudyController@updateStudyReports')->name('updateStudyReports');
Route::get('updateClassId','CommonController@updateClassIdAsClassName')->name('updateClassId');
Route::get('updateQuestionDifficultyValue','CommonController@updateQuestionDifficultyValue')->name('updateQuestionDifficultyValue');
Route::get('UpdateAttemptedExamStudentMappings','CommonController@UpdateAttemptedExamStudentMappings')->name('UpdateAttemptedExamStudentMappings');
Route::get('UpdateExamReferenceNumber','CronJobController@UpdateExamReferenceNumber')->name('UpdateExamReferenceNumber');

Route::get('student/set-default-curriculum-year','CronJobController@SetDefaultCurriculumYear')->name('SetDefaultCurriculumYear');

Route::get('updateQuestionEColumn','CronJobController@updateQuestionEColumn')->name('updateQuestionEColumn');

Route::get('UpdateStudentSelectedAnswer','CronJobController@UpdateStudentSelectedAnswer')->name('UpdateStudentSelectedAnswer');

// Copy & clone current year school data to next curriculum year
Route::get('CopyCloneCurriculumYearSchoolData','CronJobController@CopyCloneCurriculumYearSchoolData')->name('CopyCloneCurriculumYearSchoolData');

// Update  global configuration next curriculum year
Route::get('UpdateGlobalConfigurationNextCurriculumYear','CronJobController@UpdateGlobalConfigurationNextCurriculumYear')->name('UpdateGlobalConfigurationNextCurriculumYear');

// Send remainder upgrade student in next curriculum year
Route::get('SendRemainderUploadStudentNewSchoolCurriculumYear','CronJobController@SendRemainderUploadStudentNewSchoolCurriculumYear')->name('SendRemainderUploadStudentNewSchoolCurriculumYear');

/******************************************************************************************************************************
 *  End Cron Job Urls **
 * ****************************************************************************************************************************/


Route::get('update_in_all_table_curriculum_year_id','CommonController@UpdateInAllTableCurriculumYearId');
Route::get('set-curriculum-class_student_number','CommonController@setClassStudentNumberColumnValue');
Route::get('set-curriculum-year','CommonController@AjaxSetCurriculumYear')->name('set-curriculum-year');
/**********************************************
 * Frontend Routes
 * ************************************************/
Route::get('passwordCreate', 'CommonController@passwordCreate');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/', function () {
	return view('frontend.homepage');
});
Route::get('/home', function () {
	return view('frontend.homepage');
});

Route::get('forget-password', 'Auth\ForgotPasswordController@showForgetPasswordForm')->name('forget.password.get');
Route::post('forget-password', 'Auth\ForgotPasswordController@submitForgetPasswordForm')->name('forget.password.post'); 
Route::get('reset-password/{token}', 'Auth\ForgotPasswordController@showResetPasswordForm')->name('reset.password.get');
Route::post('reset-password', 'Auth\ForgotPasswordController@submitResetPasswordForm')->name('reset.password.post');

/**************************************************
 * Authentication Routes
 * ************************************************/
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::match(['GET', 'POST'], 'super-admin/login', 'Auth\LoginController@index')->name('super-admin.login');
Route::match(['GET', 'POST'], 'login', 'Auth\LoginController@index')->name('login');
Route::match(['GET', 'POST'], 'loginCheck', 'Auth\LoginController@logincheck')->name('loginCheck');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

/*Learning - tutor */
Route::post('getMultiStrandsFromSubject', 'CommonController@getMultiStrandsFromSubject');
Route::post('getMultiLearningUnitFromStrands', 'CommonController@getMultiLearningUnitFromStrands');
Route::post('getMultiLearningObjectivesFromLearningUnits', 'CommonController@getMultiLearningObjectivesFromLearningUnits');
/*Learning - tutor*/

Route::post('getSubjectFromGrades', 'CommonController@getSubjectFromGrades');
Route::post('getStrandsFromSubject', 'CommonController@getStrandsFromSubject');
Route::post('getLearningUnitFromStrands', 'CommonController@getLearningUnitFromStrands');
Route::post('getLearningObjectivesFromLearningUnits', 'CommonController@getLearningObjectivesFromLearningUnits');
Route::get('test/getTimeDuration','CommonController@getTestTimeDuration');
// temporarily delete exam of self-learning.
Route::get('delete/selflearning','commonController@selfLearningExamStudentDelete');

// Get Multiple list routes
Route::post('getLearningUnitFromMultipleStrands', 'CommonController@getLearningUnitFromMultipleStrands');
Route::post('getLearningObjectivesFromMultipleLearningUnits', 'CommonController@getLearningObjectivesFromMultipleLearningUnits');


Route::get('getSubjectCodeById/{id}', 'CommonController@getSubjectCodeById')->name('getSubjectCodeById');
Route::post('countQuestionByMapping', 'CommonController@countQuestionByMapping')->name('countQuestionByMapping');
Route::get('check-question-code-exists', 'CommonController@checkQuestionCodeExists')->name('checkQuestionCodeExists');


/**************************************************
 * Multi language Routes
 * ************************************************/
Route::get('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'LanguageController@switchLang']);
Route::get('check-email-exists', 'CommonController@CheckEmailExists')->name('check-email-exists');

Route::group(['middleware'=>['auth']], function () {

    Route::match(['GET', 'POST'], 'change-password', 'UsersController@changePassword')->name('change-password');

    // Common Routes
    Route::get('set-sidebar-class', 'CommonController@setSidebarSessionClass')->name('set-sidebar-class');
    Route::get('getGradesBySchool','CommonController@getGradesBySchool')->name('getGradesBySchool');
    Route::get('getAllAssignQuestions','QuestionController@getAllAssignQuestions')->name('getAllAssignQuestions');
    
    //only for update json parameter in attempt exam table
    Route::get('exam-questions/update', 'CommonController@addLanguageTypeinJsonFormat');

    /***********************************************************************************
     * Backend Routes (Super Admin Panel)
     * *********************************************************************************/
    Route::get('super-admin', 'AdminDashboardController@index')->middleware(['admin'])->name('superadmin');
    Route::get('super-admin/dashboard', 'AdminDashboardController@index')->middleware(['admin'])->name('superadmin.dashboard');

    /** Start Questions Module Route **/
    Route::get('question/delete/{id}', 'QuestionController@destroy')->middleware(['admin'])->name('question.destroy');
    Route::get('question/video-hint','QuestionController@getVideos');
    Route::post('getSchoolNodes', 'QuestionController@getSchoolNodes')->middleware(['admin'])->name('getSchoolNodes');
    Route::post('getSubNodes', 'QuestionController@getSubNodes')->middleware(['admin'])->name('getSubNodes');
    Route::get('questions/export','ExportController@exportQuestions')->name('questions.export');
    Route::resource('questions', 'QuestionController');
    Route::get('question-bank/get-difficulty-value/{difficultyLevel}','CommonController@getDifficultyValue');
    Route::get('question-bank/get-ai-difficulty-value/{difficultyLevel}','CommonController@getAiDifficultyValue');
    Route::post('question-preview', 'QuestionController@questionPreview')->name('question-preview');
    Route::get('question-preview-list','QuestionController@questionListPreview')->name('question-preview-list');
    Route::post('update-question-verification','QuestionController@updateQuestionVerification')->name('update-question-verification');

    Route::get('question/copy-create/{id}','QuestionController@questionCopyAndCreate')->name('question.copy-create');
    /** End Questions Module Route Strat **/

    /** Strat User Manage Module Route **/
    Route::get('user/delete/{id}', 'UsersController@destroy')->middleware(['admin'])->name('user.destroy');
    Route::post('user/grade/{id}','UsersController@getGrades')->middleware(['admin'])->name('user.grade');//for get grade       
    Route::match(['GET', 'POST'], 'users/import', 'UsersController@importUsers')->name('users.import');
    Route::get('users/export','ExportController@exportUsers')->name('users.export');
    Route::resource('users','UsersController')->middleware(['admin']);
    Route::post('getstudentdata', 'UsersController@getstudentdata')->name('getstudentdata')->middleware('admin');

    Route::post('change-user-password','UsersController@changeUserPassword')->name('change-user-password');
    /** End User Manage Module Route **/

    /** Start Strands Module Route **/
    Route::get('strand/delete/{id}', 'StrandsController@destroy')->middleware(['admin']);
    Route::resource('strands','StrandsController')->middleware(['admin']);
    /** End Strands Module Route **/

    /** Start Learning Units Module Route **/
    Route::resource('learning_units','LearningUnitsController')->middleware(['admin']);
    Route::get('learning_units/delete/{id}', 'LearningUnitsController@destroy')->middleware(['admin']);
    /** End Learning Units Module Route **/

    /** Start Learning Objectives Module Route **/
    Route::resource('learning-objective','LearningObjectivesController')->middleware(['admin']);
    Route::get('learning-objective/delete/{id}', 'LearningObjectivesController@destroy')->middleware(['admin']);
    /** End Learning Objectives Module Route **/

    /** Strat Exam management Module Route **/
    // Route::post('exams/delete/multiple', 'ExamController@deleteMultipleExams')->name('exam.multiple.delete');
    Route::get('exams/delete/{id}', 'ExamController@destroy')->name('exam.destroy');
    Route::get('exams/questions/add/{id}', 'ExamController@CreateFormExamQuestions')->name('CreateFormExamQuestions');
    Route::get('exams/students/add/{id}', 'ExamController@CreateFormExamStudents')->name('CreateFormExamStudents');
    Route::post('generate-test-exercise', 'ExamController@generateTestExercise')->name('generate-test-exercise');
    
    Route::get('get-schools','ExamController@getSchools')->name('get-schools');
    
    Route::post('update-school-assign-status','ExamController@updateSchoolAssingStatus')->name('update-school-assign-status');
   
    /* Student leader board*/
    Route::get('student/leaderboard','LeaderBoardController@getLeaderBoardDetail')->name('student/leaderboard');
    Route::get('filter-leaderboard','LeaderBoardController@filterLeaderBoardDetail')->name('filter-leaderboard');
    
    Route::get('exams/attempt/students/{id}', 'ExamController@getListAttemptedExamsStudents')->name('getListAttemptedExamsStudents');

    Route::get('get_student_questions_by_difficulty_and_speed/{id}', 'ExamController@getStudentQuestionsByDifficultyAndSpeed')->name('getStudentQuestionsByDifficultyAndSpeed');

    Route::get('exams/result/{examid}/{studentid}', 'ExamController@getExamResult')->name('exams.result');
    Route::get('exam/result/{examid}/{studentid}','ExamController@getAdminExamResult')->name('adminexams.result');
    Route::get('exams/ajax/result/{examid}/{studentid}', 'ExamController@getAjaxExamResult');
    Route::get('exams/ajax/result/single-student/{examid}/{studentid}', 'ExamController@getAjaxExamSingleResult');
    Route::resource('exams','ExamController');
    /** End Exam management Module Route **/

    /** Strat Node Management Module Route **/
    Route::get('nodes/tree-view-list','NodesManagementController@getTreeViewListNodes')->name('nodes.tree-view-list');
    Route::Resource('nodes','NodesManagementController')->middleware(['admin']);
    Route::get('nodes/delete/{id}', 'NodesManagementController@destroy')->middleware(['admin'])->name('nodesmanagement.destroy');
    Route::get('get-childnode','NodesManagementController@getChildNodelistByParent')->middleware(['admin'])->name('getChildNodelistByParent');
    Route::get('get-nodelist-by-school', 'NodesManagementController@getNodelistBySchool')->middleware(['admin'])->name('nodesmanagement.getNodelistBySchool');
    Route::get('remove-parent-node/{id}','NodesManagementController@removeParentNode')->middleware(['admin'])->name('remove-parent-node');
    /** End Exam management Module Route **/

    /** Start Roles Management **/
    Route::resource('rolesmanagement','RolesManagementController')->middleware(['admin']);
    Route::get('rolesmanagement/delete/{id}', 'RolesManagementController@destroy')->middleware(['admin']);
    /** End Roles Management **/

    /** Start Modules Management **/
    Route::resource('modulesmanagement','ModulesManagementController')->middleware(['admin']);
    Route::get('modulesmanagement/delete/{id}', 'ModulesManagementController@destroy')->middleware(['admin']);
    /** End Modules Management **/

    /** Strat User Activity Route **/
    Route::resource('useractivity','UserActivityController')->middleware(['admin']);
    /** End User Activity Route **/

    /** Strat School Management Route **/
    Route::get('school/delete/{id}', 'SchoolController@destroy')->middleware(['admin']);
    Route::resource('schoolmanagement','SchoolController')->middleware(['admin']);
    /** End School Management Route **/

    Route::get('report/export/performance-report','ExportController@exportPerformanceReport')->name('export.performance-report');
     /** Strat admin reports routes **/
    Route::group(['namespace' => 'Reports'], function () {
        Route::get('getExamGroupGradeClassList','ClassTestReportController@getExamGroupGradeClassList')->name('getExamGroupGradeClassList');
        Route::get('report/class-test-reports/correct-incorrect-answer', 'ClassTestReportController@ClassTestResultCorrectIncorrectAnswers')->name('report.class-test-reports.correct-incorrect-answer');
        Route::get('report/ajax/class-test-reports/correct-incorrect-answer', 'ClassTestReportController@AjaxClassTestResultCorrectIncorrectAnswers');
        Route::get('reports/class-test-expand-report-student', 'ClassTestReportController@AjaxClassTestExpandReportStudent');
        Route::post('report/getPerformanceGraphCurrentStudent', 'AlpAiGraphController@getPerformanceGraphCurrentStudent');
        Route::post('report/getQuestionGraphCurrentStudent', 'AlpAiGraphController@getQuestionGraphCurrentStudent');
        
        Route::get('reports/progress-report', 'ClassTestReportController@ProgressReport');
        Route::get('reports/class-ability-analysis', 'ClassTestReportController@ClassAbilityAnalysisReport');
        Route::get('report/test-summary-report', 'ClassTestReportController@TestSummaryReport');

        Route::get('report/class-test-reports/student-correct-incorrect-answer', 'ClassTestReportController@StudentClassTestResultCorrectIncorrectAnswers')->name('report.class-test-reports.student-correct-incorrect-answer');

        Route::get('reports/exam-list', 'StudentPerformanceReports@getExamsList')->name('reports.exam-list');
        Route::get('reports/attempt-exams/student-list/{id}', 'StudentPerformanceReports@getListAttemptedExamsStudents')->name('reports.attempt-exams.student-list');
        Route::get('report/exams/student-test-performance', 'StudentPerformanceReports@getStudentPerformanceResults')->name('report.exams.student-test-performance');
        Route::get('report/school-comparisons', 'SchoolComparisonsReportController@getSchoolComparisonsReport')->name('report.school-comparisons');
        Route::match(['get','post'],'report/skill-weekness', 'GroupsSkillWeeknessReportController@getSkillWeeknessReport')->name('report.skill-weekness');
    
        // Progress Report routes
        Route::get('teacher/progress-report/learning-objective', 'ProgressReportController@TeacherProgressReportLearningObjective')->name('teacher.progress-report.learning-objective');
        Route::get('teacher/progress-report/learning-units', 'ProgressReportController@TeacherProgressReportLearningUnits')->name('teacher.progress-report.learning-units');
        Route::get('student/progress-report/learning-objective/{studentId}', 'ProgressReportController@StudentProgressReportLearningObjective')->name('student.progress-report.learning-objective');
        Route::get('student/progress-report/learning-units/{studentId}','ProgressReportController@StudentProgressReportLearningUnits')->name('student.progress-report.learning-units');
        Route::get('principal/progress-report/learning-objective', 'ProgressReportController@PrincipalProgressReportLearningObjective')->name('principal.progress-report.learning-objective');
        Route::get('principal/progress-report/learning-units', 'ProgressReportController@PrincipalProgressReportLearningUnits')->name('principal.progress-report.learning-units');
    });
    /** End admin reports route **/

    /** Start Admin settings route **/
    Route::match(['GET', 'POST'], 'settings', 'SettingsController@settings')->middleware(['admin'])->name('settings');
    Route::match(['GET', 'POST','PATCH'], 'global-configuration', 'GlobalConfigurationController@ConfigurationUpdate')->middleware(['admin'])->name('global-configuration');
    /** End Admin settings route **/

    /** Start Admin Pre-Configure Difficulty Level route **/
    Route::resource('pre-configure-difficulty','PreConfigureDifficultyController');
    Route::get('pre-configure-difficulty/delete/{id}', 'PreConfigureDifficultyController@destroy')->middleware(['admin']);
    /** End Admin Pre-Configure Difficulty Level route **/

    /** Start Admin Ai Calculated Difficulty Level route **/
    Route::resource('ai-calculated-difficulty','AiCalculatedDifficulty');
    Route::get('ai-calculated-difficulty/delete/{id}', 'AiCalculatedDifficulty@destroy')->middleware(['admin']);
    /** End Admin Ai Calculated Difficulty Level route **/

    /**Student Profile Start For Admin */
    Route::get('student-profile/{id}','TeacherDashboardController@studentsProfile')->name('student-profiles');
    /**Student Profile End For Admin */


    /***********************************************************************************
     * Backend Routes (Teacher Panel)
     * *********************************************************************************/
    Route::post('assign-student-in-group','TeacherDashboardController@assignStudentInGroup')->middleware(['teacher'])->name('assign-student-in-group');

    Route::get('teacher', 'TeacherDashboardController@index')->middleware(['teacher'])->name('teacher');
    Route::get('teacher/dashboard', 'TeacherDashboardController@index')->middleware(['teacher'])->name('teacher.dashboard');
    Route::get('teacher/profile','ProfileController@teacher_profile')->name('teacher.profile');
    Route::patch('teacher/update_profile/{id}','ProfileController@update_teacher_profile')->name('teacher.profile.update');
    // Route::get('teacher/students-profile/{id}','TeacherDashboardController@studentsProfile')->middleware(['teacher'])->name('teacher.student-profiles');
    Route::get('my-class', 'TeacherDashboardController@MyClass')->middleware(['teacher'])->name('my-class');
    Route::get('my-subject', 'TeacherDashboardController@MySubject')->middleware(['teacher'])->name('my-subject');
    Route::post('get_studentdata', 'UsersController@getStudentList')->name('getstudentdata')->middleware('teacher');
    Route::match(['GET', 'POST'],'my-teaching', 'ExamController@myTeaching')->name('myTeaching');
    Route::match(['GET', 'POST'],'myteaching/assignment-tests', 'MyTeachingController@getAssignmentTestList')->name('myteaching.assignment-tests');
    Route::match(['GET', 'POST'],'myteaching/assignment-exercise', 'MyTeachingController@getAssignmentExerciseList')->name('myteaching/assignment-exercise');
    Route::match(['GET', 'POST'],'myteaching/selflearning-tests', 'MyTeachingController@getSelfLearningTestList')->name('myteaching.selflearning-tests');
    Route::match(['GET', 'POST'],'myteaching/selflearning-exercise', 'MyTeachingController@getSelfLearningExerciseList')->name('myteaching/selflearning-exercise');
    // Route::get('myteaching/progress-report', 'TeacherController@LearningProgressReport')->name('myteaching.progress-report');
   
    Route::post('myteaching/student-progress-report', 'TeacherController@StudentProgressReport')->name('myteaching.student-progress-report');
    Route::get('myteaching/document-list','DocumentController@getAllDocuments')->name('myteaching.document-list');

    // Performance Analysis graph
    Route::post('my-teaching/get-class-ability-analysis-report', 'Reports\AlpAiGraphController@getClassAbilityAnalysisReport')->name('getClassAbilityAnalsisReport');
    Route::post('my-teaching/get-test-difficulty-analysis-report', 'Reports\AlpAiGraphController@getTestDifficultyAnalysisReport')->name('getTestDifficultyAnalysisReport');
    Route::get('GraphTest', 'Reports\AlpAiGraphController@GraphTest')->name('GraphTest');

    Route::get('student-result-summary','MyTeachingController@StudentResultSummaryReport')->name('student-result-summary');





    /***********************************************************************************
     * Backend Routes (Student Panel)
     * *********************************************************************************/
    Route::get('student', 'StudentDashboardController@index')->middleware(['student'])->name('student');
    Route::match(['GET', 'POST'],'student/exam', 'ExamController@getStudentExamList')->name('getStudentExamList');
    Route::match(['GET', 'POST'], 'student/exam/{id}', 'ExamController@studentAttemptExam')->middleware(['student'])->name('studentAttemptExam');
    Route::post('check-answer', 'ExamController@CheckAnswer')->middleware(['student'])->name('check-answer');
    Route::post('next-question', 'ExamController@NextQuestion')->middleware(['student'])->name('next-question');
    Route::post('estimate_student_competence_web', 'ExamController@estimateStudentCompetenceWeb')->name('estimate_student_competence_web');
    Route::get('student/exam/change-language/{id}', 'ExamController@studentChangeLanguageAttemtExam')->middleware(['student'])->name('student.exam.change-language');
    Route::post('student/answer/save', 'ExamController@studentExamAnswerSave')->middleware(['student'])->name('student.answer.save');
    Route::get('student/dashboard', 'StudentDashboardController@index')->middleware(['student'])->name('student.dashboard');
    Route::resource('profile','ProfileController');
    Route::patch('student/update_profile/{id}','ProfileController@update_student_profile')->name('student.profile.update');
    Route::get('student/mysubjects', 'StudentDashboardController@mySubjects')->middleware(['student'])->name('student.mysubjects');
    Route::get('student/myteachers', 'StudentDashboardController@myTeachers')->middleware(['student'])->name('student.myteachers');
    Route::get('student/myclass', 'StudentDashboardController@myclass')->middleware(['student'])->name('student.myclass');
    Route::get('student/myclass/student-profile/{id}','StudentDashboardController@studentProfile')->middleware(['student'])->name('student.myclass.student-profile');
    Route::get('student/documents','StudentDashboardController@getDocuments')->middleware(['student'])->name('student.documents');
    Route::get('student/documents/{id}','StudentDashboardController@viewDoument')->name('download-files');
    Route::resource('my-desk','StudentActivityController')->middleware(['student']);
    Route::get('template-questions-student','ExamController@getTemplateQuestions')->middleware(['student'])->name('template-questions-student');
    // Route::get('mystudy/progress-report', 'StudentController@LearningProgressReport')->name('mystudy.progress-report');
    Route::get('my-peer-group','PeerGroupController@GetStudentPeerGroupList')->name('my-peer-group');
    Route::match(['GET', 'POST'],'student/self-learning/exercise','StudentController@getSelfLearningExerciseList')->middleware(['student'])->name('student.self-learning-exercise');
    Route::match(['GET', 'POST'],'student/testing-zone','StudentController@getTestingZoneList')->middleware(['student'])->name('student.testing-zone');
    Route::get('getQuestionsStudentSelfLearningTest', 'QuestionController@getQuestionsStudentSelfLearningTest')->name('getQuestionsStudentSelfLearningTest');

    // Create Student Self Learning Exercise
    //Route::match(['GET', 'POST'],'student/create/self-learning-exercise','StudentController@CreateSelfLearningExercise')->middleware(['student'])->name('student.create.self-learning-exercise');
    //Route::match(['GET', 'POST'],'student/create/self-learning-test','StudentController@CreateSelfLearningTest')->middleware(['student'])->name('student.create.self-learning-test');
    Route::post('student/create-selflearning', 'StudentController@CreateSelfLearning');

    Route::match(['GET', 'POST'],'student/create/self-learning-test','RealTimeAIQuestionGeneratorController@CreateSelfLearningTest')->middleware(['student'])->name('student.create.self-learning-test');
    Route::post('generate-question/self-learning/test', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningTest');
    Route::post('generate-question/self-learning/test/next-question', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningTestNextQuestion');
    Route::post('self-learning/test/save', 'RealTimeAIQuestionGeneratorController@SaveSelfLearningTest');
    Route::post('generate-question/self-learning/test/change-language', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningTestChangeLanguage');

    Route::match(['GET', 'POST'],'student/create/self-learning-exercise','RealTimeAIQuestionGeneratorController@CreateSelfLearningExercise')->middleware(['student'])->name('student.create.self-learning-exercise');
    Route::post('generate-question/self-learning/exercise', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningExercise');
    Route::post('generate-question/self-learning/exercise/next-question', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningExerciseNextQuestion');
    Route::post('self-learning/exercise/save', 'RealTimeAIQuestionGeneratorController@SaveSelfLearningExercise');
    Route::post('generate-question/self-learning/exercise/change-language', 'RealTimeAIQuestionGeneratorController@GenerateQuestionSelfLearningExerciseChangeLanguage');
      


    
    /***********************************************************************************
     * Backend Routes (Parent Panel)
     * *********************************************************************************/
    Route::get('parent', 'ParentDashboardController@index')->middleware(['parent'])->name('parent');
    Route::get('parent/dashboard', 'ParentDashboardController@index')->middleware(['parent'])->name('parent.dashboard');
    Route::get('parent/list', 'ParentDashboardController@ChildList')->middleware(['parent'])->name('parent.list');
    Route::get('parent/child/teacher/{id}','ParentDashboardController@GetTeacherList')->middleware(['parent'])->name('teacher-list');
    Route::get('parent/child/subject/{id}','ParentDashboardController@GetSubjectList')->middleware(['parent'])->name('subject-list');





    /***********************************************************************************
     * Backend Routes (School Panel)
     * *********************************************************************************/
    Route::get('schools', 'SchoolDashboardController@index')->middleware(['school'])->name('schools');
    Route::get('schools/dashboard', 'SchoolDashboardController@index')->middleware(['school'])->name('schools.dashboard');
    
    Route::get('schoolprofile', 'SchoolDashboardController@SchoolProfile')->middleware(['school'])->name('schoolprofile');
    Route::post('schoolprofileupdate', 'SchoolDashboardController@SchoolProfileUpdate')->middleware(['school'])->name('schoolprofileupdate');

    Route::resource('teacher','TeacherController')->middleware(['school']);
    Route::get('teacher/delete/{id}', 'TeacherController@destroy')->middleware(['school'])->name('teacher.destroy');
    
    Route::get('subject/delete/{id}', 'SubjectController@destroy')->middleware(['school'])->name('subject.destroy');
    Route::resource('subject','SubjectController')->middleware(['school']);

    Route::resource('class','ClassController')->middleware(['school']);
    Route::get('class/delete/{id}', 'ClassController@destroy')->middleware(['school'])->name('class.destroy');
    
    Route::resource('teacher-class-subject-assign','TeachersClassSubjectController')->middleware(['school']);
    Route::get('get-class-type','TeachersClassSubjectController@getClassType')->name('get-class-type');
    Route::get('get-performance-report-class-type','TeachersClassSubjectController@getPerformanceReportClassType')->name('get-performance-report-class-type');
    Route::get('class-promotion-history/{id}','StudentController@ClassPromotionHistory')->name('class-promotion-history');
    Route::get('teacher-class-subject-assign/delete/{id}', 'TeachersClassSubjectController@destroy')->middleware(['school'])->name('teacher-class-subject-assign.destroy');
    Route::post('chechteacherid', 'TeachersClassSubjectController@chechteacherid')->name('chechteacherid')->middleware('school');

    // Route::match(['GET', 'POST'],'student/import/upgrade-school-year','ImportController@StudentUpgradeSchoolYear')->middleware(['school'])->name('student.import.upgrade-school-year');\
    Route::match(['GET', 'POST'],'student/import/upgrade-school-year','ImportController@StudentUpgradeSchoolYear')->name('student.import.upgrade-school-year');

    Route::resource('Student','StudentController');
    Route::get('export-student','ExportController@exportStudents')->name('students-export');
    Route::get('Student/delete/{id}','StudentController@destroy')->name('student.destroy');
    Route::post('AddGrade', 'StudentController@AddGrade')->name('AddGrade');
    Route::post('class-promotion','StudentController@classpromotion')->name('class-promotion');
    Route::get('student/mycalendar', 'StudentController@myCalendar')->middleware(['student'])->name('student.mycalendar');
    Route::post('selectMonthData', 'StudentController@selectMonthData')->name('selectMonthData');

    //assign Students
    Route::get('school/class/assign-students/{schoolid}','ClassController@AssignStudentForm')->middleware(['school'])->name('assign-student');
    Route::post('school/class/addStudent/{studentid}','ClassController@StoreSingleStudent')->middleware(['school'])->name('StoreSingleStudent');
    Route::post('school/class/addStudents/{studentid}','ClassController@StoreAllStudents')->middleware(['school'])->name('StoreAllStudents');
    Route::match(['GET', 'POST'], 'school/class/importStudent', 'UsersController@ImportStudents')->name('ImportStudents');
    Route::match('POST', 'school/class/ImportStudentsDataCheck', 'UsersController@ImportStudentsDataCheck')->name('ImportStudentsDataCheck');
    Route::match('POST', 'school/class/ImportStudentsData', 'UsersController@ImportStudentsData')->name('ImportStudentsData');

    Route::match(['GET', 'POST'],'school/selflearning-tests', 'PrincipalController@getSelfLearningTestList')->name('school.selflearning-tests');
    Route::match(['GET', 'POST'],'school/selflearning-exercise', 'PrincipalController@getSelfLearningExerciseList')->name('school.selflearning-exercise');

    Route::match(['GET', 'POST'],'school/assignment-exercise', 'PrincipalController@getAssignmentExerciseList')->name('school.assignment-exercise');
    Route::match(['GET', 'POST'],'school/assignment-tests', 'PrincipalController@getAssignmentTestList')->name('school.assignment-tests');

    //Sub Admin
    Route::resource('sub-admin','SubAdminController');
    Route::get('sub-admin/delete/{id}','SubAdminController@destroy')->name('sub-admin.destroy');
    





    /***********************************************************************************
    * Backend Routes (External Resource Panel)
    * *********************************************************************************/
    Route::get('external_resource/dashboard', 'ExternalResourceDashboardController@index')->middleware(['external_resource'])->name('external_resource.dashboard');
    Route::resource('upload-documents','DocumentController');
    Route::get('upload-documents/delete/{id}','DocumentController@destroy')->name('upload-documents.destroy');

    Route::resource('intelligent-tutor','IntelligentTutorController');
    Route::get('intelligent-tutor/delete/{id}','IntelligentTutorController@destroy')->name('intelligent-tutor.destroy');
    Route::get('add-more-document','IntelligentTutorController@AddMoreVideoFiles')->name('add-more-document');

    Route::get('upload-documents/removefile/{id}','DocumentController@removeSingleFileFromDatabase')->name('upload-documents.removefile');
    Route::post('exam-documents/{type}','DocumentController@getExamDocument')->name('exam-documents');
    Route::post('study-documents', 'DocumentController@getExamAllDocument')->name('study-documents');

    Route::get('get-class-type-by-admin','CommonController@getClassTypeByAdmin')->name('get-class-type-by-admin');
    Route::get('get-test-list-by-gradeclass','CommonController@getTestListByGradeAndClass')->name('get-test-list-by-gradeclass');
    Route::get('get-exam-info/{id}','CommonController@getExamInfo')->name('get-exam-info');


    /***********************************************************************************
    * Peer Groups Routes
    * *********************************************************************************/
    Route::get('peer-group/remove-member','PeerGroupController@removeMember');
    Route::post('peer-group/memberlist','PeerGroupController@memberlist');
    Route::post('peer-group/get-selected-memberlist','PeerGroupController@getSelectedMemberList');
    Route::get('get-studentlist-by-grade-class','PeerGroupController@getStudentListByGradeClass')->name('getStudentListByGradeClass');
    Route::resource('peer-group','PeerGroupController');
    Route::get('get-user-info', 'CommonController@GetUserInfo')->name('get-user-info');
    Route::get('auto-peer-group','PeerGroupController@createViewAutoPeerGroup')->name('auto-peer-group');
    Route::post('create-auto-peer-group','PeerGroupController@createAutoPeerGroup')->name('create-auto-peer-group');
    Route::get('update-group-id-auto-peer-group/{id}/{group_id}','PeerGroupController@updateGroupIdAutoPeerGroup')->name('update-group-id-auto-peer-group');

    
    /**
     * USE : Principal routes
     */
    Route::match(['GET', 'POST'],'principal/selflearning-tests', 'PrincipalController@getSelfLearningTestList')->name('principal.selflearning-tests');
    Route::match(['GET', 'POST'],'principal/selflearning-exercise', 'PrincipalController@getSelfLearningExerciseList')->name('principal.selflearning-exercise');
    Route::match(['GET', 'POST'],'principal/assignment-exercise', 'PrincipalController@getAssignmentExerciseList')->name('principal.assignment-exercise');
    Route::match(['GET', 'POST'],'principal/assignment-tests', 'PrincipalController@getAssignmentTestList')->name('principal.assignment-tests');
    
    // Route::get('principal/myteaching/progress-report', 'PrincipalController@LearningProgressReport')->name('principal.myteaching.progress-report');
    Route::get('principal/dashboard', 'PrincipalController@Dashboard')->name('principal.dashboard');
    Route::get('principal/delete/{id}', 'PrincipalController@destroy')->name('principal.destroy');
    Route::resource('principal','PrincipalController');

    //Question Generator flow common routes
    Route::get('get-late-commerce-student-list','QuestionGeneratorController@GetLateCommerceStudentList')->name('get-late-commerce-student-list');
    Route::post('add-test-late-commerce-student-peer-group','QuestionGeneratorController@AddLateCommerceStudentOrPeerGroup')->name('add-test-late-commerce-student-peer-group');


    // Question Generator flow Teacher-panel
    Route::post('question-generator/getQuestionIdsFromLearningObjectives', 'QuestionGeneratorController@getQuestionIdsFromLearningObjectives');
    Route::get('question-generator/get-students-list', 'QuestionGeneratorController@getStudentListByGradeClass');

    // Question Generator flow for super-admin
    Route::post('add-more-schools','QuestionGeneratorController@addMoreSchools')->name('add-more-schools');
    Route::post('exam/status/update', 'QuestionGeneratorController@ExamStatusUpdate')->name('exam.status.update');
    Route::get('question-wizard/update-status-for-grade-class-peer-group', 'QuestionGeneratorController@updateStatusGradeClassPeerGroup')->name('question-wizard.updateStatusGradeClassPeerGroup');
    Route::get('question-wizard', 'QuestionGeneratorController@QuestionWizardList')->name('question-wizard');
    Route::match(['GET', 'POST'],'super-admin/generate-questions', 'QuestionGeneratorController@superAdminGenerateTestQuestion')->name('super-admin.generate-questions');
    Route::post('question-generator/get-questions-id-learning-objectives-admin', 'QuestionGeneratorController@getQuestionIdsFromLearningObjectivesByAdmin');
    Route::get('get-question-hint/{id}', 'QuestionController@getQuestionHint');
    Route::post('get-refresh-question', 'QuestionGeneratorController@getRefreshQuestion');
    Route::match(['GET', 'POST'],'super-admin/generate-questions-edit/{id}', 'QuestionGeneratorController@superAdminGenerateTestQuestionEdit')->name('super-admin.generate-questions-edit');
    Route::match(['GET', 'POST'],'generate-questions', 'QuestionGeneratorController@schoolGenerateTestQuestion')->name('school.generate-questions');
    Route::match(['GET', 'POST'],'generate-questions-edit/{id}', 'QuestionGeneratorController@schoolGenerateTestQuestionEdit')->name('school.generate-questions-edit');
    Route::post('question-generator/get-questions-id-learning-objectives-school', 'QuestionGeneratorController@getQuestionIdsFromLearningObjectivesBySchool');
    Route::post('getLearningObjectivesFromMultipleLearningUnitsInGenerateQuestions', 'CommonController@getLearningObjectivesFromMultipleLearningUnitsInGenerateQuestions');

    Route::get('question-wizard/preview/{id}', 'QuestionGeneratorController@examConfigurationPreview')->name('exam-configuration-preview');
    Route::post('change-exam-end-date','QuestionGeneratorController@ChangeExamEndDate')->name('ChangeExamEndDate');

    // For Question wizard proof-reading routes
    Route::match(['GET', 'POST'],'question-wizard/proof-reading-question', 'QuestionGeneratorController@InspectModeProofReadingQuestions')->name('question-wizard.proof-reading-question');
    
    // Credit Point related routes
    Route::get('credit-point-history/{id}', 'ProfileController@creditPointHistory')->name('credit-point-history');
    // Route::get('student/students-profile/{id}','TeacherDashboardController@studentsProfile')->name('student.student-profiles');

    // Route::match(['GET', 'POST'],'assign-credit-points','TeacherDashboardController@AssignCreditPoints')->middleware(['teacher'])->name('assign-credit-points');
    Route::match(['GET', 'POST'],'assign-credit-points','CreditPointController@AssignCreditPoints')->middleware(['teacher'])->name('assign-credit-points');
    Route::get('get-students-list-checkbox', 'CommonController@getStudentListByGradeClassGroup');
});

Route::get('demo-progress-report', 'Reports\StudentLearningReportsController@StudentLearningReport')->name('StudentLearningReport');
Route::get('AssignCreditPointsManually', 'CronJobController@AssignCreditPointsManually')->name('AssignCreditPointsManually');