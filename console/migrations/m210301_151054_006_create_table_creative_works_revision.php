<?php

use yii\db\Migration;

class m210301_151054_006_create_table_creative_works_revision extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_works_revision}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'timestamp' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_revision}}', 'user_id');
        $this->createIndex('works_id', '{{%creative_works_revision}}', 'works_id');
    }

    public function down()
    {
        $this->dropTable('{{%creative_works_revision}}');
    }
}
