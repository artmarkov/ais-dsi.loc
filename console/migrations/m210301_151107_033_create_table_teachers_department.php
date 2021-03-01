<?php

use yii\db\Migration;

class m210301_151107_033_create_table_teachers_department extends Migration
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
    }

    public function down()
    {
        $this->dropTable('{{%teachers_department}}');
    }
}
