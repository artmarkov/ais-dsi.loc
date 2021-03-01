<?php

use yii\db\Migration;

class m210301_151108_035_create_table_venue_place extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%venue_place}}', [
            'id' => $this->primaryKey(8),
            'country_id' => $this->integer(8)->notNull(),
            'sity_id' => $this->integer(8)->notNull(),
            'district_id' => $this->integer(8)->notNull(),
            'name' => $this->string(127)->notNull(),
            'address' => $this->string()->notNull(),
            'phone' => $this->string(24)->notNull(),
            'phone_optional' => $this->string(24)->notNull(),
            'email' => $this->string()->notNull(),
            'сontact_person' => $this->string(127)->notNull(),
            'coords' => $this->string(64),
            'map_zoom' => $this->smallInteger(2),
            'description' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('sity_id', '{{%venue_place}}', 'sity_id');
        $this->createIndex('updated_by', '{{%venue_place}}', 'updated_by');
        $this->createIndex('country_id', '{{%venue_place}}', 'country_id');
        $this->createIndex('created_by', '{{%venue_place}}', 'created_by');
        $this->createIndex('district_id', '{{%venue_place}}', 'district_id');
        $this->addForeignKey('venue_place_ibfk_1', '{{%venue_place}}', 'country_id', '{{%venue_country}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('venue_place_ibfk_2', '{{%venue_place}}', 'district_id', '{{%venue_district}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('venue_place_ibfk_3', '{{%venue_place}}', 'sity_id', '{{%venue_sity}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%venue_place}}');
    }
}
