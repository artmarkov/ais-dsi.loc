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
    }
}
