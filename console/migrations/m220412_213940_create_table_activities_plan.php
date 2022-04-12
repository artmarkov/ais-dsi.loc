<?php


class m220412_213940_create_table_activities_plan extends \artsoft\db\BaseMigration
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
            'category_flag' => $this->smallInteger(1)->defaultValue(1)->comment('Категория мероприятия(внутреннее и(или) внешнее'),
            'preparing_flag' => $this->boolean()->defaultValue(false)->comment('Требуется подготовка к мероприятию'),
            'description_flag' => $this->boolean()->defaultValue(false)->comment('Требуется описание мероприятия'),
            'afisha_flag' => $this->boolean()->defaultValue(false)->comment('Требуется афиша и программа'),
            'bars_flag' => $this->boolean()->defaultValue(false)->comment('Требуется отправлять в БАРС'),
            'efficiency_flag' => $this->boolean()->defaultValue(false)->comment('Требуется подключение показателей эффективности'),
            'schedule_flag' => $this->boolean()->defaultValue(false)->comment('Мероприятие в рамках расписания занятий'),
            'consult_flag' => $this->boolean()->defaultValue(false)->comment('Мероприятие в рамках расписания консультаций'),
            'partners_flag' => $this->boolean()->defaultValue(false)->comment('Возможность участия региональных партнеров'),
            'commission_flag' => $this->smallInteger(1)->defaultValue(1)->comment('Требуется аттестационная или приемная комиссия'),
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
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Категории мероприятий');

        $this->createIndex('guide_plan_tree_i1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex('guide_plan_tree_i2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex('guide_plan_tree_i3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex('guide_plan_tree_i4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex('guide_plan_tree_i5', self::TABLE_NAME_TREE, 'active');
        $this->addForeignKey('guide_plan_tree_ibfk_1', self::TABLE_NAME_TREE, 'created_by', 'users', 'id', 'RESTRICT', 'RESTRICT');

//        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'rules_list_read',
//            'rules_list_edit', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed', 'created_at', 'created_by'], [
//            [1, 1, 1, 2, 0, "Общая информация", "user,department,teacher,employees,student,parents,administrator", "department,teacher,employees,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
//            [2, 2, 1, 2, 0, "Информация для сотрудников", "employees,administrator", "administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
//            [3, 3, 1, 2, 0, "Информация для преподавателей", "department,teacher,administrator", "department,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
//            [4, 4, 1, 2, 0, "Информация для учеников", "department,teacher,employees,student,parents,administrator", "department,teacher,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
//            [5, 5, 1, 2, 0, "Информация для родителей", "department,teacher,employees,parents,administrator", "department,teacher,employees,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
//        ])->execute();
//        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 6)->execute();


        $this->createTableWithHistory('activities_plan', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'author_id' => $this->integer()->notNull()->comment('Автор записи'),
            'name' => $this->string(100)->comment('Название мероприятия'),
            'datetime_in' => $this->integer()->notNull()->comment('Дата и время начала'),
            'datetime_out' => $this->integer()->notNull()->comment('Дата и время окончания'),
            'places' => $this->string(512)->comment('Место проведения'),
            'auditory_id' => $this->integer()->defaultValue(0)->comment('Аудитория'),
            'department_list' => $this->string(1024)->comment('Отделы'),
            'teachers_list' => $this->string(1024)->comment('Ответственные'),
            'category_id' => $this->integer()->notNull()->comment('Категория мероприятия'),
            'form_partic' => $this->integer()->defaultValue(0)->comment('Форма участия'),
            'partic_price' => $this->string()->defaultValue(null)->comment('Стоимость участия'),
            'visit_flag' => $this->integer()->defaultValue(0)->comment('Возможность посещения'),
            'visit_content' => $this->text()->comment('Комментарий по посещению'),
            'important_flag' => $this->integer()->defaultValue(0)->comment('Значимость мероприятия'),
            'region_partners' => $this->text()->defaultValue(null)->comment('Зарубежные и региональные партнеры'),
            'site_url' => $this->string()->defaultValue(null)->comment('Ссылка на мероприятие (сайт/соцсети)'),
            'site_media' => $this->string()->defaultValue(null)->comment('Ссылка на медиаресурс'),
            'description' => $this->text()->defaultValue(null)->comment('Описание мероприятия'),
            'rider' => $this->text()->defaultValue(null)->comment('Технические требования'),
            'result' => $this->text()->defaultValue(null)->comment('Итоги мероприятия'),
            'num_users' => $this->integer()->comment('Количество участников'),
            'num_winners' => $this->integer()->comment('Количество победителей'),
            'num_visitors' => $this->integer()->comment('Количество зрителей'),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('activities_plan' ,'План мероприятий');
        $this->db->createCommand()->resetSequence('activities', 10000)->execute();

        $this->addForeignKey('activities_ibfk_1', 'activities_plan', 'category_id', 'guide_plan_tree', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_ibfk_2', 'activities_plan', 'auditory_id', 'auditory', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_ibfk_3', 'activities_plan', 'author_id', 'user_common', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_ibfk_4', 'activities_plan', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('activities_ibfk_5', 'activities_plan', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTableWithHistory('activities_plan');
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
