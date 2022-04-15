<?php


class m220412_213940_create_table_schoolplan extends \artsoft\db\BaseMigration
{
    const TABLE_NAME_TREE = 'guide_plan_tree';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_NAME_TREE, [
            'id' => $this->bigPrimaryKey(),
            'root' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'lvl' => $this->smallInteger(5)->notNull(),
            'name' => $this->string(127)->notNull()->comment('Название'),
            'description' => $this->string(512)->comment('Описание'),
            'category_sell' => $this->smallInteger(1)->defaultValue(1)->comment('Категория мероприятия(внутреннее или внешнее'),
            'commission_sell' => $this->smallInteger(1)->defaultValue(1)->comment('Требуется аттестационная или приемная комиссия'),
            'preparing_flag' => $this->boolean()->defaultValue(false)->comment('Требуется подготовка к мероприятию'),
            'description_flag' => $this->boolean()->defaultValue(false)->comment('Требуется описание мероприятия'),
            'afisha_flag' => $this->boolean()->defaultValue(false)->comment('Требуется афиша и программа'),
            'bars_flag' => $this->boolean()->defaultValue(false)->comment('Требуется отправлять в БАРС'),
            'efficiency_flag' => $this->boolean()->defaultValue(false)->comment('Требуется подключение показателей эффективности'),
            'schedule_flag' => $this->boolean()->defaultValue(false)->comment('Мероприятие в рамках расписания занятий'),
            'consult_flag' => $this->boolean()->defaultValue(false)->comment('Мероприятие в рамках расписания консультаций'),
            'partners_flag' => $this->boolean()->defaultValue(false)->comment('Возможность участия региональных партнеров'),
            'icon' => $this->string(255),
            'icon_type' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'selected' => $this->boolean()->notNull()->defaultValue(false),
            'disabled' => $this->boolean()->notNull()->defaultValue(false),
            'readonly' => $this->boolean()->notNull()->defaultValue(false),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'collapsed' => $this->boolean()->notNull()->defaultValue(false),
            'movable_u' => $this->boolean()->notNull()->defaultValue(true),
            'movable_d' => $this->boolean()->notNull()->defaultValue(true),
            'movable_l' => $this->boolean()->notNull()->defaultValue(true),
            'movable_r' => $this->boolean()->notNull()->defaultValue(true),
            'removable' => $this->boolean()->notNull()->defaultValue(true),
            'removable_all' => $this->boolean()->notNull()->defaultValue(false),
            'child_allowed' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),

        ], $tableOptions);
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Категории мероприятий плана работы');

        $this->createIndex(self::TABLE_NAME_TREE . '_i1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex(self::TABLE_NAME_TREE . '_i2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex(self::TABLE_NAME_TREE . '_i3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex(self::TABLE_NAME_TREE . '_i4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex(self::TABLE_NAME_TREE . '_i5', self::TABLE_NAME_TREE, 'active');
        $this->addForeignKey(self::TABLE_NAME_TREE . '_ibfk_1', self::TABLE_NAME_TREE, 'created_by', 'users', 'id', 'RESTRICT', 'RESTRICT');

        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'description', 'category_sell', 'commission_sell', 'preparing_flag', 'description_flag', 'afisha_flag', 'bars_flag', 'efficiency_flag', 'schedule_flag', 'consult_flag',
            'partners_flag', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d',
            'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed', 'created_at', 'created_by'], [
            [1, 1, 1, 16, 0, '1. Учебная работа','', 1, 0, false, false, false, false, false, false, false, false,'', 1, true, false, true, false, true, true, false, false, false, false, false, false, true, time(),1000],
            [2, 1, 2, 3, 1, '1.1. Педсоветы и совещания','', 1, 0, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [3, 1, 4, 5, 1, '1.2 Технические зачеты','', 1, 0, false, false, false, false, false, true, false, false,'', 1, true, false, false, false, true, false, true, true, true, true, true, false, true, time(),1000],
            [4, 1, 6, 7, 1, '1.3 Академические концерты и зачеты','', 1, 0, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, true, true, true, true, true, false, true, time(),1000],
            [5, 1, 8, 9, 1, '1.4. Прослушивания выпускников','', 1, 0, false, false, false, false, false, true, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [6, 1, 10, 11, 1, '1.5. Выпускные экзамены','', 1, 1, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [7, 1, 12, 13, 1, '1.6. Вступительные экзамены','', 1, 2, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [8, 1, 14, 15, 1, '1.7. Просмотр работ ИЗО отделения','', 1, 1, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [9, 9, 1, 16, 0, '2. Участие учащихся в творческих мероприятиях','', 0, 0, false, false, false, false, false, false, false, false,'', 1, true, false, true, false, true, true, false, false, false, false, false, false, true, time(),1000],
            [10,9,2,3,1, '2.1. Международные мероприятия','',2,0,false,false,false,true,true,false,false,false,'',1,true,false,false,false,true,false,false,false,false,false,false,false,true,time(),1000],
            [11, 9, 4, 5, 1, '2.2. Межрегиональные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [12, 9, 6, 7, 1, '2.3. Городские мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [13, 9, 8, 9, 1, '2.4. Окружные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [14, 9, 10, 11, 1, '2.5. Районные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [15, 9, 12, 13, 1, '2.6. Школьные мероприятия(с описанием)','', 1, 0, true, true, true, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [16, 9, 14, 15, 1, '2.7. Школьные мероприятия(без описания)','', 1, 0, true, false, true, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [17, 17, 1, 16, 0, '3. Участие преподавателей в творческих мероприятиях','', 0, 0, false, false, false, false, false, false, false, false,'', 1, true, false, true, false, true, true, false, false, false, false, false, false, true, time(),1000],
            [18, 17, 2, 3, 1, '3.1. Международные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [19, 17, 4, 5, 1, '3.2. Межрегиональные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [20, 17, 6, 7, 1, '3.3. Городские мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [21, 17, 8, 9, 1, '3.4. Окружные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [22, 17, 10, 11, 1, '3.5. Районные мероприятия','', 2, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [23, 17, 12, 13, 1, '3.6. Школьные мероприятия(с описанием)','', 1, 0, true, true, true, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [24, 17, 14, 15, 1, '3.7. Школьные мероприятия(без описания)','', 1, 0, true, false, true, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [25, 25, 1, 6, 0, '4. Методическая работа','', 0, 0, false, false, false, false, false, false, false, false,'', 1, true, false, true, false, true, true, false, false, false, false, false, false, true, time(),1000],
            [26, 25, 2, 3, 1, '4.1. Открытые уроки','', 1, 0, false, false, false, false, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [27, 25, 4, 5, 1, '4.2. Курсы\, семинары\, конференции\, консультации\, мастер-классы и др.','', 1, 0, false, false, false, true, true, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [28, 28, 1, 10, 0, '5. Внеклассная работа','', 1, 0, false, false, false, false, false, false, false, false,'', 1, true, false, true, false, true, true, false, false, false, false, false, false, true, time(),1000],
            [29, 28, 2, 3, 1, '5.1. Внеклассная работа с учащимися','', 1, 0, false, false, false, true, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [30, 28, 4, 5, 1, '5.2. Работа с родителями','', 1, 0, false, false, false, true, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [31, 28, 6, 7, 1, '5.3. Посещение концертов','', 2, 0, false, false, false, true, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
            [32, 28, 8, 9, 1, '5.4. Посещение выставок','', 2, 0, false, false, false, true, false, false, false, false,'', 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(),1000],
        ])->execute();
        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 33)->execute();


        $this->createTableWithHistory('schoolplan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'title' => $this->string(100)->comment('Название мероприятия'),
            'datetime_in' => $this->integer()->notNull()->comment('Дата и время начала'),
            'datetime_out' => $this->integer()->notNull()->comment('Дата и время окончания'),
            'places' => $this->string(512)->comment('Место проведения'),
            'auditory_id' => $this->integer()->defaultValue(0)->comment('Аудитория'),
            'department_list' => $this->string(1024)->comment('Отделы'),
            'executors_list' => $this->string(1024)->comment('Ответственные'),
            'category_id' => $this->integer()->notNull()->comment('Категория мероприятия'),
            'activities_over_id' => $this->integer()->defaultValue(null)->comment('ИД мероприятия вне плана (подготовка к мероприятию)'),
            'form_partic' => $this->integer()->defaultValue(1)->comment('Форма участия'),
            'partic_price' => $this->string()->defaultValue(null)->comment('Стоимость участия'),
            'visit_poss' => $this->integer()->defaultValue(1)->comment('Возможность посещения'),
            'visit_content' => $this->text()->comment('Комментарий по посещению'),
            'format_event' => $this->integer()->defaultValue(1)->comment('Формат мероприятия'),
            'important_event' => $this->integer()->defaultValue(1)->comment('Значимость мероприятия'),
            'region_partners' => $this->string()->defaultValue(null)->comment('Зарубежные и региональные партнеры'),
            'site_url' => $this->string()->defaultValue(null)->comment('Ссылка на мероприятие (сайт/соцсети)'),
            'site_media' => $this->string()->defaultValue(null)->comment('Ссылка на медиаресурс'),
            'description' => $this->text()->defaultValue(null)->comment('Описание мероприятия'),
            'rider' => $this->text()->defaultValue(null)->comment('Технические требования'),
            'result' => $this->text()->defaultValue(null)->comment('Итоги мероприятия'),
            'num_users' => $this->integer()->comment('Количество участников'),
            'num_winners' => $this->integer()->comment('Количество победителей'),
            'num_visitors' => $this->integer()->comment('Количество зрителей'),
            'bars_flag' => $this->boolean()->comment('Отправлено в БАРС'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('schoolplan', 'План мероприятий');
        $this->db->createCommand()->resetSequence('schoolplan', 10000)->execute();

        $this->addForeignKey('schoolplan_ibfk_1', 'schoolplan', 'category_id', 'guide_plan_tree', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_2', 'schoolplan', 'auditory_id', 'auditory', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_3', 'schoolplan', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_4', 'schoolplan', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('activities_over', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'title' => $this->string(100)->comment('Название мероприятия'),
            'over_category' => $this->integer()->defaultValue(0)->comment('Категория мероприятия (подготовка, штатно, замена, отмена и пр.)'),
            'datetime_in' => $this->integer()->notNull()->comment('Дата и время начала'),
            'datetime_out' => $this->integer()->notNull()->comment('Дата и время окончания'),
            'auditory_id' => $this->integer()->defaultValue(0)->comment('Аудитория'),
            'department_list' => $this->string(1024)->comment('Отделы'),
            'executors_list' => $this->string(1024)->comment('Ответственные'),
            'description' => $this->text()->defaultValue(null)->comment('Описание мероприятия'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('activities_over' ,'Календарь мероприятий вне плана');
        $this->db->createCommand()->resetSequence('activities_over', 10000)->execute();

        $this->addForeignKey('activities_over_ibfk_1', 'activities_over', 'auditory_id', 'auditory', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_over_ibfk_2', 'activities_over', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_over_ibfk_3', 'activities_over', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('schoolplan_ibfk_5', 'schoolplan', 'activities_over_id', 'activities_over', 'id', 'SET DEFAULT', 'NO ACTION');

    }

    public function down()
    {
        $this->dropForeignKey('schoolplan_ibfk_5', 'schoolplan');
        $this->dropTableWithHistory('activities_over');
        $this->dropTableWithHistory('schoolplan');
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
