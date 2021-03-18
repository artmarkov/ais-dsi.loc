<?php

use yii\db\Migration;

class m210301_150456_create_table_teachers extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers_department}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'teachers_id' => $this->integer(8)->unsigned()->notNull(),
            'department_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('department_id', '{{%teachers_department}}', 'department_id');
        $this->createIndex('teachers_id', '{{%teachers_department}}', 'teachers_id');
        $this->addForeignKey('teachers_department_ibfk_2', '{{%teachers_department}}', 'department_id', '{{%department}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%teachers_cost}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'direction_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'stake_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'stake_value' => $this->float(),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_cost}}', ['id', 'direction_id', 'stake_id', 'stake_value'], [
            [1, 1, 1, 0],
            [2, 1, 2, 0],
            [3, 1, 3, 22200],
            [4, 1, 4, 21800],
            [5, 2, 1, 24000],
            [6, 2, 2, 23500],
            [7, 2, 3, 25700],
            [8, 2, 4, 25300],
        ])->execute();

        $this->createIndex('direction_id', '{{%teachers_cost}}', 'direction_id');
        $this->createIndex('stake_id', '{{%teachers_cost}}', 'stake_id');
        $this->addForeignKey('teachers_cost_ibfk_1', '{{%teachers_cost}}', 'direction_id', '{{%teachers_direction}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_cost_ibfk_2', '{{%teachers_cost}}', 'stake_id', '{{%teachers_stake}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%teachers_work}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_work}}', ['id', 'name', 'slug'], [
            [1, 'Основная', 'Осн'],
            [2, 'По совместительству', 'Совм'],
            [3, 'Внутреннее совмещение', 'Вн.совм.'],
        ])->execute();

        $this->createTable('{{%teachers_stake}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(32),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue('1'),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_stake}}', ['id', 'name', 'slug', 'status'], [
            [1, 'Без категории', 'БК', 1],
            [2, 'Соответствие категории', 'СК', 1],
            [3, 'Первая категория', 'ПК', 1],
            [4, 'Высшая категория', 'ВК', 1],
        ])->execute();

        $this->createTable('{{%teachers_position}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_position}}', ['id', 'name', 'slug'], [
            [1, 'Директор', 'Дир'],
            [2, 'Заместитель директора', 'Зам.'],
            [3, 'Руководитель отдела', 'Рук.отд'],
            [4, 'Преподаватель', 'Преп'],
        ])->execute();

        $this->createTable('{{%teachers_level}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_level}}', ['id', 'name', 'slug'], [
            [1, 'Высшее образование', 'ВО'],
            [2, 'Высшее непроф', 'ВН'],
            [3, 'Неполное высшее', 'НВ'],
            [4, 'Среднее проф', 'СП'],
        ])->execute();

        $this->createTable('{{%teachers_direction}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_direction}}', ['id', 'name', 'slug'], [
            [1, 'Педагогическая', 'Пед-я'],
            [2, 'Концертмейстерская', 'Конц-я'],
        ])->execute();

        $this->createTable('{{%teachers_bonus_item}}', [
            'id' => $this->primaryKey(8),
            'bonus_category_id' => $this->integer(8)->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'value_default' => $this->string(127),
            'measure_id' => $this->smallInteger(2)->unsigned()->comment('ед. измерения'),
            'bonus_rule_id' => $this->tinyInteger(2)->comment('правило обработки бонуса'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue('1')->comment('1-активна, 0-удалена'),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_bonus_item}}', ['id', 'bonus_category_id', 'name', 'slug', 'value_default', 'measure_id', 'bonus_rule_id', 'status'], [
            [1, 1, 'Кандидат наук', 'КН', '20', 3, 1, 1],
            [2, 1, 'Доктор наук', 'ДН', '30', 3, 1, 1],
            [3, 2, 'Народный артист', 'НА', '50', 3, 1, 1],
            [4, 2, 'Заслуженный деятель искусств', 'ЗДИ', '50', 3, 1, 1],
            [5, 2, 'Заслуженный артист', 'ЗА', '50', 3, 1, 1],
            [6, 2, 'Заслуженный работник культуры', 'ЗРК', '50', 3, 1, 1],
            [7, 2, 'Заслуженный учитель', 'ЗУ', '50', 3, 1, 1],
            [8, 2, 'Почетный работник культуры', 'ПРК', '30', 3, 1, 1],
            [9, 2, 'Обладатель нагрудного знака', 'ОНЗ', '30', 3, 1, 1],
            [10, 2, 'Звание лауреата', 'ЗЛ', '30', 3, 1, 1],
            [12, 3, 'Молодой специалист + проезд', 'МС+', '55', 3, 1, 1],
            [13, 3, 'Молодой специалист-отличник + проезд', 'МСО+', '65', 3, 1, 1],
            [14, 4, 'Руководство отделением', 'РО', '30', 3, 1, 1],
            [15, 4, 'Руководство выставочной работой', 'РВР', '30', 3, 1, 1],
            [16, 4, 'Участие в экспертной группе город', 'ЭГГ', '30', 3, 1, 1],
            [17, 4, 'Участие в экспертной группе округ', 'ЭГО', '15', 3, 1, 1],
            [18, 4, 'Заведование секцией', 'ЗС', '15', 3, 1, 1],
        ])->execute();

        $this->createIndex('status', '{{%teachers_bonus_item}}', 'status');
        $this->createIndex('bonus_category_id', '{{%teachers_bonus_item}}', 'bonus_category_id');
        $this->createIndex('bonus_rule_id', '{{%teachers_bonus_item}}', 'bonus_rule_id');
        $this->createIndex('measure_id', '{{%teachers_bonus_item}}', 'measure_id');
        $this->addForeignKey('teachers_bonus_item_ibfk_1', '{{%teachers_bonus_item}}', 'bonus_category_id', '{{%teachers_bonus_category}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_bonus_item_ibfk_2', '{{%teachers_bonus_item}}', 'measure_id', '{{%measure_unit}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%teachers_bonus_category}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(127)->notNull(),
            'multiple' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('{{%teachers_bonus_category}}', ['id', 'name', 'slug', 'multiple'], [
            [1, 'Ученая степень', 'Уч.ст', 0],
            [2, 'Звание', 'Звание', 0],
            [3, 'Уровень специалиста', 'Уровень', 0],
            [4, 'Специальные обязанности', 'Спец.обяз-ти', 0],
        ])->execute();

        $this->createTable('{{%teachers_bonus}}', [
            'id' => $this->primaryKey(8),
            'teachers_id' => $this->integer(8),
            'bonus_item_id' => $this->integer(8)->notNull(),
        ], $tableOptions);

        $this->createIndex('bonus_item_id', '{{%teachers_bonus}}', 'bonus_item_id');
        $this->createIndex('teachers_id', '{{%teachers_bonus}}', 'teachers_id');

        $this->createTable('{{%teachers}}', [
            'id' => $this->integer(8)->unsigned()->notNull(),
            'user_id' => $this->integer(),
            'position_id' => $this->tinyInteger(2)->unsigned(),
            'level_id' => $this->tinyInteger(2)->unsigned(),
            'tab_num' => $this->string(16),
            'timestamp_serv' => $this->integer(),
            'timestamp_serv_spec' => $this->integer(),
            'status' => $this->tinyInteger(1)->unsigned(),
        ], $tableOptions);

        $this->createIndex('status_id', '{{%teachers}}', 'position_id');
        $this->createIndex('user_id', '{{%teachers}}', 'user_id');
        $this->createIndex('id', '{{%teachers}}', 'id', true);
        $this->createIndex('level_id', '{{%teachers}}', 'level_id');
        $this->addForeignKey('teachers_ibfk_1', '{{%teachers}}', 'level_id', '{{%teachers_level}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_2', '{{%teachers}}', 'position_id', '{{%teachers_position}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_3', '{{%teachers}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%teachers_activity}}', [
            'id' => $this->primaryKey(),
            'teachers_id' => $this->integer(8)->unsigned()->notNull(),
            'work_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'direction_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'stake_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex('work_id', '{{%teachers_activity}}', 'work_id');
        $this->createIndex('direction_id', '{{%teachers_activity}}', 'direction_id');
        $this->createIndex('stake_id', '{{%teachers_activity}}', 'stake_id');
        $this->createIndex('teachers_id', '{{%teachers_activity}}', 'teachers_id');
        $this->addForeignKey('teachers_activity_ibfk_1', '{{%teachers_activity}}', 'work_id', '{{%teachers_work}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_2', '{{%teachers_activity}}', 'direction_id', '{{%teachers_direction}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_3', '{{%teachers_activity}}', 'stake_id', '{{%teachers_stake}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('teachers_activity_ibfk_4', '{{%teachers_activity}}', 'teachers_id', '{{%teachers}}', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('{{%teachers_activity}}');
        $this->dropTable('{{%teachers}}');
        $this->dropTable('{{%teachers_bonus}}');
        $this->dropTable('{{%teachers_bonus_category}}');
        $this->dropTable('{{%teachers_bonus_item}}');
        $this->dropTable('{{%teachers_direction}}');
        $this->dropTable('{{%teachers_level}}');
        $this->dropTable('{{%teachers_position}}');
        $this->dropTable('{{%teachers_stake}}');
        $this->dropTable('{{%teachers_work}}');
        $this->dropTable('{{%teachers_cost}}');
        $this->dropTable('{{%teachers_department}}');
    }
}
