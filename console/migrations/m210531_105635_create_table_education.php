<?php

class m210531_105635_create_table_education extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_education_cat', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_education_cat', 'Наименование образовательной программы');
        $this->db->createCommand()->batchInsert('guide_education_cat', ['id', 'name', 'short_name', 'status'], [
            [1, 'Дополнительная общеобразовательная общеразвивающая программа', 'ОП.', 1],
            [2, 'Дополнительная предпрофессиональная общеобразовательная программа', 'ПП.', 1],
        ])->execute();

        $this->createTable('guide_education_level', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_education_level', 'Образовательный уровень');
        $this->db->createCommand()->batchInsert('guide_education_level', ['id', 'name', 'short_name', 'status'], [
            [1, 'Стартовый', 'Старт.', 1],
            [2, 'Базовый', 'База.', 1],
            [3, 'Продвинутый', 'Прод.', 1],
        ])->execute();

        $this->createTable('education_speciality', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'department_list' => $this->string(1024),
            'subject_type_list' => $this->string(1024),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('education_speciality', 'Специализации');

        $this->createTableWithHistory('education_programm', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'education_cat_id' => $this->integer()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'speciality_list' => $this->string(1024),
            'period_study' => $this->integer(),
            'description' => $this->string(1024),
            'category_list' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm', 'Учебные программы');
        $this->createIndex('education_cat_id', 'education_programm', 'education_cat_id');
        $this->addForeignKey('education_programm_ibfk_1', 'education_programm', 'education_cat_id', 'guide_education_cat', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->resetSequence('education_programm', 1000)->execute();
//        $this->db->createCommand()->batchInsert('education_programm', ['name', 'slug', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], [
//
//            ])->execute();
    }

    public function down()
    {
        $this->dropTableWithHistory('education_programm');
        $this->dropTable('guide_education_level');
        $this->dropTable('education_speciality');
        $this->dropTable('guide_education_cat');
    }
}