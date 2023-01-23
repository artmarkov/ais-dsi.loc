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
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_education_cat', 'Наименование образовательной программы');
        $this->db->createCommand()->resetSequence('guide_education_cat', 1000)->execute();

        $this->db->createCommand()->batchInsert('guide_education_cat', ['name', 'short_name', 'status'], [
            ['Дополнительная предпрофессиональная общеобразовательная программа в области музыкального искусства', 'ПП МУЗ', 1],
            ['Дополнительная общеобразовательная общеразвивающая программа в области музыкального искусства', 'ОП МУЗ', 1],
            ['Дополнительная предпрофессиональная общеобразовательная программа в области изобразительного искусства', 'ПП ИЗО', 1],
            ['Дополнительная общеразвивающая общеобразовательная программа в области изобразительного искусства', 'ОП ИЗО', 1],
            ['Дополнительная общеразвивающая общеобразовательная программа в области хореографического искусства', 'ОП ХОРЕОГР', 1],
            ['Дополнительная общеразвивающая общеобразовательная программа в области театрального искусства', 'ОП ТЕАТР', 1],
            ['Дополнительная общеразвивающая общеобразовательная программа в области искусств', 'ОП ОБЩ', 1],

        ])->execute();
        $this->createTable('guide_education_level', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addCommentOnTable('guide_education_level', 'Образовательный уровень');
        $this->db->createCommand()->resetSequence('guide_education_level', 1000)->execute();

        $this->db->createCommand()->batchInsert('guide_education_level', ['name', 'short_name', 'status'], [
            ['Стартовый', 'Старт.', 1],
            ['Базовый', 'База.', 1],
            ['Продвинутый', 'Прод.', 1],
            ['Основной', 'Осн.', 1],
            ['Углубленный', 'Углуб.', 1],
        ])->execute();

        $this->createTableWithHistory('education_programm', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'education_cat_id' => $this->integer()->notNull(),
            'name' => $this->string(512),
            'short_name' => $this->string(512),
            'term_mastering' => $this->integer()->notNull(),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm', 'Образовательные программы');
        $this->db->createCommand()->resetSequence('education_programm', 1000)->execute();

        $this->createIndex('education_cat_id', 'education_programm', 'education_cat_id');
        $this->addForeignKey('education_programm_ibfk_1', 'education_programm', 'education_cat_id', 'guide_education_cat', 'id', 'NO ACTION', 'NO ACTION');


        $this->createTableWithHistory('education_programm_level', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'programm_id' => $this->integer()->notNull(),
            'level_id' => $this->integer(),
            'course' => $this->integer(),
            'year_time_total' => $this->float()->defaultValue(0),
            'cost_month_total' => $this->float()->defaultValue(0),
            'cost_year_total' => $this->float()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm_level', 'Уровни учебной программы');
        $this->db->createCommand()->resetSequence('education_programm_level', 1000)->execute();

        $this->createIndex('programm_id', 'education_programm_level', 'programm_id');
        $this->addForeignKey('education_programm_level_ibfk_1', 'education_programm_level', 'programm_id', 'education_programm', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('education_programm_level_ibfk_2', 'education_programm_level', 'level_id', 'guide_education_level', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('education_programm_level_subject', [
            'id' => $this->primaryKey(),
            'programm_level_id' => $this->integer()->notNull(),
            'subject_cat_id' => $this->integer(),
            'subject_vid_id' => $this->integer(),
            'subject_id' => $this->integer(),
            'week_time' => $this->float()->defaultValue(0),
            'year_time' => $this->float()->defaultValue(0),
            'cost_hour' => $this->float()->defaultValue(0),
            'cost_month_summ' => $this->float()->defaultValue(0),
            'cost_year_summ' => $this->float()->defaultValue(0),
            'year_time_consult' => $this->float()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm_level_subject', 'Предметы учебной программы по годам');
        $this->createIndex('programm_level_id', 'education_programm_level_subject', 'programm_level_id');
        $this->addForeignKey('education_programm_level_subject_ibfk_1', 'education_programm_level_subject', 'programm_level_id', 'education_programm_level', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('education_programm_level_subject_ibfk_2', 'education_programm_level_subject', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('education_programm_level_subject_ibfk_3', 'education_programm_level_subject', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('education_programm_level_subject_ibfk_4', 'education_programm_level_subject', 'subject_vid_id', 'guide_subject_vid', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('education_union', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'union_name' => $this->string(64),
            'programm_list' => $this->text()->notNull(),
            'class_index' => $this->string(32),
            'term_mastering' => $this->integer()->notNull(),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_union', 'Группа учебных планов'); // включает в себя учебные планы под одно название
        $this->db->createCommand()->resetSequence('education_union', 1000)->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_cat', 'guide_education_cat', 'id', 'name', 'id', 'status', null, 'Образовательные программы'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_cat_short', 'guide_education_cat', 'id', 'short_name', 'id', 'status', null, 'Образовательные программы сокр.'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_level', 'guide_education_level', 'id', 'name', 'id', 'status', null, 'Образовательный уровень'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_level_short', 'guide_education_level', 'id', 'short_name', 'id', 'status', null, 'Образовательный уровень сокр.'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_programm_name', 'education_programm', 'id', 'name', 'id', 'status', null, 'Образовательные программы.'],
        ])->execute();
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_programm_short_name', 'education_programm', 'id', 'short_name', 'id', 'status', null, 'Образовательные программы сокр.'],
        ])->execute();

    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_programm_short_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_programm_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_level_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_level'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_cat_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_cat'])->execute();
        $this->dropForeignKey('education_programm_level_subject_ibfk_1', 'education_programm_level_subject');
        $this->dropForeignKey('education_programm_level_subject_ibfk_2', 'education_programm_level_subject');
        $this->dropForeignKey('education_programm_level_subject_ibfk_3', 'education_programm_level_subject');
        $this->dropForeignKey('education_programm_level_subject_ibfk_4', 'education_programm_level_subject');
        $this->dropTableWithHistory('education_union');
        $this->dropTableWithHistory('education_programm_level_subject');
        $this->dropTableWithHistory('education_programm_level');

        $this->dropTableWithHistory('education_programm');
        $this->dropTable('guide_education_level');
        $this->dropTable('guide_education_cat');
    }
}