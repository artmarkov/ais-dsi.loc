<?php

use yii\db\Migration;

class m210301_151057_create_table_subject extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_subject_category', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127),
            'slug' => $this->string(64)->notNull(),
            'dep_flag' => $this->tinyInteger(2)->unsigned(),
            'frequency' => $this->tinyInteger(2)->unsigned(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'sort_order' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_subject_category', 'Раздел учебных предметов');

        $this->db->createCommand()->batchInsert('guide_subject_category', ['id', 'name', 'slug', 'sort_order', 'status', 'dep_flag', 'frequency'], [
            [1000, 'Специальность', 'Спец.', 1000, 1, 1, 0],
            [1001, 'Учебные предметы художественно-творческой подготовки', 'ХТП', 1001, 1, 0, 0],
            [1002, 'Учебные предметы историко-теоретической подготовки', 'ИТП', 1002, 1, 0, 0],
            [1003, 'Музыкальный инструмент', 'Муз.инстр.', 1003, 1, 0, 0],
        ])->execute();
        $this->db->createCommand()->resetSequence('guide_subject_category', 1004)->execute();

        $this->createTable('guide_subject_vid', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(64)->notNull(),
            'slug' => $this->string(32)->notNull(),
            'qty_min' => $this->smallInteger(3)->unsigned()->notNull(),
            'qty_max' => $this->smallInteger(3)->unsigned()->notNull(),
            'info' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);
        $this->addCommentOnTable('guide_subject_vid', 'Форма занятий');
        $this->db->createCommand()->resetSequence('guide_subject_vid', 1000)->execute();
        $this->db->createCommand()->batchInsert('guide_subject_vid', ['name', 'slug', 'qty_min', 'qty_max', 'info', 'status'], [
            ['Индивидуальная', 'Инд.', 1, 1, '', 1],
            ['Мелкогрупповая', 'Мелк-гр.', 2, 3, '', 1],
            ['Групповая', 'Гр.', 4, 10, '', 1],
        ])->execute();


        $this->createTable('guide_subject_type', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(64)->notNull(),
            'type_id' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addCommentOnTable('guide_subject_type', 'Тип занятий');
        $this->db->createCommand()->resetSequence('guide_subject_type', 1000)->execute();
        $this->db->createCommand()->batchInsert('guide_subject_type', ['name', 'slug', 'status'], [
            ['Бюджет', 'Бюд.', 1],
            ['Внебюджет', 'Внеб.', 1],
        ])->execute();

        $this->createTableWithHistory('subject', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'department_list' => $this->string(1024),
            'vid_list' => $this->string(1024),
            'category_list' => $this->string(1024),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('subject', 'Учебные предметы школы');
        $this->db->createCommand()->resetSequence('subject', 1000)->execute();
        $this->db->createCommand()->batchInsert('subject', ['name', 'slug', 'status', 'department_list','category_list','vid_list','created_at', 'updated_at', 'created_by', 'updated_by'], [
            ['Академический вокал', 'Ак.вок.', 1,'1013','1000','1000', time(), time(), 10000, 10000],
            ['Аккордеон', 'Акк-н', 1,'1003','1000','1000', time(), time(), 10000, 10000],
            ['Актерское мастерство', 'Акт.маст', 1, '1017','1001','1001', time(), time(), 10000, 10000],
            ['Альт', 'Альт', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Ансамбль', 'Анс.', 1,'1001,1002,1003,1007','1001','1000,1001,1002', time(), time(), 10000, 10000],
            ['Арт-практика', 'Арт-пр-ка', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Арфа', 'Арфа', 1,'1001','1000','1000', time(), time(), 10000, 10000],
            ['Архитектурное творчество', 'Арх.тв.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Балалайка', 'Бал', 1,'1003','1000','1000', time(), time(), 10000, 10000],
            ['Баллетная гимнастика', 'Бал.гимн', 1,'1011','1001','1001', time(), time(), 10000, 10000],
            ['Баритон', 'Барит', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Бас гитара', 'Б.гит', 1,'1007','1000','1000', time(), time(), 10000, 10000],
            ['Баян', 'Баян', 1,'1003','1000','1000', time(), time(), 10000, 10000],
            ['Беседы об искусстве', 'Бес.иск.', 1,'1016','1002','1001', time(), time(), 10000, 10000],
            ['Боди-балет', 'Боди-балет', 1, '1011','1001','1001', time(), time(), 10000, 10000],
            ['Валторна', 'Валт', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Веселые нотки', 'Вес.нотки', 1,'1010','1001','1001', time(), time(), 10000, 10000],
            ['Виолончель', 'Виол', 1, '1001','1000','1000', time(), time(), 10000, 10000],
            ['Вокал', 'Вок', 1, '1006','1001','1000', time(), time(), 10000, 10000],
            ['Гармонь', 'Гарм', 1,'1003','1000,1003','1000', time(), time(), 10000, 10000],
            ['Гитара', 'Гитара', 1,'1003','1000,1003','1000',time(), time(), 10000, 10000],
            ['Гусли', 'Гусли', 1, '1003','1000,1003', '1000', time(), time(), 10000, 10000],
            ['Домра', 'Домр', 1,'1003','1000','1000', time(), time(), 10000, 10000],
            ['Живопись', 'Жив-сь.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['История изобразительного искусства', 'Ист.ИЗО', 1, '1016','1002','1001', time(), time(), 10000, 10000],
            ['История искусств в картинках', 'Ист.иск.в карт.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Кларнет', 'Клар', 1, '1002','1000','1000', time(), time(), 10000, 10000],
            ['Классический танец', 'Класс.танец', 1, '1011','1001','1001', time(), time(), 10000, 10000],
            ['Композиция', 'Комп.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Композиция станковая', 'Комп.ст.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Компьютерный класс', 'Конц.кл.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Концертмейстерский класс', 'Конц.кл.', 1, '1000','1001','1000', time(), time(), 10000, 10000],
            ['Лепка', 'Лепка', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Маленький художник', 'Мал.худ.', 1, '1016','1001','1001', time(), time(), 10000, 10000],
            ['Музыкальная литература', 'Муз.лит.', 1,  '1004','1001','1000,1001',time(), time(), 10000, 10000],
            ['Народное творчество', 'Нар.тв-во', 1, '1006','1001','1001', time(), time(), 10000, 10000],
            ['Народно-сценический танец', 'Нар-сц.танец', 1, '1011','1001','1001', time(), time(), 10000, 10000],
            ['Народный танец', 'Нар.танец', 1, '1011','1001','1001', time(), time(), 10000, 10000],
            ['Общая физическая подготовка', 'ОФП', 1, '1011','1001','1001', time(), time(), 10000, 10000],
            ['Орган', 'Орган', 1,'1000','1001','1000', time(), time(), 10000, 10000],
            ['Оркестровый класс', 'Оркестр', 1,'1001,1002,1003,1007','1001','1002', time(), time(), 10000, 10000],
            ['Основы дизайна', 'Осн.диз.', 1,'1016','1002','1001', time(), time(), 10000, 10000],
            ['Основы дирижирования', 'Дир.', 1,'1005','1001','1000', time(), time(), 10000, 10000],
            ['Основы изобразительного искусства', 'Осн.ИЗО', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Основы изобразительной деятельности и дизайна одежды', 'Осн.из.и диз.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Основы импровизации и сочинения', 'Осн.импр.', 1,'1007','1001','1001', time(), time(), 10000, 10000],
            ['Основы мультипликации и режиссуры анимационного кино', 'Основы аним.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Основы мультипликационного движения, постановки и режиссуры аним', 'Осн.мульт.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Основы сценического мастерства', 'Основы сц.маст-ва', 1,'1017','1001','1001', time(), time(), 10000, 10000],
            ['Основы актерского мастерства', 'Основы акт.маст-ва', 1,'1017','1001','1001', time(), time(), 10000, 10000],
            ['Пластика', 'Пластика', 1,'1017','1001','1001', time(), time(), 10000, 10000],
            ['Художественное слово', 'Худ.слово', 1,'1017','1001','1001', time(), time(), 10000, 10000],
            ['Партерная гимнастика', 'Парт.гим-ка', 1,'1011','1011','1001', time(), time(), 10000, 10000],
            ['Пленэр', 'Пленэр', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Рисуем иллюстрации к рассказам', 'Рис.илл.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Рисунок', 'Рис.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Ритмика и танец', 'Ритм.тан', 1,'1011','1001','1001', time(), time(), 10000, 10000],
            ['Ритмическое сольфеджио', 'Ритм.сольф.', 1,'1007','1001','1001', time(), time(), 10000, 10000],
            ['Саксофон', 'Сакс', 1,'1002,1007','1000','1000', time(), time(), 10000, 10000],
            ['Скетчинг как образ жизни', 'Скетч.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Скрипка', 'Скр', 1,'1001','1000','1000', time(), time(), 10000, 10000],
            ['Скульптура', 'Скульпт.', 1,'1016','1001','1001', time(), time(), 10000, 10000],
            ['Слушание музыки', 'Сл.муз.', 1,'1004','1002','1001', time(), time(), 10000, 10000],
            ['Современный танец', 'Совр.танец', 1,'1011','1001','1001', time(), time(), 10000, 10000],
            ['Сольфеджио', 'Сольф.', 1,'1004','1002','1000,1001', time(), time(), 10000, 10000],
            ['Сочинение и композиция', 'Соч.и комп.', 1,'1004','1002','1000', time(), time(), 10000, 10000],
            ['Тенор', 'Тенор', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Тромбон', 'Тром', 1,'1002,1007','1000','1000', time(), time(), 10000, 10000],
            ['Труба', 'Труба', 1,'1002,1007','1000','1000', time(), time(), 10000, 10000],
            ['Туба', 'Туба', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Ударные', 'Удар', 1,'1002,1007','1000','1000', time(), time(), 10000, 10000],
            ['Флейта', 'Фле', 1,'1002','1000','1000', time(), time(), 10000, 10000],
            ['Фольклорный ансамбль', 'Ф.анс.', 1,'1006','1000','1001', time(), time(), 10000, 10000],
            ['Фортепиано', 'Ф-но', 1,'1008,1008','1000,1001,1003','1000', time(), time(), 10000, 10000],
            ['Хор', 'Хор', 1,'1005','1000,1001,1003','1001,1002', time(), time(), 10000, 10000],
            ['Чтение с листа', 'Чт.лист', 1,'1000,1001,1002,1003','1001','1000', time(), time(), 10000, 10000],
            ['Чудеса в ладошках', 'Чуд.в лад.', 1,'1010','1001','1000', time(), time(), 10000, 10000],
            ['Электрогитара', 'Эл.гит', 1,'1007','1000','1000', time(), time(), 10000, 10000],
            ['Электронно-компьютерное музицирование', 'ЭКМ', 1,'1004','1001','1000', time(), time(), 10000, 10000],
            ['Элементарная теория музыки', 'Теор.муз.', 1,'1004','1002','1001', time(), time(), 10000, 10000],
            ['Эстрадный вокал', 'Эстр.вок', 1,'1015','1000','1000', time(), time(), 10000, 10000],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_category_name', 'guide_subject_category', 'id', 'name', 'id', 'status', null, 'Раздел учебных предметов (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_category_name_dev', 'guide_subject_category', 'id', 'slug', 'id', 'status', null, 'Раздел учебных предметов (кратко)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_vid_name', 'guide_subject_vid', 'id', 'name', 'id', 'status', null, 'Форма занятий (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_vid_name_dev', 'guide_subject_vid', 'id', 'slug', 'id', 'status', null, 'Форма занятий (кратко)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_type_name', 'guide_subject_type', 'id', 'name', 'id', 'status', null, 'Тип занятий (полное)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_type_name_dev', 'guide_subject_type', 'id', 'slug', 'id', 'status', null, 'Тип занятий (кратко)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_category_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_category_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_vid_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_vid_name_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_type_name'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_type_name_dev'])->execute();
        $this->dropTableWithHistory('subject');
        $this->dropTable('guide_subject_type');
        $this->dropTable('guide_subject_vid');
        $this->dropTable('guide_subject_category');
    }
}
