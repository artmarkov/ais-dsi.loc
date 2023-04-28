<?php

/**
 * Class m230420_121721_create_tables_pre_registration
 */
class m230420_121721_create_tables_pre_registration extends \artsoft\db\BaseMigration
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

        $this->createTable('entrant_programm', [
            'id' => $this->primaryKey(),
            'programm_id' => $this->integer()->notNull()->comment('Учебная программа'),
            'subject_type_id' => $this->integer()->notNull(),
            'course' => $this->integer()->notNull(),
            'name' => $this->string()->notNull()->comment('Название программы для предварительной записи'),
            'age_in' => $this->integer()->notNull()->comment('Ограничение по возрасту снизу'),
            'age_out' => $this->integer()->notNull()->comment('Ограничение по возрасту сверху'),
            'qty_entrant' => $this->integer()->notNull()->notNull()->comment('Кол-во учеников для приема'),
            'qty_reserve' => $this->integer()->notNull()->comment('Кол-во учеников для резерва'),
            'description' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_programm', 'Доступные программы для предварительной записи');
        $this->addForeignKey('entrant_programm_ibfk_1', 'entrant_programm', 'programm_id', 'education_programm', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_programm_ibfk_2', 'entrant_programm', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_programm_ibfk_3', 'entrant_programm', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('entrant_preregistrations', [
            'id' => $this->primaryKey(),
            'entrant_programm_id' => $this->integer()->notNull()->comment('Выбранная программа для предварительной записи'),
            'plan_year' => $this->integer()->notNull()->comment('Учебный год приема ученика'),
            'stydent_id' => $this->integer()->notNull()->comment('Учетная запись ученика-кандидата'),
            'reg_vid' => $this->integer()->notNull()->comment('Вид записи (Список: для приема, в резерв)'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_preregistrations', 'Список предварительной регистрации учеников');
        $this->addForeignKey('entrant_preregistrations_ibfk_1', 'entrant_preregistrations', 'entrant_programm_id', 'entrant_programm', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('entrant_preregistrations_ibfk_2', 'entrant_preregistrations', 'stydent_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('entrant_preregistrations_ibfk_3', 'entrant_preregistrations', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_preregistrations_ibfk_4', 'entrant_preregistrations', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->db->createCommand()->dropView('entrant_preregistrations_view')->execute();
        $this->dropTable('entrant_preregistrations');
        $this->dropTable('entrant_programm');
    }

}
