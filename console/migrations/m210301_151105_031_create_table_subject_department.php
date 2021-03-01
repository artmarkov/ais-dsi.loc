<?php

use yii\db\Migration;

class m210301_151105_031_create_table_subject_department extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_department}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'subject_id' => $this->integer(8)->unsigned()->notNull(),
            'department_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('department_id', '{{%subject_department}}', 'department_id');
        $this->createIndex('subject_id', '{{%subject_department}}', 'subject_id');
        $this->addForeignKey('subject_department_ibfk_1', '{{%subject_department}}', 'department_id', '{{%department}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_department_ibfk_2', '{{%subject_department}}', 'subject_id', '{{%subject}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%subject_department}}');
    }
}
