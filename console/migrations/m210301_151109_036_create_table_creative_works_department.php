<?php

use yii\db\Migration;

class m210301_151109_036_create_table_creative_works_department extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_works_department}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'department_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_department}}', 'department_id');
        $this->createIndex('works_id', '{{%creative_works_department}}', 'works_id');
        $this->addForeignKey('creative_works_department_ibfk_2', '{{%creative_works_department}}', 'department_id', '{{%department}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%creative_works_department}}');
    }
}
