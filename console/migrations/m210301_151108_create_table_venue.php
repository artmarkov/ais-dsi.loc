<?php

use yii\db\Migration;

class m210301_151108_create_table_venue extends Migration
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

        $this->createTable('{{%venue_district}}', [
            'id' => $this->primaryKey(8),
            'sity_id' => $this->integer(8)->notNull(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string(16)->notNull(),
        ], $tableOptions);

        $this->createIndex('sity_id', '{{%venue_district}}', 'sity_id');
        $this->addForeignKey('venue_district_ibfk_1', '{{%venue_district}}', 'sity_id', '{{%venue_sity}}', 'id', 'NO ACTION', 'NO ACTION');


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
        $this->addForeignKey('venue_place_ibfk_4', '{{%venue_place}}', 'created_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('venue_place_ibfk_5', '{{%venue_place}}', 'updated_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTable('{{%venue_place}}');
        $this->dropTable('{{%venue_district}}');
        $this->dropTable('{{%venue_sity}}');
        $this->dropTable('{{%venue_country}}');
    }
}
