<?php

use yii\db\Migration;

class m210316_181416_create_table_user_relation extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_user_relation', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(127),
            'slug' => $this->string(64),
        ], $tableOptions);
        $this->db->createCommand()->batchInsert('guide_user_relation', ['id', 'name', 'slug'], [
            [1, 'Мать', 'Мать'],
            [2, 'Отец', 'Отец'],
            [3, 'Бабушка', 'Баб'],
            [4, 'Дедушка', 'Дед'],
        ])->execute();

        $this->createTable('user_family', [
            'id' => $this->primaryKey(8)->unsigned(),
            'relation_id' => $this->tinyInteger(2)->unsigned(),
            'user_main_id' => $this->integer(),
            'user_slave_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('user_slave_id', 'user_family', 'user_slave_id');
        $this->createIndex('user_main_id', 'user_family', 'user_main_id');
        $this->createIndex('relation_id', 'user_family', 'relation_id');

        $this->addForeignKey('user_family_ibfk_1', 'user_family', 'user_main_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('user_family_ibfk_2', 'user_family', 'user_slave_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('user_family_ibfk_3', 'user_family', 'relation_id', 'guide_user_relation', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropTable('user_family');
        $this->dropTable('guide_user_relation');
    }
}
