<?php

use yii\db\Migration;

class m210318_144607_create_table_teachers_activity extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

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
    }
}
