<?php

use yii\db\Migration;

class m210301_151105_030_create_table_subject_category extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subject_category}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'subject_id' => $this->integer(8)->unsigned()->notNull(),
            'category_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('category_id', '{{%subject_category}}', 'category_id');
        $this->createIndex('subject_id', '{{%subject_category}}', 'subject_id');
        $this->addForeignKey('subject_category_ibfk_1', '{{%subject_category}}', 'category_id', '{{%subject_category_item}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('subject_category_ibfk_2', '{{%subject_category}}', 'subject_id', '{{%subject}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%subject_category}}');
    }
}
