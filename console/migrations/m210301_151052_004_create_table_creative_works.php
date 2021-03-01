<?php

use yii\db\Migration;

class m210301_151052_004_create_table_creative_works extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_works}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'category_id' => $this->tinyInteger(2)->unsigned()->notNull(),
            'name' => $this->string(512)->notNull(),
            'description' => $this->text()->notNull(),
            'status' => $this->integer(1)->notNull()->defaultValue('0')->comment('0-pending,1-published'),
            'comment_status' => $this->integer(1)->notNull()->defaultValue('1')->comment('0-closed,1-open'),
            'published_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('status', '{{%creative_works}}', 'status');
        $this->createIndex('apdated_by', '{{%creative_works}}', 'updated_by');
        $this->createIndex('cat_id', '{{%creative_works}}', 'category_id');
        $this->createIndex('created_by', '{{%creative_works}}', 'created_by');
        $this->addForeignKey('creative_works_ibfk_1', '{{%creative_works}}', 'category_id', '{{%creative_category}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%creative_works}}');
    }
}
