<?php

use yii\db\Migration;

class m210301_150456_create_table_teachers extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_teachers_direction', [
            'id' =>  $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_direction' ,'Направление деятельности');
        $this->db->createCommand()->batchInsert('guide_teachers_direction', ['id', 'name', 'slug'], [
            [1, 'Педагогическая', 'Пед-я'],
            [2, 'Концертмейстерская', 'Конц-я'],
        ])->execute();

        $this->createTable('guide_teachers_direction_vid', [
            'id' =>  $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_direction_vid' ,'Вид деятельности');
        $this->db->createCommand()->batchInsert('guide_teachers_direction_vid', ['id', 'name', 'slug'], [
            [1, 'Основная', 'Осн-я'],
            [2, 'Дополнительная(внутреннее совмещение)', 'Доп-я'],
        ])->execute();

        $this->createTable('guide_teachers_stake', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(32),
            'status' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_stake' ,'Ставки');
        $this->db->createCommand()->batchInsert('guide_teachers_stake', ['id', 'name', 'slug', 'status'], [
            [1, 'Без категории', 'БК', 1],
            [2, 'Соответствие категории', 'СК', 1],
            [3, 'Первая категория', 'ПК', 1],
            [4, 'Высшая категория', 'ВК', 1],
        ])->execute();

        $this->createTableWithHistory('teachers_cost', [
            'id' =>  $this->primaryKey(),
            'direction_id' => $this->integer()->notNull(),
            'stake_id' => $this->integer()->notNull(),
            'stake_value' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_cost' ,'Значения ставки');
        $this->db->createCommand()->resetSequence('teachers_cost', 1)->execute();

        $this->db->createCommand()->batchInsert('teachers_cost', ['direction_id', 'stake_id', 'stake_value', 'created_at', 'updated_at', 'created_by', 'updated_by'], [
            [1, 1, 0, time(), time(), 1000, 1000],
            [1, 2, 0, time(), time(), 1000, 1000],
            [1, 3, 22200, time(), time(), 1000, 1000],
            [1, 4, 21800, time(), time(), 1000, 1000],
            [2, 1, 24000, time(), time(), 1000, 1000],
            [2, 2, 23500, time(), time(), 1000, 1000],
            [2, 3, 25700, time(), time(), 1000, 1000],
            [2, 4, 25300, time(), time(), 1000, 1000],
        ])->execute();

        $this->createIndex('direction_id', 'teachers_cost', 'direction_id');
        $this->createIndex('stake_id', 'teachers_cost', 'stake_id');
        $this->addForeignKey('teachers_cost_ibfk_1', 'teachers_cost', 'direction_id', 'guide_teachers_direction', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_cost_ibfk_2', 'teachers_cost', 'stake_id', 'guide_teachers_stake', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('guide_teachers_work', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_work' ,'Вмд работы');
        $this->db->createCommand()->batchInsert('guide_teachers_work', ['id', 'name', 'slug'], [
            [1, 'На постоянной основе', 'Пост'],
            [2, 'По совместительству', 'Совм'],
        ])->execute();

        $this->createTable('guide_teachers_position', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_position' ,'Должность');
        $this->db->createCommand()->batchInsert('guide_teachers_position', ['id', 'name', 'slug'], [
            [1, 'Директор', 'Дир'],
            [2, 'Заместитель директора', 'Зам.'],
            [3, 'Руководитель отдела', 'Рук.отд'],
            [4, 'Преподаватель', 'Преп'],
        ])->execute();

        $this->createTable('guide_teachers_level', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_level' ,'Образование');
        $this->db->createCommand()->batchInsert('guide_teachers_level', ['id', 'name', 'slug'], [
            [1, 'Высшее образование', 'ВО'],
            [2, 'Высшее непроф', 'ВН'],
            [3, 'Неполное высшее', 'НВ'],
            [4, 'Среднее проф', 'СП'],
        ])->execute();

        $this->createTable('guide_teachers_bonus_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(127)->notNull(),
            'multiple' =>$this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_bonus_category' ,'Категории достижений');
        $this->db->createCommand()->batchInsert('guide_teachers_bonus_category', ['id', 'name', 'slug', 'multiple'], [
            [1, 'Ученая степень', 'Уч.ст', 0],
            [2, 'Звание', 'Звание', 0],
            [3, 'Уровень специалиста', 'Уровень', 0],
            [4, 'Специальные обязанности', 'Спец.обяз-ти', 0],
        ])->execute();


        $this->createTable('guide_teachers_bonus', [
            'id' => $this->primaryKey(),
            'bonus_category_id' => $this->integer()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'value_default' => $this->string(127),
            'status' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_teachers_bonus' ,'Достижения');
        $this->db->createCommand()->batchInsert('guide_teachers_bonus', ['id', 'bonus_category_id', 'name', 'slug', 'value_default', 'status'], [
            [1, 1, 'Кандидат наук', 'КН', '20', 1],
            [2, 1, 'Доктор наук', 'ДН', '30', 1],
            [3, 2, 'Народный артист', 'НА', '50', 1],
            [4, 2, 'Заслуженный деятель искусств', 'ЗДИ', '50', 1],
            [5, 2, 'Заслуженный артист', 'ЗА', '50', 1],
            [6, 2, 'Заслуженный работник культуры', 'ЗРК', '50', 1],
            [7, 2, 'Заслуженный учитель', 'ЗУ', '50', 1],
            [8, 2, 'Почетный работник культуры', 'ПРК', '30', 1],
            [9, 2, 'Обладатель нагрудного знака', 'ОНЗ', '30', 1],
            [10, 2, 'Звание лауреата', 'ЗЛ', '30', 1],
            [12, 3, 'Молодой специалист + проезд', 'МС+', '55', 1],
            [13, 3, 'Молодой специалист-отличник + проезд', 'МСО+', '65', 1],
            [14, 4, 'Руководство отделением', 'РО', '30', 1],
            [15, 4, 'Руководство выставочной работой', 'РВР', '30', 1],
            [16, 4, 'Участие в экспертной группе город', 'ЭГГ', '30', 1],
            [17, 4, 'Участие в экспертной группе округ', 'ЭГО', '15', 1],
            [18, 4, 'Заведование секцией', 'ЗС', '15', 1],
        ])->execute();

        $this->createIndex('status', 'guide_teachers_bonus', 'status');
        $this->createIndex('bonus_category_id', 'guide_teachers_bonus', 'bonus_category_id');
        $this->addForeignKey('guide_teachers_bonus_ibfk_1', 'guide_teachers_bonus', 'bonus_category_id', 'guide_teachers_bonus_category', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('teachers', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 99999)',
            'user_common_id' => $this->integer(),
            'position_id' => $this->integer(),
            'work_id' => $this->integer(),
            'level_id' => $this->integer(),
            'tab_num' => $this->string(16),
            'department_list' => $this->string(1024),
            'year_serv' => $this->float(),
            'year_serv_spec' => $this->float(),
            'date_serv' => $this->integer(),
            'date_serv_spec' => $this->integer(),
            'bonus_list' => $this->string(1024),
            'bonus_summ' => $this->float(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers' ,'Преподаватели');
        $this->db->createCommand()->resetSequence('teachers', 1000)->execute();
        $this->createIndex('position_id', 'teachers', 'position_id');
        $this->createIndex('work_id', 'teachers', 'work_id');
        $this->createIndex('user_common_id', 'teachers', 'user_common_id');
        $this->createIndex('level_id', 'teachers', 'level_id');
        $this->addForeignKey('teachers_ibfk_1', 'teachers', 'level_id', 'guide_teachers_level', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_2', 'teachers', 'position_id', 'guide_teachers_position', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_3', 'teachers', 'work_id', 'guide_teachers_work', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTableWithHistory('teachers_activity', [
            'id' => $this->primaryKey(),
            'teachers_id' => $this->integer()->notNull(),
            'direction_vid_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'stake_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('teachers_activity' ,'Сведения о трудовой деятельности');
        $this->createIndex('direction_vid_id', 'teachers_activity', 'direction_vid_id');
        $this->addForeignKey('teachers_activity_ibfk_1', 'teachers_activity', 'direction_vid_id', 'guide_teachers_direction_vid', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_2', 'teachers_activity', 'direction_id', 'guide_teachers_direction', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_3', 'teachers_activity', 'stake_id', 'guide_teachers_stake', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_4', 'teachers_activity', 'teachers_id', 'teachers', 'id', 'CASCADE', 'CASCADE');

        $this->db->createCommand()->createView('teachers_view', '
         SELECT users.id AS user_id, user_common.id AS user_common_id, teachers.id AS teachers_id, users.username, users.email, users.status AS user_status, 
                user_common.status, position_id, department_list, user_common.last_name,user_common.first_name,user_common.middle_name, 
                CONCAT(user_common.last_name, \' \',user_common.first_name, \' \',user_common.middle_name) AS fullname, 
                CONCAT(user_common.last_name ,\' \', left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'.\') as fio, 
                CONCAT(left(user_common.first_name, 1), \'.\', left(user_common.middle_name, 1), \'. \', user_common.last_name) as iof
        FROM teachers 
        INNER JOIN user_common ON user_common.id = teachers.user_common_id 
        LEFT JOIN users ON user_common.user_id = users.id 
        WHERE user_common.user_category=\'teachers\'
        ORDER BY user_common.last_name, user_common.first_name
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_fio', 'teachers_view', 'teachers_id', 'fio', 'fio', 'status', null, 'Преподаватели (Фамилия И.О.)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_fullname', 'teachers_view', 'teachers_id', 'fullname', 'fullname', 'status', null, 'Преподаватели (Фамилия Имя Отчество)'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_users', 'teachers_view', 'teachers_id', 'user_id', 'teachers_id', 'status', null, 'Преподаватели (ссылка на id учетной записи)'],
        ])->execute();

        $this->db->createCommand()->createView('teachers_stake_view', '
        SELECT teachers_activity.teachers_id, teachers_activity.stake_id, teachers_cost.stake_value
        FROM teachers_activity INNER JOIN teachers_cost  ON teachers_activity.stake_id = teachers_cost.id
        WHERE teachers_activity.direction_vid_id = 1;   
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers_stake', 'teachers_stake_view', 'teachers_id', 'stake_value', 'stake_value', null, null, 'Преподаватели (Основная ставка)'],
        ])->execute();
    }

    public function down()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_stake'])->execute();
        $this->db->createCommand()->dropView('teachers_stake_view')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_users'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_fullname'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers_fio'])->execute();
        $this->db->createCommand()->dropView('teachers_view')->execute();
        $this->dropForeignKey('teachers_cost_ibfk_1', 'teachers_cost');
        $this->dropForeignKey('teachers_cost_ibfk_2', 'teachers_cost');
        $this->dropForeignKey('guide_teachers_bonus_ibfk_1', 'guide_teachers_bonus');
        $this->dropForeignKey('teachers_ibfk_1', 'teachers');
        $this->dropForeignKey('teachers_ibfk_2', 'teachers');
        $this->dropForeignKey('teachers_ibfk_3', 'teachers');
        $this->dropForeignKey('teachers_activity_ibfk_1', 'teachers_activity');
        $this->dropForeignKey('teachers_activity_ibfk_2', 'teachers_activity');
        $this->dropForeignKey('teachers_activity_ibfk_3', 'teachers_activity');
        $this->dropForeignKey('teachers_activity_ibfk_4', 'teachers_activity');
        $this->dropTableWithHistory('teachers_activity');
        $this->dropTableWithHistory('teachers');
        $this->dropTable('guide_teachers_bonus_category');
        $this->dropTable('guide_teachers_bonus');
        $this->dropTable('guide_teachers_direction_vid');
        $this->dropTable('guide_teachers_direction');
        $this->dropTable('guide_teachers_level');
        $this->dropTable('guide_teachers_position');
        $this->dropTable('guide_teachers_stake');
        $this->dropTable('guide_teachers_work');
        $this->dropTableWithHistory('teachers_cost');
    }
}
