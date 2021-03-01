<?php

use yii\db\Migration;

class m210301_151102_create_table_event extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%event_category}}', [
            'id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(128)->notNull(),
            'color' => $this->string(32),
            'text_color' => $this->string(32),
            'border_color' => $this->string(32),
            'rendering' => $this->tinyInteger(1)->notNull()->defaultValue('0')->comment('как фон или бар'),
            'description' => $this->string(256),
        ], $tableOptions);

        $this->createTable('{{%event}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->smallInteger(3)->unsigned()->notNull(),
            'auditory_id' => $this->integer(8)->unsigned()->notNull(),
            'title' => $this->string(100),
            'description' => $this->text(),
            'start_timestamp' => $this->integer()->notNull(),
            'end_timestamp' => $this->integer(),
            'all_day' => $this->tinyInteger(1)->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('category_id', '{{%event}}', 'category_id');
        $this->addForeignKey('event_ibfk_1', '{{%event}}', 'category_id', '{{%event_category}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%event}}');
        $this->dropTable('{{%event_category}}');
    }
}
