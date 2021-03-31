<?php


class m210302_153750_create_table_routine extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_routine_cat', [
            'id' => $this->primaryKey(8),
            'name' => $this->string(255)->notNull(),
            'color' => $this->string(127)->notNull(),
            'plan_flag' => $this->tinyInteger(1)->notNull()->comment('Учитывать при планировании'),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('guide_routine_cat', ['id', 'name', 'color', 'plan_flag'], [
            [1, 'Каникулы', '#0000ff', 1],
            [2, 'Праздники', '#ff0000', 1],
            [3, 'Отпуск преподавателей', '#6aa84f', 1],
            [4, 'Методический день', '#ff00ff', 1],
            [5, 'Учебное время', '#ffd966', 0],
        ])->execute();

        $this->createTable('routine', [
            'id' => $this->primaryKey(8),
            'description' => $this->string(1024)->notNull(),
            'cat_id' => $this->integer(8)->notNull(),
            'start_timestamp' => $this->integer()->notNull(),
            'end_timestamp' => $this->integer()->notNull(),
        ], $tableOptions);

//        $this->createIndex('cat_id', 'routine', 'cat_id');
        $this->addForeignKey('routine_ibfk_1', 'routine', 'cat_id', 'guide_routine_cat', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('routine');
        $this->dropTable('guide_routine_cat');
    }
}
