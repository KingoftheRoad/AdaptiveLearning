<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modules;
use App\Constants\DbConstant as cn;

class ModulesTableSeeder extends Seeder {
    public function run(){
        $modules = [
            [
                'module_name'     => 'Question Bank',
                'module_slug'     => 'question_bank',
            ],//1
            [   
                'module_name'     => 'User Management',
                'module_slug'     => 'user_management',
            ],//2
            [   
                'module_name'     => 'Group Management',
                'module_slug'     => 'group_management',
            ],//3
            [   
                'module_name'     => 'Exam Management',
                'module_slug'     => 'exam_management',
            ],//4
            [   
                'module_name'     => 'School Management',
                'module_slug'     => 'school_management',
            ],//5
            [   
                'module_name'     => 'Roles Management',
                'module_slug'     => 'roles_management',
            ],//6
            [   
                'module_name'     => 'User Activity',
                'module_slug'     => 'user_activity',
            ],//7
            [
                'module_name'     => 'Reports',
                'module_slug'     => 'reports',
            ],//8
            [
                'module_name'      => 'Setting',
                'module_slug'      => 'setting',
            ],//9
            [
                'module_name'     => 'Modules Management',
                'module_slug'     => 'modules_management',
            ],//10
            [
                'module_name'     => 'Result Management',
                'module_slug'     => 'result_management',
            ],//11
            [                
                'module_name'     => 'Profile Management',
                'module_slug'     => 'profile_management',
            ],//12
            [
                'module_name'     => 'Teacher Management',
                'module_slug'     => 'teacher_management',
            ],//13
            [
                'module_name'     => 'Grade Management',
                'module_slug'     => 'grade_management',
            ],//14
            [                
                'module_name'     => 'Subject Management',
                'module_slug'     => 'subject_management',
            ],//15
            [
                'module_name'     => 'Student Management',
                'module_slug'     => 'student_management',
            ],//16
            [
                'module_name' => 'Knowledge Tree',
                'module_slug' => 'knowledge_tree'
            ],//17
            [
                'module_name' => 'Node Management',
                'module_slug' => 'node_management'
            ],//18
            [
                'module_name'     => 'Assign Test Question',
                'module_slug'     => 'assign_test_question',
            ],//19
            [
                'module_name'     => 'Assign Test User',
                'module_slug'     => 'assign_test_user',
            ],//20
            [
                'module_name'     => 'Assign Test Group',
                'module_slug'     => 'assign_test_group',
            ],//21
            [
                'module_name' => 'Upload Documents',
                'module_slug' => 'upload_documents'
            ],//22
            [
                'module_name' => 'Test Template Management',
                'module_slug' => 'test_template_management'
            ],//23
            [
                'module_name'     => 'Attempt Exam',
                'module_slug'     => 'attempt_exam',
            ],//24
            [
                'module_name'     => 'Teacher Class And Subject Assign',
                'module_slug'     => 'teacher_class_and_subject_assign',
            ],//25
            [
                'module_name'     => 'Ai Calculate Difficulty',
                'module_slug'     => 'ai_calculate_difficulty',
            ],//26
            [
                'module_name'     => 'Pre Configure Difficulty',
                'module_slug'     => 'pre_configure_difficulty',
            ],//27
            [
                'module_name'     => 'Strands Management',
                'module_slug'     => 'strands_management',
            ],//28
            [
                'module_name'     => 'Learning Units Management',
                'module_slug'     => 'learning_units_management',
            ],//29
            [
                'module_name'     => 'Learning Objectives Management',
                'module_slug'     => 'learning_objectives_management',
            ],//30
            [
                'module_name'     => 'Global Configurations',
                'module_slug'     => 'global_configurations'
            ],//31
            [
                'module_name'     => 'Sub Admin Management',
                'module_slug'     => 'sub_admin_management'
            ],//32
            [   
                'module_name'     => 'My Classes',
                'module_slug'     => 'my_classes',
            ],//33
            [   
                'module_name'     => 'My Subjects',
                'module_slug'     => 'my_subjects',
            ],//34
            [   
                'module_name'     => 'My Teaching',
                'module_slug'     => 'my_teaching',
            ],//35
            [   
                'module_name'     => 'Self Learning',
                'module_slug'     => 'self_learning',
            ],//36
            [   
                'module_name'     => 'Assignment Or Test',
                'module_slug'     => 'assignment_or_test',
            ],//37
            [   
                'module_name'     => 'Progress Report',
                'module_slug'     => 'progress_report',
            ],//38
            [   
                'module_name'     => 'Peer Group',
                'module_slug'     => 'peer_group',
            ],//39
            [   
                'module_name'     => 'My Teachers',
                'module_slug'     => 'my_teachers',
            ],//40
            [   
                'module_name'     => 'My Calendar',
                'module_slug'     => 'my_calendar',
            ],//41
            [   
                'module_name'     => 'My Account',
                'module_slug'     => 'my_account',
            ],//42
            [   
                'module_name'     => 'Change Password',
                'module_slug'     => 'change_password',
            ],//43
            [   
                'module_name'     => 'Principal Management',
                'module_slug'     => 'principal_management',
            ],//44
            [   
                'module_name'     => 'Documents',
                'module_slug'     => 'documents',
            ],//45
            [   
                'module_name'     => 'Leaderboard',
                'module_slug'     => 'leaderboard',
            ],//46
            [
                'module_name'     => 'Assign Credit Points',
                'module_slug'     => 'assign_credit_points',
            ],//47
            [
                'module_name'     => 'Intelligent Tutor',
                'module_slug'     => 'intelligent_tutor',
            ],//48
        ];

        foreach ($modules as $module) {
            $ModuleData = Modules::where(cn::MODULES_MODULE_SLUG_COL,$module['module_slug'])->first();
            if(isset($ModuleData) && !empty($ModuleData)){
                Modules::where(cn::MODULES_MODULE_SLUG_COL,$module['module_slug'])->update($module);
            }else{
                Modules::updateOrCreate($module);
            }
        }
    }
}
