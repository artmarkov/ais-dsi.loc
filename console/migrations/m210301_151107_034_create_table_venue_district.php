<?php

use yii\db\Migration;

class m210301_151107_034_create_table_venue_district extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%venue_district}}', [
            'id' => $this->primaryKey(8),
            'sity_id' => $this->integer(8)->notNull(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string(16)->notNull(),
        ], $tableOptions);

        $this->createIndex('sity_id', '{{%venue_district}}', 'sity_id');
        $this->addForeignKey('venue_district_ibfk_1', '{{%venue_district}}', 'sity_id', '{{%venue_sity}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%venue_district}}');
    }
}
