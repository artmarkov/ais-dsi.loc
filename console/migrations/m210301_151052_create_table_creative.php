<?php

use yii\db\Migration;

class m210301_151052_create_table_creative extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_works_department}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'department_id' => $this->tinyInteger(2)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_department}}', 'department_id');
        $this->createIndex('works_id', '{{%creative_works_department}}', 'works_id');
        $this->addForeignKey('creative_works_department_ibfk_2', '{{%creative_works_department}}', 'department_id', '{{%department}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->createTable('{{%creative_works_revision}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'timestamp' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_revision}}', 'user_id');
        $this->createIndex('works_id', '{{%creative_works_revision}}', 'works_id');


        $this->createTable('{{%creative_works_author}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'timestamp_weight' => $this->integer()->comment('Отчетный период надбавки'),
            'weight' => $this->smallInteger(1)->unsigned()->defaultValue('0')->comment('Надбавка'),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_author}}', 'author_id');
        $this->createIndex('works_id', '{{%creative_works_author}}', 'works_id');


        $this->createTable('{{%creative_category}}', [
            'id' => $this->tinyInteger(2)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(256)->notNull(),
            'description' => $this->text()->notNull(),
        ], $tableOptions);


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
        $this->addForeignKey('creative_works_ibfk_2', '{{%creative_works}}', 'updated_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_ibfk_3', '{{%creative_works}}', 'created_by', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_author_ibfk_1', '{{%creative_works_author}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_author_ibfk_2', '{{%creative_works_author}}', 'author_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_revision_ibfk_1', '{{%creative_works_revision}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_revision_ibfk_2', '{{%creative_works_revision}}', 'user_id', '{{%user}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_department_ibfk_1', '{{%creative_works_department}}', 'works_id', '{{%creative_works}}', 'id', 'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTable('{{%creative_works}}');
        $this->dropTable('{{%creative_category}}');
        $this->dropTable('{{%creative_works_author}}');
        $this->dropTable('{{%creative_works_revision}}');
        $this->dropTable('{{%creative_works_department}}');
    }
}
