<?php


class m210309_064940_create_table_activities extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('guide_activities_cat', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(128)->notNull(),
            'color' => $this->string(32),
            'rendering' => $this->tinyInteger(1)->notNull()->defaultValue('0')->comment('как фон или бар'),
            'description' => $this->string(256),
        ], $tableOptions);

        $this->addCommentOnTable('guide_activities_cat' ,'Категории мероприятий');
        $this->db->createCommand()->resetSequence('guide_activities_cat', 1000)->execute();
        $this->db->createCommand()->batchInsert('guide_activities_cat', ['name', 'color'], [
            ['Индивидуальные занятия', '#4a86e8'],
            ['Мелкогрупповые занятия', '#e40000'],
            ['Групповые занятия', '#783f04'],
            ['Согласно плану работы', '#3c78d8'],
            ['Консультации', '#e69138'],
            ['Внеплановые мероприятия', '#6aa84f'],
        ])->execute();

        $this->createTableWithHistory('activities', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 10000 and 99999)',
            'category_id' => $this->integer()->notNull(),
            'auditory_id' => $this->integer()->notNull(),
            'title' => $this->string(100),
            'description' => $this->text(),
            'start_time' => $this->integer()->notNull(),
            'end_time' => $this->integer(),
            'all_day' => $this->tinyInteger(1)->defaultValue('0'),
        ], $tableOptions);

        $this->addCommentOnTable('activities' ,'Календарь мероприятий');
        $this->db->createCommand()->resetSequence('activities', 10000)->execute();

        $this->addForeignKey('activities_ibfk_1', 'activities', 'category_id', 'guide_activities_cat', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('activities_ibfk_2', 'activities', 'auditory_id', 'auditory', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTableWithHistory('activities');
        $this->dropTable('guide_activities_cat');
    }
}
