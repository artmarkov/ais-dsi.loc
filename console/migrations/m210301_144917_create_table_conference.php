<?php

use yii\db\Migration;

class m210301_144917_create_table_conference extends Migration
{
auditory_building,auditory_cat,creative_category,creative_works,creative_works_author,creative_works_department,creative_works_revision,department,division,event,event_category,image_manager,measure,measure_unit,student,student_position,subject,subject_category,subject_category_item,subject_department,subject_type,subject_vid,teachers_bonus,teachers_bonus_category,teachers_bonus_item,teachers_cost,teachers_department,teachers_direction,teachers_level,teachers_position,teachers_stake,teachers_work,venue_country,venue_district,venue_place,venue_sity,    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%conference}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127)->notNull(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
        ], $tableOptions);

    }


    public function down()
    {
        $this->dropTable('{{%conference}}');
    }
}
