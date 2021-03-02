<?php

use yii\db\Migration;

class m210302_153750_create_table_routine extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%routine_cat}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(256)->notNull(),
            'color' => $this->string(128)->notNull(),
            'plan_flag' => $this->tinyInteger(1)->notNull()->comment('Учитывать при планировании'),
        ], $tableOptions);

        $this->createTable('{{%routine}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(1024)->notNull(),
            'cat_id' => $this->integer(8)->notNull(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('cat_id', '{{%routine}}', 'cat_id');
        $this->addForeignKey('routine_ibfk_1', '{{%routine}}', 'cat_id', '{{%routine_cat}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%routine}}');
        $this->dropTable('{{%routine_cat}}');
    }
}
