<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCurriculumYearIdInSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add curriculum_year_id in grades_school_mapping table
        Schema::table(cn::GRADES_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::GRADES_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in grade_class_mapping table
        Schema::table(cn::GRADE_CLASS_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::GRADE_CLASS_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in class_promotion_history table
        Schema::table(cn::CLASS_PROMOTION_HISTORY_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::CLASS_PROMOTION_HISTORY_ID_COL, function($table){
                $table->unsignedBigInteger(cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in attempt_exams table
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in attempt_exam_student_mapping table
        Schema::table(cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAM_STUDENT_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

         //add curriculum_year_id in audit_logs table
         Schema::table(cn::AUDIT_LOGS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::AUDIT_LOGS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

         //add curriculum_year_id in class_subject_mapping table
         Schema::table(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::CLASS_SUBJECT_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in exam_configurations_details table
        Schema::table(cn::EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_CONFIGURATIONS_DETAILS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in exam_credit_point_rules_mapping table
        Schema::table(cn::EXAM_CREDIT_POINT_RULES_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in exam_school_grade_class_mapping table
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in exam_school_mapping table
        Schema::table(cn::EXAM_SCHOOL_MAPPING_TABLE, function (Blueprint $table) {
            $table->after(cn::EXAM_SCHOOL_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in intelligent_tutor_videos table
        Schema::table(cn::INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::INTELLIGENT_TUTOR_VIDEOS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in peer_group table
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_ID_COL, function($table){
                $table->unsignedBigInteger(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in peer_group_members table
        Schema::table(cn::PEER_GROUP_MEMBERS_TABLE, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_MEMBERS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in pre_configured_difficulty table
        Schema::table(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PRE_CONFIGURE_DIFFICULTY_ID_COL, function($table){
                $table->unsignedBigInteger(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in subjects_school_mapping table
        Schema::table(cn::SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SUBJECT_MAPPING_ID_COL, function($table){
                $table->unsignedBigInteger(cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

         //add curriculum_year_id in teachers_class_subject_assign table
         Schema::table(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::TEACHER_CLASS_SUBJECT_ID_COL, function($table){
                $table->unsignedBigInteger(cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

         //add curriculum_year_id in teaching_report table
         Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::TEACHING_REPORT_ID_COL, function($table){
                $table->unsignedBigInteger(cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in upload_documents table
        Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::UPLOAD_DOCUMENTS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in user_credit_points table
        Schema::table(cn::USER_CREDIT_POINTS_TABLE, function (Blueprint $table) {
            $table->after(cn::USER_CREDIT_POINTS_ID_COL, function($table){
                $table->unsignedBigInteger(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

        //add curriculum_year_id in user_credit_point_history table
        Schema::table(cn::USER_CREDIT_POINT_HISTORY, function (Blueprint $table) {
            $table->after(cn::USER_CREDIT_POINT_HISTORY_ID_COL, function($table){
                $table->unsignedBigInteger(cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL)->nullable();
                $table->foreign(cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL)->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });

         //add curriculum_year_id in strand_units_objectives_mappings table
         Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::OBJECTIVES_MAPPINGS_ID_COL, function($table){
                $table->unsignedBigInteger('curriculum_year_id')->nullable();
                $table->foreign('curriculum_year_id')->references(cn::CURRICULUM_YEAR_ID_COL)->on(cn::CURRICULUM_YEAR_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //remove foreign key and remove curriculum year id column in grades_school_mapping table
        Schema::table(cn::GRADES_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in grade_class_mapping table
        Schema::table(cn::GRADE_CLASS_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in class_promotion_history table
        Schema::table(cn::CLASS_PROMOTION_HISTORY_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in attempt_exams table
         Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in attempt_exam_student_mapping table
         Schema::table(cn::ATTEMPT_EXAM_STUDENT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in audit_logs table
        Schema::table(cn::AUDIT_LOGS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in class_subject_mapping table
         Schema::table(cn::CLASS_SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in exam_configurations_details table
        Schema::table(cn::EXAM_CONFIGURATIONS_DETAILS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in exam_credit_point_rules_mapping table
        Schema::table(cn::EXAM_CREDIT_POINT_RULES_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in exam_school_grade_class_mapping table
        Schema::table(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in exam_school_mapping table
        Schema::table(cn::EXAM_SCHOOL_MAPPING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in intelligent_tutor_videos table
        Schema::table(cn::INTELLIGENT_TUTOR_VIDEOS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in peer_group table
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in peer_group_members table
         Schema::table(cn::PEER_GROUP_MEMBERS_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in pre_configured_difficulty table
        Schema::table(cn::PRE_CONFIGURE_DIFFICULTY_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in subjects_school_mapping table
        Schema::table(cn::SUBJECT_MAPPING_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in teachers_class_subject_assign table
         Schema::table(cn::TEACHER_CLASS_SUBJECT_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in teaching_report table
        Schema::table(cn::TEACHING_REPORT_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in upload_documents table
         Schema::table(cn::UPLOAD_DOCUMENTS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL);
        });

         //remove foreign key and remove curriculum year id column in user_credit_points table
         Schema::table(cn::USER_CREDIT_POINTS_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::USER_CREDIT_POINTS_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in user_credit_point_history table
        Schema::table(cn::USER_CREDIT_POINT_HISTORY, function (Blueprint $table) {
            $table->dropForeign([cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL]);
            $table->dropColumn(cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL);
        });

        //remove foreign key and remove curriculum year id column in strand_units_objectives_mappings table
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign(['curriculum_year_id']);
            $table->dropColumn('curriculum_year_id');
        });

    }
}
