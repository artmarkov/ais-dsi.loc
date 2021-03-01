<?php

use yii\db\Migration;

class m210301_151102_026_create_table_venue_sity extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%venue_sity}}', [
            'id' => $this->primaryKey(8),
            'country_id' => $this->integer(8),
            'name' => $this->string(64)->notNull(),
            'latitude' => $this->float(),
            'longitude' => $this->float(),
        ], $tableOptions);

        $this->createIndex('country_id', '{{%venue_sity}}', 'country_id');
        $this->createIndex('latitude', '{{%venue_sity}}', 'latitude');
        $this->createIndex('longitude', '{{%venue_sity}}', 'longitude');
        $this->addForeignKey('venue_sity_ibfk_1', '{{%venue_sity}}', 'country_id', '{{%venue_country}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%venue_sity}}');
    }
}
