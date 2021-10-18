<?php

class m211018_151837_create_table_board extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTableWithHistory('board', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'author_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull()->defaultValue(0),
            'importance_id' => $this->integer()->notNull()->defaultValue(0),
            'title' => $this->string(127)->notNull(),
            'description' => $this->string(1024)->notNull(),
            'recipients_list' => $this->string(1024),
            'board_date' => $this->integer()->notNull(),
            'delete_date' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('board', 'Доска объявлений');
        $this->addForeignKey('board_ibfk_1', 'board', 'author_id', 'users', 'id', 'NO ACTION', 'NO ACTION');

        $this->db->createCommand()->resetSequence('board', 1000)->execute();
    }

    public function down()
    {
        $this->dropForeignKey('board_ibfk_1', 'board');
        $this->dropTableWithHistory('board');
    }
}