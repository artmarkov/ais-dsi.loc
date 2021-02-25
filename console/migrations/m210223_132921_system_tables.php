<?php

use artsoft\db\BaseMigration;

/**
 * Class m210223_132921_system_tables
 */
class m210223_132921_system_tables extends BaseMigration
{
    /**
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {

        $this->createTable('files', [
            'id'               => $this->primaryKey(),
            'name'             => $this->string(500)->notNull(),
            'size'             => $this->bigInteger()->notNull(),
            'content'          => $this->binary(),
            'type'             => $this->string(100)->notNull()->defaultValue('application/octet-stream'),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer()->notNull(),
            'deleted_at'       => $this->integer(),
            'deleted_by'       => $this->integer(),
            'object_type'      => $this->string(50),
            'object_id'        => $this->integer()
        ]);

        $this->createTableWithHistory('groups', [
            'id'               => $this->primaryKey(),
            'parent_id'        => $this->integer(),
            'name'             => $this->string(255)->notNull(),
            'type'             => $this->string(64)->notNull(),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer(),
            'updated_at'       => $this->integer()->notNull(),
            'updated_by'       => $this->integer(),
            'version'          => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('groups_parentid_fk', 'groups', 'parent_id', 'groups', 'id');

        $this->db->createCommand()->createView('group_childs','
         with recursive r as (
           select id, parent_id, id as root_id from groups
           union all
           select t.id, t.parent_id, r.root_id from groups t, r where t.parent_id=r.id
         )
         select root_id, id from r order by root_id,id
        ')->execute();

        }

    public function safeDown()
    {
        $this->db->createCommand()->dropView('group_childs')->execute();
        $this->dropTableWithHistory('groups');
        $this->dropTable('files');
    }

}
