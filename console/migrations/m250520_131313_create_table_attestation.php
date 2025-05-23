<?php

class m250520_131313_create_table_attestation extends \artsoft\db\BaseMigration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('attestation_items', [
            'id' => $this->primaryKey(),
            'plan_year' => $this->integer(),
            'studyplan_subject_id' => $this->integer()->notNull()->comment('Учебный предмет ученика'),
            'lesson_mark_id' => $this->integer()->comment('Оценка'),
            'mark_rem' => $this->string(127),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('attestation_items', 'Аттестация');

        $this->addForeignKey('attestation_items_ibfk_1', 'attestation_items', 'studyplan_subject_id', 'studyplan_subject', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('attestation_items_ibfk_2', 'attestation_items', 'lesson_mark_id', 'guide_lesson_mark', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTableWithHistory('attestation_items');
    }
}
