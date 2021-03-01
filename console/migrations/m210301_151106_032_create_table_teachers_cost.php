<?php

use yii\db\Migration;

class m210301_151106_032_create_table_teachers_cost extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

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
    }

    public function down()
    {
        $this->dropTable('{{%teachers_cost}}');
    }
}
