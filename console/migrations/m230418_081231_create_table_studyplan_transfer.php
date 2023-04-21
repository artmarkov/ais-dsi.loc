<?php


/**
 * Class m230418_081231_create_table_studyplan_transfer
 */
class m230418_081231_create_table_studyplan_transfer extends \artsoft\db\BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('studyplan_transfer', [
            'id' => $this->primaryKey(),
            'studyplan_in' => $this->integer()->notNull()->defaultValue(0),
            'studyplan_out' => $this->integer()->notNull()->defaultValue(0),
            'studyplan_position' => $this->integer()->notNull(),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('studyplan_transfer', 'Переводы и история обучения учащихся');
        $this->addForeignKey('studyplan_transfer_ibfk_1', 'studyplan_transfer', 'studyplan_position', 'guide_student_position', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_transfer_ibfk_2', 'studyplan_transfer', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('studyplan_transfer_ibfk_3', 'studyplan_transfer', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->createView('studyplan_transfer_view', '
SELECT data.id, 
	   data.studyplan_in,
	   data.studyplan_out,
	   data.studyplan_position,
	   data.position_name,
	   data.description,
	   data.programm_in,
	   data.programm_out,
	   data.student_id,
	   data.course_in,
	   data.course_out,
	   data.plan_year_in,
	   data.plan_year_out,
       user_common.last_name,
       user_common.first_name,
       user_common.middle_name,
       concat(user_common.last_name, \' \', user_common.first_name, \' \', user_common.middle_name) AS student_fullname,
       concat(user_common.last_name, \' \', "left"(user_common.first_name::text, 1), \'.\', "left"(user_common.middle_name::text, 1), \'.\') AS student_fio
		 FROM (
		        SELECT studyplan_transfer.id,
	   studyplan_transfer.studyplan_in,
	   studyplan_transfer.studyplan_out,
	   studyplan_transfer.studyplan_position,
	   guide_student_position.name as position_name,
	   studyplan_transfer.description as description,
	   sta.programm_id AS programm_in,
	   stb.programm_id AS programm_out,
	   CASE
            WHEN sta.student_id IS NOT NULL THEN sta.student_id
            ELSE stb.student_id
        END AS student_id,
		 CASE
            WHEN sta.course IS NOT NULL THEN sta.course
            ELSE NULL
        END AS course_in,
		CASE
            WHEN stb.course IS NOT NULL THEN stb.course
            ELSE NULL
        END AS course_out,
	    CASE
            WHEN sta.plan_year IS NOT NULL THEN sta.plan_year
            ELSE NULL
        END AS plan_year_in,
		 CASE
            WHEN stb.plan_year IS NOT NULL THEN stb.plan_year
            ELSE NULL
        END AS plan_year_out
	FROM studyplan_transfer
	LEFT JOIN studyplan sta ON sta.id = studyplan_transfer.studyplan_in
	LEFT JOIN studyplan stb ON stb.id = studyplan_transfer.studyplan_out
	JOIN guide_student_position ON guide_student_position.id = studyplan_transfer.studyplan_position
	) data
	JOIN students ON students.id = data.student_id
	JOIN user_common ON user_common.id = students.user_common_id
  WHERE user_common.user_category::text = \'students\'::text;
         ')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->dropView('studyplan_transfer_view')->execute();
        $this->dropTableWithHistory('studyplan_transfer');
    }
}
