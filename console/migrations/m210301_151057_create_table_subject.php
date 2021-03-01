<?php

use yii\db\Migration;

class m210301_151057_create_table_subject extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_category_item}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127),
            'slug' => $this->string(64)->notNull(),
            'order' => $this->tinyInteger(2)->unsigned()->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%subject_department}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'subject_id' => $this->integer(8)->unsigned()->notNull(),
            'department_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('department_id', '{{%subject_department}}', 'department_id');
        $this->createIndex('subject_id', '{{%subject_department}}', 'subject_id');
        $this->addForeignKey('subject_department_ibfk_1', '{{%subject_department}}', 'department_id', '{{%department}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_department_ibfk_2', '{{%subject_department}}', 'subject_id', '{{%subject}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%subject_category}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'subject_id' => $this->integer(8)->unsigned()->notNull(),
            'category_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('category_id', '{{%subject_category}}', 'category_id');
        $this->createIndex('subject_id', '{{%subject_category}}', 'subject_id');
        $this->addForeignKey('subject_category_ibfk_1', '{{%subject_category}}', 'category_id', '{{%subject_category_item}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_category_ibfk_2', '{{%subject_category}}', 'subject_id', '{{%subject}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%subject_vid}}', [
            'id' => $this->tinyInteger(2)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(64)->notNull(),
            'slug' => $this->string(32)->notNull(),
            'qty_min' => $this->smallInteger(3)->unsigned()->notNull(),
            'qty_max' => $this->smallInteger(3)->unsigned()->notNull(),
            'info' => $this->text()->notNull(),
            'status' => $this->tinyInteger(1)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%subject_type}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(127)->notNull(),
            'slug' => $this->string(64)->notNull(),
            'status' => $this->tinyInteger(1)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'name' => $this->string(64),
            'slug' => $this->string(32),
            'order' => $this->integer(8)->unsigned()->notNull(),
            'status' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%subject}}');
        $this->dropTable('{{%subject_type}}');
        $this->dropTable('{{%subject_vid}}');
        $this->dropTable('{{%subject_category}}');
        $this->dropTable('{{%subject_department}}');
        $this->dropTable('{{%subject_category_item}}');
    }
}
