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
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'color' => $this->string(32),
            'rendering' => $this->tinyInteger(1)->notNull()->defaultValue('0')->comment('как фон или бар'),
            'description' => $this->string(256),
        ], $tableOptions);

        $this->createTableWithHistory('activities', [
            'id' => $this->primaryKey(),
            'category_id' => $this->smallInteger(3)->unsigned()->notNull(),
            'auditory_id' => $this->integer(8)->unsigned()->notNull(),
            'title' => $this->string(100),
            'description' => $this->text(),
            'start_timestamp' => $this->integer()->notNull(),
            'end_timestamp' => $this->integer(),
            'all_day' => $this->tinyInteger(1)->defaultValue('0'),
        ], $tableOptions);

//        $this->createIndex('auditory_id', 'activities', 'auditory_id');
//        $this->createIndex('category_id', 'activities', 'category_id');
        $this->addForeignKey('activities_ibfk_1', 'activities', 'category_id', 'guide_activities_cat', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('activities_ibfk_2', 'activities', 'auditory_id', 'auditory', 'id', 'RESTRICT', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTableWithHistory('activities');
        $this->dropTable('guide_activities_cat');
    }
}
