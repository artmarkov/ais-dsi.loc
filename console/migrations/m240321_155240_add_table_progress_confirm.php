<?php

class m240321_155240_add_table_progress_confirm extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('progress_confirm', [
            'id' => $this->primaryKey(),
            'subject_sect_studyplan_id' => $this->integer()->notNull()->defaultValue(0),
            'timestamp_month' => $this->integer()->comment('Отчетный месяц-год - timestamp'),
            'teachers_id' => $this->integer(),
            'teachers_sign' => $this->integer(),
            'confirm_status' => $this->integer()->notNull()->defaultValue(0),
            'sign_message' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('progress_confirm', 'Утверждение Журнала успеваемости за месяц');
        $this->addForeignKey('progress_confirm_ibfk_1', 'progress_confirm', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('progress_confirm_ibfk_2', 'progress_confirm', 'teachers_sign', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('progress_confirm_indiv', [
            'id' => $this->primaryKey(),
            'subject_key' => $this->string(),
            'timestamp_month' => $this->integer()->comment('Отчетный месяц-год - timestamp'),
            'teachers_id' => $this->integer(),
            'teachers_sign' => $this->integer(),
            'confirm_status' => $this->integer()->notNull()->defaultValue(0),
            'sign_message' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('progress_confirm_indiv', 'Утверждение Журнала успеваемости за месяц');
        $this->addForeignKey('progress_confirm_indiv_ibfk_1', 'progress_confirm_indiv', 'teachers_id', 'teachers', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('progress_confirm_indiv_ibfk_2', 'progress_confirm_indiv', 'teachers_sign', 'teachers', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTableWithHistory('progress_confirm_indiv');
        $this->dropTableWithHistory('progress_confirm');
    }
}
