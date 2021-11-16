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
            'youtube_code' => $this->string(1024),
            'rules_list_read' => $this->string(1024),
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
            'child_allowed' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),

        ], $tableOptions);
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Руководство пользователя');

        $this->createIndex('tree_HT1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex('tree_HT2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex('tree_HT3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex('tree_HT4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex('tree_HT5', self::TABLE_NAME_TREE, 'active');

        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'rules_list_read', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed', 'created_at', 'created_by'], [
            [1, 1, 1, 2, 0, "Общий справочник", "user,department,teacher,employees,student,parents,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [2, 2, 1, 2, 0, "Для преподавателя", "department,teacher,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [3, 3, 1, 2, 0, "Для руководителя отдела", "department,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [4, 4, 1, 2, 0, "Для ученика и родителя", "department,teacher,student,parents,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [5, 5, 1, 2, 0, "Для сотрудника", "employees,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [6, 6, 1, 2, 0, "Для администратора", "system,administrator", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
        ])->execute();
        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 7)->execute();

    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
