<?php

class m220407_085200_create_table_document extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('document', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'user_common_id' => $this->integer()->notNull(),
            'title' => $this->string(127)->notNull(),
            'description' => $this->string(1024)->notNull(),
            'doc_date' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addCommentOnTable('document', 'Документы пользователей');
        $this->db->createCommand()->resetSequence('document', 1000)->execute();
        $this->addForeignKey('board_ibfk_1', 'document', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('board_ibfk_2', 'document', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('board_ibfk_3', 'document', 'user_common_id', 'user_common', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropForeignKey('board_ibfk_3', 'document');
        $this->dropForeignKey('board_ibfk_2', 'document');
        $this->dropForeignKey('board_ibfk_1', 'document');
        $this->dropTable('document');
    }
}