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

        $this->createIndex('direction_id', '{{%teachers_cost}}', 'direction_id');
        $this->createIndex('stake_id', '{{%teachers_cost}}', 'stake_id');
        $this->addForeignKey('teachers_cost_ibfk_1', '{{%teachers_cost}}', 'direction_id', '{{%teachers_direction}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_cost_ibfk_2', '{{%teachers_cost}}', 'stake_id', '{{%teachers_stake}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%teachers_work}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->createTable('{{%teachers_stake}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(32),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue('1'),
        ], $tableOptions);

        $this->createTable('{{%teachers_position}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->createTable('{{%teachers_level}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->createTable('{{%teachers_direction}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

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
            'work_id' => $this->tinyInteger(2)->unsigned(),
            'level_id' => $this->tinyInteger(2)->unsigned(),
            'cost_main_id' => $this->tinyInteger(2)->unsigned(),
            'cost_optional_id' => $this->tinyInteger(2)->unsigned(),
            'tab_num' => $this->string(16),
            'timestamp_serv' => $this->integer(),
            'timestamp_serv_spec' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('status_id', '{{%teachers}}', 'position_id');
        $this->createIndex('user_id', '{{%teachers}}', 'user_id');
        $this->createIndex('work_id', '{{%teachers}}', 'work_id');
        $this->createIndex('cost_main_id', '{{%teachers}}', 'cost_main_id');
        $this->createIndex('cost_optional_id', '{{%teachers}}', 'cost_optional_id');
        $this->createIndex('id', '{{%teachers}}', 'id', true);
        $this->createIndex('level_id', '{{%teachers}}', 'level_id');
        $this->addForeignKey('teachers_ibfk_1', '{{%teachers}}', 'level_id', '{{%teachers_level}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_2', '{{%teachers}}', 'position_id', '{{%teachers_position}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_3', '{{%teachers}}', 'work_id', '{{%teachers_work}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_4', '{{%teachers}}', 'cost_main_id', '{{%teachers_cost}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_ibfk_5', '{{%teachers}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
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
