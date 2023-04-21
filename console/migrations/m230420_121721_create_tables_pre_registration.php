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

        $this->createTableWithHistory('entrant_programm', [
            'id' => $this->primaryKey(),
            'education_programm_level_id' => $this->integer()->notNull()->comment('Уровень учебной программы'),
            'name' => $this->string(124)->comment('Название программы для предварительной записи'),
            'short_name' => $this->string(64)->comment('Короткое название программы для предварительной записи'),
            'plan_year' => $this->integer()->comment('Учебный год приема ученика'),
            'date_in' => $this->integer()->comment('Открытия формы предварительной записи'),
            'date_out' => $this->integer()->comment('Закрытие формы предварительной записи'),
            'age_in' => $this->integer()->comment('Ограничение по возрасту снизу'),
            'age_out' => $this->integer()->comment('Ограничение по возрасту сверху'),
            'qty_entrant' => $this->integer()->notNull()->comment('Кол-во учеников для приема'),
            'qty_reserve' => $this->integer()->comment('Резерв учеников для приема'),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('entrant_programm', 'Доступные программы для предварительной записи');
        $this->addForeignKey('entrant_programm_ibfk_1', 'entrant_programm', 'education_programm_level_id', 'education_programm_level', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_programm_ibfk_2', 'entrant_programm', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('entrant_programm_ibfk_3', 'entrant_programm', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->db->createCommand()->dropView('studyplan_transfer_view')->execute();
        $this->dropTableWithHistory('entrant_programm');
    }

}
