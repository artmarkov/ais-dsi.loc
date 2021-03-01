<?php

use yii\db\Migration;

class m210301_150325_create_table_auditory extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auditory_cat}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(256)->notNull(),
            'study_flag' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
            'order' => $this->integer(8)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%auditory_building}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(128)->notNull(),
            'slug' => $this->string(64)->notNull(),
            'address' => $this->string()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%auditory}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'building_id' => $this->integer(8)->unsigned()->notNull(),
            'cat_id' => $this->integer(8)->unsigned(),
            'study_flag' => $this->tinyInteger(1)->notNull()->defaultValue('0'),
            'num' => $this->integer(3),
            'name' => $this->string(128),
            'floor' => $this->string(32)->notNull(),
            'area' => $this->float()->notNull(),
            'capacity' => $this->integer(3)->notNull(),
            'description' => $this->string(),
            'order' => $this->integer(8)->unsigned()->notNull(),
            'status' => $this->integer(8)->notNull()->defaultValue('10'),
        ], $tableOptions);

        $this->createIndex('building_id', '{{%auditory}}', 'building_id');
        $this->createIndex('cat_id', '{{%auditory}}', 'cat_id');
        $this->addForeignKey('auditory_ibfk_1', '{{%auditory}}', 'cat_id', '{{%auditory_cat}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('auditory_ibfk_2', '{{%auditory}}', 'building_id', '{{%auditory_building}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%auditory}}');
        $this->dropTable('{{%auditory_building}}');
        $this->dropTable('{{%auditory_cat}}');
    }
}
