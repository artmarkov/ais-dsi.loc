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

        $this->db->createCommand()->batchInsert('education_speciality', ['department_list', 'subject_type_list', 'name', 'short_name', 'status'], [
            ['2', '1,2', 'Фортепиано', 'Ф-но', 1],
            ['9', '1,2', 'Инструменты эстрадного оркестра', 'Эстр', 1],
            ['4', '1,2', 'Духовые и ударные инструменты', 'Дух', 1],
            ['5', '1,2', 'Народные инструменты', 'Нар', 1],
            ['3', '1,2', 'Струнные инструменты', 'Стр', 1],
            ['8', '1,2', 'Музыкальный фольклор', 'Фольк', 1],
            ['7', '1,2', 'Хоровое пение', 'Хор', 1],
            ['12', '1,2', 'Художественное отделение', 'Худ. бюдж', 1],
            ['12', '2', 'Художественное отделение (х/р)', 'Худ х/р', 1],
            ['12', '2', 'Подгот. группа (художники)', 'П.гр.(худ)', 1],
            ['14', '2', 'Художественная керамика (х/р)', 'Керам х/р', 1],
            ['12', '2', 'Группа раннего развития (художники)', 'ГРР худ', 1],
            ['13', '2', 'Отделение развития (МО)', 'ОР (МО)', 1],
            ['13', '2', 'Группа раннего развития "Мандариновое детство"', 'ГРР (муз)', 1],
            ['2,3,4,5,7,8,9,10', '2', 'Хозрасчетное отделение муз.', 'Муз х/р', 1],
            ['7', '2', 'Эстетическое отделение "Элегия"', 'Эст отд. "Элегия"', 1],
            ['15', '2', 'Бально-спортивные танцы', 'Бальн-сп.танцы', 1],
            ['15', '2', 'Эстрадные танцы', 'Эстр.танцы', 1],
            ['13', '2', 'Дошкольное отделение (МО)', 'ДО (МО)', 1],
            ['2,3,4,5,7,8,9,10', '1,2', '8 класс(МО)', '8 класс(МО)', 1],
            ['15', '2', 'Классическая хореография', 'Кл.хореогр.', 1],
            ['15', '2', 'Классическая хореография (подг.отд.)     ', 'Класс.хореогр.(подг.отд.)', 1],
            ['15', '2', 'Подготовительный класс (эстр;бальн-спорт.)', 'П.класс (эстр;бальн-спорт.)', 1],
            ['16', '2', 'Подготовительная группа(Театр)', 'П.Г.(Театр)', 1],
            ['16', '2', 'Младшая группа (Театр)', 'М.Г.(Театр)', 1],
            ['16', '2', 'Театр', 'Театр', 1],
            ['17', '2', 'Группа архитекторов', 'Гр.арх.', 1],
            ['18', '1,2', 'Группа дизайнеров', 'Гр.диз.', 1],
            ['15', '2', 'Брейк-данс', 'Брейк-данс', 1],
            ['19', '1,2', 'Академический вокал', 'Акад.вок', 1],
            ['15', '2', 'Body ballet', 'Body ballet', 1],
            ['15', '2', 'Латина соло', 'Латина', 1],
            ['12', '2', 'Группа раннего развития углубл.(художники)', 'ГРРУ худ', 1],
            ['2,3,4,5,7,8,9,10', '1,2', '6 класс(МО)', '6 класс(МО)', 1],
            ['15', '2', 'Классическая хореография (раннее развитие)', 'Классич. хореогр.(ранн.разв.)', 1],
            ['20', '2', 'Сценическое мастерство', 'Сцен. маст-во', 1],
            ['20', '2', 'Сценическое мастерство (подгот. отд.)', 'Сцен.маст-во(ПО)', 1],
            ['12', '2', 'Группа раннего развития (Чудеса в ладошках)', 'ГРР (ЧВЛ)', 1],
            ['12', '2', 'Мастерская красок', 'МК', 1],
            ['12', '2', 'Живая кисточка', 'ЖК', 1],
            ['12', '2', 'Чудеса в ладошках', 'ЧВЛ', 1],
            ['14', '2', 'Скульптура (х/р)', 'Скульп х/р', 1],
            ['15', '2', 'Классический танец', 'Кл.танец', 1],
            ['15', '2', 'Ритмика и танец', 'Рит.тан', 1],
        ])->execute();

        $this->createTableWithHistory('education_programm', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'education_cat_id' => $this->integer()->notNull(),
            'name' => $this->string(127),
            'speciality_list' => $this->string(1024),
            'period_study' => $this->integer(),
            'description' => $this->string(1024),
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

        $this->createTableWithHistory('education_programm_subject', [
            'id' => $this->primaryKey(),
            'programm_id' => $this->integer()->notNull(),
            'subject_cat_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable( 'education_programm_subject', 'Дисциплины учебной программы');
        $this->createIndex('programm_id', 'education_programm_subject', 'programm_id');
        $this->addForeignKey('education_programm_subject_ibfk_1', 'education_programm_subject', 'programm_id', 'education_programm', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('education_programm_subject_ibfk_2', 'education_programm_subject', 'subject_cat_id', 'guide_subject_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('education_programm_subject_ibfk_3', 'education_programm_subject', 'subject_id', 'subject', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('education_programm_subject_time', [
            'id' => $this->primaryKey(),
            'programm_subject_id' => $this->integer()->notNull(),
            'cource' => $this->integer()->notNull(),
            'week_time' => $this->float(),
            'cost_week_hour' => $this->float(),
            'year_time' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('education_programm_subject_time', 'Нагрузка дисциплины учебной программы');
        $this->createIndex('programm_subject_id', 'education_programm_subject_time', 'programm_subject_id');
        $this->addForeignKey('education_programm_subject_time_ibfk_1', 'education_programm_subject_time', 'programm_subject_id', 'education_programm_subject', 'id', 'CASCADE', 'CASCADE');


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

    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_programm_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_level_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_level'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_speciality_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_speciality'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_cat_short'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'education_cat'])->execute();
        $this->dropForeignKey('education_programm_subject_ibfk_3', 'education_programm_subject');
        $this->dropForeignKey('education_programm_subject_ibfk_2', 'education_programm_subject');
        $this->dropForeignKey('education_programm_subject_ibfk_1', 'education_programm_subject');
        $this->dropTableWithHistory('education_programm_subject_time');
        $this->dropTableWithHistory('education_programm_subject');
        $this->dropTableWithHistory('education_programm');
        $this->dropTable('guide_education_level');
        $this->dropTable('education_speciality');
        $this->dropTable('guide_education_cat');
    }
}