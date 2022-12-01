<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Constants\DbConstant as cn;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
                    [   
                        'id'            => 1,
                        'role_name'     => 'Super Admin',
                        'role_slug'     => 'super_admin',
                        'permission'    => 'question_bank_create,question_bank_read,question_bank_update,question_bank_delete,user_management_create,user_management_read,user_management_update,user_management_delete,group_management_create,group_management_read,group_management_update,group_management_delete,exam_management_create,exam_management_read,exam_management_update,exam_management_delete,school_management_create,school_management_read,school_management_update,school_management_delete,roles_management_create,roles_management_read,roles_management_update,roles_management_delete,user_activity_create,user_activity_read,user_activity_update,user_activity_delete,reports_create,reports_read,reports_update,reports_delete,setting_create,setting_read,setting_update,setting_delete,modules_management_create,modules_management_read,modules_management_update,modules_management_delete,result_management_update,knowledge_tree_read,node_management_create,node_management_read,node_management_update,node_management_delete,assign_test_question_update,assign_test_user_update,assign_test_group_update,upload_documents_create,upload_documents_read,upload_documents_update,upload_documents_delete,test_template_management_create,test_template_management_read,test_template_management_update,test_template_management_delete,result_management_update,ai_calculate_difficulty_create,ai_calculate_difficulty_read,ai_calculate_difficulty_update,ai_calculate_difficulty_delete,pre_configure_difficulty_create,pre_configure_difficulty_read,pre_configure_difficulty_update,pre_configure_difficulty_delete,strands_management_create,strands_management_read,strands_management_update,strands_management_delete,learning_units_create,learning_units_read,learning_units_update,learning_units_delete,learning_objectives_management_create,learning_objectives_management_read,learning_objectives_management_update,learning_objectives_management_delete,global_configurations_create,global_configurations_read,global_configurations_update,global_configurations_delete,my_account_read,change_password_read,change_password_update,learning_units_management_create,learning_units_management_read,learning_units_management_update,learning_units_management_delete,intelligent_tutor_create,intelligent_tutor_update,intelligent_tutor_read,intelligent_tutor_delete'
                    ],
                    [
                        'id'            => 2,
                        'role_name' => 'Teacher',
                        'role_slug' => 'teacher',
                        'permission' => 'exam_management_create,exam_management_read,exam_management_update,exam_management_delete,reports_create,reports_read,reports_update,reports_delete,result_management_create,result_management_read,result_management_update,result_management_delete,profile_management_create,profile_management_read,assign_test_question_create,assign_test_question_read,assign_test_question_update,assign_test_question_delete,assign_test_user_create,assign_test_user_read,assign_test_user_update,assign_test_user_delete,assign_test_group_create,assign_test_group_read,assign_test_group_update,assign_test_group_delete,test_template_management_read,attempt_exam_read,result_management_create,result_management_read,result_management_update,result_management_delete,my_classes_read,my_subjects_read,my_teaching_read,self_learning_read,assignment_or_test_read,progress_report_read,peer_group_create,peer_group_read,peer_group_update,peer_group_delete,my_account_read,change_password_read,change_password_update,documents_read,leaderboard_read,assign_credit_points_create,assign_credit_points_read,assign_credit_points_update,assign_credit_points_delete,intelligent_tutor_read'
                    ],
                    [
                        'id'            => 3,
                        'role_name' => 'Student',
                        'role_slug' => 'student',
                        'permission' => 'exam_management_read,result_management_read,profile_management_create,profile_management_read,test_template_management_read,attempt_exam_read,attempt_exam_update,result_management_read,my_classes_read,my_subjects_read,my_teachers_read,my_calendar_read,my_account_read,change_password_read,change_password_update,documents_read,progress_report_read,peer_group_read,leaderboard_read,intelligent_tutor_read'
                    ],
                    [
                        'id'            => 4,
                        'role_name' => 'Parent',
                        'role_slug' => 'parent',
                        'permission'=> ''
                    ],
                    [
                        'id'            => 5,
                        'role_name' => 'School',
                        'role_slug' => 'school',
                        'permission'=> 'question_bank_create,question_bank_read,question_bank_update,question_bank_delete,user_management_create,user_management_read,user_management_update,user_management_delete,group_management_create,group_management_read,group_management_update,group_management_delete,exam_management_create,exam_management_read,exam_management_update,exam_management_delete,reports_create,reports_read,reports_update,reports_delete,result_management_create,result_management_read,result_management_update,result_management_delete,profile_management_create,profile_management_read,teacher_management_create,teacher_management_read,teacher_management_update,teacher_management_delete,grade_management_create,grade_management_read,grade_management_update,grade_management_delete,subject_management_create,subject_management_read,subject_management_update,subject_management_delete,student_management_create,student_management_read,student_management_update,student_management_delete,assign_test_question_create,assign_test_question_read,assign_test_question_update,assign_test_question_delete,assign_test_user_create,assign_test_user_read,assign_test_user_update,assign_test_user_delete,assign_test_group_create,assign_test_group_read,assign_test_group_update,assign_test_group_delete,result_management_create,result_management_read,result_management_update,result_management_delete,teacher_class_and_subject_assign_create,teacher_class_and_subject_assign_read,teacher_class_and_subject_assign_update,teacher_class_and_subject_assign_delete,sub_admin_management_create,sub_admin_management_read,sub_admin_management_update,sub_admin_management_delete,my_account_read,change_password_read,change_password_update,principal_management_create,principal_management_read,principal_management_update,principal_management_delete,peer_group_read,peer_group_create,peer_group_update,peer_group_delete,leaderboard_read,intelligent_tutor_read'
                    ],
                    [
                        'id'        => 6,
                        'role_name' => 'External Resource',
                        'role_slug' => 'external_resource',
                        'permission'=> 'question_bank_create,question_bank_read,user_management_create,user_management_read,user_management_update,user_management_delete,group_management_create,group_management_read,group_management_update,group_management_delete,exam_management_create,exam_management_read,exam_management_update,exam_management_delete,user_activity_create,user_activity_read,user_activity_update,user_activity_delete,reports_create,reports_read,reports_update,reports_delete,teacher_management_create,teacher_management_read,teacher_management_update,teacher_management_delete,grade_management_create,grade_management_read,grade_management_update,grade_management_delete,subject_management_create,subject_management_read,subject_management_update,subject_management_delete,student_management_create,student_management_read,student_management_update,student_management_delete,change_password_update'
                    ],
                    [
                        'id'        => 7,
                        'role_name' => 'Principal',
                        'role_slug' => 'principal',
                        'permission'=> 'exam_management_create,exam_management_read,exam_management_update,exam_management_delete,reports_read,student_management_create,student_management_read,student_management_update,student_management_delete,my_teaching_read,self_learning_read,assignment_or_test_read,progress_report_read,documents_read,result_management_update,leaderboard_read,intelligent_tutor_read'
                    ]
                ];

        foreach ($roles as $role) {
            $RoleData = Role::where(cn::ROLES_ROLE_SLUG_COL, $role['role_slug'])->first();
            if(isset($RoleData) && !empty($RoleData)){
                Role::where(cn::ROLES_ROLE_SLUG_COL, $role['role_slug'])->Update($role);
            }else{
                Role::Create($role);
            }
        }
    }
}
