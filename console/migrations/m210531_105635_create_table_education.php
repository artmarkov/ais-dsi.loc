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

        $this->db->createCommand()->batchInsert('guide_education_cat', ['name', 'short_name', 'status'], [
            ['Дополнительная общеобразовательная общеразвивающая программа', 'ОП.', 1],
            ['Дополнительная предпрофессиональная общеобразовательная программа', 'ПП.', 1],

        ])->execute();
        $this->createTable('guide_education_level', [
            'id' => $this->primaryKey(),
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_education_level', 'Образовательный уровень');
        $this->db->createCommand()->batchInsert('guide_education_level', ['name', 'short_name', 'status'], [
            ['Стартовый', 'Старт.', 1],
            ['Базовый', 'База.', 1],
            ['Продвинутый', 'Прод.', 1],
            ['Основной', 'Осн.', 1],
            ['Углубленный', 'Углуб.', 1],
        ])->execute();

        $this->createTable('education_speciality', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127),
            'short_name' => $this->string(64)->notNull(),
            'department_list' => $this->string(1024),
            'subject_type_list' => $this->string(1024),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('education_speciality', 'Специализации');
        $this->db->createCommand()->resetSequence('education_speciality', 1000)->execute();

        $this->db->createCommand()->batchInsert('education_speciality', ['department_list', 'subject_type_list', 'name', 'short_name', 'status'], [
            ['1000', '1000,1001', 'Фортепиано', 'Ф-но', 1],
            ['1007', '1000,1001', 'Инструменты эстрадного оркестра', 'Эстр', 1],
            ['1002', '1000,1001', 'Духовые и ударные инструменты', 'Дух', 1],
            ['1003', '1000,1001', 'Народные инструменты', 'Нар', 1],
            ['1001', '1000,1001', 'Струнные инструменты', 'Стр', 1],
            ['1006', '1000,1001', 'Музыкальный фольклор', 'Фольк', 1],
            ['1005', '1000,1001', 'Хоровое пение', 'Хор', 1],
            ['1010', '1000,1001', 'Художественное отделение', 'Худ. бюдж', 1],
            ['1010', '1001', 'Художественное отделение (х/р)', 'Худ х/р', 1],
            ['1010', '1001', 'Подгот. группа (художники)', 'П.гр.(худ)', 1],
            ['1012', '1001', 'Художественная керамика (х/р)', 'Керам х/р', 1],
            ['1010', '1001', 'Группа раннего развития (художники)', 'ГРР худ', 1],
            ['1011', '1001', 'Отделение развития (МО)', 'ОР (МО)', 1],
            ['1011', '1001', 'Группа раннего развития "Мандариновое детство"', 'ГРР (муз)', 1],
            ['1013', '1001', 'Бально-спортивные танцы', 'Бальн-сп.танцы', 1],
            ['1013', '1001', 'Эстрадные танцы', 'Эстр.танцы', 1],
            ['1011', '1001', 'Дошкольное отделение (МО)', 'ДО (МО)', 1],
            ['1013', '1001', 'Классическая хореография', 'Кл.хореогр.', 1],
            ['1013', '1001', 'Классическая хореография (подг.отд.)     ', 'Класс.хореогр.(подг.отд.)', 1],
            ['1013', '1001', 'Подготовительный класс (эстр;бальн-спорт.)', 'П.класс (эстр;бальн-спорт.)', 1],
            ['1014', '1001', 'Подготовительная группа(Театр)', 'П.Г.(Театр)', 1],
            ['1014', '1001', 'Младшая группа (Театр)', 'М.Г.(Театр)', 1],
            ['1014', '1001', 'Театр', 'Театр', 1],
            ['1015', '1001', 'Группа архитекторов', 'Гр.арх.', 1],
            ['1016', '1000,1001', 'Группа дизайнеров', 'Гр.диз.', 1],
            ['1013', '1001', 'Брейк-данс', 'Брейк-данс', 1],
            ['1017', '1000,1001', 'Академический вокал', 'Акад.вок', 1],
            ['1013', '1001', 'Body ballet', 'Body ballet', 1],
            ['1013', '1001', 'Латина соло', 'Латина', 1],
            ['1010', '1001', 'Группа раннего развития углубл.(художники)', 'ГРРУ худ', 1],
            ['1013', '1001', 'Классическая хореография (раннее развитие)', 'Классич. хореогр.(ранн.разв.)', 1],
            ['1018', '1001', 'Сценическое мастерство', 'Сцен. маст-во', 1],
            ['1018', '1001', 'Сценическое мастерство (подгот. отд.)', 'Сцен.маст-во(ПО)', 1],
            ['1010', '1001', 'Группа раннего развития (Чудеса в ладошках)', 'ГРР (ЧВЛ)', 1],
            ['1010', '1001', 'Мастерская красок', 'МК', 1],
            ['1010', '1001', 'Живая кисточка', 'ЖК', 1],
            ['1010', '1001', 'Чудеса в ладошках', 'ЧВЛ', 1],
            ['1012', '1001', 'Скульптура (х/р)', 'Скульп х/р', 1],
            ['1013', '1001', 'Классический танец', 'Кл.танец', 1],
            ['1013', '1001', 'Ритмика и танец', 'Рит.тан', 1],
        ])->execute();

        $this->createTableWithHistory('education_programm', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'education_cat_id' => $this->integer()->notNull(),
            'name' => $this->string(512),
            'short_name' => $this->string(512),
            'term_mastering' => $this->integer()->notNull(),
//            'speciality_list' => $this->string(1024),
            'description' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm', 'Образовательные программы');
        $this->createIndex('education_cat_id', 'education_programm', 'education_cat_id');
        $this->addForeignKey('education_programm_ibfk_1', 'education_programm', 'education_cat_id', 'guide_education_cat', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->resetSequence('education_programm', 1000)->execute();

        $this->createTableWithHistory('education_programm_level', [
            'id' => $this->primaryKey(),
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
            'description' => $this->string(1024)->notNull(),
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
            ['education_speciality', 'education_speciality', 'id', 'name', 'id', 'status', null, 'Специализации'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['education_speciality_short', 'education_speciality', 'id', 'short_name', 'id', 'status', null, 'Специализации сокр.'],
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
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_speciality_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_speciality'])->execute();
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
        $this->dropTable('education_speciality');
        $this->dropTable('guide_education_cat');
    }
}