<?php

use yii\db\Migration;

class m210301_151103_027_create_table_department extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%department}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'division_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('division_id', '{{%department}}', 'division_id');
        $this->addForeignKey('department_ibfk_1', '{{%department}}', 'division_id', '{{%division}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%department}}');
    }
}
