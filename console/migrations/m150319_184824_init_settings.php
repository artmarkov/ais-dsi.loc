<?php

class m150319_184824_init_settings extends yii\db\Migration
{

    const TABLE_NAME = '{{%setting}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'group' => $this->string(64)->defaultValue('general'),
            'key' => $this->string(64)->notNull(),
            'language' => $this->string(6),
            'value' => $this->text(),
            'description' => $this->text(),
        ], $tableOptions);

        $this->createIndex('setting_group_lang', self::TABLE_NAME, ['group', 'key', 'language']);
        
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'phone_mask', 'value' => '+7 (999) 999 99 99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'date_mask', 'value' => '99.99.9999']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'time_mask', 'value' => '99:99']);
        $this->insert(self::TABLE_NAME, ['group' => 'reading', 'key' => 'date_time_mask', 'value' => '99.99.9999 99:99']);
    }	

    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }

}
