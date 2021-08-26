<?php

class m210301_151052_create_table_creative extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('guide_creative_category', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(256)->notNull(),
            'description' => $this->string(1024)->notNull(),
        ], $tableOptions);

        $this->addCommentOnTable('guide_creative_category' ,'Категории творческих работ');
        $this->db->createCommand()->resetSequence('guide_creative_category', 1000)->execute();
        $this->db->createCommand()->batchInsert('guide_creative_category', ['id', 'name', 'description'], [
            [1000, 'Творческие работы', ''],
            [1001, 'Методические работы', ''],
            [1002, 'Сертификаты', ''],
        ])->execute();

        $this->createTableWithHistory('creative_works', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 99999)',
            'category_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(1024)->notNull(),
            'description' => $this->string(512),
            'department_list' => $this->string(1024),
            'teachers_list' => $this->string(1024),
            'published_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addCommentOnTable('creative_works' ,'Творческие и методические работы, сертификаты');
        $this->db->createCommand()->resetSequence('creative_works', 1000)->execute();

        $this->addForeignKey('creative_works_ibfk_1', 'creative_works', 'category_id', 'guide_creative_category', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_ibfk_2', 'creative_works', 'updated_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('creative_works_ibfk_3', 'creative_works', 'created_by', 'users', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function down()
    {
        $this->dropForeignKey('creative_works_ibfk_1','creative_works');
        $this->dropForeignKey('creative_works_ibfk_2','creative_works');
        $this->dropForeignKey('creative_works_ibfk_3','creative_works');
        $this->dropTableWithHistory('creative_works');
        $this->dropTable('guide_creative_category');
    }
}
