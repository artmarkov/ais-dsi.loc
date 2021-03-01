<?php

use yii\db\Migration;

class m210301_151102_025_create_table_venue_country extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%venue_country}}', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(127)->notNull(),
            'fips' => $this->string(2)->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%venue_country}}');
    }
}
