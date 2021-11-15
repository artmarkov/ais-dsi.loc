<?php

class m211113_190454_create_table_files_catalog_tree extends \artsoft\db\BaseMigration
{
    const TABLE_NAME_TREE = 'files_catalog_tree';

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
            'rules_list_read' => $this->string(1024),
            'rules_list_edit' => $this->string(1024),
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
        $this->addCommentOnTable(self::TABLE_NAME_TREE, 'Файловый каталог пользователя');

        $this->createIndex('files_catalog_tree_i1', self::TABLE_NAME_TREE, 'root');
        $this->createIndex('files_catalog_tree_i2', self::TABLE_NAME_TREE, 'lft');
        $this->createIndex('files_catalog_tree_i3', self::TABLE_NAME_TREE, 'rgt');
        $this->createIndex('files_catalog_tree_i4', self::TABLE_NAME_TREE, 'lvl');
        $this->createIndex('files_catalog_tree_i5', self::TABLE_NAME_TREE, 'active');
        $this->addForeignKey('files_catalog_tree_ibfk_1', self::TABLE_NAME_TREE, 'created_by', 'users', 'id', 'RESTRICT', 'RESTRICT');

        $this->db->createCommand()->batchInsert(self::TABLE_NAME_TREE, ['id', 'root', 'lft', 'rgt', 'lvl', 'name', 'rules_list_read',
            'rules_list_edit', 'icon', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all', 'child_allowed', 'created_at', 'created_by'], [
            [1, 1, 1, 2, 0, "Общая информация", "user,department,teacher,employees,student,parents", "department,teacher", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [2, 2, 1, 2, 0, "Информация для сотрудников", "employees", "", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [3, 3, 1, 2, 0, "Информация для преподавателей", "department,teacher", "", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [4, 4, 1, 2, 0, "Информация для учеников", "department,teacher,student,parents", "department,teacher", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
            [5, 5, 1, 2, 0, "Информация для родителей", "department,teacher,parents", "department,teacher", "", 1, true, false, false, false, true, false, false, false, false, false, false, false, true, time(), 1000],
        ])->execute();
        $this->db->createCommand()->resetSequence(self::TABLE_NAME_TREE, 4)->execute();

    }

    public function down()
    {
        $this->dropTable(self::TABLE_NAME_TREE);
    }
}
