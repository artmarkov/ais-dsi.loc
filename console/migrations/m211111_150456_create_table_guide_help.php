<?php

class m211111_150456_create_table_guide_help extends \artsoft\db\BaseMigration
{
    const TABLE_NAME_TREE = 'guide_help_tree';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TABLE_NAME_TREE, [
            'id' => $this->bigPrimaryKey(),
            'root' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'lvl' => $this->smallInteger(5)->notNull(),
            'name' => $this->string(512)->notNull(),
            'description' => $this->string(1024),
            'icon' => $this->string(255),
            'icon_type' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'selected' => $this->boolean()->notNull()->defaultValue(false),
            'disabled' => $this->boolean()->notNull()->defaultValue(false),
            'readonly' => $this->boolean()->notNull()->defaultValue(false),
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'collapsed' => $this->boolean()->notNull()->defaultValue(false),
            'movable_u' => $this->boolean()->notNull()->defaultValue(true),
            'movable_d' => $this->boolean()->notNull()->defaultValue(true),
            'movable_l' => $this->boolean()->notNull()->defaultValue(true),
            'movable_r' => $this->boolean()->notNull()->defaultValue(true),
            'removable' => $this->boolean()->notNull()->defaultValue(true),
            'removable_all' => $this->boolean()->notNull()->defaultValue(false),
            'child_allowed' => $this->boolean()->notNull()->defaultValue(true)

        ], $tableOptions);
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Руководство пользователя');

        $this->createIndex('tree_HT1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex('tree_HT2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex('tree_HT3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex('tree_HT4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex('tree_HT5', self::TABLE_NAME_TREE, 'active');

        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed'], [
            [1, 1, 1, 2, 0, "Введение", "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [2, 2, 1, 2, 0, "Начало работы", "", 1, true, false, false, false, true, false, true, true, true, true, true, false, true],
            [3, 3, 1, 8, 0, "Регистрация и авторизация пользователей", "", 1, true, false, true, false, true, false, false, false, false, false, false, false, true],
            ])->execute();
        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 4)->execute();

    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
