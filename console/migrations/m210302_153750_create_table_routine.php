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
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'color' => $this->string(127)->notNull(),
            'plan_flag' => $this->tinyInteger(1)->notNull()->comment('Учитывать при планировании'),
        ], $tableOptions);

        $this->addCommentOnTable('guide_routine_cat' ,'Категории производственного календаря');
        $this->db->createCommand()->batchInsert('guide_routine_cat', ['name', 'color', 'plan_flag'], [
            ['Каникулы', '#0000ff', 1],
            ['Праздники', '#ff0000', 1],
            ['Отпуск преподавателей', '#6aa84f', 1],
            ['Методический день', '#ff00ff', 1],
            ['Учебное время', '#ffd966', 0],
        ])->execute();

        $this->createTableWithHistory('routine', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 999999)',
            'description' => $this->string(1024)->notNull(),
            'cat_id' => $this->integer()->notNull(),
            'start_date' => $this->integer()->notNull(),
            'end_date' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('routine' ,'Производственный календарь');
        $this->db->createCommand()->resetSequence('routine', 1000)->execute();

        $this->addForeignKey('routine_ibfk_1', 'routine', 'cat_id', 'guide_routine_cat', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('routine');
        $this->dropTable('guide_routine_cat');
    }
}
