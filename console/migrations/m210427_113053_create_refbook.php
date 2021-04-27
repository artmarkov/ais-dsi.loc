<?php

/**
 * Class m210427_113053_create_refbook
 */
class m210427_113053_create_refbook extends \artsoft\db\BaseMigration
{
    /**
     * @return bool|void
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createTable('refbooks', [
            'name' => $this->string(50)->notNull(),
            'table_name' => $this->string(30)->notNull(),
            'key_field' => $this->string(30)->notNull(),
            'value_field' => $this->string(30)->notNull(),
            'sort_field' => $this->string(30)->notNull(),
            'ref_field' => $this->string(30),
            'group_field' => $this->string(30),
            'note' => $this->string(100)
        ]);
        $this->addPrimaryKey('refbooks_pkey', 'refbooks', 'name');
    }

    /**
     * @return bool|void
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropTable('refbooks');
    }
}
