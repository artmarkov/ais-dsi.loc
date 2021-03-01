<?php

use yii\db\Migration;

class m210301_151053_005_create_table_creative_works_author extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%creative_works_author}}', [
            'id' => $this->primaryKey(8)->unsigned(),
            'works_id' => $this->integer(8)->unsigned()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'timestamp_weight' => $this->integer()->comment('Отчетный период надбавки'),
            'weight' => $this->smallInteger(1)->unsigned()->defaultValue('0')->comment('Надбавка'),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%creative_works_author}}', 'author_id');
        $this->createIndex('works_id', '{{%creative_works_author}}', 'works_id');
    }

    public function down()
    {
        $this->dropTable('{{%creative_works_author}}');
    }
}
