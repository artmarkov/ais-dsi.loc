<?php

class m210408_110456_create_table_teachers_efficiency extends \artsoft\db\BaseMigration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('guide_teachers_efficiency', [
            'id' =>  $this->primaryKey(),
            'name' => $this->string(128),
            'slug' => $this->string(32),
        ], $tableOptions);

        $this->db->createCommand()->batchInsert('guide_teachers_efficiency', ['id', 'name', 'slug'], [
            [1, '', ''],
            [2, '', ''],
        ])->execute();


        $this->createTableWithHistory('teachers_efficiency', [
            'id' => $this->primaryKey() . ' constraint check_range check (id between 1000 and 9999)',
            'efficiency_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->db->createCommand()->resetSequence('teachers_efficiency', 1000)->execute();
        $this->createIndex('efficiency_id', 'teachers_efficiency', 'efficiency_id');
        $this->addForeignKey('teachers_efficiency_ibfk_1', 'teachers_efficiency', 'efficiency_id', 'guide_teachers_efficiency', 'id', 'NO ACTION', 'NO ACTION');


    }

    public function down()
    {
        $this->dropForeignKey('teachers_efficiency', 'teachers_efficiency');
        $this->dropTableWithHistory('teachers_efficiency');
        $this->dropTable('guide_teachers_efficiency');
    }
}
