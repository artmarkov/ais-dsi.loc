<?php

use yii\db\Migration;

class m210301_151059_019_create_table_teachers_bonus_item extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers_bonus_item}}', [
            'id' => $this->primaryKey(8),
            'bonus_category_id' => $this->integer(8)->notNull(),
            'name' => $this->string(127),
            'slug' => $this->string(32),
            'value_default' => $this->string(127),
            'measure_id' => $this->smallInteger(2)->unsigned()->comment('ед. измерения'),
            'bonus_rule_id' => $this->tinyInteger(2)->comment('правило обработки бонуса'),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue('1')->comment('1-активна, 0-удалена'),
        ], $tableOptions);

        $this->createIndex('status', '{{%teachers_bonus_item}}', 'status');
        $this->createIndex('bonus_category_id', '{{%teachers_bonus_item}}', 'bonus_category_id');
        $this->createIndex('bonus_rule_id', '{{%teachers_bonus_item}}', 'bonus_rule_id');
        $this->createIndex('measure_id', '{{%teachers_bonus_item}}', 'measure_id');
        $this->addForeignKey('teachers_bonus_item_ibfk_1', '{{%teachers_bonus_item}}', 'bonus_category_id', '{{%teachers_bonus_category}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('teachers_bonus_item_ibfk_2', '{{%teachers_bonus_item}}', 'measure_id', '{{%measure_unit}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('{{%teachers_bonus_item}}');
    }
}
