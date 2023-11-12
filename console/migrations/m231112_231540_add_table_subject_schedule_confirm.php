<?php


class m231112_231540_add_table_subject_schedule_confirm extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('subject_schedule_confirm', [
            'id' => $this->primaryKey(),
            'teachers_id' => $this->integer()->notNull(),
            'plan_year' => $this->integer()->notNull(),
            'confirm_flag' => $this->boolean()->notNull()->defaultValue(false),
            'teachers_sign' => $this->integer(),
            'timestamp_sign' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject_schedule_confirm', 'Утверждение расписания занятий');
        $this->addForeignKey('subject_schedule_confirm_ibfk_1', 'subject_schedule_confirm', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_schedule_confirm_ibfk_2', 'subject_schedule_confirm', 'teachers_sign', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {

        $this->dropForeignKey('subject_schedule_confirm_ibfk_2', 'subject_schedule_confirm');
        $this->dropForeignKey('subject_schedule_confirm_ibfk_1', 'subject_schedule_confirm');
        $this->dropTableWithHistory('subject_schedule_confirm');

    }
}
