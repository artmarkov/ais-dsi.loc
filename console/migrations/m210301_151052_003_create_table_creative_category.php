<?php

use yii\db\Migration;

class m210301_151052_003_create_table_creative_category extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_category}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(256)->notNull(),
            'description' => $this->text()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%creative_category}}');
    }
}
